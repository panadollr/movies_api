<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GzipMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (config('gzip.enabled')) {
            $this->compressResponse($response);
        }

        return $response;
    }

    protected function compressResponse($response)
    {
        $acceptHeader = $response->headers->get('Accept-Encoding');

        if (strpos($acceptHeader, 'gzip') !== false) {
            $response->setContent(gzencode($response->getContent(), config('gzip.level', 6)));
            $response->headers->set('Content-Encoding', 'gzip');
        }

        return $response;
    }
}
