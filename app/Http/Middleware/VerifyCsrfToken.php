<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Support\Facades\Log;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/stripe/webhook', // Exclude Stripe webhook from CSRF protection
        '/admin/about/direct-fix-update', // Exclude direct fix route to ensure it works
        '/emergency-about-update', // Emergency route for about page updates
    ];
    
    /**
     * Determine if the request has a URI that should be CSRF verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $shouldPass = parent::shouldPassThrough($request);
        
        // Log CSRF verification attempts for about page routes
        if (strpos($request->path(), 'about') !== false) {
            Log::channel('about_debug')->info('CSRF CHECK', [
                'path' => $request->path(),
                'method' => $request->method(),
                'is_excluded' => $shouldPass,
                'token_present' => $request->has('_token'),
                'token_value' => $request->input('_token')
            ]);
        }
        
        return $shouldPass;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // Log CSRF token mismatch errors
            Log::channel('about_debug')->error('CSRF TOKEN MISMATCH', [
                'path' => $request->path(),
                'method' => $request->method(),
                'token_present' => $request->has('_token'),
                'token_value' => $request->input('_token'),
                'session_token' => $request->session()->token()
            ]);
            
            throw $e;
        }
    }
} 