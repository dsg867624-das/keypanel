<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Key extends Model
{
    protected $fillable = [
        'key',
        'status',
        'duration',
        'expires_at',
        'max_uses',
        'used_count',
        'note',
        'username',
        'password',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public static function generateKey(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $segments = [];
        for ($i = 0; $i < 4; $i++) {
            $segment = '';
            for ($j = 0; $j < 4; $j++) {
                $segment .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $segments[] = $segment;
        }
        return implode('-', $segments);
    }
}
