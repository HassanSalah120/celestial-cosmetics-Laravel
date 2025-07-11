<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Facades\Settings;
use Illuminate\Support\Facades\Schema;
use App\Models\GeneralSetting;

class CheckRegistrationEnabled
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
        // Get registration setting directly from the general_settings table
        $registrationEnabled = false;
        
        if (Schema::hasTable('general_settings')) {
            $settings = GeneralSetting::first();
            if ($settings) {
                $registrationEnabled = (bool)$settings->enable_registration;
            }
        }
        
        // If not found in general_settings, fall back to the Settings facade
        if (!$registrationEnabled) {
            // For POST requests, block registration attempts completely
            if ($request->isMethod('post')) {
                return redirect()->route('register')
                    ->with('error', 'Registration is currently disabled.');
            }
            // For GET requests, allow through so the view can display a message
        }

        return $next($request);
    }
} 