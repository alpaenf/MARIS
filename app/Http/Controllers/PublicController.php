<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PublicController extends Controller
{
    public function index(): Response
    {
        $analyses = \App\Models\Analysis::query()
            ->latest()
            ->get()
            ->map(function ($item) {
                // Cari snapshot BMKG terupdate dengan pencocokan fleksibel
                $bmkgSnap = $this->findLatestSnapshot($item->region_name, 'bmkg.prakiraan-cuaca');

                // Cari snapshot Marine terupdate dengan pencocokan fleksibel
                $marineSnap = $this->findLatestSnapshot($item->region_name, 'marine.stormglass');

                $item->realtime_weather = $bmkgSnap ? [
                    'temp' => $bmkgSnap->metrics['t'] ?? null,
                    'humidity' => $bmkgSnap->metrics['hu'] ?? null,
                    'desc' => $bmkgSnap->metrics['weather_desc'] ?? null,
                    'wind_speed' => $bmkgSnap->metrics['ws'] ?? null,
                ] : null;

                $item->realtime_marine = $marineSnap ? [
                    'wave_height' => $marineSnap->metrics['waveHeight'] ?? null,
                    'wind_speed' => $marineSnap->metrics['windSpeed'] ?? null,
                    'water_temp' => $marineSnap->metrics['waterTemperature'] ?? null,
                    'tide_height' => $marineSnap->metrics['tideHeight'] ?? null,
                ] : null;

                return $item;
            });

        return Inertia::render('Public/Index', [
            'analyses' => $analyses,
        ]);
    }

    public function methodology(): Response
    {
        return Inertia::render('Public/Methodology');
    }

    public function compare(): Response
    {
        $analyses = \App\Models\Analysis::query()
            ->where('seeded_by_admin', true)
            ->get();

        return Inertia::render('Public/Compare', [
            'analyses' => $analyses,
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
