<?php

namespace App\Services;

use App\Models\DeltaChange;
use App\Models\HourlySnapshot;
use App\Models\RawIngest;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarineIngestService
{
    public function ingestHourly(): void
    {
        $provider = config('maris.marine.provider', 'stormglass');

        $success = 0;
        $failed = 0;

        if ($provider !== 'stormglass') {
            Log::channel('ingest')->warning('Marine ingest skipped: unsupported provider.', [
                'provider' => $provider,
            ]);
            return;
        }

        $apiKey = config('services.stormglass.key');
        $locations = $this->getLocations();

        if (!$apiKey || empty($locations)) {
            $this->recordSkipped($locations);
            Log::channel('ingest')->warning('Marine ingest skipped: missing API key or locations.');
            return;
        }

        foreach ($locations as $location) {
            $weather = $this->fetchStormglassWeather($apiKey, $location);
            $tide = $this->fetchStormglassTide($apiKey, $location);

            $payload = [
                'weather' => $weather,
                'tide' => $tide,
            ];

            $checksum = hash('sha256', json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

            $ingest = RawIngest::create([
                'source' => 'marine.stormglass',
                'region_code' => $location['code'],
                'region_name' => $location['name'],
                'fetched_at' => now(),
                'checksum' => $checksum,
                'payload' => $payload,
                'status' => ($weather['ok'] && $tide['ok']) ? 'ok' : 'partial',
                'error_message' => $this->buildErrorMessage($weather, $tide),
            ]);

            if (!$weather['ok'] || !$tide['ok']) {
                $failed++;
                Log::channel('ingest')->warning('Marine ingest partial failure.', [
                    'region' => $location['name'],
                    'error' => $ingest->error_message,
                ]);
            } else {
                $success++;
            }

            $this->recordDelta($ingest);
            $this->recordSnapshot($ingest);
        }

        Log::channel('ingest')->info('Marine ingest completed.', [
            'success' => $success,
            'failed' => $failed,
        ]);
    }

    private function getLocations(): array
    {
        $raw = config('maris.marine.locations', '');
        $items = array_filter(array_map('trim', explode('|', $raw)));

        return collect($items)
            ->map(function (string $item) {
                $parts = array_map('trim', explode(':', $item));

                if (count($parts) !== 3) {
                    return null;
                }

                return [
                    'name' => $parts[0],
                    'code' => $parts[0],
                    'lat' => (float) $parts[1],
                    'lng' => (float) $parts[2],
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function fetchStormglassWeather(string $apiKey, array $location): array
    {
        $response = Http::timeout(20)
            ->retry(2, 300)
            ->withHeaders(['Authorization' => $apiKey])
            ->get('https://api.stormglass.io/v2/weather/point', [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
                'params' => 'waveHeight,wavePeriod,waveDirection,windSpeed,windDirection,waterTemperature',
                'source' => 'noaa',
            ]);

        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }

    private function fetchStormglassTide(string $apiKey, array $location): array
    {
        $response = Http::timeout(20)
            ->retry(2, 300)
            ->withHeaders(['Authorization' => $apiKey])
            ->get('https://api.stormglass.io/v2/tide/point', [
                'lat' => $location['lat'],
                'lng' => $location['lng'],
            ]);

        return [
            'ok' => $response->ok(),
            'status' => $response->status(),
            'body' => $response->json(),
        ];
    }

    private function buildErrorMessage(array $weather, array $tide): ?string
    {
        if ($weather['ok'] && $tide['ok']) {
            return null;
        }

        $errors = [];

        if (!$weather['ok']) {
            $errors[] = "weather:HTTP {$weather['status']}";
        }

        if (!$tide['ok']) {
            $errors[] = "tide:HTTP {$tide['status']}";
        }

        return implode(', ', $errors);
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
        $metrics = $this->mapMarineMetrics($ingest->payload);

        HourlySnapshot::updateOrCreate(
            [
                'source' => $ingest->source,
                'region_name' => $ingest->region_name,
                'snapshot_at' => $ingest->fetched_at->copy()->startOfHour(),
            ],
            [
                'metrics' => $metrics,
            ]
        );
    }

    private function mapMarineMetrics(array $payload): array
    {
        $weather = data_get($payload, 'weather.body.hours', []);
        $tide = data_get($payload, 'tide.body.data', []);

        $now = now();

        $nearestWeather = $this->findNearestByTime($weather, $now, 'time');
        $nearestTide = $this->findNearestByTime($tide, $now, 'time');

        return array_filter([
            'waveHeight' => data_get($nearestWeather, 'waveHeight.noaa'),
            'wavePeriod' => data_get($nearestWeather, 'wavePeriod.noaa'),
            'waveDirection' => data_get($nearestWeather, 'waveDirection.noaa'),
            'windSpeed' => data_get($nearestWeather, 'windSpeed.noaa'),
            'windDirection' => data_get($nearestWeather, 'windDirection.noaa'),
            'waterTemperature' => data_get($nearestWeather, 'waterTemperature.noaa'),
            'tideHeight' => data_get($nearestTide, 'height'),
            'tideType' => data_get($nearestTide, 'type'),
        ], fn ($value) => $value !== null);
    }

    private function findNearestByTime(array $items, Carbon $now, string $key): ?array
    {
        if (empty($items)) {
            return null;
        }

        return collect($items)
            ->filter(fn ($item) => is_array($item) && !empty($item[$key] ?? null))
            ->map(function ($item) use ($now, $key) {
                $parsed = Carbon::parse($item[$key]);

                return [
                    'item' => $item,
                    'delta' => abs($parsed->diffInMinutes($now, false)),
                ];
            })
            ->sortBy('delta')
            ->first()['item'] ?? null;
    }

    private function recordSkipped(array $locations): void
    {
        $now = now();

        foreach ($locations as $location) {
            RawIngest::create([
                'source' => 'marine.stormglass',
                'region_code' => $location['code'],
                'region_name' => $location['name'],
                'fetched_at' => $now,
                'status' => 'skipped',
                'error_message' => 'Missing StormGlass API key or locations',
                'payload' => [],
            ]);
        }
    }
}
