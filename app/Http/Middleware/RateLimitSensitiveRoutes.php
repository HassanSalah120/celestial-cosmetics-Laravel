<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RateLimitSensitiveRoutes
{
    /**
     * Sensitive routes that should be rate limited
     * 
     * @var array
     */
    protected $sensitiveRoutes = [
        'login', 'register', 'password/email', 'password/reset', 'api/login', 'api/register'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestPath = $request->path();
        
        // Check if this is a sensitive route that should be rate limited
        $isSensitiveRoute = false;
        
        foreach ($this->sensitiveRoutes as $route) {
            if ($request->is($route) || $request->is($route.'/*')) {
                $isSensitiveRoute = true;
                break;
            }
        }
        
        if ($isSensitiveRoute) {
            // Create a unique key based on IP address and route
            $key = 'sensitive:' . $requestPath . ':' . $request->ip();
            
            // Define rate limiting: 5 attempts per minute
            $maxAttempts = 5;
            $decaySeconds = 60;
            
            // Check if rate limit exceeded
            if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
                // Log suspicious activity
                Log::channel('security')->warning('Rate limit exceeded for sensitive route', [
                    'ip' => $request->ip(),
                    'route' => $requestPath,
                    'user_agent' => $request->userAgent(),
                ]);
                
                // Get retry after timestamp
                $retryAfter = RateLimiter::availableIn($key);
                
                // Return rate limit exceeded response
                return response()->json([
                    'error' => 'Too many attempts, please try again later.',
                    'retry_after' => $retryAfter,
                ], Response::HTTP_TOO_MANY_REQUESTS)->header('Retry-After', $retryAfter);
            }
            
            // Increment the rate limiter
            RateLimiter::hit($key, $decaySeconds);
            
            // Add rate limit headers
            $headers = [
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => RateLimiter::remaining($key, $maxAttempts),
            ];
            
            // Process the request
            $response = $next($request);
            
            // Add headers to response
            foreach ($headers as $name => $value) {
                $response->headers->set($name, $value);
            }
            
            return $response;
        }
        
        // Not a sensitive route, proceed normally
        return $next($request);
    }
} 