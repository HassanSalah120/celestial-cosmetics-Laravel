@php
    use Illuminate\Support\Facades\Auth;
    use App\Facades\Cart;
    use Illuminate\Support\Str;
    use App\Models\GlobalHeaderSetting;
    
    // Get all header settings at once for better performance
    $headerSettings = App\Models\GlobalHeaderSetting::getSettings();
@endphp

<!-- Main Navigation -->
<header class="bg-primary shadow-md border-b border-primary-dark/20" x-data="{ mobileMenuOpen: false }">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-3">
            <!-- Logo -->
            @if($headerSettings->show_logo)
            <div class="flex-shrink-0 flex items-center logo-container">
                <a href="{{ route('home') }}" class="flex items-center">
                    @if(Settings::get('site_logo'))
                        <img src="{{ asset('storage/' . Settings::get('site_logo')) }}" alt="{{ Settings::get('site_name') }}" class="h-8 sm:h-10 {{ is_rtl() ? 'ml-2' : 'mr-2' }}">
                    @else
                        <div class="w-8 h-8 sm:w-10 sm:h-10 flex items-center justify-center rounded-full bg-white {{ is_rtl() ? 'ml-2' : 'mr-2' }}">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-primary" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" fill="currentColor" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl sm:text-2xl font-display text-accent truncate max-w-[120px] sm:max-w-none">
                        @if(is_rtl())
                            سيليستيال كوزمتكس
                        @else
                            {{ Settings::get('site_name') }}
                        @endif
                    </span>
                </a>
            </div>
            @endif

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex space-x-8 {{ is_rtl() ? 'space-x-reverse' : '' }}">
                @if(isset($headerItems) && count($headerItems) > 0)
                    @foreach($headerItems as $item)
                        @if($item->has_dropdown && $item->children && $item->children->count() > 0)
                            <div class="relative" x-data="{ open: false, timeout: null }">
                                <button @click="open = !open" 
                                   @mouseenter="clearTimeout(timeout); open = true" 
                                   @mouseleave="timeout = setTimeout(() => open = false, 300)" 
                                   class="text-white hover:text-accent transition-colors duration-200 font-medium flex items-center dropdown-trigger relative group nav-link-hover"
                                   :class="{'dropdown-open': open}">
                                    <span>
                                        @if(is_rtl() && !empty($item->name_ar))
                                            {{ $item->name_ar }}
                                        @else
                                            {{ $item->name }}
                                        @endif
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 transition-transform duration-300 dropdown-arrow" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                     @mouseenter="clearTimeout(timeout); open = true" 
                                     @mouseleave="timeout = setTimeout(() => open = false, 300)" 
                                     class="absolute left-0 mt-2 py-2 w-48 bg-white rounded-md shadow-lg z-10 dropdown-menu" 
                                     x-cloak>
                                    @foreach($item->children as $child)
                                        <a href="{{ !empty($child->route) ? route($child->route) : $child->url }}" 
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700 hover:pl-6 transition-all duration-200"
                                           @if($child->open_in_new_tab) target="_blank" @endif>
                                            @if(is_rtl() && !empty($child->name_ar))
                                                {{ $child->name_ar }}
                                            @else
                                                {{ $child->name }}
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ !empty($item->route) ? route($item->route) : $item->url }}" 
                               class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->url() == (!empty($item->route) ? route($item->route) : $item->url) ? 'text-accent active' : '' }}"
                               @if($item->open_in_new_tab) target="_blank" @endif>
                                <span>
                                @if(is_rtl() && !empty($item->name_ar))
                                    {{ $item->name_ar }}
                                @else
                                    {{ $item->name }}
                                @endif
                                </span>
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('home') }}" class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->routeIs('home') ? 'text-accent active' : '' }}">
                        <span>{{ is_rtl() ? 'الرئيسية' : 'Home' }}</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->routeIs('categories.*') ? 'text-accent active' : '' }}">
                        <span>{{ is_rtl() ? 'الفئات' : 'Categories' }}</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->routeIs('products.*') ? 'text-accent active' : '' }}">
                        <span>{{ is_rtl() ? 'المنتجات' : 'Products' }}</span>
                    </a>
                    <a href="{{ route('offers.index') }}" class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->routeIs('offers.*') ? 'text-accent active' : '' }}">
                        <span>{{ is_rtl() ? 'عروض خاصة' : 'Special Offers' }}</span>
                    </a>
                    <a href="{{ route('about') }}" class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->routeIs('about') ? 'text-accent active' : '' }}">
                        <span>{{ is_rtl() ? 'من نحن' : 'About Us' }}</span>
                    </a>
                    <a href="{{ route('contact') }}" class="text-white hover:text-accent transition-colors duration-200 font-medium relative group nav-link-hover {{ request()->routeIs('contact') ? 'text-accent active' : '' }}">
                        <span>{{ is_rtl() ? 'اتصل بنا' : 'Contact' }}</span>
                    </a>
                @endif
            </nav>

            <!-- Desktop Right Side -->
            <div class="hidden md:flex items-center space-x-4 {{ is_rtl() ? 'space-x-reverse' : '' }}">
                <!-- Search Button -->
                @if($headerSettings->show_search)
                <div class="relative search-container" x-data="{ open: false }">
                    <button @click="open = !open" class="text-white hover:text-accent transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8a4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </button>
                    <!-- Search Dropdown -->
                    <div x-show="open" @click.away="open = false" class="absolute {{ is_rtl() ? 'left-0' : 'right-0' }} mt-2 w-64 bg-white rounded-md shadow-lg z-10" x-cloak>
                        <form action="{{ secure_url(route('products.search', [], false)) }}" method="GET" class="p-4 search-form">
                            <input id="search-input" type="text" name="query" placeholder="{{ __('Search here...') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary">
                        </form>
                        <div id="search-results" class="hidden max-h-60 overflow-y-auto shadow-md">
                            <div class="search-placeholder p-4 text-gray-500 text-center">{{ __('Type to search...') }}</div>
                            <div class="search-results-container"></div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Store Status -->
                @if($headerSettings->show_store_hours)
                <div class="px-2 py-1 bg-white/10 rounded-full backdrop-blur-sm mr-2 relative z-50">
                    <x-store-status />
                </div>
                @endif
                
                <!-- Language Switcher -->
                @if($headerSettings->show_language_switcher)
                <div x-data="{ open: false }" class="relative mr-2">
                    <button @click="open = !open" class="text-white hover:text-accent transition-colors duration-200 flex items-center language-switcher">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                        <span class="text-sm">{{ is_rtl() ? 'AR' : 'EN' }}</span>
                    </button>
                    
                    <!-- Language Dropdown -->
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50" style="display: none;">
                        <div class="py-1">
                        @php
                                // Get timestamp for cache busting
                                $timestamp = time();
                            $availableLanguages = Settings::get('available_languages', ['en' => 'English', 'ar' => 'العربية']);
                            
                                // Try to decode if it's a JSON string
                            if (is_string($availableLanguages)) {
                                    $decodedLanguages = json_decode($availableLanguages, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedLanguages)) {
                                        $availableLanguages = $decodedLanguages;
                                    } else {
                                        // If not valid JSON, default to basic languages
                                        $availableLanguages = ['en' => 'English', 'ar' => 'العربية'];
                                    }
                                } elseif (!is_array($availableLanguages)) {
                                    // If any error occurs, default to basic languages
                                    $availableLanguages = ['en' => 'English', 'ar' => 'العربية'];
                            } elseif (isset($availableLanguages[0])) {
                                $formattedLanguages = [];
                                foreach ($availableLanguages as $code) {
                                        // Set language names based on code
                                    $languageNames = [
                                        'en' => 'English',
                                        'ar' => 'العربية',
                                        'fr' => 'Français',
                                        'de' => 'Deutsch',
                                            'es' => 'Español',
                                    ];
                                    $formattedLanguages[$code] = $languageNames[$code] ?? $code;
                                }
                                $availableLanguages = $formattedLanguages;
                            }
                        @endphp
                        
                        @foreach($availableLanguages as $code => $name)
                                <a href="/language/{{ $code }}?redirect={{ urlencode(request()->path()) }}"
                                   class="block px-4 py-2 text-sm {{ (is_rtl() && $code === 'ar') || (!is_rtl() && $code === 'en') ? 'bg-teal-50 text-teal-700 font-medium border-l-3 border-accent' : 'text-gray-700 hover:bg-teal-50 hover:text-teal-700 border-l-3 border-transparent' }} transition-all">
                                {{ $name }}
                            </a>
                        @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="relative text-white hover:text-accent transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    @auth
                        @php
                            $wishlistCount = auth()->user()->wishlists()->count();
                        @endphp
                        @if($wishlistCount > 0)
                            <span class="absolute -top-2 -right-2 bg-accent text-primary text-xs rounded-full h-4 w-4 flex items-center justify-center wishlist-badge">
                                {{ $wishlistCount }}
                            </span>
                        @endif
                    @endauth
                </a>

                <!-- Cart -->
                @if($headerSettings->show_cart)
                <a href="{{ route('cart.index') }}" class="relative text-white hover:text-accent transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                    </svg>
                    @if(Cart::count() > 0)
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-accent text-primary text-xs rounded-full h-4 w-4 flex items-center justify-center cart-badge">
                            {{ Cart::count() }}
                        </span>
                    @endif
                </a>
                @endif
                
                <!-- User Profile/Login -->
                @if($headerSettings->show_profile)
                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center text-white hover:text-accent transition-colors duration-200">
                            <div class="w-8 h-8 rounded-full overflow-hidden bg-white/10 flex-shrink-0">
                                @if(Auth::user()->profile_image)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=6D28D9" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10" x-cloak>
                            <div class="py-1">
                                <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                                    {{ __('Profile') }}
                                </a>
                                <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                                    {{ __('My Orders') }}
                                </a>
                                @if(Auth::user()->hasPermission('access_admin_panel'))
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                                        {{ __('Admin Dashboard') }}
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-teal-50 hover:text-teal-700">
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    @if($headerSettings->show_auth_links)
                    <a href="{{ route('login') }}" class="text-white hover:text-accent transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                        </svg>
                    </a>
                    @endif
                @endauth
                @endif
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden space-x-4 {{ is_rtl() ? 'space-x-reverse' : '' }}">
                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="relative text-white hover:text-accent transition-colors duration-200">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                    </svg>
                    @auth
                        @php
                            $wishlistCount = auth()->user()->wishlists()->count();
                        @endphp
                        @if($wishlistCount > 0)
                            <span class="absolute -top-2 -right-2 bg-accent text-primary text-xs rounded-full h-4 w-4 flex items-center justify-center wishlist-badge">
                                {{ $wishlistCount }}
                            </span>
                        @endif
                    @endauth
                </a>
                
                <!-- Cart -->
                @if($headerSettings->show_cart)
                <a href="{{ route('cart.index') }}" class="relative text-white hover:text-accent transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                    </svg>
                    @if(Cart::count() > 0)
                        <span class="absolute -top-2 -right-2 bg-accent text-primary text-xs rounded-full h-4 w-4 flex items-center justify-center">
                            {{ Cart::count() }}
                        </span>
                    @endif
                </a>
                @endif
                
                <!-- Hamburger Button -->
                <button type="button" class="text-white hover:text-accent focus:outline-none hamburger-button" @click="mobileMenuOpen = !mobileMenuOpen">
                    <svg x-show="!mobileMenuOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileMenuOpen" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen" 
         class="md:hidden bg-primary-dark border-t border-primary-dark/30 fixed inset-0 z-50 overflow-y-auto pb-20 mobile-menu"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         x-cloak>
        <!-- Close button -->
        <div class="fixed top-4 right-4 z-50">
            <button @click="mobileMenuOpen = false" class="p-2 rounded-full bg-primary-light/20 text-white hover:bg-primary-light/30 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="px-4 py-3 space-y-4 mt-16">
            <!-- Store Status -->
            @if($headerSettings->show_store_hours)
            <div class="bg-white/10 rounded-lg p-3 mb-4 flex items-center justify-between">
                <div class="text-white text-sm font-medium">{{ is_rtl() ? 'حالة المتجر' : 'Store Status' }}</div>
                <div class="relative z-50">
                    <x-store-status />
                </div>
            </div>
            @endif
            
            <!-- Search Bar -->
            @if($headerSettings->show_search)
            <div class="relative">
                <form action="{{ secure_url(route('products.search', [], false)) }}" method="GET" class="search-form">
                    <input id="mobile-search-input" type="text" name="query" placeholder="{{ is_rtl() ? 'ابحث هنا...' : 'Search here...' }}" class="w-full bg-primary-light/20 text-white placeholder-white/70 rounded-lg pl-10 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-accent text-base">
                    <div class="absolute left-3 top-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white/70" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8a4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </form>
                <div id="mobile-search-results" class="hidden absolute top-full left-0 right-0 bg-white rounded-md mt-1 shadow-lg z-10 max-h-60 overflow-y-auto">
                    <div class="search-placeholder p-4 text-gray-500 text-center">{{ __('Type to search...') }}</div>
                    <div class="search-results-container"></div>
                </div>
            </div>
            @endif
            
            <!-- Navigation Links -->
            <nav class="space-y-0 rounded-lg overflow-hidden bg-primary-light/10 mobile-menu-content">
                @if(isset($headerItems) && count($headerItems) > 0)
                    @foreach($headerItems as $item)
                        @if($item->has_dropdown && $item->children && $item->children->count() > 0)
                            <div x-data="{ open: false }">
                                <button @click="open = !open" 
                                   class="w-full block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between"
                                   :class="{'dropdown-open': open}">
                                    <span>
                                        @if(is_rtl() && !empty($item->name_ar))
                                            {{ $item->name_ar }}
                                        @else
                                            {{ $item->name }}
                                        @endif
                                    </span>
                                    <svg class="w-4 h-4 transition-transform duration-300 dropdown-arrow" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div x-show="open" class="bg-primary-light/10">
                                    @foreach($item->children as $child)
                                        <a href="{{ !empty($child->route) ? route($child->route) : $child->url }}" 
                                           class="block text-white hover:bg-primary-light/30 transition-all duration-300 py-3 px-6 border-b border-primary-dark/10 text-base flex items-center hover:pl-8"
                                           @if($child->open_in_new_tab) target="_blank" @endif>
                                            <span class="ml-2">
                                                @if(is_rtl() && !empty($child->name_ar))
                                                    {{ $child->name_ar }}
                                                @else
                                                    {{ $child->name }}
                                                @endif
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <a href="{{ !empty($item->route) ? route($item->route) : $item->url }}" 
                               class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->url() == (!empty($item->route) ? route($item->route) : $item->url) ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}"
                               @if($item->open_in_new_tab) target="_blank" @endif>
                                <span>
                                @if(is_rtl() && !empty($item->name_ar))
                                    {{ $item->name_ar }}
                                @else
                                    {{ $item->name }}
                                @endif
                                </span>
                                <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        @endif
                    @endforeach
                @else
                    <a href="{{ route('home') }}" class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->routeIs('home') ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}">
                        <span>{{ is_rtl() ? 'الرئيسية' : 'Home' }}</span>
                        <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('categories.index') }}" class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->routeIs('categories.*') ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}">
                        <span>{{ is_rtl() ? 'الفئات' : 'Categories' }}</span>
                        <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('products.index') }}" class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->routeIs('products.*') ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}">
                        <span>{{ is_rtl() ? 'المنتجات' : 'Products' }}</span>
                        <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('offers.index') }}" class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->routeIs('offers.*') ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}">
                        <span>{{ is_rtl() ? 'عروض خاصة' : 'Special Offers' }}</span>
                        <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('about') }}" class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->routeIs('about') ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}">
                        <span>{{ is_rtl() ? 'من نحن' : 'About Us' }}</span>
                        <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <a href="{{ route('contact') }}" class="block text-white hover:bg-primary-light/20 transition-all duration-300 py-4 px-4 border-b border-primary-dark/20 text-lg flex items-center justify-between relative overflow-hidden {{ request()->routeIs('contact') ? 'bg-primary-light/15 border-l-4 border-l-accent px-3 active' : '' }}">
                        <span>{{ is_rtl() ? 'اتصل بنا' : 'Contact' }}</span>
                        <svg class="w-4 h-4 rtl-flip transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                @endif
            </nav>

            <!-- User Account Section -->
            @if($headerSettings->show_profile)
            <div class="bg-primary-light/10 rounded-lg overflow-hidden mt-6">
                @auth
                    <div class="p-4 border-b border-primary-dark/20 flex items-center">
                        <div class="w-10 h-10 rounded-full overflow-hidden bg-white/10 flex-shrink-0 {{ is_rtl() ? 'ml-3' : 'mr-3' }}">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/' . Auth::user()->profile_image) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=FFFFFF&background=6D28D9" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-white">{{ Str::words(Auth::user()->name, 1, '') }}</div>
                            <div class="text-xs text-white/70">{{ Auth::user()->email }}</div>
                        </div>
                    </div>
                    <a href="{{ route('profile') }}" class="block text-white hover:bg-primary-light/20 transition-colors duration-200 py-3 px-4 border-b border-primary-dark/20 text-base">
                        {{ __('navigation.profile') }}
                    </a>
                    <a href="{{ route('orders.index') }}" class="block text-white hover:bg-primary-light/20 transition-colors duration-200 py-3 px-4 border-b border-gray-100">
                        {{ __('navigation.my_orders') }}
                    </a>
                    @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block text-white hover:bg-primary-light/20 transition-colors duration-200 py-3 px-4 border-b border-gray-100">
                            {{ __('navigation.admin') }}
                        </a>
                    @endif
                    <form method="POST" action="{{ secure_url(route('logout', [], false)) }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-3 text-white hover:bg-primary-light/20 transition-colors duration-200 text-base">
                            {{ __('navigation.logout') }}
                        </button>
                    </form>
                @else
                    @if($headerSettings->show_auth_links)
                    <div class="border-b border-primary-dark/20">
                        <a href="{{ route('login') }}" class="flex items-center justify-center py-4 text-white hover:text-accent hover:bg-primary-light/10 transition-colors duration-200 text-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                    @endif
                @endauth
            </div>
            @endif

            <!-- Language Switch -->
            @if($headerSettings->show_language_switcher)
            <div class="mt-6">
                <div class="text-white/80 text-sm mb-2">{{ is_rtl() ? 'اختر اللغة' : 'Select Language' }}</div>
                
                <div class="grid grid-cols-2 gap-2">
                    @php
                        $availableLanguages = Settings::get('available_languages', ['en' => 'English', 'ar' => 'العربية']);
                        
                        // Ensure it's in the correct format
                        if (is_string($availableLanguages)) {
                            try {
                                $decodedLanguages = json_decode($availableLanguages, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedLanguages)) {
                                    $availableLanguages = $decodedLanguages;
                                } else {
                                    // If not valid JSON, default to basic languages
                                    $availableLanguages = ['en' => 'English', 'ar' => 'العربية'];
                                }
                            } catch (\Exception $e) {
                                // If any error occurs, default to basic languages
                                $availableLanguages = ['en' => 'English', 'ar' => 'العربية'];
                            }
                        } elseif (isset($availableLanguages[0])) {
                            $formattedLanguages = [];
                            foreach ($availableLanguages as $code) {
                                // Use a more comprehensive mapping
                                $languageNames = [
                                    'en' => 'English',
                                    'ar' => 'العربية',
                                    'fr' => 'Français',
                                    'de' => 'Deutsch',
                                    'es' => 'Español'
                                ];
                                $formattedLanguages[$code] = $languageNames[$code] ?? $code;
                            }
                            $availableLanguages = $formattedLanguages;
                        }
                        
                        // Generate unique timestamp for cache busting
                        $timestamp = time();
                    @endphp
                    
                    @foreach($availableLanguages as $code => $name)
                        <a href="/language/{{ $code }}?redirect={{ urlencode(request()->path()) }}"
                           class="flex items-center justify-center py-2 px-3 rounded-md {{ (is_rtl() && $code === 'ar') || (!is_rtl() && $code === 'en') ? 'bg-accent text-primary font-semibold' : 'bg-primary-light/20 text-white hover:bg-primary-light/30' }} transition-colors duration-200">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar -->
    <div class="fixed bottom-0 left-0 right-0 bg-primary border-t border-primary-dark/20 px-2 py-2 md:hidden z-30">
        <div class="grid grid-cols-5 gap-1">
            <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-1 rounded-md {{ request()->routeIs('home') ? 'text-accent mobile-bottom-nav-active' : 'text-white' }} hover:bg-primary-light/20 transition-all duration-300 relative group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
                <span class="text-xs mt-1">{{ is_rtl() ? 'الرئيسية' : 'Home' }}</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex flex-col items-center justify-center py-1 rounded-md {{ request()->routeIs('products.*') ? 'text-accent mobile-bottom-nav-active' : 'text-white' }} hover:bg-primary-light/20 transition-all duration-300 relative group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                <span class="text-xs mt-1">{{ is_rtl() ? 'المنتجات' : 'Products' }}</span>
            </a>
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="flex flex-col items-center justify-center py-1 rounded-md text-white hover:bg-primary-light/20 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs mt-1">{{ is_rtl() ? 'القائمة' : 'Menu' }}</span>
            </button>
            @if($headerSettings->show_cart)
            <a href="{{ route('cart.index') }}" class="flex flex-col items-center justify-center py-1 rounded-md {{ request()->routeIs('cart.*') ? 'text-accent mobile-bottom-nav-active' : 'text-white' }} hover:bg-primary-light/20 transition-all duration-300 relative group">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" />
                    </svg>
                    @if(Cart::count() > 0)
                        <span class="absolute -top-2 -right-2 bg-accent text-primary text-xs rounded-full h-4 w-4 flex items-center justify-center">
                            {{ Cart::count() }}
                        </span>
                    @endif
                </div>
                <span class="text-xs mt-1">{{ is_rtl() ? 'السلة' : 'Cart' }}</span>
            </a>
            @else
            <div class="flex flex-col items-center justify-center py-1 rounded-md text-white/30">
                <!-- Empty placeholder -->
            </div>
            @endif
            @if($headerSettings->show_profile)
            <a href="{{ Auth::check() ? route('profile') : route('login') }}" class="flex flex-col items-center justify-center py-1 rounded-md {{ request()->routeIs('profile') || request()->routeIs('login') ? 'text-accent mobile-bottom-nav-active' : 'text-white' }} hover:bg-primary-light/20 transition-all duration-300 relative group">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd" />
                </svg>
                <span class="text-xs mt-1">{{ is_rtl() ? 'حسابي' : 'Account' }}</span>
            </a>
            @else
            <div class="flex flex-col items-center justify-center py-1 rounded-md text-white/30">
                <!-- Empty placeholder -->
            </div>
            @endif
        </div>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');
        
        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Sync cart counts between mobile and desktop views
        function updateCartCount(count) {
            const cartCountElements = document.querySelectorAll('#cart-count, #mobile-cart-count');
            cartCountElements.forEach(element => {
                if (count > 0) {
                    element.textContent = count;
                    element.classList.remove('hidden');
                } else {
                    element.classList.add('hidden');
                }
            });
        }

        // Listen for custom cart update events
        document.addEventListener('cartUpdated', function(e) {
            if (e.detail && typeof e.detail.count !== 'undefined') {
                updateCartCount(e.detail.count);
            }
        });
        
        // Real-time Search Functionality
        const searchInputs = document.querySelectorAll('#search-input, #mobile-search-input');
        const searchResults = document.querySelectorAll('#search-results, #mobile-search-results');
        const searchForms = document.querySelectorAll('.search-form');
        
        searchInputs.forEach((input, index) => {
            const resultsContainer = searchResults[index];
            const form = searchForms[index];
            
            // Variables for debouncing
            let searchTimeout;
            const debounceTime = 300; // milliseconds
            
            input.addEventListener('focus', function() {
                if (this.value.length >= 2) {
                    resultsContainer.classList.remove('hidden');
                }
            });
            
            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                // Clear any existing timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // Show placeholder when less than 2 characters
                if (query.length < 2) {
                    resultsContainer.querySelector('.search-placeholder').style.display = 'block';
                    resultsContainer.querySelector('.search-results-container').innerHTML = '';
                    if (query.length === 0) {
                        resultsContainer.classList.add('hidden');
                    } else {
                        resultsContainer.classList.remove('hidden');
                    }
                    return;
                }
                
                // Debounce the search to avoid too many requests
                searchTimeout = setTimeout(() => {
                    fetchSearchResults(query, resultsContainer);
                }, debounceTime);
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !resultsContainer.contains(e.target)) {
                    resultsContainer.classList.add('hidden');
                }
            });
            
            // Submit form on pressing enter
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    form.submit();
                }
            });
        });
        
        // Function to fetch search results
        function fetchSearchResults(query, resultsContainer) {
            fetch(`{{ route('products.autocomplete') }}?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    const resultsHtml = renderSearchResults(data.products);
                    const searchResultsContainer = resultsContainer.querySelector('.search-results-container');
                    searchResultsContainer.innerHTML = resultsHtml;
                    
                    resultsContainer.querySelector('.search-placeholder').style.display = 
                        data.products.length === 0 ? 'block' : 'none';
                        
                    if (data.products.length === 0) {
                        resultsContainer.querySelector('.search-placeholder').textContent = 
                            '{{ __("messages.no_results") ?? "No results found" }}';
                    }
                    
                    resultsContainer.classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching search results:', error);
                });
        }
        
        // Function to render search results
        function renderSearchResults(products) {
            if (products.length === 0) return '';
            
            return products.map(product => {
                // Handle potentially missing properties with defaults
                const name = product.name || 'Product';
                const category = product.category_name || '';
                const price = product.final_price || product.price || '';
                const slug = product.slug || product.id;
                const imgPath = product.featured_image ? `/storage/${product.featured_image}` : '';
                
                return `
                <a href="/products/${slug}" class="block hover:bg-gray-50">
                    <div class="flex items-center p-3 border-b border-gray-100">
                        <div class="w-12 h-12 flex-shrink-0 bg-gray-100 rounded-md overflow-hidden">
                            ${imgPath ? 
                                `<img src="${imgPath}" alt="${name}" class="w-full h-full object-cover">` : 
                                `<div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>`
                            }
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${name}</div>
                            <div class="text-xs text-gray-500">${category}</div>
                        </div>
                        <div class="ml-auto text-sm font-medium text-accent">
                            ${price}
                        </div>
                    </div>
                </a>
            `;
            }).join('');
        }
    });
</script>

<!-- Language Switcher Script -->
<script>
    // Language switcher script
    document.addEventListener('DOMContentLoaded', function() {
        // Function to switch language and reload the page
        window.switchLanguage = function(locale) {
            // Create form element
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/language/' + locale;
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            form.appendChild(csrfInput);
            
            // Add to document, submit, and clean up
            document.body.appendChild(form);
            form.submit();
            
            // Prevent default behavior
            return false;
        };
    });
</script> 