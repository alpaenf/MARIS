<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawIngest extends Model
{
    protected $fillable = [
        'source',
        'region_code',
        'region_name',
        'fetched_at',
        'checksum',
        'payload',
        'status',
        'error_message',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
        'payload' => 'array',
    ];
}
