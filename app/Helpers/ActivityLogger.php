<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class ActivityLogger
{
    public static function log(string $event, array $properties = []): void
    {
        try {
            if (!Schema::hasTable('activity_logs')) {
                Schema::create('activity_logs', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('user_id')->nullable();
                    $table->string('event');
                    $table->string('ip_address', 45)->nullable();
                    $table->string('user_agent')->nullable();
                    $table->text('properties')->nullable();
                    $table->timestamps();
                });
            }

            ActivityLog::create([
                'user_id'    => Auth::id(),
                'event'      => $event,
                'ip_address' => Request::ip(),
                'user_agent' => substr((string) Request::userAgent(), 0, 500),
                'properties' => $properties,
            ]);
        } catch (\Throwable $e) {
            // login / panel ko crash mat karo
        }
    }
}
