@extends('layouts.app')

@section('meta_tags')
    @php
        use App\Helpers\TranslationHelper;
    @endphp
    <x-seo :title="$title"
           :description="$description"
           :keywords="$keywords"
           type="website" />
@endsection

@section('content')
<div class="bg-gradient-to-r from-primary-dark to-primary pt-16 pb-24 relative overflow-hidden">
    <!-- Animated stars background with improved animation -->
    <div class="absolute inset-0 opacity-40 overflow-hidden">
        <div class="stars-container h-full w-full">
            <!-- Mobile-optimized stars (fewer on small screens) -->
            <span class="star-icon text-xl sm:text-2xl animate-twinkle hidden sm:block" style="top: 15%; left: 10%; animation-delay: 0.5s;">✦</span>
            <span class="star-icon text-2xl sm:text-3xl animate-twinkle" style="top: 25%; left: 20%; animation-delay: 1.2s;">✧</span>
            <span class="star-icon text-lg sm:text-xl animate-twinkle hidden sm:block" style="top: 10%; left: 30%; animation-delay: 2.3s;">✦</span>
            <span class="star-icon text-xl sm:text-2xl animate-twinkle" style="top: 30%; left: 40%; animation-delay: 0.8s;">✧</span>
            <span class="star-icon text-lg sm:text-xl animate-twinkle" style="top: 20%; left: 50%; animation-delay: 1.6s;">✦</span>
            <span class="star-icon text-2xl sm:text-3xl animate-twinkle hidden md:block" style="top: 15%; left: 60%; animation-delay: 2.5s;">✧</span>
            <span class="star-icon text-xl sm:text-2xl animate-twinkle hidden sm:block" style="top: 25%; left: 70%; animation-delay: 0.7s;">✦</span>
            <span class="star-icon text-lg sm:text-xl animate-twinkle hidden md:block" style="top: 30%; left: 80%; animation-delay: 1.9s;">✧</span>
            <span class="star-icon text-2xl sm:text-3xl animate-twinkle hidden lg:block" style="top: 20%; left: 90%; animation-delay: 2.1s;">✦</span>
            <span class="moon-icon text-3xl sm:text-4xl animate-orbit" style="top: 70%; left: 15%; animation-delay: 1.1s;">☾</span>
            <span class="cosmic-icon text-2xl sm:text-3xl animate-spin-slow" style="top: 75%; left: 85%; animation-delay: 0.3s;">✯</span>
        </div>
    </div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-accent mb-4 drop-shadow-md" data-aos="fade-down" data-aos-delay="100">{{ is_rtl() ? 'عروض خاصة' : 'Special Offers' }}</h1>
            <p class="text-white text-opacity-90 text-lg sm:text-xl max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                {{ is_rtl() ? 'اكتشف العروض الحصرية والترويجات محدودة الوقت المخصصة لك.' : 'Discover exclusive deals and limited-time promotions specially curated for you.' }}
            </p>
            
            <div class="w-16 sm:w-20 md:w-24 h-1 bg-accent mx-auto mt-6 sm:mt-8 rounded-full" data-aos="zoom-in" data-aos-delay="300"></div>
        </div>
    </div>
</div>

