<?php

return [
    'ingest' => [
        /*
        |--------------------------------------------------------------------------
        | Default Parameter Ekologis (fallback semua wilayah)
        |--------------------------------------------------------------------------
        | Digunakan bila tidak ada region_override untuk ADM4 tertentu.
        | Nilai mengacu pada kondisi rata-rata pesisir Pantura Jawa Tengah.
        */
        'defaults' => [
            'area_size'          => 250,
            'rainfall'           => 2100,
            'abrasion_level'     => 3,
            'flood_risk'         => 3,
            'mangrove_loss'      => 40,
            'population_density' => 1600,
            'dominant_species'   => 'Rhizophora mucronata',
            'soil_type'          => 'mineral',
        ],

        /*
        |--------------------------------------------------------------------------
        | Region Overrides (per ADM4 code)
        |--------------------------------------------------------------------------
        | Format kunci: kode ADM4 BMKG (sama dengan BMKG_ADM4_LIST di .env)
        | Referensi data: KLHK 2023, InaRISK BNPB, Global Mangrove Watch
        */
        'region_overrides' => [

            // ── Sayung-Bedono, Demak ─────────────────────────────────────
            // Abrasi kritis: kehilangan daratan ±5 ha/tahun, rob intensif
            '33.21.04.2012' => [
                'area_size'          => 145,
                'rainfall'           => 2200,
                'abrasion_level'     => 5,
                'flood_risk'         => 5,
                'mangrove_loss'      => 68,
                'population_density' => 1850,
                'dominant_species'   => 'Rhizophora mucronata',
                'soil_type'          => 'mineral',
            ],

            // ── Kaliwungu Utara, Kendal ───────────────────────────────────
            // Abrasi garis pantai >200m, tambak tergerus, mangrove terdegradasi
            '33.24.01.2001' => [
                'area_size'          => 320,
                'rainfall'           => 2000,
                'abrasion_level'     => 4,
                'flood_risk'         => 3,
                'mangrove_loss'      => 42,
                'population_density' => 2100,
                'dominant_species'   => 'Avicennia marina',
                'soil_type'          => 'mineral',
            ],

            // ── Tegal Barat, Kota Tegal ───────────────────────────────────
            // Kawasan pesisir padat penduduk, abrasi sedang-tinggi, rob tahunan
            '33.76.01.1002' => [
                'area_size'          => 210,
                'rainfall'           => 1950,
                'abrasion_level'     => 4,
                'flood_risk'         => 4,
                'mangrove_loss'      => 52,
                'population_density' => 3800,
                'dominant_species'   => 'Avicennia marina',
                'soil_type'          => 'mineral',
            ],

            // ── Brebes-Randusanga, Brebes ─────────────────────────────────
            // Sedimentasi & abrasi berselang, kawasan mangrove terancam tambak
            '33.29.01.2001' => [
                'area_size'          => 410,
                'rainfall'           => 1900,
                'abrasion_level'     => 3,
                'flood_risk'         => 3,
                'mangrove_loss'      => 35,
                'population_density' => 1200,
                'dominant_species'   => 'Sonneratia alba',
                'soil_type'          => 'mineral',
            ],

        ],
    ],

    'marine' => [
        'provider'  => env('MARINE_PROVIDER', 'stormglass'),
        'locations' => env('MARINE_LOCATIONS', ''),
    ],
];
