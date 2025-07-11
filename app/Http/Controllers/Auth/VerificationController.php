<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        // If user is already verified, redirect to home
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/');
        }
        
        // Special handling for Google users - auto verify them
        if (session()->has('auth_via_google') && !$request->user()->hasVerifiedEmail()) {
            $request->user()->markEmailAsVerified();
            $request->user()->save();
            
            return redirect('/')->with('status', 'Your account has been verified successfully!');
        }
        
        return view('auth.verify-email');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        $request->fulfill();
 
        return redirect('/')->with('status', 'Your email has been verified!');
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resend(Request $request)
    {
        // Ensure the user's email is stored in lowercase in the notification
        if ($request->user()->email !== strtolower($request->user()->email)) {
            $request->user()->forceFill([
                'email' => strtolower($request->user()->email)
            ])->save();
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification link sent!');
    }
} 