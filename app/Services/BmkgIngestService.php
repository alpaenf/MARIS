<?php

namespace App\Services;

use App\Models\DeltaChange;
use App\Models\HourlySnapshot;
use App\Models\RawIngest;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BmkgIngestService
{
    public function __construct(
        private readonly ScientificCalculationService $calculator
    ) {}

    public function ingestHourly(): void
    {
        $baseUrl = config('services.bmkg.base_url');
        $adm4List = $this->getAdm4List();

        $success = 0;
        $failed = 0;

        if (empty($adm4List)) {
            Log::channel('ingest')->warning('BMKG ingest skipped: empty ADM4 list.');
            return;
        }

        foreach ($adm4List as $adm4) {
            $response = Http::timeout(20)
                ->retry(2, 300)
                ->get("{$baseUrl}/prakiraan-cuaca", [
                    'adm4' => $adm4,
                ]);

            if (!$response->ok()) {
                $failed++;
                RawIngest::create([
                    'source' => 'bmkg.prakiraan-cuaca',
                    'region_code' => $adm4,
                    'region_name' => $adm4,
                    'fetched_at' => now(),
                    'status' => 'error',
                    'error_message' => "HTTP {$response->status()}",
                    'payload' => ['body' => $response->body()],
                ]);
                Log::channel('ingest')->warning('BMKG ingest failed.', [
                    'adm4' => $adm4,
                    'status' => $response->status(),
                ]);
                continue;
            }

            $payload = $response->json();
            $regionName = $this->resolveRegionName($payload, $adm4);
            $checksum = hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            $ingest = RawIngest::create([
                'source' => 'bmkg.prakiraan-cuaca',
                'region_code' => $adm4,
                'region_name' => $regionName,
                'fetched_at' => now(),
                'checksum' => $checksum,
                'payload' => $payload,
                'status' => 'ok',
            ]);

            $this->recordDelta($ingest);
            $this->recordSnapshot($ingest);
            $success++;
        }

        Log::channel('ingest')->info('BMKG ingest completed.', [
            'success' => $success,
            'failed' => $failed,
        ]);
    }

    private function getAdm4List(): array
    {
        $raw = config('services.bmkg.adm4_list', '');
        $items = array_filter(array_map('trim', explode(',', $raw)));

        return array_values(array_unique($items));
    }

    private function resolveRegionName(array $payload, string $fallback): string
    {
        $candidates = [
            data_get($payload, 'lokasi.kecamatan'),
            data_get($payload, 'lokasi.desa'),
            data_get($payload, 'lokasi.kota'),
            data_get($payload, 'lokasi.provinsi'),
        ];

        foreach ($candidates as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return $fallback;
    }

    private function recordDelta(RawIngest $ingest): void
    {
        $previous = RawIngest::query()
            ->where('source', $ingest->source)
            ->where('region_code', $ingest->region_code)
            ->where('id', '!=', $ingest->id)
            ->latest('fetched_at')
            ->first();

        if (!$previous) {
            DeltaChange::create([
                'source' => $ingest->source,
                'region_name' => $ingest->region_name,
                'snapshot_at' => $ingest->fetched_at,
                'change_type' => 'created',
                'to_ingest_id' => $ingest->id,
            ]);
            return;
        }

        if ($previous->checksum === $ingest->checksum) {
            return;
        }

        DeltaChange::create([
            'source' => $ingest->source,
            'region_name' => $ingest->region_name,
            'snapshot_at' => $ingest->fetched_at,
            'change_type' => 'updated',
            'from_ingest_id' => $previous->id,
            'to_ingest_id' => $ingest->id,
        ]);
    }

    private function recordSnapshot(RawIngest $ingest): void
    {
        $forecast = $this->findNearestForecast($ingest->payload);
        $metrics = $forecast ? $this->mapForecastMetrics($forecast) : [];
        $analysisInput = $this->buildAnalysisInput($ingest, $metrics);
        $analysis = $analysisInput ? $this->calculator->compute($analysisInput) : [];

        HourlySnapshot::updateOrCreate(
            [
                'source' => $ingest->source,
                'region_name' => $ingest->region_name,
                'snapshot_at' => $ingest->fetched_at->copy()->startOfHour(),
            ],
            [
                'hazard_score' => $analysis['hazard_score'] ?? null,
                'mcvi_score' => $analysis['mcvi'] ?? null,
                'mrps_score' => $analysis['mrps'] ?? null,
                'carbon_tons' => $analysis['carbon_potential'] ?? null,
                'metrics' => array_merge($metrics, [
                    'analysis_input' => $analysisInput,
                ]),
            ]
        );
    }

    private function findNearestForecast(array $payload): ?array
    {
        $entries = collect(data_get($payload, 'data.0.cuaca', []))
            ->flatten(1)
            ->filter(fn ($item) => is_array($item));

        if ($entries->isEmpty()) {
            return null;
        }

        $now = now();

        return $entries
            ->map(function ($item) use ($now) {
                $time = data_get($item, 'local_datetime') ?? data_get($item, 'utc_datetime');
                $parsed = $time ? Carbon::parse($time) : null;

                return [
                    'item' => $item,
                    'delta' => $parsed ? abs($parsed->diffInMinutes($now, false)) : PHP_INT_MAX,
                ];
            })
            ->sortBy('delta')
            ->first()['item'] ?? null;
    }

    private function mapForecastMetrics(array $forecast): array
    {
        return Arr::only($forecast, [
            'local_datetime',
            'utc_datetime',
            't',
            'hu',
            'weather_desc',
            'weather_desc_en',
            'ws',
            'wd',
            'tcc',
            'vs_text',
            'analysis_date',
        ]);
    }

    private function buildAnalysisInput(RawIngest $ingest, array $metrics): ?array
    {
        if (empty($metrics)) {
            return null;
        }

        $defaults = config('maris.ingest.defaults', []);
        $override = config("maris.ingest.region_overrides.{$ingest->region_code}", []);
        $input = array_merge($defaults, $override);

        if (empty($input)) {
            return null;
        }

        $input['rainfall'] = $this->estimateRainfall($metrics, (int) ($input['rainfall'] ?? 0));

        return $input;
    }

    private function estimateRainfall(array $metrics, int $fallback): int
    {
        $desc = strtolower((string) ($metrics['weather_desc'] ?? ''));

        if ($desc === '') {
            return $fallback;
        }

        return match (true) {
            str_contains($desc, 'hujan lebat') => 3500,
            str_contains($desc, 'hujan sedang') => 2500,
            str_contains($desc, 'hujan ringan') => 1500,
            str_contains($desc, 'hujan') => 2000,
            str_contains($desc, 'berawan') => 1000,
            str_contains($desc, 'cerah') => 500,
            default => $fallback,
        };
    }
}
