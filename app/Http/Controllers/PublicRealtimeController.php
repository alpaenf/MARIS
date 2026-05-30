<?php

namespace App\Http\Controllers;

use App\Models\HourlySnapshot;
use App\Models\RawIngest;
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

        // ── Semua wilayah yang ada snapshot-nya ─────────────────────────
        $allRegionNames = HourlySnapshot::query()
            ->when($source !== '', fn ($q) => $q->where('source', $source))
            ->select('region_name')
            ->distinct()
            ->orderBy('region_name')
            ->pluck('region_name')
            ->values()
            ->all();

        // ── Wilayah terpilih (untuk detail tren) ────────────────────────
        $region = $request->string('region')->toString();
        if ($region === '' && !empty($allRegionNames)) {
            $region = $allRegionNames[0];
        }

        // ── Data Perbandingan: snapshot terbaru tiap wilayah ─────────────
        // Satu baris per wilayah = snapshot paling baru
        $compareData = collect($allRegionNames)->map(function ($regionName) use ($source) {
            $snap = HourlySnapshot::query()
                ->when($source !== '', fn ($q) => $q->where('source', $source))
                ->where('region_name', $regionName)
                ->latest('snapshot_at')
                ->first();

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

        return Inertia::render('Public/Realtime', [
            'filters' => [
                'source' => $source,
                'region' => $region,
                'hours'  => $hours,
            ],
            'sources'      => $sources,
            'regions'      => $allRegionNames,
            'compareData'  => $compareData,   // ← NEW: semua wilayah terbaru
            'series'       => $series,         // tren historis wilayah terpilih
            'status'       => $lastIngest ? [
                'time'  => $lastIngest->fetched_at?->toDateTimeString(),
                'state' => $lastIngest->status,
                'error' => $lastIngest->error_message,
            ] : null,
        ]);
    }
}
