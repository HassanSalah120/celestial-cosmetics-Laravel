@extends('layouts.app')

    @php
        use App\Helpers\TranslationHelper;
use App\Helpers\SettingsHelper as Settings;

$productTitle = Settings::get('products_meta_title') ?? (is_rtl() ? 'منتجاتنا' : 'Our Products') . ' | ' . config('app.name');
$productDescription = is_rtl() ? 'اكتشف مجموعتنا من منتجات الجمال المستوحاة من السماء والمصممة لإظهار إشراقتك الداخلية.' : 'Discover our range of celestial-inspired beauty products designed to bring out your inner radiance.';
$productKeywords = Settings::get('products_meta_keywords') ?? (is_rtl() ? 'مستحضرات التجميل، منتجات الجمال، العناية بالبشرة' : 'cosmetics, beauty products, skincare');
@endphp

@section('meta_tags')
    <x-seo :title="$productTitle"
           :description="$productDescription"
           :keywords="$productKeywords"
           :ogImage="Settings::get('products_og_image')"
           type="website" />
@endsection

@section('content')
<div class="bg-background min-h-screen">
    <!-- Hero Section -->
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
                <h1 class="text-4xl sm:text-5xl font-display font-bold text-accent mb-4 drop-shadow-md" data-aos="fade-down" data-aos-delay="100">{{ is_rtl() ? 'منتجاتنا' : 'Our Products' }}</h1>
                <p class="text-white text-opacity-90 text-lg sm:text-xl max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    {{ is_rtl() ? 'اكتشف مجموعتنا من منتجات الجمال المستوحاة من السماء والمصممة لإظهار إشراقتك الداخلية.' : 'Discover our range of celestial-inspired beauty products designed to bring out your inner radiance.' }}
            </p>
                
                <div class="w-16 sm:w-20 md:w-24 h-1 bg-accent mx-auto mt-6 sm:mt-8 rounded-full" data-aos="zoom-in" data-aos-delay="300"></div>
            </div>
    </div>
