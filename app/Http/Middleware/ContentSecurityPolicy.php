<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof Response) {
            $response->headers->set('Content-Security-Policy', "form-action 'self' *;");
        }

        return $response;
    }
}
