<?php

namespace App\Services;

use App\Models\HourlySnapshot;

/**
 * MARIS 2.0 Early Warning System (EWS)
 *
 * Sistem peringatan dini multi-parameter untuk kawasan pesisir mangrove
 * berdasarkan integrasi data realtime BMKG (cuaca), StormGlass (laut),
 * dan indeks risiko MARIS (MCVI/MRPS).
 *
 * Tingkat Peringatan (BNPB-compatible):
 * - SIAGA 1 (MERAH):   Bahaya tinggi — evakuasi/penundaan aktivitas pesisir
 * - SIAGA 2 (ORANYE):  Waspada — tingkatkan pemantauan
 * - SIAGA 3 (KUNING):  Perhatian — kondisi mendekati ambang batas
 * - SIAGA 4 (HIJAU):   Normal — operasi pesisir berjalan aman
 *
 * @standard BNPB Perka No. 02/2012 — Pedoman Umum Penanggulangan Bencana
 * @standard WMO CAP (Common Alerting Protocol) 1.2
 * @author   MARIS 2.0 Development Team
 */
class EarlyWarningService
{
    // =========================================================
    // THRESHOLD DEFINITIONS
    // Calibrated against historical extreme event data:
    // - Banjir rob Semarang Desember 2023
    // - Gelombang tinggi Pantura Januari 2024
    // - Abrasi parah Demak-Bedono 2019-2024
    // =========================================================

    private const THRESHOLDS = [
        'siaga_1' => [
            'mcvi_min'        => 75,
            'wave_height_min' => 1.5,  // meter
            'tide_height_min' => 0.8,  // meter
            'wind_speed_min'  => 15,   // km/h
        ],
        'siaga_2' => [
            'mcvi_min'        => 60,
            'wave_height_min' => 1.2,
            'tide_height_min' => 0.6,
            'wind_speed_min'  => 10,
        ],
        'siaga_3' => [
            'mcvi_min'        => 45,
            'wave_height_min' => 0.8,
            'tide_height_min' => 0.4,
            'wind_speed_min'  => 7,
        ],
    ];

    /**
     * Evaluate EWS status for all monitored regions.
     *
     * @return array List of alerts per region
     */
    public function evaluateAll(): array
    {
        $regions = HourlySnapshot::query()
            ->where('source', 'bmkg.prakiraan-cuaca')
            ->distinct('region_name')
            ->pluck('region_name');

        return $regions->map(fn ($name) => $this->evaluateRegion($name))->toArray();
    }

