<?php

namespace App\Http\Controllers;

use App\Models\HourlySnapshot;
use App\Models\RawIngest;
use App\Models\Analysis;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicRealtimeController extends Controller
{
    public function index(Request $request): Response
    {
        $sources = HourlySnapshot::query()
            ->select('source')
            ->distinct()
            ->pluck('source')
            ->values()
            ->all();

        $source = $request->string('source')->toString();
        if ($source === '' && !empty($sources)) {
            $source = $sources[0];
        }

        $hours = (int) ($request->query('hours', 24));
        $hours = max(6, min($hours, 168));

        // ── Ambil 5 wilayah pesisir utama dari tabel Analysis ─────────────────────────
        $allRegionNames = Analysis::query()
            ->where('seeded_by_admin', true)
            ->orderBy('region_name')
            ->pluck('region_name')
            ->values()
            ->all();

        // ── Wilayah terpilih (untuk detail tren) ────────────────────────
        $region = $request->string('region')->toString();
        if ($region === '' && !empty($allRegionNames)) {
            $region = $allRegionNames[0];
        }

        // ── Data Perbandingan: snapshot terbaru tiap wilayah pesisir ─────────────
        // Satu baris per wilayah = snapshot paling baru (dicari dengan fuzzy matching)
        $compareData = collect($allRegionNames)->map(function ($regionName) use ($source) {
            $snap = $this->findLatestSnapshot($regionName, $source);

            if (!$snap) {
                return [
                    'region'  => $regionName,
                    'time'    => null,
                    'hazard'  => null,
                    'mcvi'    => null,
                    'mrps'    => null,
                    'carbon'  => null,
                    'metrics' => [],
                ];
            }

            return [
                'region'  => $regionName,
                'time'    => $snap->snapshot_at->toDateTimeString(),
                'hazard'  => $snap->hazard_score,
                'mcvi'    => $snap->mcvi_score,
                'mrps'    => $snap->mrps_score,
                'carbon'  => $snap->carbon_tons,
                'metrics' => $snap->metrics ?? [],
            ];
        })->values()->all();

        // ── Tren historis wilayah terpilih ───────────────────────────────
        $seriesQuery = HourlySnapshot::query()
            ->when($source !== '', fn ($q) => $q->where('source', $source))
            ->when($region !== '', fn ($q) => $q->where('region_name', $region))
            ->where('snapshot_at', '>=', now()->subHours($hours))
            ->orderBy('snapshot_at');

        $series = $seriesQuery->get([
            'snapshot_at',
            'hazard_score',
            'mcvi_score',
            'mrps_score',
            'carbon_tons',
        ])->map(fn ($row) => [
            'time'   => $row->snapshot_at->toDateTimeString(),
            'hazard' => $row->hazard_score,
            'mcvi'   => $row->mcvi_score,
            'mrps'   => $row->mrps_score,
            'carbon' => $row->carbon_tons,
        ])->values();

        // ── Status ingest terakhir ────────────────────────────────────────
        $lastIngest = RawIngest::query()
            ->when($source !== '', fn ($q) => $q->where('source', $source))
            ->latest('fetched_at')
            ->first();

        // ── Forecast Engine (MARIS 2.0) ─────────────────────────────────
        $forecastService = app(\App\Services\ForecastingService::class);
        $forecastData = $forecastService->forecastAll();

        // ── Early Warning System (MARIS 2.0) ────────────────────────────
        $ewsService = app(\App\Services\EarlyWarningService::class);
        $ewsAlerts = collect(['Bedono', 'Kaliwungu', 'Tegal Barat', 'Randusanga Kulon', 'Tirto'])->map(function ($name) use ($ewsService) {
            return $ewsService->evaluateRegion($name);
        })->toArray();

        // ── ML Prediction (MARIS 2.0) ───────────────────────────────────
        $mlService = app(\App\Services\MangroveMLService::class);
        $mlPredictions = collect($allRegionNames)->map(function ($name) use ($mlService) {
            $analysis = Analysis::where('region_name', $name)->latest()->first();
            if (!$analysis) return null;
            $prediction = $mlService->predict($analysis->toArray(), 10); // Predict 10 years!
            $prediction['region'] = $name;
            return $prediction;
        })->filter()->values()->all();

        return Inertia::render('Public/Realtime', [
            'filters' => [
                'source' => $source,
                'region' => $region,
                'hours'  => $hours,
            ],
            'sources'        => $sources,
            'regions'        => $allRegionNames,
            'compareData'    => $compareData,
            'series'         => $series,
            'status'         => $lastIngest ? [
                'time'  => $lastIngest->fetched_at?->toDateTimeString(),
                'state' => $lastIngest->status,
                'error' => $lastIngest->error_message,
            ] : null,
            // MARIS 2.0 — New Data Streams
            'forecastData'   => $forecastData,
            'ewsAlerts'      => $ewsAlerts,
            'mlPredictions'  => $mlPredictions,
        ]);
    }

    /**
     * Algoritma pencocokan nama wilayah yang sangat fleksibel (Fuzzy Matching)
     * Menghubungkan nama wilayah analisis (misal: Sayung-Bedono) dengan nama wilayah di snapshot BMKG (misal: Bedono)
     */
    private function findLatestSnapshot(string $regionName, string $source): ?\App\Models\HourlySnapshot
    {
        $parts = array_filter(array_map('trim', explode('-', $regionName)));

        // Ambil data snapshot terbaru untuk source terkait
        $snapshots = \App\Models\HourlySnapshot::where('source', $source)
            ->latest('snapshot_at')
            ->get();

        foreach ($snapshots as $snap) {
            $snapName = strtolower($snap->region_name);

            // 1. Cocok persis
            if ($snapName === strtolower($regionName)) {
                return $snap;
            }

            // 2. Cocok sebagian (Fuzzy match)
            foreach ($parts as $part) {
                $partLower = strtolower($part);
                if (str_contains($snapName, $partLower) || str_contains($partLower, $snapName)) {
                    return $snap;
                }
            }
        }

        return null;
    }
}
