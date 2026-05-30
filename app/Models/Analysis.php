<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'seeded_by_admin',
        'region_name',
        'province',
        'area_size',
        'rainfall',
        'abrasion_level',
        'flood_risk',
        'mangrove_loss',
        'population_density',
        // Scientific Inputs
        'dominant_species',
        'soil_type',
        // IPCC AR6 Pillars
        'hazard_score',
        'exposure_score',
        'vulnerability_score',
        // Core Indices
        'climate_risk_score',
        'mcvi',
        'mrps',
        'restoration_priority',
        // Blue Carbon Tier-2
        'carbon_agb',
        'carbon_bgb',
        'carbon_soc',
        'carbon_potential',
        // AI & DPSIR
        'ai_explanation',
        'restoration_recommendation',
        'result_payload',
        'dpsir_payload',
    ];

    protected function casts(): array
    {
        return [
            'result_payload' => 'array',
            'dpsir_payload'  => 'array',
        ];
    }
}
