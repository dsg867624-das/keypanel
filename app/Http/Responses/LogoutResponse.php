<?php

namespace App\Http\Responses;

use App\Helpers\ActivityLogger;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        // Log logout
        if ($request->user()) {
            ActivityLogger::log('logout', [
                'email' => $request->user()->email ?? null,
            ]);
        }

        return redirect('/');
    }
}
