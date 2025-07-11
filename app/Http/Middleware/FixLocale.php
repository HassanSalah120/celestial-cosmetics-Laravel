<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class FixLocale
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
        // Process the request first
        $response = $next($request);
        
        // Then ensure the locale is applied AFTER all other middleware
        if (Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            $appLocale = App::getLocale();
            
            // If there's a mismatch, fix it
            if ($sessionLocale !== $appLocale) {
                // This runs after all other middleware, to ensure locale is correct
                App::setLocale($sessionLocale);
                config(['app.locale' => $sessionLocale]);
                app()->setLocale($sessionLocale);
                
                // Share locale information with all views
                view()->share('currentLocale', $sessionLocale);
                view()->share('isRtl', in_array($sessionLocale, ['ar']));
                view()->share('textDirection', in_array($sessionLocale, ['ar']) ? 'rtl' : 'ltr');
                
                Log::alert('Fixed app locale mismatch', [
                    'session_locale' => $sessionLocale,
                    'previous_app_locale' => $appLocale,
                    'new_app_locale' => App::getLocale()
                ]);
                
                // Also fix the response if it includes a html/lang attribute
                if (method_exists($response, 'getContent')) {
                    $content = $response->getContent();
                    if (is_string($content) && str_contains($content, '<html lang="')) {
                        $content = preg_replace('/<html lang="[^"]*"/', '<html lang="' . $sessionLocale . '"', $content);
                        $response->setContent($content);
                    }
                }
            }
        }
        
        return $response;
    }
} 