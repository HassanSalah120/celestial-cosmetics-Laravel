<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if this is an about page route
        if (strpos($request->path(), 'about') !== false) {
            // Log detailed request information
            Log::channel('about_debug')->info('REQUEST LOG', [
                'path' => $request->path(),
                'full_url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'all_headers' => $request->headers->all(),
                'request_data' => $request->all(),
                'session_id' => $request->session()->getId(),
                'has_session' => $request->hasSession(),
                'session_status' => session_status()
            ]);
        }

        // Process the request
        $response = $next($request);

        // Log response for about page routes
        if (strpos($request->path(), 'about') !== false) {
            Log::channel('about_debug')->info('RESPONSE LOG', [
                'path' => $request->path(),
                'status' => $response->getStatusCode(),
                'content_type' => $response->headers->get('Content-Type')
            ]);
        }

        return $response;
    }
} 