<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class HttpsProtocol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force HTTPS if the request is HTTP
        if (!$request->secure() && !App::environment('local')) {
            return redirect()->secure($request->getRequestUri());
        }

        // Add security headers
        $response = $next($request);
        
        if (!$response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubdomains');
            $response->headers->set('X-Content-Type-Options', 'nosniff');
            $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
            $response->headers->set('X-XSS-Protection', '1; mode=block');
        }
        
        return $response;
    }
}
