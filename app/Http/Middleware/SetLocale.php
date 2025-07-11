<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SetLocale
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
        // Get the available locales
        $availableLocales = config('app.available_locales', ['en', 'ar']);
        
        // Convert to a simple array of locale codes if it's an associative array
        $localeKeys = is_array($availableLocales) ? 
            (array_keys($availableLocales) !== range(0, count($availableLocales) - 1) ? 
                array_keys($availableLocales) : $availableLocales) : 
            ['en', 'ar'];
        
        // Default locale
        $defaultLocale = config('app.locale', 'en');
        
        // Priority 1: URL explicit_locale parameter (for direct language switch)
        if ($request->has('explicit_locale') && in_array($request->explicit_locale, $localeKeys)) {
            $locale = $request->explicit_locale;
            $this->setLanguagePreferences($locale);
            // Log this special case
            Log::info('Using explicit_locale from URL', ['locale' => $locale]);
        }
        // Priority 2: URL parameter
        elseif ($request->has('locale') && in_array($request->locale, $localeKeys)) {
            $locale = $request->locale;
            $this->setLanguagePreferences($locale);
        } 
        // Priority 3: Session
        elseif (Session::has('locale') && in_array(Session::get('locale'), $localeKeys)) {
            $locale = Session::get('locale');
            // Make sure we call setLanguagePreferences to ensure all systems are set
            $this->setLanguagePreferences($locale);
        }
        // Priority 4: Cookie
        elseif ($request->cookie('locale') && in_array($request->cookie('locale'), $localeKeys)) {
            $locale = $request->cookie('locale');
            $this->setLanguagePreferences($locale);
        }
        // Priority 5: Browser preference
        elseif ($request->header('Accept-Language')) {
            $browserLocale = $this->getBrowserLocale($request->header('Accept-Language'), $localeKeys);
            $locale = $browserLocale ?? $defaultLocale;
            $this->setLanguagePreferences($locale);
        }
        // Priority 6: Default locale
        else {
            $locale = $defaultLocale;
            $this->setLanguagePreferences($locale);
        }
        
        // FORCE set the application locale in multiple ways to ensure it takes effect
        App::setLocale($locale);
        app()->setLocale($locale);
        config(['app.locale' => $locale]);
        
        // Share locale data with views
        $isRtl = ($locale === 'ar');
        $direction = $isRtl ? 'rtl' : 'ltr';
        
        // Force set the RTL values in session to ensure consistency
        Session::put('locale', $locale);
        Session::put('text_direction', $direction);
        
        view()->share('isRtl', $isRtl);
        view()->share('textDirection', $direction);
        view()->share('currentLocale', $locale);
        view()->share('availableLocales', $availableLocales);
        
        // Log the current locale settings
        Log::debug('SetLocale middleware processed', [
            'session_locale' => Session::get('locale'),
            'app_locale' => App::getLocale(),
            'config_locale' => config('app.locale'),
            'is_rtl' => $isRtl,
            'direction' => $direction
        ]);
        
        // Process the request
        $response = $next($request);
        
        return $response;
    }
    
    /**
     * Set language preferences in session and cookie
     *
     * @param string $locale
     * @return void
     */
    private function setLanguagePreferences($locale)
    {
        // Store in session
        Session::put('locale', $locale);
        
        // Set direction
        $direction = in_array($locale, ['ar']) ? 'rtl' : 'ltr';
        Session::put('text_direction', $direction);
        
        // Set cookie (30 days)
        Cookie::queue('locale', $locale, 60 * 24 * 30);
        
        // Also force-set the config and App locales here
        config(['app.locale' => $locale]);
        App::setLocale($locale);
    }
    
    /**
     * Get the preferred locale from browser settings
     *
     * @param string $acceptLanguageHeader
     * @param array $availableLocales
     * @return string|null
     */
    private function getBrowserLocale($acceptLanguageHeader, array $availableLocales)
    {
        $browserLocales = explode(',', $acceptLanguageHeader);
        
        foreach ($browserLocales as $browserLocale) {
            // Extract the language code (first 2 characters)
            $languageCode = substr(trim(explode(';', $browserLocale)[0]), 0, 2);
            
            if (in_array($languageCode, $availableLocales)) {
                return $languageCode;
            }
        }
        
        return null;
    }
} 