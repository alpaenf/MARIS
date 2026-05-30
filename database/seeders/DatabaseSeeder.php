<?php

namespace Database\Seeders;

use App\Models\Analysis;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     *
     * Menyiapkan:
     *  1. User admin & analis default
     *  2. Empat preset wilayah pesisir Jawa Tengah rawan abrasi
     *     (seeded_by_admin = true → tampil sebagai preset di halaman Analisis)
     *
     * Referensi data:
     *  - KLHK Dirjen PDASRH 2023 (luas mangrove)
     *  - InaRISK BNPB (tingkat abrasi & banjir rob)
     *  - Global Mangrove Watch v3.0 (persentase kehilangan)
     *  - BPS Kabupaten 2023 (kepadatan penduduk)
     */
    public function run(): void
    {
        // ── 1. Users ──────────────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@maris.id'],
            [
                'name'     => 'Admin MARIS',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'     => 'Test User',
                'password' => Hash::make('password'),
                'role'     => 'analis',
            ]
        );

        // ── 2. Admin yang dipakai untuk seeding (ambil admin user) ────────
        $adminId = User::where('email', 'admin@maris.id')->value('id');

        // ── 3. Preset Wilayah Pesisir Jawa Tengah ────────────────────────
        // Setiap record ini akan muncul sebagai tombol preset di halaman
        // "Buat Analisis" sehingga analis bisa langsung load parameter aktual.
        // Skor ilmiah (MCVI, MRPS, dll.) sudah dihitung deterministik.

        $regions = [

            // ── Sayung-Bedono, Demak ──────────────────────────────────────
            // Paling kritis: kehilangan daratan ±5 ha/tahun, rob intensif.
            // Spesies Rhizophora mucronata dominan di kawasan tambak eks-sawah.
            [
                'region_name'         => 'Sayung-Bedono',
                'province'            => 'Jawa Tengah',
                'area_size'           => 145.0,
                'rainfall'            => 2200,
                'abrasion_level'      => 5,
                'flood_risk'          => 5,
                'mangrove_loss'       => 68,
                'population_density'  => 1850,
                'dominant_species'    => 'Rhizophora mucronata',
                'soil_type'           => 'mineral',
                // IPCC AR6 Pillars (pre-computed)
                'hazard_score'        => 92.0,
                'exposure_score'      => 74.0,
                'vulnerability_score' => 88.0,
                // Core Indices
                'climate_risk_score'  => 84.0,
                'mcvi'                => 86.0,
                'mrps'                => 83.0,
                'restoration_priority'=> 'Tinggi',
                // Blue Carbon Tier-2 (Rhizophora mucronata, mineral)
                'carbon_agb'          => round(178.0  * 145.0, 2),
                'carbon_bgb'          => round(89.0   * 145.0, 2),
                'carbon_soc'          => round(255.0  * 145.0, 2),
                'carbon_potential'    => round((178.0 + 89.0 + 255.0) * 145.0, 2),
            ],

            // ── Kaliwungu Utara, Kendal ───────────────────────────────────
            // Abrasi garis pantai >200m dalam 10 tahun. Mangrove terdegradasi
            // oleh konversi tambak intensif. Avicennia marina tahan salinitas.
            [
                'region_name'         => 'Kaliwungu',
                'province'            => 'Jawa Tengah',
                'area_size'           => 320.0,
                'rainfall'            => 2000,
                'abrasion_level'      => 4,
                'flood_risk'          => 3,
                'mangrove_loss'       => 42,
                'population_density'  => 2100,
                'dominant_species'    => 'Avicennia marina',
                'soil_type'           => 'mineral',
                // IPCC AR6 Pillars
                'hazard_score'        => 74.0,
                'exposure_score'      => 70.0,
                'vulnerability_score' => 68.0,
                // Core Indices
                'climate_risk_score'  => 70.0,
                'mcvi'                => 71.0,
                'mrps'                => 70.0,
                'restoration_priority'=> 'Tinggi',
                // Blue Carbon Tier-2 (Avicennia marina, mineral)
                'carbon_agb'          => round(143.2  * 320.0, 2),
                'carbon_bgb'          => round(71.6   * 320.0, 2),
                'carbon_soc'          => round(265.0  * 320.0, 2),
                'carbon_potential'    => round((143.2 + 71.6 + 265.0) * 320.0, 2),
            ],

            // ── Tegal Barat, Kota Tegal ───────────────────────────────────
            // Pesisir padat penduduk, abrasi sedang-tinggi, banjir rob hampir
            // tahunan. Kawasan mangrove tersisa di area TPI dan muara sungai.
            [
                'region_name'         => 'Tegal Barat',
                'province'            => 'Jawa Tengah',
                'area_size'           => 210.0,
                'rainfall'            => 1950,
                'abrasion_level'      => 4,
                'flood_risk'          => 4,
                'mangrove_loss'       => 52,
                'population_density'  => 3800,
                'dominant_species'    => 'Avicennia marina',
                'soil_type'           => 'mineral',
                // IPCC AR6 Pillars
                'hazard_score'        => 78.0,
                'exposure_score'      => 82.0,
                'vulnerability_score' => 76.0,
                // Core Indices
                'climate_risk_score'  => 78.0,
                'mcvi'                => 79.0,
                'mrps'                => 77.0,
                'restoration_priority'=> 'Tinggi',
                // Blue Carbon Tier-2 (Avicennia marina, mineral)
                'carbon_agb'          => round(143.2  * 210.0, 2),
                'carbon_bgb'          => round(71.6   * 210.0, 2),
                'carbon_soc'          => round(265.0  * 210.0, 2),
                'carbon_potential'    => round((143.2 + 71.6 + 265.0) * 210.0, 2),
            ],

            // ── Brebes-Randusanga, Brebes ─────────────────────────────────
            // Kawasan mangrove terluas dari 4 wilayah ini. Abrasi berselang
            // dengan sedimentasi. Konversi tambak udang jadi tekanan utama.
            // Sonneratia alba pionir perintis di substrat lumpur baru.
            [
                'region_name'         => 'Brebes-Randusanga',
                'province'            => 'Jawa Tengah',
                'area_size'           => 410.0,
                'rainfall'            => 1900,
                'abrasion_level'      => 3,
                'flood_risk'          => 3,
                'mangrove_loss'       => 35,
                'population_density'  => 1200,
                'dominant_species'    => 'Sonneratia alba',
                'soil_type'           => 'mineral',
                // IPCC AR6 Pillars
                'hazard_score'        => 60.0,
                'exposure_score'      => 58.0,
                'vulnerability_score' => 62.0,
                // Core Indices
                'climate_risk_score'  => 60.0,
                'mcvi'                => 61.0,
                'mrps'                => 60.0,
                'restoration_priority'=> 'Sedang',
                // Blue Carbon Tier-2 (Sonneratia alba, mineral)
                'carbon_agb'          => round(215.8  * 410.0, 2),
                'carbon_bgb'          => round(107.9  * 410.0, 2),
                'carbon_soc'          => round(230.0  * 410.0, 2),
                'carbon_potential'    => round((215.8 + 107.9 + 230.0) * 410.0, 2),
            ],
        ];

        foreach ($regions as $regionData) {
            Analysis::updateOrCreate(
                [
                    'seeded_by_admin' => true,
                    'region_name'     => $regionData['region_name'],
                ],
                array_merge($regionData, [
                    'user_id'         => $adminId,
                    'seeded_by_admin' => true,
                ])
            );
        }
    }
}
