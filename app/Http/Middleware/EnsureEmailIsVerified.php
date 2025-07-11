<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $redirectToRoute
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse|null
     */
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        $user = $request->user();
        
        // Skip verification check for certain user situations
        if (!$user || 
            !($user instanceof MustVerifyEmail) || 
            $user->hasVerifiedEmail()) {
            return $next($request);
        }
        
        // Force verification for Google-authenticated users if needed
        if (session()->has('auth_via_google') && !$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            return $next($request);
        }
        
        // Standard verification redirect for other users
        return $request->expectsJson()
                ? abort(403, 'Your email address is not verified.')
                : Redirect::guest(URL::route($redirectToRoute ?: 'verification.notice'));
    }
} 