<div class="py-16 bg-white">
    <div class="container mx-auto px-4">
        @if($offers->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                @foreach($offers as $offer)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden transform transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="flex flex-col md:flex-row">
                            @if($offer->image)
                                <div class="md:w-2/5 h-60 md:h-auto overflow-hidden">
                                    <img src="{{ asset('storage/' . str_replace('storage/', '', $offer->image)) }}" alt="{{ $offer->title }}" class="w-full h-full object-cover transform transition-transform duration-500 hover:scale-105">
                                </div>
                            @endif
                            <div class="p-6 md:w-3/5 flex flex-col justify-between bg-gradient-to-br from-white to-gray-50">
                                @if($offer->tag)
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
                                    @if($offer->subtitle)
                                        <p class="text-accent-dark font-medium mb-2">
                                            {{ $offer->subtitle }}
                                        </p>
                                    @endif
                                    @if($offer->description)
                                        <p class="text-gray-600 mb-4">
                                            {{ $offer->description }}
                                        </p>
                                    @endif
                                </div>
                                
                                @if($offer->original_price && $offer->discounted_price)
                                <div class="mt-auto">
                                    <div class="flex flex-wrap items-center gap-3 mb-3">
                                        <span class="text-gray-500 line-through text-lg">{{ $currencySymbol }}{{ number_format($offer->original_price, 2) }}</span>
                                        <span class="text-3xl font-bold text-red-600">{{ $currencySymbol }}{{ number_format($offer->discounted_price, 2) }}</span>
                                        
                                        @if($offer->discount_text)
                                            <span class="py-1 px-3 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full shadow-sm">
                                                {{ $offer->discount_text }}
                                            </span>
                                        @elseif(method_exists($offer, 'getDiscountPercentageAttribute'))
                                            <span class="py-1 px-3 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full shadow-sm">
                                                {{ $offer->discount_percentage }}% {{ is_rtl() ? 'خصم' : 'OFF' }}
                                            </span>
                                        @endif
                                    </div>
                                    
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
                                </div>
                                @endif
                                
                                <div class="flex flex-wrap gap-2 mt-4">
                                    @if($offer->button_url)
                                        <a href="{{ $offer->button_url }}" class="inline-flex items-center justify-center px-6 py-3 bg-accent text-white rounded-lg hover:bg-accent-dark transition-colors duration-300">
                                            {{ $offer->button_text ?? (is_rtl() ? 'عرض العرض' : 'View Offer') }}
                                            <svg class="ml-2 -mr-1 w-5 h-5 rtl-flip" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    @if($offer->product)
                                        <a href="{{ route('products.show', $offer->product->slug) }}" class="inline-flex items-center justify-center px-6 py-3 bg-white border border-accent text-accent rounded-lg hover:bg-accent-dark/10 transition-colors duration-300">
                                            {{ is_rtl() ? 'عرض التفاصيل' : 'View Details' }}
                                        </a>
                                    @endif
                                    
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
                                    @endif
                                    
                                    <!-- Stock Status -->
                                    @if($offer->stock !== null)
                                        <div class="mt-2 w-full">
                                            @if($offer->stock <= 0)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    {{ is_rtl() ? 'نفذت الكمية' : 'Out of Stock' }}
                                                </span>
                                            @elseif($offer->stock <= ($offer->low_stock_threshold ?? 5))
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    {{ is_rtl() ? 'كمية محدودة' : 'Low Stock' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    {{ is_rtl() ? 'متوفر' : 'In Stock' }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $offers->links() }}
            </div>
        @else
            <div class="py-16 text-center">
                <h3 class="text-2xl font-medium text-gray-900 mb-4">{{ is_rtl() ? 'لا توجد عروض نشطة في الوقت الحالي' : 'No active offers at the moment' }}</h3>
                <p class="text-gray-600 mb-8">{{ is_rtl() ? 'تحقق مرة أخرى قريبًا للحصول على عروض وترويجات مثيرة.' : 'Check back soon for exciting offers and promotions.' }}</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors duration-300">
                    {{ is_rtl() ? 'تصفح المنتجات' : 'Browse Products' }}
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const copyButtons = document.querySelectorAll('.copy-code');
        
        copyButtons.forEach(button => {
            button.addEventListener('click', function() {
                const code = this.getAttribute('data-code');
                navigator.clipboard.writeText(code).then(() => {
                    // Show success message
                    const originalText = this.innerHTML;
                    this.innerHTML = `<span class="text-green-600">{{ is_rtl() ? 'تم النسخ!' : 'Copied!' }}</span>`;
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 2000);
                });
            });
        });
        
        // Initialize countdown timers
        initCountdownTimers();
        
        // Initialize AOS with custom settings if not already initialized
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });
        }
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
                    // Expired
                    daysElement.textContent = '00';
                    hoursElement.textContent = '00';
                    minutesElement.textContent = '00';
                    secondsElement.textContent = '00';
                    timer.classList.add('expired');
                    return;
                }
                
                // Time calculations
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Add leading zeros
                daysElement.textContent = days < 10 ? '0' + days : days;
                hoursElement.textContent = hours < 10 ? '0' + hours : hours;
                minutesElement.textContent = minutes < 10 ? '0' + minutes : minutes;
                secondsElement.textContent = seconds < 10 ? '0' + seconds : seconds;
                
                // Animate when seconds change
                if (seconds === 0) {
                    secondsElement.classList.add('pulse-animation');
                    setTimeout(() => {
                        secondsElement.classList.remove('pulse-animation');
                    }, 1000);
                }
            }
            
            // Initial update
            updateTimer();
            
            // Update every second
            setInterval(updateTimer, 1000);
        });
    }
</script>
@endpush

@push('styles')
<style>
    /* Enhanced cosmic animations */
    .stars-container {
        position: relative;
    }
    
    .star-icon, .moon-icon, .cosmic-icon {
        position: absolute;
        color: rgb(var(--color-accent) / 0.6);
        opacity: 0.7;
        animation-duration: 3s;
        animation-iteration-count: infinite;
    }
    
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
</style>
@endpush
@endsection 