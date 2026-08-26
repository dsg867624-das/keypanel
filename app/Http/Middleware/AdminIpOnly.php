<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIpOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cloudflare se real IP nikaalo
        $clientIp = $request->header('CF-Connecting-IP') 
                    ?? $request->header('X-Forwarded-For') 
                    ?? $request->ip();

        // Multiple IPs ho to pehla lo
        if (str_contains($clientIp, ',')) {
            $clientIp = trim(explode(',', $clientIp)[0]);
        }

        $allowedIps = [
            '117.99.85.209',     // tumhara public IP
            '127.0.0.1',         // localhost
            '::1',               // localhost IPv6
        ];

        if (!in_array($clientIp, $allowedIps)) {
            abort(403, 'Unauthorized IP Address: ' . $clientIp);
        }

        return $next($request);
    }
}
