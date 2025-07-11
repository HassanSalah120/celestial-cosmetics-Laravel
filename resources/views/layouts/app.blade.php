@php
use App\Facades\Settings;

// CRITICAL: Check for inconsistency between app locale and session locale
$appLocale = app()->getLocale();
$sessionLocale = session('locale');

// Check if we have a mismatch and log it
if ($sessionLocale && $appLocale !== $sessionLocale) {
    \Illuminate\Support\Facades\Log::warning('LOCALE MISMATCH DETECTED', [
        'app_locale' => $appLocale,
        'session_locale' => $sessionLocale
    ]);
    
    // Always trust session over app
    app()->setLocale($sessionLocale);
    \Illuminate\Support\Facades\App::setLocale($sessionLocale);
    config(['app.locale' => $sessionLocale]);
    
    // Log the correction
    \Illuminate\Support\Facades\Log::alert('CORRECTED LOCALE IN VIEW', [
        'new_app_locale' => app()->getLocale(),
        'from_session' => $sessionLocale
    ]);
}

// Check if we should use the admin layout instead
if ((isset($isAdminPage) && $isAdminPage === true)) {
    // Only pass variables that exist
    $viewData = [];
    if (isset($isAdminPage)) $viewData['isAdminPage'] = $isAdminPage;
    if (isset($layoutName)) $viewData['layoutName'] = $layoutName;
    
    echo view('layouts.admin', $viewData);
    return;
}
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Locale Script - Ensures consistent language settings -->
    <script>
    (function() {
        // Get current locale from meta
        var htmlLang = document.documentElement.lang || 'en';
        var htmlDir = document.documentElement.dir || 'ltr';
        
        // Log what we detected (for debugging)
        console.log('Detected HTML lang:', htmlLang);
        console.log('Detected HTML dir:', htmlDir);
        
        // Force consistency by redirecting if needed
        if (htmlLang === 'ar' && htmlDir !== 'rtl') {
            console.log('Inconsistency detected: Arabic language without RTL direction');
            window.location.href = '/debug/fix-locale/ar?redirect=' + encodeURIComponent(window.location.pathname) + '&_=' + Date.now();
        } else if (htmlLang === 'en' && htmlDir !== 'ltr') {
            console.log('Inconsistency detected: English language without LTR direction');
            window.location.href = '/debug/fix-locale/en?redirect=' + encodeURIComponent(window.location.pathname) + '&_=' + Date.now();
        }
    })();
    </script>
    
    <title>{{ config('app.name', 'Laravel') }}</title>

    @hasSection('meta_tags')
        @yield('meta_tags')
        @else
        <x-seo :title="$title ?? null" 
               :description="$description ?? null" 
               :keywords="$keywords ?? null" 
               :ogImage="$ogImage ?? null" 
               :type="$type ?? null"
               :canonical="$canonical ?? null"
               :robots="$robots ?? null" />
        @endif
    
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ Settings::get('og_site_name') }}">
    
    <!-- Favicon -->
    @if(Settings::get('site_favicon'))
        <link rel="icon" href="{{ secure_asset('storage/' . Settings::get('site_favicon')) }}">
    @endif
    
    <!-- Performance Optimization -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Source+Sans+Pro:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <!-- Additional Meta Tags -->
    @yield('additional_meta')
    
    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AOS - Animate On Scroll Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    
    <!-- Mutation Events Fix -->
    <script src="{{ secure_asset('js/mutation-fix.js') }}"></script>
    
    <!-- Menu Translation Fix -->
    <script src="{{ secure_asset('js/menu-translation-fix.js') }}"></script>
    
    <!-- Language Switcher Fix -->
    <script src="{{ secure_asset('js/language-switcher-fix.js') }}"></script>
    
    <!-- RTL Support -->
    @if(is_rtl())
        <link rel="stylesheet" href="{{ secure_asset('css/rtl.css') }}">
    @endif
    
    <!-- Enhanced Header Styling -->
    <link rel="stylesheet" href="{{ secure_asset('css/enhanced-header.css') }}">
    
    <!-- Additional Styles -->
    @stack('styles')
    
    <!-- AlpineJS x-cloak -->
    <style>
        [x-cloak] { display: none !important; }
        
        @php
            function hex2rgb($hex) {
                $hex = str_replace('#', '', $hex);
                if(strlen($hex) == 3) {
                    $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                    $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                    $b = hexdec(substr($hex,2,1).substr($hex,2,1));
                } else {
                    $r = hexdec(substr($hex,0,2));
                    $g = hexdec(substr($hex,2,2));
                    $b = hexdec(substr($hex,4,2));
                }
                return implode(' ', [$r, $g, $b]);
            }
        @endphp

        @if(isset($themeColors) && !empty($themeColors))
        :root {
            --color-primary: {{ hex2rgb($themeColors['primary'] ?? '#1f5964') }};
            --color-primary-light: {{ hex2rgb($themeColors['primary-light'] ?? '#2d6e7e') }};
            --color-primary-dark: {{ hex2rgb($themeColors['primary-dark'] ?? '#174853') }};

            --color-secondary: {{ hex2rgb($themeColors['secondary'] ?? '#312e43') }};
            --color-secondary-light: {{ hex2rgb($themeColors['secondary-light'] ?? '#423f5a') }};
            --color-secondary-dark: {{ hex2rgb($themeColors['secondary-dark'] ?? '#272536') }};

            --color-accent: {{ hex2rgb($themeColors['accent'] ?? '#d4af37') }};
            --color-accent-light: {{ hex2rgb($themeColors['accent-light'] ?? '#dbba5d') }};
            --color-accent-dark: {{ hex2rgb($themeColors['accent-dark'] ?? '#b3932e') }};
        }
        @endif
    </style>
