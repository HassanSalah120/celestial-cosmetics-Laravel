<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\GoogleSignupConfirmation;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Client;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user already exists with this email
            $user = User::where('email', $googleUser->getEmail())->first();
            
            $isNewUser = false;
            
            if (!$user) {
                // Create new user
                $isNewUser = true;
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(24)), // Random secure password
                    'email_verified_at' => now(), // Auto-verify Google users
                ]);
                
                // Save profile image if available
                if ($googleUser->getAvatar()) {
                    try {
                        // Use HTTP client with proper headers
                        $client = new Client();
                        $response = $client->get($googleUser->getAvatar(), [
                            'headers' => [
                                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                            ],
                        ]);
                        
                        if ($response->getStatusCode() === 200) {
                            $imageContents = $response->getBody()->getContents();
                            $filename = 'profile-images/' . Str::uuid() . '.jpg';
                            
                            Storage::disk('public')->put($filename, $imageContents);
                            
                            $user->profile_image = $filename;
                            $user->save();
                        }
                    } catch (\Exception $e) {
                        // Log the error but continue with authentication
                        Log::error('Failed to download Google avatar: ' . $e->getMessage());
                    }
                }
                
                event(new Registered($user));
                
                // Send confirmation email to new Google users
                Mail::to($user->email)->send(new GoogleSignupConfirmation($user));
            }
            
            // Make sure the user is verified to bypass verification middleware
            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
                $user->save();
            }
            
            // Login the user with remember flag
            Auth::login($user, true);
            
            // Set a session flag to indicate this user was authenticated via Google
            session(['auth_via_google' => true]);
            
            // Specifically regenerate session to apply verified status
            session()->regenerate();
            
            // Redirect to dashboard or intended page with appropriate message
            if ($isNewUser) {
                return redirect()->intended('/')->with('status', 'Your account has been created and verified successfully! Welcome to Celestial Cosmetics.');
            } else {
                return redirect()->intended('/');
            }
            
        } catch (Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }
} 