<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Redirect;
use Illuminate\Support\Facades\Cache;

class RedirectsMiddleware
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
        // Only check for redirects on GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }
        
        // Get the current path without the leading slash
        $path = ltrim($request->path(), '/');
        
        // Check for a redirect in the cache first
        $redirect = Cache::remember('redirect:' . $path, 3600, function() use ($path) {
            return Redirect::where('source_url', $path)
                ->where('is_active', true)
                ->first();
        });
        
        if ($redirect) {
            $type = $redirect->type == '301' ? 301 : 302;
            
            // Log the redirect
            activity()
                ->causedBy($request->user())
                ->withProperties([
                    'source' => $path,
                    'target' => $redirect->target_url,
                    'type' => $type
                ])
                ->log('Redirected from ' . $path . ' to ' . $redirect->target_url);
            
            return redirect($redirect->target_url, $type);
        }
        
        return $next($request);
    }
} 