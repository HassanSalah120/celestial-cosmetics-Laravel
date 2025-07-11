@php
use App\Facades\Settings;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Prevent caching for theme changes -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <title>
        @hasSection('title')
            @yield('title') | {{ config('app.name', 'Celestial Cosmetics') }} Admin
        @else
            {{ config('app.name', 'Celestial Cosmetics') }} Admin
        @endif
    </title>

    <!-- Favicon -->
    @if(Settings::get('site_favicon'))
        <link rel="icon" href="{{ secure_asset('storage/' . Settings::get('site_favicon')) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Mutation Events Fix -->
    <script src="{{ secure_asset('js/mutation-fix.js') }}"></script>
    
    <!-- AG Grid -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-alpine.css">
    <script src="https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js"></script>

    <!-- Additional Styles -->
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        
        @php
            if (!function_exists('hex2rgb')) {
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
<body class="bg-gray-100 font-sans antialiased admin-panel">
    <div class="min-h-screen bg-gray-50">
        <!-- Mobile Sidebar Backdrop -->
        <div x-data="{ open: false }" @keydown.window.escape="open = false">
            <!-- Mobile Menu Overlay -->
            <div x-show="open" class="fixed inset-0 z-40 md:hidden" x-description="Off-canvas menu for mobile, show/hide based on off-canvas menu state." x-ref="dialog" aria-modal="true" style="display: none;">
                <div x-show="open" x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="open = false" aria-hidden="true"></div>

                <div x-show="open" x-transition:enter="transition ease-in-out duration-300 transform" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="relative flex-1 flex flex-col max-w-xs w-full bg-primary">
                    <div x-show="open" x-transition:enter="ease-in-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="absolute top-0 right-0 -mr-12 pt-2">
                        <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" @click="open = false">
                            <span class="sr-only">Close sidebar</span>
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Mobile Sidebar Content -->
                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <div class="flex-shrink-0 flex items-center px-4">
                            @if(Settings::get('site_logo'))
                                <img src="{{ asset('storage/' . Settings::get('site_logo')) }}" alt="{{ config('app.name') }}" class="h-8 w-auto rounded">
                            @else
                            <img src="{{ asset('storage/logo.jpg') }}" alt="{{ config('app.name') }}" class="h-8 w-auto rounded">
                            @endif
                            <span class="ml-2 text-xl font-bold text-white">Admin Panel</span>
                        </div>
                        @include('layouts.partials.admin-navigation')
                    </div>
                </div>

                <div class="flex-shrink-0 w-14" aria-hidden="true">
                    <!-- Force sidebar to shrink to fit close icon -->
                </div>
            </div>

            <!-- Static sidebar for desktop -->
            <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
                <div class="flex-1 flex flex-col min-h-0 bg-primary">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex items-center flex-shrink-0 px-4">
                            @if(Settings::get('site_logo'))
                                <img src="{{ asset('storage/' . Settings::get('site_logo')) }}" alt="{{ config('app.name') }}" class="h-8 w-auto rounded">
                            @else
                            <img src="{{ asset('storage/logo.jpg') }}" alt="{{ config('app.name') }}" class="h-8 w-auto rounded">
                            @endif
                            <span class="ml-2 text-xl font-bold text-white">Admin Panel</span>
                        </div>
                        @include('layouts.partials.admin-navigation')
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="md:pl-64 flex flex-col flex-1">
                <!-- Top Navigation -->
                <div class="sticky top-0 z-10 md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-gray-100">
                    <button type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary" @click="open = true">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Page Content -->
                <main class="flex-1">
                    <div class="py-6">
                        <div class="mx-auto px-4 sm:px-6 md:px-8">
                            @if (session('success'))
                                <div class="mb-4 bg-green-50 border border-green-200 text-green-600 px-4 py-3 rounded relative" role="alert">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded relative" role="alert">
                                    {{ session('error') }}
                                </div>
                            @endif

                            @yield('content')
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    @stack('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const collapsibleButtons = document.querySelectorAll('.collapsible-menu-btn');
            
            collapsibleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const submenu = this.nextElementSibling;
                    const icon = this.querySelector('.submenu-icon');
                    
                    if (submenu.classList.contains('hidden')) {
                        // Show this submenu
                        submenu.classList.remove('hidden');
                        icon.classList.add('rotate-180');
                    } else {
                        // Hide this submenu
                        submenu.classList.add('hidden');
                        icon.classList.remove('rotate-180');
                    }
                });
            });

            // Check for active submenu items and open their parent menus
            const currentUrl = window.location.href;
            
            // First try exact match
            let activeLinks = document.querySelectorAll('.submenu a[href="' + currentUrl + '"]');
            
            // If no exact match, try checking if the current URL starts with the link href
            if (activeLinks.length === 0) {
                document.querySelectorAll('.submenu a').forEach(link => {
                    if (currentUrl.startsWith(link.href)) {
                        activeLinks = [link, ...activeLinks];
                    }
                });
            }
            
            // Open submenus containing active links
            activeLinks.forEach(activeLink => {
                const parentSubmenu = activeLink.closest('.submenu');
                if (parentSubmenu && parentSubmenu.classList.contains('hidden')) {
                    parentSubmenu.classList.remove('hidden');
                    const button = parentSubmenu.previousElementSibling;
                    if (button && button.classList.contains('collapsible-menu-btn')) {
                        const icon = button.querySelector('.submenu-icon');
                        if (icon) {
                            icon.classList.add('rotate-180');
                        }
                    }
                }
            });
            
            // Also check for any submenu that should be open based on class
            document.querySelectorAll('.submenu:not(.hidden)').forEach(submenu => {
                const button = submenu.previousElementSibling;
                if (button && button.classList.contains('collapsible-menu-btn')) {
                    const icon = button.querySelector('.submenu-icon');
                    if (icon) {
                        icon.classList.add('rotate-180');
                    }
                }
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
</body>
</html>