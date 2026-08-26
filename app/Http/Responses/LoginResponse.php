<?php

namespace App\Http\Responses;

use App\Helpers\ActivityLogger;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Log successful login
        ActivityLogger::log('login', [
            'email' => $request->user()->email ?? null,
        ]);

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect()->intended(Fortify::redirects('login'));
    }
}
