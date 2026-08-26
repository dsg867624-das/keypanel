<?php

namespace App\Providers;

use App\Helpers\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Sirf tab HTTPS force karo jab localhost na ho
        if (!$this->app->environment('local') && !request()->is('127.0.0.1*') && request()->ip() !== '127.0.0.1') {
            URL::forceScheme('https');
        }

        // Better way for Cloudflare Tunnel
        if (request()->header('CF-Connecting-IP') || request()->secure()) {
            URL::forceScheme('https');
        }

        // Login successful
        Event::listen(Login::class, function (Login $event) {
            ActivityLogger::log('login', [
                'email' => $event->user->email ?? null,
            ]);
        });

        // Logout
        Event::listen(Logout::class, function (Logout $event) {
            ActivityLogger::log('logout', [
                'email' => $event->user->email ?? null,
            ]);
        });

        // Failed Login
        Event::listen(Failed::class, function (Failed $event) {
            ActivityLogger::log('failed_login', [
                'email' => $event->credentials['email'] ?? null,
            ]);
        });
    }
}
