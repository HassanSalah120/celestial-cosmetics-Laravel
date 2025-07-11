<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ForceLocaleForDebug
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
        // First force sync the locale with session if exists
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            
            // Force app locale to match session locale
            App::setLocale($sessionLocale);
            app()->setLocale($sessionLocale);
            config(['app.locale' => $sessionLocale]);
            
            Log::alert('DEBUG ROUTE: Forced locale from session', [
                'session_locale' => $sessionLocale,
                'app_locale_after' => App::getLocale(),
                'app_instance_locale' => app()->getLocale(),
                'config_locale' => config('app.locale')
            ]);
        }
        
        return $next($request);
    }
} 