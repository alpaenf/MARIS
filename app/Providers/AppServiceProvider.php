<?php

namespace App\Providers;

use App\Models\Analysis;
use App\Models\User;
use App\Services\ScientificCalculationService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // ── Auto-seed 4 wilayah pesisir Jawa Tengah ──────────────────────
        // Dijalankan sekali saat boot jika tabel sudah ada tapi preset belum.
        // Aman dijalankan berulang (updateOrCreate = idempotent).
        $this->autoSeedRegions();
    }

    /**
     * Pastikan 4 preset wilayah rawan abrasi Jawa Tengah selalu tersedia.
     *
     * Logika:
     *  1. Cek apakah tabel analyses & users sudah exist (jaga-jaga fresh install)
     *  2. Cek apakah sudah ada record seeded_by_admin
     *  3. Jika belum ada → seed otomatis menggunakan ScientificCalculationService
     *
     * Referensi data: KLHK 2023, InaRISK BNPB, Global Mangrove Watch v3.0
     */
    private function autoSeedRegions(): void
    {
        // Guard: jangan jalan sebelum migrasi selesai
        if (! Schema::hasTable('analyses') || ! Schema::hasTable('users')) {
            return;
        }

        // Sudah ada 5 preset → skip (tidak perlu seed ulang)
        if (Analysis::where('seeded_by_admin', true)->count() >= 5) {
            return;
        }

        // Ambil atau buat admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@maris.id'],
            [
                'name'     => 'Admin MARIS',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        /** @var ScientificCalculationService $calc */
        $calc = $this->app->make(ScientificCalculationService::class);

        // ── 5 Wilayah Pesisir Jawa Tengah Rawan Abrasi ───────────────────
        $regions = [

            // Sayung-Bedono, Demak — paling kritis, kehilangan ±5 ha/tahun
            [
                'region_name'        => 'Sayung-Bedono',
                'province'           => 'Jawa Tengah',
                'area_size'          => 145.0,
                'rainfall'           => 2200,
                'abrasion_level'     => 5,
                'flood_risk'         => 5,
                'mangrove_loss'      => 68,
                'population_density' => 1850,
                'dominant_species'   => 'Rhizophora mucronata',
                'soil_type'          => 'mineral',
            ],

            // Kaliwungu, Kendal — abrasi >200m dalam 10 tahun
            [
                'region_name'        => 'Kaliwungu',
                'province'           => 'Jawa Tengah',
                'area_size'          => 320.0,
                'rainfall'           => 2000,
                'abrasion_level'     => 4,
                'flood_risk'         => 3,
                'mangrove_loss'      => 42,
                'population_density' => 2100,
                'dominant_species'   => 'Avicennia marina',
                'soil_type'          => 'mineral',
            ],

            // Tegal Barat, Kota Tegal — pesisir padat, banjir rob tahunan
            [
                'region_name'        => 'Tegal Barat',
                'province'           => 'Jawa Tengah',
                'area_size'          => 210.0,
                'rainfall'           => 1950,
                'abrasion_level'     => 4,
                'flood_risk'         => 4,
                'mangrove_loss'      => 52,
                'population_density' => 3800,
                'dominant_species'   => 'Avicennia marina',
                'soil_type'          => 'mineral',
            ],

            // Brebes-Randusanga, Brebes — mangrove terluas, tekanan tambak
            [
                'region_name'        => 'Brebes-Randusanga',
                'province'           => 'Jawa Tengah',
                'area_size'          => 410.0,
                'rainfall'           => 1900,
                'abrasion_level'     => 3,
                'flood_risk'         => 3,
                'mangrove_loss'      => 35,
                'population_density' => 1200,
                'dominant_species'   => 'Sonneratia alba',
                'soil_type'          => 'mineral',
            ],

            // Pekalongan-Tirto, Pekalongan — banjir rob sangat parah & abrasi kritis
            [
                'region_name'        => 'Pekalongan-Tirto',
                'province'           => 'Jawa Tengah',
                'area_size'          => 185.0,
                'rainfall'           => 2400,
                'abrasion_level'     => 5,
                'flood_risk'         => 5,
                'mangrove_loss'      => 74,
                'population_density' => 4200,
                'dominant_species'   => 'Rhizophora mucronata',
                'soil_type'          => 'mineral',
            ],
        ];

        foreach ($regions as $data) {
            // Hitung semua skor ilmiah (IPCC AR6, MCVI, MRPS, Blue Carbon)
            $computed = $calc->compute($data);

            Analysis::updateOrCreate(
                [
                    'seeded_by_admin' => true,
                    'region_name'     => $data['region_name'],
                ],
                array_merge($data, [
                    'user_id'              => $admin->id,
                    'seeded_by_admin'      => true,
                    // IPCC AR6 Pillars
                    'hazard_score'         => $computed['hazard_score'],
                    'exposure_score'       => $computed['exposure_score'],
                    'vulnerability_score'  => $computed['vulnerability_score'],
                    // Core Indices
                    'climate_risk_score'   => $computed['climate_risk_score'],
                    'mcvi'                 => $computed['mcvi'],
                    'mrps'                 => $computed['mrps'],
                    'restoration_priority' => $computed['restoration_priority'],
                    // Blue Carbon Tier-2
                    'carbon_agb'           => $computed['carbon_agb'],
                    'carbon_bgb'           => $computed['carbon_bgb'],
                    'carbon_soc'           => $computed['carbon_soc'],
                    'carbon_potential'     => $computed['carbon_potential'],
                    // Payloads
                    'dpsir_payload'        => $computed['dpsir_payload'],
                    'result_payload'       => $computed,
                ])
            );
        }

        // ── Auto-seed mock snapshots jika tabel hourly_snapshots masih kosong ──
        if (Schema::hasTable('hourly_snapshots') && \App\Models\HourlySnapshot::count() < 10) {
            $mockRegions = [
                'Bedono' => [
                    'temp' => 28, 'desc' => 'Hujan Ringan', 'wave' => 1.2, 'water_temp' => 29, 'tide' => 0.8
                ],
                'Kaliwungu' => [
                    'temp' => 30, 'desc' => 'Cerah Berawan', 'wave' => 0.6, 'water_temp' => 30, 'tide' => 0.4
                ],
                'Tegal Barat' => [
                    'temp' => 31, 'desc' => 'Cerah', 'wave' => 0.8, 'water_temp' => 29, 'tide' => 0.5
                ],
                'Randusanga Kulon' => [
                    'temp' => 29, 'desc' => 'Berawan', 'wave' => 1.1, 'water_temp' => 28, 'tide' => 0.7
                ],
                'Tirto' => [
                    'temp' => 27, 'desc' => 'Hujan Lebat', 'wave' => 1.5, 'water_temp' => 28, 'tide' => 0.9
                ],
            ];

            $now = now()->startOfHour();
            for ($i = 0; $i < 24; $i++) {
                $time = $now->copy()->subHours($i);
                foreach ($mockRegions as $name => $mock) {
                    // BMKG Weather Snapshot
                    \App\Models\HourlySnapshot::create([
                        'source' => 'bmkg.prakiraan-cuaca',
                        'region_name' => $name,
                        'snapshot_at' => $time,
                        'metrics' => [
                            't' => $mock['temp'] + rand(-1, 1),
                            'hu' => 80 + rand(-5, 5),
                            'weather_desc' => $mock['desc'],
                            'ws' => 6 + rand(-2, 2),
                            'wd' => 'Barat Laut',
                        ]
                    ]);

                    // Marine Waves Snapshot
                    \App\Models\HourlySnapshot::create([
                        'source' => 'marine.stormglass',
                        'region_name' => $name,
                        'snapshot_at' => $time,
                        'metrics' => [
                            'waveHeight' => round($mock['wave'] + (rand(-2, 2) / 10), 2),
                            'windSpeed' => round(5.4 + (rand(-10, 10) / 10), 2),
                            'waterTemperature' => $mock['water_temp'],
                            'tideHeight' => round($mock['tide'] + (rand(-2, 2) / 10), 2),
                        ]
                    ]);
                }
            }
        }
    }
}
