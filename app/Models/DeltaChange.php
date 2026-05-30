<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeltaChange extends Model
{
    protected $fillable = [
        'source',
        'region_name',
        'snapshot_at',
        'change_type',
        'changed_fields',
        'from_ingest_id',
        'to_ingest_id',
        'processed_at',
    ];

    protected $casts = [
        'snapshot_at' => 'datetime',
        'changed_fields' => 'array',
        'processed_at' => 'datetime',
    ];
}
