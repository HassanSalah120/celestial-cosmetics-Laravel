<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Facades\Settings;

class CheckSocialLoginEnabled
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
        // Check if social login is enabled
        if (Settings::get('enable_social_login', '1') != '1') {
            return redirect()->route('login')
                ->with('error', 'Social login is currently disabled.');
        }

        return $next($request);
    }
} 