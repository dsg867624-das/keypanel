<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'key_hash',
        'name',
        'status',
        'expires_at',
        'activation_limit',
        'activation_count',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'activation_limit' => 'integer',
        'activation_count' => 'integer',
    ];
}

