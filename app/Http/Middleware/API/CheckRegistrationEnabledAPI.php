<?php

namespace App\Http\Middleware\API;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Facades\Settings;
use Illuminate\Support\Facades\Schema;
use App\Models\GeneralSetting;

class CheckRegistrationEnabledAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get registration setting directly from the general_settings table
        $registrationEnabled = false;
        
        if (Schema::hasTable('general_settings')) {
            $settings = GeneralSetting::first();
            if ($settings) {
                $registrationEnabled = (bool)$settings->enable_registration;
            }
        }
        
        // If registration is disabled, return error response
        if (!$registrationEnabled) {
            return response()->json([
                'success' => false,
                'message' => 'Registration is currently disabled.'
            ], 403);
        }

        return $next($request);
    }
}
