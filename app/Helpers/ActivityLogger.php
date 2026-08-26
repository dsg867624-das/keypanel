<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    public static function log(string $event, array $properties = []): void
    {
        ActivityLog::create([
            'user_id'    => Auth::id(),
            'event'      => $event,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'properties' => $properties,
        ]);
    }
}
