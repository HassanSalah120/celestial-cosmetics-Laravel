<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the registration form
     */
    public function showRegistrationForm()
    {
        // Check if the site is in RTL mode (Arabic)
        $isRtl = is_rtl();
        
        // Set page title based on language
        $title = $isRtl ? 'التسجيل' : 'Register';
        $description = $isRtl ? 'إنشاء حساب جديد في سيليستيال كوزمتكس' : 'Create a new account with Celestial Cosmetics';
        
        // Check if registration is enabled (directly from database)
        $registrationEnabled = false;
        
        if (Schema::hasTable('general_settings')) {
            $settings = \App\Models\GeneralSetting::first();
            if ($settings) {
                $registrationEnabled = (bool)$settings->enable_registration;
            }
        }
        
        return view('auth.register', [
            'title' => $title,
            'description' => $description,
            'registration_disabled' => !$registrationEnabled
        ]);
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        // Check if registration is enabled (directly from database)
        $registrationEnabled = false;
        
        if (Schema::hasTable('general_settings')) {
            $settings = \App\Models\GeneralSetting::first();
            if ($settings) {
                $registrationEnabled = (bool)$settings->enable_registration;
            }
        }
        
        // If registration is disabled, redirect back with error
        if (!$registrationEnabled) {
            return redirect()->route('register')
                ->with('error', 'Registration is currently disabled.');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'string', 
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
        ]);

        // Check if auto-verify flag is set (for testing)
        $autoVerify = $request->has('auto_verify');
        
        // For development/testing purpose - auto verify user if query param is set
        if ($autoVerify) {
            $user->markEmailAsVerified();
            session()->flash('status', 'Your account has been registered and verified automatically for testing.');
        } else {
            event(new Registered($user));
        }

        Auth::login($user);

        // If auto-verified, redirect to main page, otherwise to verification notice
        return $autoVerify ? redirect('/') : redirect()->route('verification.notice');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 
                'string', 
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);
    }
}