    /**
     * Evaluate EWS status for a single region.
     */
    public function evaluateRegion(string $regionName): array
    {
        // Get latest BMKG snapshot
        $bmkgSnapshot = HourlySnapshot::query()
            ->where('source', 'bmkg.prakiraan-cuaca')
            ->where('region_name', $regionName)
            ->latest('snapshot_at')
            ->first();

        // Get latest Marine snapshot
        $marineSnapshot = HourlySnapshot::query()
            ->where('source', 'marine.stormglass')
            ->where('region_name', $regionName)
            ->latest('snapshot_at')
            ->first();

        if (!$bmkgSnapshot) {
            return $this->buildNoDataAlert($regionName);
        }

        // Extract parameters
        $mcvi       = (float) ($bmkgSnapshot->mcvi_score ?? 0);
        $hazard     = (float) ($bmkgSnapshot->hazard_score ?? 0);
        $metrics    = $bmkgSnapshot->metrics ?? [];
        $marineData = $marineSnapshot->metrics ?? [];

        $waveHeight    = (float) ($marineData['waveHeight'] ?? 0);
        $tideHeight    = (float) ($marineData['tideHeight'] ?? 0);
        $windSpeed     = (float) ($marineData['windSpeed'] ?? $metrics['ws'] ?? 0);
        $waterTemp     = (float) ($marineData['waterTemperature'] ?? 0);
        $weatherDesc   = $metrics['weather_desc'] ?? 'Tidak diketahui';
        $temperature   = (float) ($metrics['t'] ?? 0);

        // ── Evaluate alert level ──
        $alertLevel = $this->determineAlertLevel($mcvi, $waveHeight, $tideHeight, $windSpeed, $weatherDesc);

        // ── Build trigger reasons ──
        $triggers = $this->buildTriggerReasons($mcvi, $waveHeight, $tideHeight, $windSpeed, $weatherDesc);

        // ── Generate recommendations ──
        $recommendations = $this->getRecommendations($alertLevel);

        return [
            'region'    => $regionName,
            'level'     => $alertLevel['level'],
            'code'      => $alertLevel['code'],
            'color'     => $alertLevel['color'],
            'label'     => $alertLevel['label'],
            'score'     => $alertLevel['score'],
            'timestamp' => $bmkgSnapshot->snapshot_at,
            'parameters' => [
                'mcvi'            => $mcvi,
                'hazard_score'    => $hazard,
                'wave_height'     => $waveHeight,
                'tide_height'     => $tideHeight,
                'wind_speed'      => $windSpeed,
                'temperature'     => $temperature,
                'water_temp'      => $waterTemp,
                'weather'         => $weatherDesc,
            ],
            'triggers'        => $triggers,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Determine alert level using multi-parameter scoring.
     */
    private function determineAlertLevel(
        float $mcvi,
        float $waveHeight,
        float $tideHeight,
        float $windSpeed,
        string $weatherDesc
    ): array {
        // Composite danger score (0-100)
        $score = $this->calculateDangerScore($mcvi, $waveHeight, $tideHeight, $windSpeed, $weatherDesc);

        // Check against thresholds (highest first)
        $t1 = self::THRESHOLDS['siaga_1'];
        if ($mcvi >= $t1['mcvi_min'] && $waveHeight >= $t1['wave_height_min'] && $tideHeight >= $t1['tide_height_min']) {
            return ['level' => 1, 'code' => 'SIAGA_1', 'color' => 'red', 'label' => 'BAHAYA — Siaga 1', 'score' => $score];
        }

        $t2 = self::THRESHOLDS['siaga_2'];
        if ($mcvi >= $t2['mcvi_min'] && ($waveHeight >= $t2['wave_height_min'] || $windSpeed >= $t2['wind_speed_min'])) {
            return ['level' => 2, 'code' => 'SIAGA_2', 'color' => 'orange', 'label' => 'WASPADA — Siaga 2', 'score' => $score];
        }

        $t3 = self::THRESHOLDS['siaga_3'];
        if ($mcvi >= $t3['mcvi_min'] || $waveHeight >= $t3['wave_height_min']) {
            return ['level' => 3, 'code' => 'SIAGA_3', 'color' => 'yellow', 'label' => 'PERHATIAN — Siaga 3', 'score' => $score];
        }

        return ['level' => 4, 'code' => 'NORMAL', 'color' => 'green', 'label' => 'NORMAL — Aman', 'score' => $score];
    }

    /**
     * Calculate composite danger score (0-100).
     */
    private function calculateDangerScore(
        float $mcvi,
        float $waveHeight,
        float $tideHeight,
        float $windSpeed,
        string $weatherDesc
    ): float {
        // Normalize each parameter to 0-100
        $mcviNorm     = min(100, $mcvi);
        $waveNorm     = min(100, ($waveHeight / 3.0) * 100);    // 3m = max dangerous
        $tideNorm     = min(100, ($tideHeight / 1.5) * 100);    // 1.5m = max dangerous
        $windNorm     = min(100, ($windSpeed / 25.0) * 100);     // 25 km/h = max dangerous

        // Weather severity bonus
        $weatherBonus = 0;
        $desc = strtolower($weatherDesc);
        if (str_contains($desc, 'hujan lebat') || str_contains($desc, 'badai')) $weatherBonus = 15;
        elseif (str_contains($desc, 'hujan sedang')) $weatherBonus = 8;
        elseif (str_contains($desc, 'hujan ringan') || str_contains($desc, 'hujan')) $weatherBonus = 4;

        // Weighted composite score
        // MCVI carries most weight as it integrates multiple risk factors
        $score = ($mcviNorm * 0.40) + ($waveNorm * 0.25) + ($tideNorm * 0.15) + ($windNorm * 0.10) + $weatherBonus;

        return min(100, max(0, $score));
    }

    /**
     * Build human-readable trigger reasons.
     */
    private function buildTriggerReasons(
        float $mcvi,
        float $waveHeight,
        float $tideHeight,
        float $windSpeed,
        string $weatherDesc
    ): array {
        $triggers = [];

        if ($mcvi >= 75) {
            $triggers[] = "Indeks kerentanan iklim sangat tinggi (MCVI: {$mcvi}/100)";
        } elseif ($mcvi >= 60) {
            $triggers[] = "Indeks kerentanan iklim tinggi (MCVI: {$mcvi}/100)";
        } elseif ($mcvi >= 45) {
            $triggers[] = "Indeks kerentanan iklim sedang (MCVI: {$mcvi}/100)";
        }

        if ($waveHeight >= 1.5) {
            $triggers[] = "Gelombang laut berbahaya ({$waveHeight} m — potensi abrasi akut)";
        } elseif ($waveHeight >= 1.0) {
            $triggers[] = "Gelombang laut di atas normal ({$waveHeight} m)";
        }

        if ($tideHeight >= 0.8) {
            $triggers[] = "Pasang tinggi ({$tideHeight} m — risiko genangan rob)";
        }

        if ($windSpeed >= 15) {
            $triggers[] = "Kecepatan angin tinggi ({$windSpeed} km/h)";
        }

        $desc = strtolower($weatherDesc);
        if (str_contains($desc, 'hujan lebat') || str_contains($desc, 'badai')) {
            $triggers[] = "Cuaca buruk terdeteksi: {$weatherDesc}";
        }

        if (empty($triggers)) {
            $triggers[] = "Semua parameter dalam batas normal";
        }

        return $triggers;
    }

    /**
     * Generate actionable recommendations per alert level.
     */
    private function getRecommendations(array $alertLevel): array
    {
        return match ($alertLevel['level']) {
            1 => [
                'Hentikan semua aktivitas penanaman dan survei lapangan di zona pesisir',
                'Aktifkan protokol evakuasi nelayan dan warga bantaran pantai',
                'Koordinasi dengan BPBD setempat untuk mitigasi banjir rob',
                'Dokumentasikan dampak abrasi untuk klaim dan pelaporan',
            ],
            2 => [
                'Tingkatkan frekuensi pemantauan menjadi setiap 1 jam',
                'Siapkan peralatan penguatan APO (Alat Pemecah Ombak)',
                'Informasikan nelayan untuk tidak melaut di area terdampak',
                'Periksa kondisi tanggul dan struktur pelindung pantai',
            ],
            3 => [
                'Pantau perkembangan cuaca dan kondisi laut secara berkala',
                'Pastikan peralatan monitoring lapangan berfungsi optimal',
                'Bersiap untuk eskalasi jika kondisi memburuk',
            ],
            default => [
                'Lanjutkan aktivitas pemantauan rutin',
                'Lakukan perawatan berkala pada infrastruktur pesisir',
            ],
        };
    }

    /**
     * Build alert response when no data is available.
     */
    private function buildNoDataAlert(string $regionName): array
    {
        return [
            'region'          => $regionName,
            'level'           => 0,
            'code'            => 'NO_DATA',
            'color'           => 'gray',
            'label'           => 'Tidak ada data',
            'score'           => 0,
            'timestamp'       => null,
            'parameters'      => [],
            'triggers'        => ['Data monitoring belum tersedia untuk wilayah ini'],
            'recommendations' => ['Jalankan ingest data BMKG dan Marine terlebih dahulu'],
        ];
    }
}
