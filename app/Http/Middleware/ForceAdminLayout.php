<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ForceAdminLayout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set these values specifically for admin routes
        View::share('isRtl', false);
        View::share('textDirection', 'ltr');
        
        // Store the admin layout preference in the session
        Session::put('admin_text_direction', 'ltr');
        
        // This will ensure the admin section always uses LTR regardless of language
        return $next($request);
    }
}
