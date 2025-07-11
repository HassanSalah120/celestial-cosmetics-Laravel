@extends('layouts.app')

@section('meta_tags')
    <x-seo :title="Settings::get('homepage_meta_title')"
           :description="Settings::get('homepage_meta_description')"
           :keywords="Settings::get('homepage_meta_keywords')"
           :ogImage="Settings::get('homepage_og_image')"
           type="website" />
@endsection

@section('content')
    @php
        $bgColor = 'bg-white'; // Start with white background
    @endphp
    
    @foreach($sectionOrder as $section)
        @if($section === 'hero')
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center bg-gradient-to-b from-primary to-secondary overflow-hidden">
        <!-- Enhanced background elements with reduced blur -->
        <div class="absolute inset-0">
            <!-- Sharper background gradient with lighter noise texture -->
            <div class="absolute inset-0 opacity-20 mix-blend-overlay bg-noise"></div>
            
            <!-- Improved decorative elements with reduced blur -->
            <div class="absolute top-20 left-10 w-72 h-72 bg-accent/15 rounded-full blur-2xl animate-float-slow transform-gpu"></div>
            <div class="absolute bottom-20 right-10 w-96 h-96 bg-accent/15 rounded-full blur-2xl animate-float transform-gpu delay-1000"></div>
            <div class="absolute top-1/3 right-1/4 w-48 h-48 bg-primary-light/25 rounded-full blur-2xl animate-pulse-slow transform-gpu"></div>
            
            @if($enableAnimations)
            <!-- Enhanced Celestial Elements with better visibility -->
            <div class="star-icon top-1/4 left-1/4 w-4 h-4 text-accent/60 animate-twinkle">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"/></svg>
            </div>
            <div class="star-icon delay-2 top-1/3 right-1/4 w-6 h-6 text-accent/60 animate-twinkle-slow">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.4h7.6l-6.2 4.5 2.4 7.4-6.2-4.5-6.2 4.5 2.4-7.4-6.2-4.5h7.6z"/></svg>
            </div>
            <div class="moon-icon delay-3 top-1/4 right-1/3 w-8 h-8 text-accent/60 animate-orbit">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z"/></svg>
            </div>
            <div class="cosmic-icon delay-1 bottom-1/4 left-1/3 w-12 h-12 text-accent/60 animate-spin-slow">
                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/></svg>
            </div>
            
            <!-- Improved Shooting Stars with better visibility -->
            <div class="absolute w-20 h-1 bg-accent/80 -rotate-45 animate-shooting-star" style="top: 20%; left: -10%"></div>
            <div class="absolute w-32 h-1 bg-accent/70 -rotate-45 animate-shooting-star delay-3" style="top: 40%; left: -15%"></div>
            <div class="absolute w-16 h-1 bg-accent/90 -rotate-45 animate-shooting-star delay-5" style="top: 60%; left: -10%"></div>
            @endif
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div class="text-center {{ is_rtl() ? 'md:text-right' : 'md:text-left' }} mb-8 md:mb-0" data-aos="{{ is_rtl() ? 'fade-left' : 'fade-right' }}">
                    <!-- Pretitle tag with improved contrast -->
                    <div class="mb-6 inline-block bg-white/15 backdrop-blur-sm px-4 py-1 rounded-full">
                        @php
                            $currentLocale = session('locale', app()->getLocale());
                            $isArabic = $currentLocale === 'ar';
                            $headtag = $isArabic 
                                ? (isset($homepageHero->headtag_ar) && !empty($homepageHero->headtag_ar) ? $homepageHero->headtag_ar : 'استكشف الكون')
                                : (isset($homepageHero->headtag) && !empty($homepageHero->headtag) ? $homepageHero->headtag : 'Experience the Cosmos');
                        @endphp
                        <span class="text-white text-sm font-medium">{{ $headtag }}</span>
                    </div>
                    
                    <!-- Enhanced typography with stronger text gradient -->
                    <h1 class="font-display text-3xl md:text-5xl lg:text-6xl text-white mb-4 md:mb-6 leading-tight font-bold">
                        {!! $homepageHero->title !!}
                    </h1>
                    
                    <p class="text-white text-base md:text-xl mb-8 max-w-lg mx-auto md:mx-0 leading-relaxed">
                        {{ $homepageHero->description }}
                    </p>
                    
                    <!-- Improved button styles with hover effects -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ $homepageHero->button_url }}" class="group rtl-btn inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-4 bg-accent text-white rounded-full text-base md:text-lg font-semibold hover:bg-accent-dark transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-accent/50">
                            <span class="relative">
                                {{ $homepageHero->button_text }}
                                <span class="absolute bottom-0 left-0 w-full h-0.5 bg-white scale-x-0 group-hover:scale-x-100 transition-transform origin-left duration-300"></span>
                            </span>
                            <svg class="w-4 h-4 md:w-5 md:h-5 ml-2 rtl-flip transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        @if(isset($homepageHero->secondary_button_text) && !empty(trim($homepageHero->secondary_button_text)))
                        <a href="{{ $homepageHero->secondary_button_url }}" class="group inline-flex items-center justify-center px-6 py-3 md:px-8 md:py-4 bg-white/20 text-white rounded-full text-base md:text-lg font-semibold hover:bg-white/30 transition-all duration-300 backdrop-blur-sm">
                            {{ $homepageHero->secondary_button_text }}
                            <svg class="w-4 h-4 md:w-5 md:h-5 ml-2 opacity-0 group-hover:opacity-100 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                    
                    <!-- Trust indicators with improved visibility -->
                    <div class="mt-8 flex items-center justify-center md:justify-start gap-6 text-white/90">
                        @php
                            // Get current locale directly from session
                            $currentLocale = session('locale', app()->getLocale());
                            $isArabic = $currentLocale === 'ar';
                            
                            // Properly decode JSON strings from database
                            $heroTags = isset($homepageHero->hero_tags) && !empty($homepageHero->hero_tags) 
                                ? json_decode($homepageHero->hero_tags, true) 
                                : ['Cruelty-Free', '100% Natural'];
                                
                            $heroTagsAr = isset($homepageHero->hero_tags_ar) && !empty($homepageHero->hero_tags_ar) 
                                ? json_decode($homepageHero->hero_tags_ar, true) 
                                : ['خالي من القسوة', '100٪ طبيعي'];
                                
                            // Use the appropriate tags based on current locale
                            $displayTags = $isArabic ? $heroTagsAr : $heroTags;
                            
                            // SVG icons to cycle through
                            $icons = [
                                '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.54-.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>',
                                '<path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1z" clip-rule="evenodd"/><path d="M14 4a4 4 0 11-8 0 4 4 0 018 0zm-8 9a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6z"/>',
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />'
                            ];
                        @endphp
                        
                        @foreach($displayTags as $index => $tag)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    {!! $icons[$index % count($icons)] !!}
                                </svg>
                                <span class="text-sm">{{ $tag }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="block" data-aos="fade-left">
                    <div class="relative">
                        <!-- Improved product image display with subtle reflection -->
                        <div class="relative z-10">
                            <img src="{{ $homepageHero->image && (str_starts_with($homepageHero->image, '/storage/') || str_starts_with($homepageHero->image, 'http')) ? $homepageHero->image : asset(Settings::get('default_product_image')) }}" 
                                 alt="{{ is_rtl() ? 'منتج مميز' : 'Featured Product' }}" 
                                 class="w-full transform hover:scale-105 transition-all duration-700 ease-in-out shadow-xl" 
                                 loading="lazy">
                                 
                            <!-- Enhanced product tag with less blur -->
                            <div class="absolute top-4 left-4 bg-accent/90 text-white text-xs py-1 px-3 rounded-full shadow-lg">
                                @if(is_rtl())
                                    منتج مميز
                                @else
                                    Featured Product
                                @endif
                            </div>
                            
                            <!-- Add subtle reflection effect -->
                            <div class="absolute -bottom-10 left-0 right-0 h-20 bg-gradient-to-b from-accent/10 to-transparent transform scale-x-75 blur-md"></div>
                        </div>
                                
                        @if($enableAnimations)
                        <!-- Improved floating elements with better visibility -->
                        <div class="absolute -top-8 -left-8 z-0 bg-white/15 rounded-full p-4 animate-float shadow-lg">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                        </div>
                        <div class="absolute top-1/3 -right-7 z-0 bg-white/15 rounded-full p-3 animate-float-reverse shadow-lg">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <div class="absolute -bottom-4 right-10 z-0 bg-white/15 rounded-full p-4 animate-pulse-slow shadow-lg">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Improved scroll indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex flex-col items-center text-white animate-bounce-slow">
            @php
                $currentLocale = session('locale', app()->getLocale());
                $isArabic = $currentLocale === 'ar';
                $scrollText = $isArabic 
                    ? (isset($homepageHero->scroll_indicator_text_ar) && !empty($homepageHero->scroll_indicator_text_ar) ? $homepageHero->scroll_indicator_text_ar : 'مرر للاستكشاف')
                    : (isset($homepageHero->scroll_indicator_text) && !empty($homepageHero->scroll_indicator_text) ? $homepageHero->scroll_indicator_text : 'Scroll to explore');
            @endphp
            <span class="text-sm mb-2">{{ $scrollText }}</span>
            <svg class="w-6 h-6 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
        
        <!-- CSS for animations with better performance -->
        <style>
            @keyframes twinkle {
                0%, 100% { opacity: 0.5; }
                50% { opacity: 1; }
            }
            .animate-twinkle {
                animation: twinkle 3s ease-in-out infinite;
            }
            .animate-twinkle-slow {
                animation: twinkle 5s ease-in-out infinite;
            }
            .animate-spin-slow {
                animation: spin 12s linear infinite;
            }
            .animate-float-slow {
                animation: float 8s ease-in-out infinite;
            }
            .animate-float-reverse {
                animation: float 6s ease-in-out infinite reverse;
            }
            .animate-orbit {
                animation: orbit 15s linear infinite;
            }
            .animate-pulse-slow {
                animation: pulse 7s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            .animate-bounce-slow {
                animation: bounce 2s infinite;
            }
            @keyframes orbit {
                0% { transform: rotate(0deg) translateX(20px) rotate(0deg); }
                100% { transform: rotate(360deg) translateX(20px) rotate(-360deg); }
            }
            .bg-noise {
                background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='2' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
            }
        </style>
    </section>
    @php $bgColor = 'bg-gray-50'; @endphp
        @elseif($section === 'offers')
            @if(isset($activeOffers) && $activeOffers->count() > 0)
            <!-- Special Offers Section -->
            <section class="py-16 {{ $bgColor }}">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-display text-primary mb-3">{{ isset($textSettings['offers']['title']) ? $textSettings['offers']['title'] : (isset($textSettings['offers_title']) ? $textSettings['offers_title'] : 'Special Offers') }}</h2>
                        <p class="text-gray-600 max-w-2xl mx-auto">{{ isset($textSettings['offers']['description']) ? $textSettings['offers']['description'] : (isset($textSettings['offers_description']) ? $textSettings['offers_description'] : 'Take advantage of these limited-time special offers and exclusive deals.') }}</p>
                    </div>
                    
                    @if(isset($activeOffers) && $activeOffers->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
                        @foreach($activeOffers as $offer)
                            <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                                <div class="flex flex-col md:flex-row">
                                        @if(isset($offer->image) && $offer->image)
                                            <div class="md:w-2/5 h-60 md:h-auto overflow-hidden">
                                                <img src="{{ asset('storage/' . str_replace('storage/', '', $offer->image)) }}" alt="{{ $offer->title }}" class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105" loading="lazy">
                                            </div>
                                        @else
                                        <div class="md:w-2/5 h-60 md:h-auto overflow-hidden">
                                                <img src="https://source.unsplash.com/300x400/?cosmetics,beauty,makeup,{{ urlencode($offer->title ?? 'beauty-product') }}" alt="{{ $offer->title ?? (is_rtl() ? 'عرض خاص' : 'Special Offer') }}" class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105" loading="lazy">
                                        </div>
                                    @endif
                                    <div class="p-6 md:w-3/5 flex flex-col justify-between bg-gradient-to-br from-white to-gray-50">
                                            @if(isset($offer->tag) && $offer->tag)
                                            <div class="mb-2">
                                                <span class="inline-block py-1 px-3 bg-primary-dark text-white text-xs uppercase font-bold rounded-full shadow-sm">
                                                        {{ $offer->tag }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-2xl font-display font-bold text-gray-900 mb-1">
                                                    {{ $offer->title }}
                                            </h3>
                                                @if(isset($offer->subtitle) && $offer->subtitle)
                                                <p class="text-accent-dark font-medium mb-2">
                                                        {{ $offer->subtitle }}
                                                </p>
                                            @endif
                                                @if(isset($offer->description) && $offer->description)
                                                <p class="text-gray-600 mb-4">
                                                        {{ $offer->description }}
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-2">
                                                @if(isset($offer->original_price) && $offer->original_price)
                                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                                        <span class="text-gray-500 line-through text-lg">{{ $currencySymbol ?? '$' }}{{ number_format($offer->original_price, 2) }}</span>
                                                        <span class="text-3xl font-bold text-red-600">{{ $currencySymbol ?? '$' }}{{ number_format($offer->discounted_price, 2) }}</span>
                                                        @if(isset($offer->discount_text) && $offer->discount_text)
                                                        <span class="py-1 px-3 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full shadow-sm">
                                                                {{ $offer->discount_text }}
                                                        </span>
                                                        @elseif(method_exists($offer, 'getDiscountPercentageAttribute'))
                                                        <span class="py-1 px-3 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full shadow-sm">
                                                {{ $offer->getDiscountPercentageAttribute() }}% {{ is_rtl() ? 'خصم' : 'OFF' }}
                                                        </span>
                                                    @endif
                                                    </div>
                                                @endif
                                            
                                                @if(isset($offer->expires_at) && $offer->expires_at)
                                                <div class="mb-4">
                                                    <div class="flex items-center mb-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <span class="font-medium text-gray-700">{{ is_rtl() ? 'ينتهي العرض في:' : 'Limited Time Offer:' }}</span>
                                                    </div>
                                                    <div class="countdown-timer bg-gray-100 p-2 rounded-md flex justify-center space-x-2 text-center" data-expires="{{ $offer->expires_at->timestamp }}">
                                                        <div class="flex flex-col">
                                                            <span class="days font-bold text-lg text-primary">00</span>
                                                            <span class="text-xs text-gray-500">{{ is_rtl() ? 'أيام' : 'Days' }}</span>
                                                        </div>
                                                        <div class="text-lg font-bold text-primary">:</div>
                                                        <div class="flex flex-col">
                                                            <span class="hours font-bold text-lg text-primary">00</span>
                                                            <span class="text-xs text-gray-500">{{ is_rtl() ? 'ساعات' : 'Hrs' }}</span>
                                                        </div>
                                                        <div class="text-lg font-bold text-primary">:</div>
                                                        <div class="flex flex-col">
                                                            <span class="minutes font-bold text-lg text-primary">00</span>
                                                            <span class="text-xs text-gray-500">{{ is_rtl() ? 'دقائق' : 'Min' }}</span>
                                                        </div>
                                                        <div class="text-lg font-bold text-primary">:</div>
                                                        <div class="flex flex-col">
                                                            <span class="seconds font-bold text-lg text-primary">00</span>
                                                            <span class="text-xs text-gray-500">{{ is_rtl() ? 'ثواني' : 'Sec' }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                                <div class="flex flex-wrap gap-2 mt-4">
                                                    @if(isset($offer->button_url) || isset($offer->button_text))
                                                <a href="{{ $offer->button_url ?: '/products' }}" class="inline-flex items-center justify-center px-6 py-3 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-300">
                                                            {{ $offer->button_text ?? (is_rtl() ? 'عرض العرض' : 'View Offer') }}
                                                    <svg class="ml-2 -mr-1 w-5 h-5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                    </svg>
                                                </a>
                                            @endif
                                            
                                                    @if(isset($offer->id))
                                                        @if($offer->stock > 0)
                                                            <form action="{{ url('/cart/add-offer/' . $offer->id) }}" method="POST" class="inline-block">
                                                                @csrf
                                                                <input type="hidden" name="quantity" value="1">
                                                                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-primary border border-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-300">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                                    </svg>
                                                                    {{ is_rtl() ? 'أضف إلى السلة' : 'Add to Cart' }}
                                                                </button>
                                                            </form>
                                                        @else
                                                            <span class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-red-100 text-red-800">
                                                                {{ is_rtl() ? 'نفذت الكمية' : 'Out of Stock' }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @else
                        <div class="bg-white p-8 rounded-lg shadow-md text-center">
                            <p>{{ is_rtl() ? 'يرجى العودة لاحقًا للاطلاع على عروضنا الخاصة' : 'Please check back later for our special offers' }}</p>
                        </div>
                    @endif
                </div>
            </section>
                @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
            @endif
        @elseif($section === 'featured_products')
    <!-- Featured Products Section -->
            <section class="py-16 {{ $bgColor }}">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                        <h2 class="text-4xl font-display text-primary mb-3">{{ isset($textSettings['featured_products']['title']) ? $textSettings['featured_products']['title'] : $textSettings['featured_products_title'] }}</h2>
                        <p class="text-gray-600 max-w-2xl mx-auto">{{ isset($textSettings['featured_products']['description']) ? $textSettings['featured_products']['description'] : $textSettings['featured_products_description'] }}</p>
            </div>
            
                    @if(isset($featuredProducts) && $featuredProducts->count() > 0)
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-2">
                            <a href="{{ url('/products/' . $product->slug) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-100">
                                @if($product->featured_image && $product->featured_image != '' && !str_ends_with(asset('storage/' . $product->featured_image), '/storage'))
                                    <img 
                                        src="{{ asset('storage/' . $product->featured_image) }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                        loading="lazy"
                                    >
                                @elseif($product->images && $product->images->isNotEmpty() && !empty($product->images->first()->image_path))
                                    <img 
                                        src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                        alt="{{ $product->name }}" 
                                        class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="flex flex-col items-center justify-center h-full bg-gradient-to-br from-primary to-accent p-4 text-center text-white">
                                        <h4 class="text-xl font-bold mb-2">{{ $product->name }}</h4>
                                        @if($product->category)
                                            <p class="text-sm mb-2">{{ $product->category->name }}</p>
                                        @endif
                                        <p class="font-bold">
                                            @if($product->discount_price && $product->discount_price < $product->price)
                                                <span class="line-through text-white/70 text-sm">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                                {{ $currencySymbol }}{{ number_format($product->discount_price, 2) }}
                                            @else
                                                {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                @if($product->category)
                                    <span class="absolute top-2 left-2 bg-primary/90 text-white text-xs px-2 py-1 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                @endif
                                
                                @if(isset($product->is_trending) && $product->is_trending)
                                    <span class="absolute top-2 right-2 bg-gradient-to-r from-red-600 to-orange-500 text-white text-xs px-3 py-1 rounded-full font-bold shadow-md animate-pulse flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        {{ is_rtl() ? 'رائج' : 'TRENDING' }}
                                    </span>
                                @endif
                                
                                <!-- Add Wishlist Button -->
                                @auth
                                    @php
                                        $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                                            ->where('product_id', $product->id)
                                            ->exists();
                                    @endphp
                                    <form action="{{ $inWishlist ? route('wishlist.remove', $product) : route('wishlist.add', $product) }}" method="POST" class="absolute bottom-2 right-2">
                                        @csrf
                                        @if($inWishlist)
                                            @method('DELETE')
                                        @endif
                                        <button type="submit" class="w-8 h-8 {{ $inWishlist ? 'bg-red-50 border-red-200' : 'bg-white/80 hover:bg-white' }} rounded-full flex items-center justify-center shadow-md transition-all duration-200">
                                            <svg class="w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-600' }}" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('login') }}" class="absolute bottom-2 right-2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-all duration-200" title="{{ __('common.sign_in_to_add_to_wishlist') }}">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </a>
                                @endauth
                            </a>
                            <div class="p-5">
                                <h3 class="text-2xl font-extrabold text-gray-800 mb-3 line-clamp-2 hover:line-clamp-none transition-all">{{ $product->name }}</h3>
                                <div class="flex flex-col space-y-3">
                                    <span class="text-base font-medium text-primary">
                                        @if($product->discount_price && $product->discount_price < $product->price)
                                            <span class="line-through text-gray-400 text-xs mr-2">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                            {{ $currencySymbol }}{{ number_format($product->discount_price, 2) }}
                                        @else
                                            {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                                        @endif
                                    </span>
                                    <div class="flex items-center justify-between mt-2">
                                        <a href="{{ url('/products/' . $product->slug) }}" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium px-3 py-1.5 rounded-md transition-colors duration-200 flex items-center text-sm">
                                                {{ is_rtl() ? 'عرض التفاصيل' : 'View details' }}
                                            <svg class="ml-1.5 w-4 h-4 rtl-flip" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        <button 
                                            class="add-to-cart-btn bg-accent hover:bg-accent-dark text-white rounded-full px-3 py-2 flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-md" 
                                            data-product-id="{{ $product->id }}"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ is_rtl() ? 'ml-1' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            {{ is_rtl() ? 'أضف للسلة' : 'Add to Cart' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-white p-8 rounded-lg shadow-md text-center">
                                                    <p>{{ is_rtl() ? 'لا توجد منتجات مميزة متاحة' : 'No featured products available' }}</p>
                    </div>
                    @endif
                    
                    <div class="mt-10 text-center">
                        <a href="{{ route('products.index') }}" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium px-8 py-3 rounded-md transition-colors duration-200">
                            {{ isset($textSettings['featured_products']['button_text']) ? $textSettings['featured_products']['button_text'] : (is_rtl() ? 'عرض جميع المنتجات' : 'View All Products') }}
                </a>
            </div>
        </div>
    </section>
            @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
        @elseif($section === 'new_arrivals')
    @if(isset($newArrivals) && $newArrivals->count() > 0)
    <!-- New Arrivals Section -->
            <section class="py-16 {{ $bgColor }}">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                              <div class="mb-2 inline-block">
                                  <span class="bg-accent/20 text-accent inline-block py-1 px-3 text-sm font-medium rounded">{{ isset($textSettings['new_arrivals']['tag']) ? $textSettings['new_arrivals']['tag'] : $textSettings['new_arrivals_tag'] }}</span>
                              </div>
                          <h2 class="text-4xl font-display text-primary mb-3">{{ isset($textSettings['new_arrivals']['title']) ? $textSettings['new_arrivals']['title'] : $textSettings['new_arrivals_title'] }}</h2>
                          <p class="text-gray-600 max-w-2xl mx-auto">{{ isset($textSettings['new_arrivals']['description']) ? $textSettings['new_arrivals']['description'] : $textSettings['new_arrivals_description'] }}</p>
              </div>
            
                    <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                @foreach($newArrivals as $product)
                                <div class="bg-white rounded-xl shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-2">
                                    <a href="{{ url('/products/' . $product->slug) }}" class="block relative aspect-[4/3] overflow-hidden bg-gray-100">
                                        @if($product->featured_image && $product->featured_image != '' && !str_ends_with(asset('storage/' . $product->featured_image), '/storage'))
                                            <img 
                                                src="{{ asset('storage/' . $product->featured_image) }}" 
                                                alt="{{ $product->name }}" 
                                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                                loading="lazy"
                                            >
                                        @elseif($product->images && $product->images->isNotEmpty() && !empty($product->images->first()->image_path))
                                            <img 
                                                src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                                alt="{{ $product->name }}" 
                                                class="w-full h-full object-cover transition-transform duration-500 hover:scale-110"
                                                loading="lazy"
                                            >
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full bg-gradient-to-br from-primary to-accent p-4 text-center text-white">
                                                <h4 class="text-xl font-bold mb-2">{{ $product->name }}</h4>
                                                @if($product->category)
                                                    <p class="text-sm mb-2">{{ $product->category->name }}</p>
                                                @endif
                                                <p class="font-bold">
                                                    @if($product->discount_price && $product->discount_price < $product->price)
                                                        <span class="line-through text-white/70 text-sm">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                                        {{ $currencySymbol }}{{ number_format($product->discount_price, 2) }}
                                                    @else
                                                        {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                                                    @endif
                                                </p>
                                                <span class="mt-2 bg-accent text-primary text-xs px-2 py-1 rounded-md font-bold">
                                                    {{ __('new') }}
                                                </span>
                                            </div>
                                        @endif

                                        @if($product->category)
                                            <span class="absolute top-2 left-2 bg-primary/90 text-white text-xs px-2 py-1 rounded-full">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                        
                                        @if(isset($product->is_trending) && $product->is_trending)
                                            <span class="absolute top-2 right-2 bg-gradient-to-r from-red-600 to-orange-500 text-white text-xs px-3 py-1 rounded-full font-bold shadow-md animate-pulse flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                                {{ is_rtl() ? 'رائج' : 'TRENDING' }}
                                            </span>
                                        @endif
                                        
                                        <!-- Add Wishlist Button -->
                                        @auth
                                            @php
                                                $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                                                    ->where('product_id', $product->id)
                                                    ->exists();
                                            @endphp
                                            <form action="{{ $inWishlist ? route('wishlist.remove', $product) : route('wishlist.add', $product) }}" method="POST" class="absolute bottom-2 right-2">
                                                @csrf
                                                @if($inWishlist)
                                                    @method('DELETE')
                                                @endif
                                                <button type="submit" class="w-8 h-8 {{ $inWishlist ? 'bg-red-50 border-red-200' : 'bg-white/80 hover:bg-white' }} rounded-full flex items-center justify-center shadow-md transition-all duration-200">
                                                    <svg class="w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-600' }}" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" class="absolute bottom-2 right-2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-all duration-200" title="{{ __('common.sign_in_to_add_to_wishlist') }}">
                                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </a>
                                        @endauth
                                    </a>
                                    <div class="p-5">
                                        <h3 class="text-2xl font-extrabold text-gray-800 mb-3 line-clamp-2 hover:line-clamp-none transition-all">{{ $product->name }}</h3>
                                        <div class="flex flex-col space-y-3">
                                            <span class="text-base font-medium text-primary">
                                                @if($product->discount_price && $product->discount_price < $product->price)
                                                    <span class="line-through text-gray-400 text-xs mr-2">{{ $currencySymbol }}{{ number_format($product->price, 2) }}</span>
                                                    {{ $currencySymbol }}{{ number_format($product->discount_price, 2) }}
                                                @else
                                                    {{ $currencySymbol }}{{ number_format($product->price, 2) }}
                                                @endif
                                            </span>
                                            <div class="flex items-center justify-between mt-2">
                                                <a href="{{ url('/products/' . $product->slug) }}" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium px-3 py-1.5 rounded-md transition-colors duration-200 flex items-center text-sm">
                                                {{ is_rtl() ? 'عرض التفاصيل' : 'View details' }}
                                                    <svg class="ml-1.5 w-4 h-4 rtl-flip" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                                <button 
                                                    class="add-to-cart-btn bg-accent hover:bg-accent-dark text-white rounded-full px-3 py-2 flex items-center justify-center transition-all duration-300 transform hover:scale-105 shadow-md" 
                                                    data-product-id="{{ $product->id }}"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ is_rtl() ? 'ml-1' : 'mr-1' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    {{ is_rtl() ? 'أضف للسلة' : 'Add to Cart' }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-10 text-center">
                            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium px-8 py-3 rounded-md transition-colors duration-200">
                                {{ isset($textSettings['new_arrivals']['button_text']) ? $textSettings['new_arrivals']['button_text'] : (is_rtl() ? 'استكشف المنتجات الجديدة' : 'Explore New Arrivals') }}
                            </a>
                        </div>
        </div>
    </section>
        @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
    @endif
        @elseif($section === 'our_story')
    <!-- Our Story Section -->
            <section class="py-20 {{ $bgColor }} relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute inset-0">
                    <div class="absolute top-0 right-0 w-1/3 h-full bg-primary-light/10 -skew-x-12 transform origin-top-right"></div>
                    <div class="absolute bottom-0 left-0 w-1/4 h-1/2 bg-accent/5 rounded-tr-full"></div>
                </div>
                
                <div class="container mx-auto px-4 relative z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <!-- Left content area -->
                        <div class="lg:col-span-7 lg:pr-12">
                            <div class="mb-6">
                                <span class="bg-accent/20 text-accent inline-block py-1 px-3 text-sm font-medium rounded-full">
                                    {{ is_rtl() ? ($ourStoryContent->subtitle_ar ?: $ourStoryContent->subtitle) : $ourStoryContent->subtitle }}
                                </span>
                            </div>
                            
                            <h2 class="text-4xl lg:text-5xl font-display text-primary mb-6 leading-tight">
                                {{ is_rtl() ? ($ourStoryContent->title_ar ?: $ourStoryContent->title) : $ourStoryContent->title }}
                            </h2>
                            
                            <div class="prose prose-lg max-w-none text-gray-600 mb-8">
                                <p class="leading-relaxed">{!! is_rtl() ? ($ourStoryContent->description_ar ?: $ourStoryContent->description) : $ourStoryContent->description !!}</p>
                            </div>
                            
                            <!-- Features/Benefits -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @php
                                                $feature1Icon = $ourStoryContent->feature1_icon ?? 'check-circle';
                                                switch($feature1Icon) {
                                                    case 'check-circle':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                                        break;
                                                    case 'star':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />';
                                                        break;
                                                    case 'heart':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />';
                                                        break;
                                                    case 'badge-check':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />';
                                                        break;
                                                    case 'sparkles':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />';
                                                        break;
                                                    case 'beaker':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />';
                                                        break;
                                                    case 'leaf':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />';
                                                        break;
                                                    case 'globe':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                                        break;
                                                    default:
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
                                                }
                                            @endphp
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ is_rtl() ? ($ourStoryContent->feature1_title_ar ?: $ourStoryContent->feature1_title) : $ourStoryContent->feature1_title }}</h3>
                                        <p class="text-gray-600">{{ is_rtl() ? ($ourStoryContent->feature1_text_ar ?: $ourStoryContent->feature1_text) : $ourStoryContent->feature1_text }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @php
                                                $feature2Icon = $ourStoryContent->feature2_icon ?? 'star';
                                                switch($feature2Icon) {
                                                    case 'check-circle':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                                        break;
                                                    case 'star':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />';
                                                        break;
                                                    case 'heart':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />';
                                                        break;
                                                    case 'badge-check':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />';
                                                        break;
                                                    case 'sparkles':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />';
                                                        break;
                                                    case 'beaker':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />';
                                                        break;
                                                    case 'leaf':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />';
                                                        break;
                                                    case 'globe':
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />';
                                                        break;
                                                    default:
                                                        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />';
                                                }
                                            @endphp
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">{{ is_rtl() ? ($ourStoryContent->feature2_title_ar ?: $ourStoryContent->feature2_title) : $ourStoryContent->feature2_title }}</h3>
                                        <p class="text-gray-600">{{ is_rtl() ? ($ourStoryContent->feature2_text_ar ?: $ourStoryContent->feature2_text) : $ourStoryContent->feature2_text }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ $ourStoryContent->button_url ?? '/about' }}" 
                                   class="inline-flex items-center px-6 py-3 bg-primary text-white rounded-full hover:bg-primary-dark transition-all duration-300 shadow-md hover:shadow-xl transform hover:translate-y-px">
                                    {{ is_rtl() ? ($ourStoryContent->button_text_ar ?: $ourStoryContent->button_text) : $ourStoryContent->button_text }}
                                    <svg class="w-5 h-5 ml-2 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                                
                                <a href="{{ $ourStoryContent->secondary_button_url ?? '/products' }}" class="inline-flex items-center px-6 py-3 border border-primary text-primary rounded-full hover:bg-primary-light/10 transition-all duration-300">
                                    {{ is_rtl() ? ($ourStoryContent->secondary_button_text_ar ?: $ourStoryContent->secondary_button_text) : $ourStoryContent->secondary_button_text }}
                                </a>
                            </div>
                        </div>
                        
                        <!-- Right image area with overlapping elements -->
                        <div class="lg:col-span-5 relative">
                            <!-- Main circular image container -->
                            <div class="relative z-10 mx-auto lg:mr-0 rounded-full overflow-hidden w-72 h-72 lg:w-80 lg:h-80 border-8 border-white/90 shadow-xl">
                                <!-- Company logo or image -->
                                <div class="w-full h-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center p-4">
                                    @php
                                        $ourStoryImage = $ourStoryContent->image ?? null;
                                        $logoImage = Settings::get('site_logo', 'logo.jpg');
                                        $imageToShow = $ourStoryImage ?: $logoImage;
                                    @endphp
                                    <img src="{{ $imageToShow ? (str_starts_with($imageToShow, '/storage/') ? $imageToShow : asset($imageToShow)) : asset('storage/' . Settings::get('site_logo', 'logo.jpg')) }}" 
                                         alt="{{ config('app.name') }}" 
                                         class="max-h-full max-w-full object-contain"
                                         loading="lazy">
                                </div>
                            </div>
                            
                            <!-- Decorative elements -->
                            <div class="absolute top-0 right-0 -mt-6 -mr-6 w-24 h-24 rounded-full bg-accent/30 z-0"></div>
                            <div class="absolute bottom-0 left-4 -mb-8 w-32 h-32 rounded-full bg-accent/20 z-0"></div>
                            
                            <!-- Year founded pill -->
                            <div class="absolute bottom-12 right-0 bg-white rounded-full px-4 py-2 shadow-lg z-20">
                                <span class="text-primary font-bold">{{ is_rtl() ? __('تأسست') : __('Est.') }} {{ $ourStoryContent->year_founded ?? '2023' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
        @elseif($section === 'categories')
            <!-- Shop by Category Section -->
            <section class="py-16 {{ $bgColor }}">
                <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-display text-primary mb-3">{{ isset($textSettings['categories']['title']) ? $textSettings['categories']['title'] : $textSettings['shop_by_category_title'] }}</h2>
                        <p class="text-gray-600 max-w-2xl mx-auto">{{ isset($textSettings['categories']['description']) ? $textSettings['categories']['description'] : $textSettings['shop_by_category_description'] }}</p>
                    </div>
                    
                    @if(isset($categories) && $categories->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($categories as $category)
                        <div class="flex">
                            <a href="{{ url('/products/category/' . $category->slug) }}" class="group w-full">
                                <div class="relative h-80 rounded-2xl overflow-hidden shadow-lg transition-all duration-300 transform group-hover:-translate-y-2 group-hover:shadow-xl w-full">
                                    <!-- Image container -->
                                    <div class="relative h-full w-full bg-gray-100">
                                        @if(isset($category->featured_product_image) && $category->featured_product_image)
                                            <img src="{{ asset('storage/' . $category->featured_product_image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-all duration-500 transform group-hover:scale-110" loading="lazy">
                                        @elseif(isset($category->image) && $category->image)
                                            <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-all duration-500 transform group-hover:scale-110" loading="lazy">
                                        @else
                                            <!-- Fallback to text-based image with category name -->
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary to-primary-dark p-4 text-center">
                                                <h3 class="text-2xl md:text-3xl lg:text-4xl font-display text-white">{{ $category->name ?? 'Category' }}</h3>
                                            </div>
                                        @endif
                                        
                                        <!-- Overlay with animated gradient on hover -->
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent opacity-80 group-hover:opacity-70 transition-opacity duration-300"></div>
                                    </div>
                                    
                                    <!-- Content overlay with enhanced hover effects -->
                                    <div class="absolute bottom-0 left-0 right-0 p-6 z-10 transform transition-transform duration-300">
                                        <div class="transform group-hover:-translate-y-2 transition-all duration-300">
                                        <h3 class="text-2xl font-display font-bold text-white mb-2 drop-shadow-lg">{{ $category->name }}</h3>
                                        @if(isset($category->description) && $category->description)
                                                <p class="text-white/90 mb-3 text-sm drop-shadow-md max-h-0 group-hover:max-h-20 overflow-hidden transition-all duration-300 opacity-0 group-hover:opacity-100">
                                                {{ is_rtl() ? 
                                                    (strlen($category->description) > 80 ? substr(__($category->description), 0, 80) . '...' : __($category->description)) :
                                                    (strlen($category->description) > 80 ? substr($category->description, 0, 80) . '...' : $category->description) }}
                                            </p>
                                        @endif
                                        </div>
                                        <div class="flex items-center mt-2">
                                            <span class="px-4 py-2 bg-accent text-white rounded-md text-sm font-medium inline-flex items-center transform translate-y-4 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 shadow-md">
                                                {{ is_rtl() ? 'استكشف الفئة' : 'Explore Category' }}
                                                <svg class="ml-1.5 w-4 h-4 rtl-flip transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                        <div class="bg-white p-8 rounded-lg shadow-md text-center">
                            <p>{{ is_rtl() ? 'لم يتم العثور على فئات' : 'No categories found' }}</p>
                        </div>
                    @endif
                </div>
            </section>
            @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
        @elseif($section === 'testimonials')
    <!-- Testimonials Section -->
            <section class="py-16 {{ $bgColor }}">
        <div class="container mx-auto px-4">
                    <div class="text-center mb-12">
                        <h2 class="text-4xl font-display text-primary mb-3">{{ isset($textSettings['testimonials']['title']) ? $textSettings['testimonials']['title'] : $textSettings['testimonials_title'] }}</h2>
                        <p class="text-gray-600 max-w-2xl mx-auto">{{ isset($textSettings['testimonials']['description']) ? $textSettings['testimonials']['description'] : $textSettings['testimonials_description'] }}</p>
            </div>
            
                        @if(isset($testimonials) && count($testimonials) > 0)
                    <!-- Testimonials Carousel -->
                    <div class="testimonials-carousel relative overflow-hidden">
                        <div class="testimonials-track flex transition-transform duration-500 ease-in-out">
                            @foreach($testimonials->take(6) as $index => $testimonial)
                                <div class="testimonial-slide px-3" data-index="{{ $index }}">
                                    <div class="bg-white p-6 rounded-xl shadow-lg h-full flex flex-col transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl">
                                    <div class="flex items-center mb-4">
                                            @if(isset($testimonial->avatar) && $testimonial->avatar)
                                        <div class="mr-4">
                                                    <img src="{{ asset('storage/' . $testimonial->avatar) }}" alt="{{ $testimonial->customer_name }}" class="w-14 h-14 rounded-full object-cover border-2 border-accent shadow-md">
                                                </div>
                                            @else
                                                <div class="mr-4">
                                                    <div class="w-14 h-14 bg-gradient-to-br from-accent to-accent-dark text-white rounded-full flex items-center justify-center text-xl font-bold shadow-md">
                                                {{ substr($testimonial->customer_name ?? 'User', 0, 1) }}
                                            </div>
                                        </div>
                                            @endif
                                        <div>
                                                <h4 class="font-bold text-lg">{{ $testimonial->customer_name }}</h4>
                                                <p class="text-accent text-sm font-medium">{{ $testimonial->customer_role ?: (is_rtl() ? 'عميل مخلص' : 'Verified Customer') }}</p>
                                                <div class="flex text-yellow-400 mt-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= ($testimonial->rating ?? 5))
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.54-.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                                                    @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.54-.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                                    @endif
                            @endfor
                        </div>
                    </div>
                        </div>
                                        <div class="flex-grow">
                                    <h3 class="text-xl font-semibold mb-2">{{ $testimonial->title }}</h3>
                                    <p class="text-gray-600">{{ mb_substr($testimonial->message, 0, 150) }}{{ strlen($testimonial->message) > 150 ? '...' : '' }}</p>
                                        </div>
                                        <div class="mt-4 pt-4 border-t border-gray-100">
                                            <p class="text-xs text-gray-400 italic">{{ $testimonial->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                        </div>
                        @endforeach
                        </div>
                        
                        <!-- Navigation controls -->
                        <button class="testimonial-prev absolute top-1/2 -left-2 sm:left-0 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-lg z-10 focus:outline-none hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <button class="testimonial-next absolute top-1/2 -right-2 sm:right-0 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-lg z-10 focus:outline-none hover:bg-gray-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        
                        <!-- Dots indicators -->
                        <div class="testimonial-dots flex justify-center mt-6 space-x-2">
                            @foreach($testimonials->take(6)->chunk(3) as $index => $chunk)
                                <button class="testimonial-dot w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-primary transition-colors duration-200 {{ $index === 0 ? 'active bg-primary' : '' }}" data-index="{{ $index }}"></button>
                            @endforeach
                        </div>
                    </div>
                        @else
                        <div class="text-center">
                                <p class="text-lg">{{ is_rtl() ? 'الشهادات قادمة قريبًا.' : 'Testimonials coming soon.' }}</p>
                            </div>
                        @endif
            
                    <div class="text-center mt-10">
                        <a href="/testimonials" class="inline-block py-3 px-6 bg-primary text-white rounded-lg hover:bg-primary-dark transition duration-300">{{ is_rtl() ? 'عرض جميع الآراء' : 'View All Testimonials' }}</a>
            </div>
        </div>
    </section>
            @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
        @else
            <!-- Fallback Section -->
            <section class="py-8 {{ $bgColor }}">
                <div class="container mx-auto px-4">
                    <p class="text-center text-gray-500">
                        @if(is_rtl())
                            القسم "{{ $section }}" معطل أو البيانات غير متوفرة.
                        @else
                            Section "{{ $section }}" is disabled or data is not available.
                        @endif
                    </p>
                </div>
            </section>
            @php $bgColor = $bgColor === 'bg-white' ? 'bg-gray-50' : 'bg-white'; @endphp
        @endif
    @endforeach
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize countdown timers
        initCountdownTimers();
        
        // Initialize testimonials carousel
        initTestimonialsCarousel();
        
        // Add to Cart functionality
        const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
        
        addToCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const quantity = 1; // Default quantity
                
                // Show loading state
                const originalText = this.innerHTML;
                this.classList.add('opacity-70', 'cursor-wait');
                this.innerHTML = `
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                `;
                
                // Get CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                // Make AJAX request with fetch
                fetch(`/cart/add/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ quantity: quantity })
                })
                .then(response => response.json())
                .then(data => {
                    // Reset button state
                    button.classList.remove('opacity-70', 'cursor-wait');
                    button.innerHTML = originalText;
                    
                    // Update cart count
                    const cartCount = document.querySelector('#cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        if (data.cart_count > 0) {
                            cartCount.classList.remove('hidden');
                        }
                    }
                    
                    // Show success message
                    const successMessage = document.createElement('div');
                    successMessage.className = 'fixed top-20 right-5 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
                    successMessage.innerHTML = `
                        <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>${data.message}</span>
                    `;
                    document.body.appendChild(successMessage);
                    
                    // Remove success message after 3 seconds
                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000);
                })
                .catch(error => {
                    console.error('Error adding to cart:', error);
                    // Reset button state
                    button.classList.remove('opacity-70', 'cursor-wait');
                    button.innerHTML = originalText;
                    
                    // Show error message
                    const errorMessage = document.createElement('div');
                    errorMessage.className = 'fixed top-20 right-5 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center';
                    errorMessage.innerHTML = `
                        <svg class="h-5 w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <span>Error adding product to cart. Please try again.</span>
                    `;
                    document.body.appendChild(errorMessage);
                    
                    // Remove error message after 3 seconds
                    setTimeout(() => {
                        errorMessage.remove();
                    }, 3000);
                });
            });
        });
    });
    
    function initCountdownTimers() {
        const countdownTimers = document.querySelectorAll('.countdown-timer');
        
        countdownTimers.forEach(timer => {
            const expiresTimestamp = parseInt(timer.getAttribute('data-expires')) * 1000;
            const daysElement = timer.querySelector('.days');
            const hoursElement = timer.querySelector('.hours');
            const minutesElement = timer.querySelector('.minutes');
            const secondsElement = timer.querySelector('.seconds');
            
            function updateTimer() {
                const now = new Date().getTime();
                const distance = expiresTimestamp - now;
                
                if (distance <= 0) {
                    clearInterval(interval);
                    timer.innerHTML = '<span class="text-red-600 font-bold">Offer Expired</span>';
                    return;
                }
                
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                if (daysElement) daysElement.textContent = days.toString().padStart(2, '0');
                if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, '0');
                if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, '0');
                if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, '0');
            }
            
            updateTimer(); // Initial call
            const interval = setInterval(updateTimer, 1000);
        });
    }
    
    // Initialize testimonials carousel
    function initTestimonialsCarousel() {
        const carousel = document.querySelector('.testimonials-carousel');
        if (!carousel) return;
        
        const track = carousel.querySelector('.testimonials-track');
        const slides = carousel.querySelectorAll('.testimonial-slide');
        const prevButton = carousel.querySelector('.testimonial-prev');
        const nextButton = carousel.querySelector('.testimonial-next');
        const dots = carousel.querySelectorAll('.testimonial-dot');
        
        let currentIndex = 0;
        let slideWidth = 0;
        let slidesPerView = 3; // Default for desktop
        
        // Determine slides per view based on screen width
        function updateSlidesPerView() {
            if (window.innerWidth < 768) {
                slidesPerView = 1; // Mobile
            } else if (window.innerWidth < 1024) {
                slidesPerView = 2; // Tablet
            } else {
                slidesPerView = 3; // Desktop
            }
            
            // Get container width and calculate slide width
            const containerWidth = carousel.clientWidth;
            slideWidth = containerWidth / slidesPerView;
            
            // Set width for each slide with padding considered
            slides.forEach(slide => {
                // Set fixed width on slides
                slide.style.width = `${slideWidth}px`;
                slide.style.minWidth = `${slideWidth}px`;
                slide.style.maxWidth = `${slideWidth}px`;
            });
            
            // Update track position for current index
            updateTrackPosition();
        }
        
        // Update track position based on current index
        function updateTrackPosition() {
            // Ensure we don't go beyond the available slides
            if (currentIndex > slides.length - slidesPerView) {
                currentIndex = slides.length - slidesPerView;
            }
            if (currentIndex < 0) {
                currentIndex = 0;
            }
            
            const offset = currentIndex * -slideWidth;
            track.style.transform = `translateX(${offset}px)`;
        }
        
        // Update active dot
        function updateActiveDot() {
            const dotCount = dots.length;
            if (dotCount === 0) return;
            
            const activeDotIndex = Math.floor(currentIndex / slidesPerView);
            dots.forEach((dot, index) => {
                if (index === activeDotIndex) {
                    dot.classList.add('active', 'bg-primary');
                    dot.classList.remove('bg-gray-300');
                } else {
                    dot.classList.remove('active', 'bg-primary');
                    dot.classList.add('bg-gray-300');
                }
            });
        }
        
        // Go to specific slide
        function goToSlide(index) {
            currentIndex = index;
            if (currentIndex < 0) {
                currentIndex = 0;
            } else if (currentIndex > slides.length - slidesPerView) {
                currentIndex = slides.length - slidesPerView;
            }
            
            updateTrackPosition();
            updateActiveDot();
        }
        
        // Event listeners
        if (prevButton) {
            prevButton.addEventListener('click', () => {
                goToSlide(currentIndex - 1);
            });
        }
        
        if (nextButton) {
            nextButton.addEventListener('click', () => {
                goToSlide(currentIndex + 1);
            });
        }
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                goToSlide(index * slidesPerView);
            });
        });
        
        // Auto-rotate every 5 seconds
        let autoRotateInterval = setInterval(() => {
            if (currentIndex >= slides.length - slidesPerView) {
                goToSlide(0);
            } else {
                goToSlide(currentIndex + 1);
            }
        }, 5000);
        
        // Pause auto-rotation when hovering over carousel
        carousel.addEventListener('mouseenter', () => {
            clearInterval(autoRotateInterval);
        });
        
        carousel.addEventListener('mouseleave', () => {
            autoRotateInterval = setInterval(() => {
                if (currentIndex >= slides.length - slidesPerView) {
                    goToSlide(0);
                } else {
                    goToSlide(currentIndex + 1);
                }
            }, 5000);
        });
        
        // Handle window resize
        window.addEventListener('resize', () => {
            // Recalculate on resize with a small debounce
            clearTimeout(window.resizeTimer);
            window.resizeTimer = setTimeout(() => {
                updateSlidesPerView();
            }, 250);
        });
        
        // Initialize on load
        updateSlidesPerView();
    }
</script>
@endpush

@push('styles')
<style>
    /* Pulse animation for countdown timer */
    @keyframes pulse-animation {
        0% { transform: scale(1); }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    
    .pulse-animation {
        animation: pulse-animation 1s ease-in-out;
    }
    
    /* Style for expired timers */
    .countdown-timer.expired {
        opacity: 0.7;
    }
    
    /* Testimonials Carousel Styles */
    .testimonials-carousel {
        position: relative;
        width: 100%;
        margin: 0 auto;
        padding: 0 20px;
    }
    
    .testimonials-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
    }
    
    .testimonial-slide {
        box-sizing: border-box;
        padding: 0 12px;
    }
    
    /* Active dot styling */
    .testimonial-dot.active {
        transform: scale(1.2);
    }
    
    /* Responsive navigation buttons */
    @media (min-width: 640px) {
        .testimonials-carousel {
            padding: 0 30px;
        }
    }
    
    /* Fix for rtl support */
    .rtl .testimonial-prev {
        left: auto;
        right: -2px;
    }
    
    .rtl .testimonial-next {
        right: auto;
        left: -2px;
    }
    
    @media (min-width: 640px) {
        .rtl .testimonial-prev {
            right: 0;
        }
        
        .rtl .testimonial-next {
            left: 0;
        }
    }
</style>
@endpush
