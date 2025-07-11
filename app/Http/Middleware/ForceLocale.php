<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ForceLocale
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
        // Priority 1: Get locale from session
        $locale = Session::get('locale');
        
        // Priority 2: If not in session, use route parameter if exists
        if (!$locale && $request->route('locale')) {
            $locale = $request->route('locale');
        }
        
        // Priority 3: Use cookie if exists
        if (!$locale && $request->cookie('locale')) {
            $locale = $request->cookie('locale');
        }
        
        // Priority 4: Last resort - use app default
        if (!$locale) {
            $locale = config('app.locale', 'en');
        }
        
        // Validate the locale (only allow en or ar)
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        
        // CRITICAL: Make sure we override any cached config from .env
        config(['app.locale' => $locale]);
        
        // FORCE set the locale in ALL possible places
        App::setLocale($locale);
        app()->setLocale($locale);
        config(['app.locale' => $locale]);
        Session::put('locale', $locale);
        
        // Set RTL status based ONLY on the locale (not any other factors)
        $isRtl = ($locale === 'ar');
        $direction = $isRtl ? 'rtl' : 'ltr';
        
        // Store text direction in session
        Session::put('text_direction', $direction);
        
        // Share with all views
        View::share('isRtl', $isRtl);
        View::share('textDirection', $direction);
        View::share('currentLocale', $locale);
        
        // Set HTML direction
        app()->singleton('htmldir', function () use ($direction) {
            return $direction;
        });
        
        // Set a very long cookie (1 year)
        Cookie::queue('locale', $locale, 60 * 24 * 365);
        
        // Debug log to track what's happening
        Log::debug('ForceLocale middleware executed', [
            'locale' => $locale,
            'isRtl' => $isRtl,
            'app()->getLocale()' => App::getLocale(),
            'session_locale' => Session::get('locale'),
            'direction' => $direction
        ]);
        
        $response = $next($request);
        
        // CRITICAL: Check again after the response is generated to catch any middleware that might have changed it
        if (App::getLocale() !== $locale) {
            Log::warning('Locale changed during request! Forcing back to: ' . $locale);
            App::setLocale($locale);
            app()->setLocale($locale);
            config(['app.locale' => $locale]);
        }
        
        return $response;
    }
} 