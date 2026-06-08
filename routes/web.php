<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PublicRealtimeController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SimulationController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [PublicController::class, 'index']);

Route::get('/realtime', [PublicRealtimeController::class, 'index'])->name('public.realtime');
Route::get('/methodology', [PublicController::class, 'methodology'])->name('public.methodology');
Route::get('/compare', [PublicController::class, 'compare'])->name('public.compare');

if (app()->environment('local')) {
    Route::get('/seed-admin', function () {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@maris.id'],
            [
                'name' => 'Admin MARIS',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
        return 'Admin successfully seeded! You can now log in using admin@maris.id / admin123';
    });

    // ── Route darurat: seed 4 preset wilayah tanpa artisan ──────────────────
    Route::get('/seed-regions', function () {
        $adminId = \App\Models\User::where('email', 'admin@maris.id')->value('id');
        if (!$adminId) {
            return 'Admin user belum ada. Buka /seed-admin terlebih dahulu.';
        }

        $calc = app(\App\Services\ScientificCalculationService::class);

        $regions = [
            [
                'region_name' => 'Sayung-Bedono', 'province' => 'Jawa Tengah',
                'area_size' => 145.0, 'rainfall' => 2200, 'abrasion_level' => 5,
                'flood_risk' => 5, 'mangrove_loss' => 68, 'population_density' => 1850,
                'dominant_species' => 'Rhizophora mucronata', 'soil_type' => 'mineral',
            ],
            [
                'region_name' => 'Kaliwungu', 'province' => 'Jawa Tengah',
                'area_size' => 320.0, 'rainfall' => 2000, 'abrasion_level' => 4,
                'flood_risk' => 3, 'mangrove_loss' => 42, 'population_density' => 2100,
                'dominant_species' => 'Avicennia marina', 'soil_type' => 'mineral',
            ],
            [
                'region_name' => 'Tegal Barat', 'province' => 'Jawa Tengah',
                'area_size' => 210.0, 'rainfall' => 1950, 'abrasion_level' => 4,
                'flood_risk' => 4, 'mangrove_loss' => 52, 'population_density' => 3800,
                'dominant_species' => 'Avicennia marina', 'soil_type' => 'mineral',
            ],
            [
                'region_name' => 'Brebes-Randusanga', 'province' => 'Jawa Tengah',
                'area_size' => 410.0, 'rainfall' => 1900, 'abrasion_level' => 3,
                'flood_risk' => 3, 'mangrove_loss' => 35, 'population_density' => 1200,
                'dominant_species' => 'Sonneratia alba', 'soil_type' => 'mineral',
            ],
            [
                'region_name' => 'Pekalongan-Tirto', 'province' => 'Jawa Tengah',
                'area_size' => 185.0, 'rainfall' => 2400, 'abrasion_level' => 5,
                'flood_risk' => 5, 'mangrove_loss' => 74, 'population_density' => 4200,
                'dominant_species' => 'Rhizophora mucronata', 'soil_type' => 'mineral',
            ],
        ];

        $results = [];
        foreach ($regions as $data) {
            $computed = $calc->compute($data);
            \App\Models\Analysis::updateOrCreate(
                ['seeded_by_admin' => true, 'region_name' => $data['region_name']],
                array_merge($data, [
                    'user_id'              => $adminId,
                    'seeded_by_admin'      => true,
                    'hazard_score'         => $computed['hazard_score'],
                    'exposure_score'       => $computed['exposure_score'],
                    'vulnerability_score'  => $computed['vulnerability_score'],
                    'climate_risk_score'   => $computed['climate_risk_score'],
                    'mcvi'                 => $computed['mcvi'],
                    'mrps'                 => $computed['mrps'],
                    'restoration_priority' => $computed['restoration_priority'],
                    'carbon_agb'           => $computed['carbon_agb'],
                    'carbon_bgb'           => $computed['carbon_bgb'],
                    'carbon_soc'           => $computed['carbon_soc'],
                    'carbon_potential'     => $computed['carbon_potential'],
                    'dpsir_payload'        => $computed['dpsir_payload'],
                    'result_payload'       => $computed,
                ])
            );
            $results[] = "✅ {$data['region_name']} — Risk: {$computed['climate_risk_score']}, MCVI: {$computed['mcvi']}, Prioritas: {$computed['restoration_priority']}";
        }

        return implode('<br>', $results) . '<br><br>🎉 5 preset wilayah berhasil di-seed! Kembali ke /analysis/create';
    });

    // ── Route darurat: force seed snapshot cuaca realtime tiruan ────────────────
    Route::get('/seed-snapshots-force', function () {
        \App\Models\HourlySnapshot::truncate();

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
        $insertedCount = 0;

        for ($i = 0; $i < 24; $i++) {
            $time = $now->copy()->subHours($i);
            foreach ($mockRegions as $name => $mock) {
                $scores = match ($name) {
                    'Bedono' => ['hazard' => 91.0, 'mcvi' => 62.13, 'mrps' => 55.51, 'carbon' => 42429],
                    'Kaliwungu' => ['hazard' => 74.0, 'mcvi' => 47.55, 'mrps' => 42.67, 'carbon' => 110420],
                    'Tegal Barat' => ['hazard' => 73.75, 'mcvi' => 53.43, 'mrps' => 50.51, 'carbon' => 65726],
                    'Randusanga Kulon' => ['hazard' => 57.5, 'mcvi' => 37.71, 'mrps' => 34.15, 'carbon' => 172502],
                    'Tirto' => ['hazard' => 88.0, 'mcvi' => 72.0, 'mrps' => 78.0, 'carbon' => 88720],
                    default => ['hazard' => 60.0, 'mcvi' => 50.0, 'mrps' => 50.0, 'carbon' => 50000],
                };

                // BMKG Weather Snapshot
                \App\Models\HourlySnapshot::create([
                    'source' => 'bmkg.prakiraan-cuaca',
                    'region_name' => $name,
                    'snapshot_at' => $time,
                    'hazard_score' => $scores['hazard'],
                    'mcvi_score' => $scores['mcvi'],
                    'mrps_score' => $scores['mrps'],
                    'carbon_tons' => $scores['carbon'],
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
                    'hazard_score' => $scores['hazard'],
                    'mcvi_score' => $scores['mcvi'],
                    'mrps_score' => $scores['mrps'],
                    'carbon_tons' => $scores['carbon'],
                    'metrics' => [
                        'waveHeight' => round($mock['wave'] + (rand(-2, 2) / 10), 2),
                        'windSpeed' => round(5.4 + (rand(-10, 10) / 10), 2),
                        'waterTemperature' => $mock['water_temp'],
                        'tideHeight' => round($mock['tide'] + (rand(-2, 2) / 10), 2),
                    ]
                ]);

                $insertedCount += 2;
            }
        }

        return "🎉 Berhasil melakukan force-seed {$insertedCount} data cuaca realtime tiruan! Buka kembali beranda utama untuk melihat data realtime di peta!";
    });
}



Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

Route::middleware(['auth', 'verified', 'role:admin,analis'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index')->middleware('role:admin');
    Route::post('/admin/users', [\App\Http\Controllers\AdminController::class, 'storeUser'])->name('admin.users.store')->middleware('role:admin');
    Route::patch('/admin/users/{user}', [\App\Http\Controllers\AdminController::class, 'updateUserRole'])->name('admin.users.update')->middleware('role:admin');
    Route::delete('/admin/users/{user}', [\App\Http\Controllers\AdminController::class, 'destroyUser'])->name('admin.users.destroy')->middleware('role:admin');
    Route::post('/admin/regions', [\App\Http\Controllers\AdminController::class, 'storeRegion'])->name('admin.regions.store')->middleware('role:admin');
    Route::delete('/admin/regions/{analysis}', [\App\Http\Controllers\AdminController::class, 'destroyRegion'])->name('admin.regions.destroy')->middleware('role:admin');
    Route::post('/admin/regions/import', [\App\Http\Controllers\AdminController::class, 'importCSV'])->name('admin.regions.import')->middleware('role:admin');
    Route::post('/admin/datasets', [\App\Http\Controllers\AdminController::class, 'storeDataset'])->name('admin.datasets.store')->middleware('role:admin');
    Route::delete('/admin/datasets/{dataset}', [\App\Http\Controllers\AdminController::class, 'destroyDataset'])->name('admin.datasets.destroy')->middleware('role:admin');

    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis.index');
    Route::get('/analysis/create', [AnalysisController::class, 'create'])->name('analysis.create');
    Route::post('/analysis', [AnalysisController::class, 'store'])->name('analysis.store');
    Route::get('/analysis/{id}', [AnalysisController::class, 'show'])->name('analysis.show');
    Route::delete('/analysis/{id}', [AnalysisController::class, 'destroy'])->name('analysis.destroy');

    Route::get('/map', [RegionController::class, 'index'])->name('map.index');
    Route::get('/simulator', [SimulationController::class, 'index'])->name('simulator.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/analysis/{analysis}/export', [ReportController::class, 'export'])->name('reports.export');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
