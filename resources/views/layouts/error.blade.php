<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ is_rtl() ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Celestial Cosmetics') }} - Error</title>
    
    <!-- Favicon -->
    @if(Settings::get('site_favicon'))
        <link rel="icon" href="{{ asset('storage/' . Settings::get('site_favicon')) }}">
    @else
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @endif
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'sans-serif'],
                        'display': ['Playfair Display', 'serif']
                    },
                    colors: {
                        'indigo': {
                            900: '#312e81'
                        },
                        'purple': {
                            200: '#e9d5ff',
                            300: '#d8b4fe',
                            500: '#a855f7',
                            600: '#9333ea',
                            700: '#7e22ce'
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-black text-white">
    <main>
        @yield('content')
    </main>
    
    @stack('scripts')
</body>
</html> 