<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HourlySnapshot extends Model
{
    protected $fillable = [
        'source',
        'region_name',
        'snapshot_at',
        'hazard_score',
        'mcvi_score',
        'mrps_score',
        'carbon_tons',
        'metrics',
    ];

    protected $casts = [
        'snapshot_at' => 'datetime',
        'metrics' => 'array',
    ];
}