</div>

    <!-- Products Section -->
    <div class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-2xl shadow-md p-6 sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-medium text-gray-900">{{ is_rtl() ? 'الفلاتر' : 'Filters' }}</h2>
                        <button id="mobile-filter-toggle" class="lg:hidden text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        </button>
                    </div>
                    
                    <div id="filter-content" class="space-y-6">
                        <!-- Categories Filter -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">{{ is_rtl() ? 'الفئات' : 'Categories' }}</h3>
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <input id="category-all" name="category" type="radio" value="" 
                                           class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded"
                                        {{ !request('category') ? 'checked' : '' }}>
                                    <label for="category-all" class="ml-3 text-sm text-gray-600">
                                        {{ is_rtl() ? 'جميع الفئات' : 'All Categories' }}
                                    </label>
                                </div>
                                
                                @foreach($categories as $category)
                                <div class="flex items-center">
                                    <input id="category-{{ $category->id }}" name="category" type="radio" value="{{ $category->slug }}" 
                                           class="h-4 w-4 text-accent focus:ring-accent border-gray-300 rounded"
                                        {{ request('category') == $category->slug ? 'checked' : '' }}>
                                    <label for="category-{{ $category->id }}" class="ml-3 text-sm text-gray-600">
                                        {{ $category->name }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">{{ is_rtl() ? 'نطاق السعر' : 'Price Range' }}</h3>
                            <div class="flex items-center space-x-4">
                                <div>
                                    <label for="min-price" class="sr-only">{{ is_rtl() ? 'الحد الأدنى' : 'Min' }}</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="min-price" id="min-price" min="0" 
                                               class="block w-full pl-7 pr-3 py-2 border-gray-300 rounded-md focus:ring-accent focus:border-accent sm:text-sm"
                                               placeholder="{{ is_rtl() ? 'الحد الأدنى' : 'Min' }}" value="{{ request('min_price') }}">
                                    </div>
                                </div>
                                
                                <div>
                                    <label for="max-price" class="sr-only">{{ is_rtl() ? 'الحد الأقصى' : 'Max' }}</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input type="number" name="max-price" id="max-price" min="0" 
                                               class="block w-full pl-7 pr-3 py-2 border-gray-300 rounded-md focus:ring-accent focus:border-accent sm:text-sm"
                                               placeholder="{{ is_rtl() ? 'الحد الأقصى' : 'Max' }}" value="{{ request('max_price') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sort By Filter -->
                        <div>
                            <h3 class="text-sm font-medium text-gray-900 mb-3">{{ is_rtl() ? 'الترتيب حسب' : 'Sort By' }}</h3>
                            <select id="sort-by" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent sm:text-sm">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>{{ is_rtl() ? 'الأحدث' : 'Newest' }}</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ is_rtl() ? 'السعر: من الأقل إلى الأعلى' : 'Price: Low to High' }}</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ is_rtl() ? 'السعر: من الأعلى إلى الأقل' : 'Price: High to Low' }}</option>
                                <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>{{ is_rtl() ? 'الشعبية' : 'Popularity' }}</option>
                            </select>
                        </div>
                        
                        <!-- Apply Filters Button -->
                        <div class="pt-2">
                            <button id="apply-filters" class="w-full bg-accent hover:bg-accent-dark text-white py-2 px-4 rounded-md font-medium transition-colors duration-300">
                                {{ is_rtl() ? 'تطبيق الفلاتر' : 'Apply Filters' }}
                            </button>
                            
                            <button id="clear-filters" class="w-full mt-2 bg-gray-100 hover:bg-gray-200 text-gray-800 py-2 px-4 rounded-md font-medium transition-colors duration-300">
                                {{ is_rtl() ? 'مسح الفلاتر' : 'Clear Filters' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="lg:w-3/4">
                <!-- Header -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center">
                        <h2 class="text-2xl font-display text-primary mb-4 sm:mb-0">
                            {{ is_rtl() ? 'جميع المنتجات' : 'All Products' }}
                        </h2>
                        
                        <p class="text-sm text-gray-500">
                            {{ is_rtl() ? 'عرض ' . ($products->firstItem() ?? 0) . ' - ' . ($products->lastItem() ?? 0) . ' من ' . $products->total() . ' منتج' : 'Showing ' . ($products->firstItem() ?? 0) . ' - ' . ($products->lastItem() ?? 0) . ' of ' . $products->total() . ' products' }}
                        </p>
                    </div>
                    
                    <!-- Active Filters -->
                    @if(request('category') || request('min_price') || request('max_price'))
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-500">{{ is_rtl() ? 'الفلاتر النشطة:' : 'Active filters:' }}</span>
                        
                        @if(request('category'))
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent text-white">
                            <span id="category-filter">
                                {{ is_rtl() ? 'الفئة:' : 'Category:' }} <span id="category-name">{{ request('category') ? collect($categories)->firstWhere('slug', request('category'))->name : '' }}</span>
                            </span>
                            <button type="button" class="remove-filter ml-1.5 inline-flex text-white hover:text-white" data-filter="category">
                                <span class="sr-only">Remove</span>
                                <svg class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        @endif
                        
                        @if(request('min_price') || request('max_price'))
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent text-white">
                            <span id="price-filter">
                                {{ is_rtl() ? 'السعر:' : 'Price:' }}
                                {{ is_rtl() ? 
                                    (request('min_price') && request('max_price') ? request('min_price') . ' - ' . request('max_price') . ' ' . Settings::get('currency_symbol', '$') : 
                                    (request('min_price') ? 'من ' . request('min_price') . ' ' . Settings::get('currency_symbol', '$') : 
                                    'حتى ' . request('max_price') . ' ' . Settings::get('currency_symbol', '$'))) : 
                                    (request('min_price') && request('max_price') ? Settings::get('currency_symbol', '$') . request('min_price') . ' - ' . Settings::get('currency_symbol', '$') . request('max_price') : 
                                    (request('min_price') ? 'From ' . Settings::get('currency_symbol', '$') . request('min_price') : 
                                    'Up to ' . Settings::get('currency_symbol', '$') . request('max_price'))) 
                                }}
                            </span>
                            <button type="button" class="remove-filter ml-1.5 inline-flex text-white hover:text-white" data-filter="price">
                                <span class="sr-only">Remove</span>
                                <svg class="h-2 w-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
                
                <!-- Products Grid -->
                @if(count($products) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'لم يتم العثور على منتجات' : 'No products found' }}</h3>
                    <p class="text-gray-500 mb-6">
                        {{ is_rtl() ? 'لم نتمكن من العثور على أي منتجات تطابق معاييرك.' : 'We couldn\'t find any products matching your criteria.' }}
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark">
                        {{ is_rtl() ? 'إعادة ضبط الفلاتر' : 'Reset Filters' }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Mobile filter toggle
    const mobileFilterToggle = document.getElementById('mobile-filter-toggle');
    const filterContent = document.getElementById('filter-content');
    
    if (mobileFilterToggle && filterContent) {
        mobileFilterToggle.addEventListener('click', function() {
            filterContent.classList.toggle('hidden');
            filterContent.classList.toggle('lg:block');
        });
    }
    
    // Apply filters
    const applyFiltersBtn = document.getElementById('apply-filters');
    const categoryInputs = document.querySelectorAll('input[name="category"]');
    const minPriceInput = document.getElementById('min-price');
    const maxPriceInput = document.getElementById('max-price');
    const sortBySelect = document.getElementById('sort-by');
    
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyFilters();
        });
    }
    
    // Clear filters
    const clearFiltersBtn = document.getElementById('clear-filters');
    
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            window.location.href = '{{ route('products.index') }}';
        });
    }
    
    // Remove individual filter
    const removeFilterBtns = document.querySelectorAll('.remove-filter');
    
    if (removeFilterBtns.length > 0) {
        removeFilterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filterType = this.dataset.filter;
                
                if (filterType === 'category') {
                    // Reset category filter
                    categoryInputs.forEach(input => {
                        if (input.value === '') {
                            input.checked = true;
                        } else {
                            input.checked = false;
                        }
                    });
                } else if (filterType === 'price') {
                    // Reset price filter
                    minPriceInput.value = '';
                    maxPriceInput.value = '';
                }
                
                applyFilters();
            });
        });
    }
    
    // Function to apply all filters
    function applyFilters() {
        let url = new URL(window.location.href);
        let params = new URLSearchParams(url.search);
        
        // Category filter
        let selectedCategory = '';
        categoryInputs.forEach(input => {
            if (input.checked) {
                selectedCategory = input.value;
            }
        });
        
        if (selectedCategory) {
            params.set('category', selectedCategory);
        } else {
            params.delete('category');
        }
        
        // Price filter
        if (minPriceInput.value) {
            params.set('min_price', minPriceInput.value);
        } else {
            params.delete('min_price');
        }
        
        if (maxPriceInput.value) {
            params.set('max_price', maxPriceInput.value);
        } else {
            params.delete('max_price');
        }
        
        // Sort filter
        if (sortBySelect.value) {
            params.set('sort', sortBySelect.value);
        } else {
            params.delete('sort');
        }
        
        // Update URL and reload page
        window.location.href = `{{ route('products.index') }}?${params.toString()}`;
    }
    
    // Handle price filter display
    const priceFilter = document.getElementById('price-filter');
    
    if (priceFilter) {
        const minPrice = '{{ request('min_price') }}';
        const maxPrice = '{{ request('max_price') }}';
        
        if (minPrice && maxPrice) {
            const priceRangeDisplay = `{{ is_rtl() ? 
                '${minPrice} - ${maxPrice} ' . Settings::get('currency_symbol', '$') : 
                Settings::get('currency_symbol', '$') . '${minPrice} - ' . Settings::get('currency_symbol', '$') . '${maxPrice}' }}`;
            
            const priceSpan = document.getElementById('price-filter');
            priceSpan.innerHTML = '{{ is_rtl() ? 'السعر:' : 'Price:' }} ' + priceRangeDisplay;
        } else if (minPrice) {
            const priceRangeDisplay = `{{ is_rtl() ? 
                'من ${minPrice} ' . Settings::get('currency_symbol', '$') : 
                'From ' . Settings::get('currency_symbol', '$') . '${minPrice}' }}`;
            
            const priceSpan = document.getElementById('price-filter');
            priceSpan.innerHTML = '{{ is_rtl() ? 'السعر:' : 'Price:' }} ' + priceRangeDisplay;
        } else if (maxPrice) {
            const priceRangeDisplay = `{{ is_rtl() ? 
                'حتى ${maxPrice} ' . Settings::get('currency_symbol', '$') : 
                'Up to ' . Settings::get('currency_symbol', '$') . '${maxPrice}' }}`;
            
            const priceSpan = document.getElementById('price-filter');
            priceSpan.innerHTML = '{{ is_rtl() ? 'السعر:' : 'Price:' }} ' + priceRangeDisplay;
        }
        }
    });
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
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
</style>
@endpush
@endsection