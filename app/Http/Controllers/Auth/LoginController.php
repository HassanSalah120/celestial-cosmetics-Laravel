<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Check if the site is in RTL mode (Arabic)
        $isRtl = is_rtl();
        
        // Set page title based on language
        $title = $isRtl ? 'تسجيل الدخول' : 'Login';
        $description = $isRtl ? 'تسجيل الدخول إلى حسابك في سيليستيال كوزمتكس' : 'Sign in to your Celestial Cosmetics account';
        
        return view('auth.login', [
            'title' => $title,
            'description' => $description
        ]);
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        // Normalize email to lowercase to ensure case-insensitive comparison
        $credentials['email'] = strtolower($credentials['email']);
 
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
 
            // Redirect to dashboard if user has permission
            if (Auth::user()->hasPermission('view_dashboard')) {
                return redirect()->route('admin.dashboard');
            }
 
            return redirect()->intended('/');
        }
 
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect('/');
    }
}
