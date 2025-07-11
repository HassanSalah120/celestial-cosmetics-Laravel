<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use App\Models\GlobalHeaderSetting;
use App\Models\HeaderNavigationItem;

class DebugController extends Controller
{
    public function fixLocale(Request $request, $locale = null)
    {
        // Get locale from request or use the specified one
        $locale = $locale ?? $request->input('locale', 'en');
        
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            $locale = 'en';
        }
        
        // NUCLEAR OPTION: Reset everything
        
        // 1. Clear all cookies related to locale
        $cookies = [
            Cookie::forget('locale'),
            Cookie::forget('is_rtl'),
            Cookie::forget('text_direction'),
            Cookie::forget('laravel_session')
        ];
        
        // 2. Clear all session data completely - not just locale keys
        Session::flush();
        
        // 3. Set new cookies with strong settings
        $cookies[] = Cookie::make('locale', $locale, 60 * 24 * 365, null, null, false, false);
        
        // 4. Set session data
        Session::put('locale', $locale);
        $isRtl = ($locale === 'ar');
        $direction = $isRtl ? 'rtl' : 'ltr';
        Session::put('text_direction', $direction);
        Session::save(); // Force immediate save
        
        // 5. Apply to application configuration
        config(['app.locale' => $locale]);
        App::setLocale($locale);
        app()->setLocale($locale);
        
        // 6. Apply to views
        View::share('isRtl', $isRtl);
        View::share('textDirection', $direction);
        View::share('currentLocale', $locale);
        
        // 7. Log this aggressive reset
        Log::alert('COMPLETE LOCALE SYSTEM RESET', [
            'forced_locale' => $locale,
            'is_rtl' => $isRtl,
            'direction' => $direction,
            'app_locale_after' => App::getLocale(),
            'session_data' => Session::all()
        ]);
        
        // Handle redirect with cache-busting and secure headers
        $redirect = $request->input('redirect');
        if ($redirect) {
            return redirect(urldecode($redirect))
                ->withCookies($cookies)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
        }
        
        // Return to homepage with nuclear cache-busting
        return redirect('/?_' . time() . '&force_locale=' . $locale)
            ->withCookies($cookies)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    
    public function displayLocaleInfo()
    {
        // Output current locale information as HTML
        $locale = App::getLocale();
        $sessionLocale = Session::get('locale');
        $textDirection = Session::get('text_direction');
        $isRtl = in_array($locale, ['ar', 'he', 'fa', 'ur']);
        $functionIsRtl = function_exists('is_rtl') ? is_rtl() : 'function not defined';
        
        $output = "<h1>Locale Debug Information</h1>";
        $output .= "<p>app()->getLocale(): <strong>{$locale}</strong></p>";
        $output .= "<p>Session locale: <strong>{$sessionLocale}</strong></p>";
        $output .= "<p>Text direction: <strong>{$textDirection}</strong></p>";
        $output .= "<p>is_rtl() function: <strong>{$functionIsRtl}</strong></p>";
        $output .= "<p>In-array RTL check: <strong>" . ($isRtl ? 'true' : 'false') . "</strong></p>";
        $output .= "<p>Translation test - Featured Product: <strong>" . __('Featured Product') . "</strong></p>";
        
        // Add language switching links
        $output .= "<div style='margin-top:20px'>";
        $output .= "<a href='/debug/fix-locale/en' style='padding:10px; background:#4CAF50; color:white; text-decoration:none; margin-right:10px;'>Fix & Switch to English</a>";
        $output .= "<a href='/debug/fix-locale/ar' style='padding:10px; background:#2196F3; color:white; text-decoration:none;'>Fix & Switch to Arabic</a>";
        $output .= "</div>";
        
        return response($output);
    }

    /**
     * Show header settings
     */
    public function headerSettings()
    {
        $headerSettings = GlobalHeaderSetting::getSettings();
        $navigationItems = HeaderNavigationItem::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with('children')
            ->get();
            
        return view('debug.header_settings', compact('headerSettings', 'navigationItems'));
    }
}