</head>
<body class="font-sans antialiased {{ is_rtl() ? 'rtl' : 'ltr' }} min-h-screen flex flex-col bg-gray-50">
    @include('layouts.navigation')
    
    <main class="flex-grow transition-opacity duration-500 opacity-100">
        @yield('content')
    </main>

    @include('layouts.footer')
    
    <!-- Toast Container -->
    <div id="toast-container" class="fixed z-50 bottom-4 right-4 flex flex-col space-y-2"></div>
    
    <!-- Cart Loading Overlay -->
    <div id="cart-loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <div class="animate-spin inline-block w-8 h-8 border-4 border-primary border-t-transparent rounded-full mb-2"></div>
            <p class="text-gray-700">{{ __('messages.loading') ?? 'Loading...' }}</p>
        </div>
    </div>
    
    <!-- Additional JavaScript -->
    @stack('scripts')
    
    <!-- Initialize AOS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 1000,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });
        });
    </script>
    
    <!-- Deferred Scripts -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.12.0/cdn.min.js"></script>
    
    <!-- Quick View Implementation -->
    <script>
        // Define the quickViewProduct function globally
        window.quickViewProduct = function(button) {
            console.log('Quick view clicked for product:', button.dataset.productId);
            
            // This is a temporary implementation until the full modal is added back
            const productName = button.dataset.productName;
            const productUrl = `/products/${button.dataset.productSlug}`;
            
            // Redirect to product page for now
            window.location.href = productUrl;
        };
    </script>
    
    <!-- Cart AJAX request fix -->
    <script>
        // Store the original fetch function
        const originalFetch = window.fetch;
        
        // Override the fetch function
        window.fetch = function(url, options) {
            // Check if this is a cart/add request
            if (typeof url === 'string' && url.includes('/cart/add')) {
                // Make sure options exists
                options = options || {};
                
                // Make sure headers exist
                options.headers = options.headers || {};
                
                // Set the correct Content-Type
                options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
                options.headers['Accept'] = 'application/json';
                
                // Fix the body if it's JSON
                if (options.body && typeof options.body === 'string' && options.body.startsWith('{')) {
                    try {
                        const data = JSON.parse(options.body);
                        let formBody = [];
                        for (let property in data) {
                            const encodedKey = encodeURIComponent(property);
                            const encodedValue = encodeURIComponent(data[property]);
                            formBody.push(encodedKey + "=" + encodedValue);
                        }
                        options.body = formBody.join("&");
                    } catch (e) {
                        console.error('Error parsing JSON body:', e);
                    }
                }
            }
            
            // Call the original fetch with our modified options
            return originalFetch.call(this, url, options);
        };
    </script>
</body>
</html>