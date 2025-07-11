@extends('layouts.app')

@section('meta_tags')
    <x-seo :title="$title ?? 'Categories'"
           :description="$description ?? 'Browse our product categories'"
           :keywords="$keywords ?? 'categories, products, shop'" 
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
                <h1 class="text-4xl sm:text-5xl font-display font-bold text-accent mb-4 drop-shadow-md" data-aos="fade-down" data-aos-delay="100">{{ is_rtl() ? 'تصفح الفئات' : 'Browse Categories' }}</h1>
                <p class="text-white text-opacity-90 text-lg sm:text-xl max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                    {{ is_rtl() ? 'اكتشف مجموعتنا المتنوعة من الفئات واعثر على المنتجات المثالية لك' : 'Discover our diverse range of categories and find the perfect products for you' }}
                </p>
                
                <div class="w-16 sm:w-20 md:w-24 h-1 bg-accent mx-auto mt-6 sm:mt-8 rounded-full" data-aos="zoom-in" data-aos-delay="300"></div>
            </div>
        </div>
    </div>

    <!-- Categories Section -->
    <div class="container mx-auto px-4 py-12">
        <div class="mb-8">
            <h2 class="text-2xl font-display text-primary mb-2">
                {{ is_rtl() ? 'جميع الفئات' : 'All Categories' }}
            </h2>
            <p class="text-gray-600">
                {{ is_rtl() ? 'اختر فئة لاستكشاف منتجاتها' : 'Choose a category to explore its products' }}
            </p>
        </div>
        
        @if($categories->isEmpty())
            <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'لم يتم العثور على فئات' : 'No categories found' }}</h3>
                <p class="text-gray-500 mb-6">
                    {{ is_rtl() ? 'لم نتمكن من العثور على أي فئات حتى الآن.' : 'We couldn\'t find any categories yet.' }}
                </p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark">
                    {{ is_rtl() ? 'تصفح جميع المنتجات' : 'Browse All Products' }}
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($categories as $category)
                    <a href="{{ route('products.category', $category->slug) }}" class="group">
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transition-all duration-300 transform group-hover:shadow-lg group-hover:-translate-y-1">
                            <div class="h-48 overflow-hidden">
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            </div>
                            <div class="p-5">
                                <h3 class="text-xl font-display font-medium text-primary mb-2 group-hover:text-accent transition-colors">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-gray-600 text-sm mb-3">
                                    {{ $category->products_count }} {{ is_rtl() ? 'منتج' : ($category->products_count == 1 ? 'product' : 'products') }}
                                </p>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-accent">
                                        {{ is_rtl() ? 'تصفح الفئة' : 'Browse Category' }}
                                    </span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-accent transform transition-transform duration-300 group-hover:translate-x-1 rtl:group-hover:-translate-x-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
    
    <!-- Featured Products Section -->
    <div class="container mx-auto px-4 py-12 border-t border-gray-200">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-display text-primary mb-2">
                    {{ is_rtl() ? 'منتجات مميزة' : 'Featured Products' }}
                </h2>
                <p class="text-gray-600">
                    {{ is_rtl() ? 'استكشف أفضل منتجاتنا' : 'Explore our best products' }}
                </p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center text-accent hover:text-accent-dark transition-colors">
                <span>{{ is_rtl() ? 'عرض الكل' : 'View all' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 rtl:transform rtl:rotate-180" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </a>
        </div>
        
        @php
            $featuredProducts = \App\Models\Product::where('is_featured', true)
                ->where('is_visible', true)
                ->with('category')
                ->take(4)
                ->get();
        @endphp
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</div>
@endsection 