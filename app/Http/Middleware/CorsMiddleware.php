<?php

namespace App\Http\Middleware;

use Closure;

class CorsMiddleware
{
    // public function handle($request, Closure $next)
    // {
    //     $response = $next($request);

    //     $response->headers->set('Access-Control-Allow-Origin', '*');
    //     $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    //     $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');
    //     $response->headers->set('Access-Control-Allow-Credentials', 'true');

    //     // Thêm header cho Preflight Request (OPTIONS)
    //     if ($request->isMethod('OPTIONS')) {
    //         return response('', 200)
    //             ->header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE')
    //             ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');
    //     }

    //     return $response;
    // }

    public function handle($request, Closure $next)
    {
        header("Access-Control-Allow-Origin: *");

        $headers = [
            'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Auth-Token, Origin, Authorization'
        ];
        if ($request->getMethod() == "OPTIONS") {
            return response('OK')
                ->withHeaders($headers);
        }

        $response = $next($request);
        foreach ($headers as $key => $value)
            $response->header($key, $value);
        return $response;
    }
}
