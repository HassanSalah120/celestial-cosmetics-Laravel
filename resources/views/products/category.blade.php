@extends('layouts.app')

@section('meta_tags')
    @php
        use Illuminate\Support\Str;
        use App\Helpers\SettingsHelper as Settings;

        // Get category info
        $categoryName = $category->name ?? (is_rtl() ? 'الفئة' : 'Category');
        $categoryDescription = $category->description ?? '';
        $categoryImage = $category->image ?? '';

        // Meta tags
        $metaTitle = $metaTitle ?? $categoryName . ' | ' . config('app.name');
        $metaDescription = $metaDescription ?? Str::limit($categoryDescription, 160);
        $metaKeywords = $metaKeywords ?? $categoryName . ', ' . config('app.name');

        // Currency symbol fallback
        $currencySymbol = $currencySymbol ?? '$';
    @endphp
    
    <x-seo :title="$metaTitle"
           :description="$metaDescription"
           :keywords="$metaKeywords"
           :ogImage="$categoryImage ? asset('storage/' . $categoryImage) : null"
           type="website" />
@endsection

@section('content')
<div class="bg-background min-h-screen">
    <!-- Hero Section -->
    <div class="relative">
        <!-- Hero Background -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-primary/80 to-primary/20 z-10"></div>
            @if($category->image)
                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-br from-primary to-accent/30"></div>
            @endif
        </div>
        
        <!-- Hero Content -->
        <div class="relative z-20 container mx-auto px-4 py-24 md:py-32">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-display font-bold text-white mb-6">
                    {{ $category->name }}
                </h1>
                @if($category->description)
                <p class="text-xl text-white/90 max-w-2xl mx-auto">
                    {{ $category->description }}
                </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container mx-auto px-4 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Products Grid - Full width -->
            <div class="w-full">
                <div class="mb-6 flex flex-col sm:flex-row justify-between items-center">
                    <h2 class="text-2xl font-display text-primary mb-4 sm:mb-0">
                        {{ $category->name }}
                    </h2>
                    
                    <div class="flex items-center gap-4">
                        <p class="text-sm text-gray-500">
                            @if(is_string($productsTotalString ?? null))
                                {{ is_rtl() ? 'عرض ' . $productsTotalString . ' منتج' : 'Showing ' . $productsTotalString . ' products' }}
                            @else
                                {{ is_rtl() ? 'عرض المنتجات' : 'Showing products' }}
                            @endif
                        </p>
                        
                        <form id="sort-form" method="GET" action="{{ url()->current() }}">
                            <select name="sort" id="sort" class="block py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-accent focus:border-accent sm:text-sm" onchange="this.form.submit()">
                                <option value="newest" {{ request('sort', 'newest') == 'newest' ? 'selected' : '' }}>{{ is_rtl() ? 'الأحدث' : 'Sort: Newest' }}</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>{{ is_rtl() ? 'السعر: من الأقل إلى الأعلى' : 'Sort: Price Low to High' }}</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>{{ is_rtl() ? 'السعر: من الأعلى إلى الأقل' : 'Sort: Price High to Low' }}</option>
                            </select>
                        </form>
                    </div>
                </div>
                
                <!-- Products Container -->
                @if($products->isEmpty())
                <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ is_rtl() ? 'لم يتم العثور على منتجات' : 'No products found' }}</h3>
                    <p class="text-gray-500 mb-6">
                        {{ is_rtl() ? 'لم نتمكن من العثور على أي منتجات في هذه الفئة حتى الآن.' : 'We couldn\'t find any products in this category yet.' }}
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark">
                        {{ is_rtl() ? 'تصفح جميع المنتجات' : 'Browse All Products' }}
                    </a>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 