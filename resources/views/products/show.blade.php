@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
use Illuminate\Support\Str;
@endphp

@section('meta_tags')
    <x-seo :title="$product->meta_title ?? $product->name . ' | ' . config('app.name')"
           :description="$product->meta_description ?? Str::limit(strip_tags($product->description), 160)"
           :keywords="$product->meta_keywords ?? (isset($product->category) ? $product->category->name . ', ' : '') . $product->name"
           :ogImage="$product->featured_image ? asset('storage/' . $product->featured_image) : null"
           type="product" />
@endsection

@section('content')
<style>
    /* Remove number input spinners in all browsers */
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>

<div class="bg-background min-h-screen py-16">
    <div class="container mx-auto px-4">
        <!-- Breadcrumbs -->
        <nav class="mb-6 text-sm" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li>
                    <a href="{{ route('home') }}" class="text-gray-500 hover:text-accent transition-colors">{{ is_rtl() ? 'الرئيسية' : 'Home' }}</a>
                </li>
                <li class="flex items-center">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('products.index') }}" class="ml-2 text-gray-500 hover:text-accent transition-colors">{{ is_rtl() ? 'المنتجات' : 'Products' }}</a>
                </li>
                <li class="flex items-center">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <a href="{{ route('products.category', $product->category->slug) }}" class="ml-2 text-gray-500 hover:text-accent transition-colors">{{ $product->category->name }}</a>
                </li>
                <li class="flex items-center">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="ml-2 text-gray-700 font-medium">{{ $product->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Product Images -->
            <div class="w-full lg:w-1/2">
                <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                    <div id="main-image" class="h-96 md:h-[500px] w-full bg-white relative">
                        @if($product->featured_image)
                        <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-6">
                        @if($product->is_new)
                        <div class="absolute top-4 left-4 bg-accent text-white text-xs uppercase font-bold px-3 py-1 rounded-full shadow-md">{{ is_rtl() ? 'جديد' : 'New' }}</div>
                        @endif
                        @if($product->discount_percentage > 0)
                        <div class="absolute top-4 right-4 bg-red-500 text-white text-xs uppercase font-bold px-3 py-1 rounded-full shadow-md">-{{ $product->discount_percentage }}%</div>
                        @endif
                        @else
                        <div class="flex h-full w-full items-center justify-center bg-gray-100">
                            <svg class="h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        @endif
                    </div>

                    @if(count($product->images) > 0)
                    <div class="px-6 pb-6">
                        <div class="grid grid-cols-5 gap-2 mt-4">
                            @if($product->featured_image)
                            <div class="thumbnail-item cursor-pointer border-2 border-accent rounded-lg overflow-hidden">
                                <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}" class="h-16 w-full object-cover" data-image="{{ asset('storage/' . $product->featured_image) }}">
                            </div>
                            @endif
                            @foreach($product->images as $image)
                            <div class="thumbnail-item cursor-pointer border-2 border-transparent hover:border-accent rounded-lg overflow-hidden transition-colors">
                                <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $product->name }}" class="h-16 w-full object-cover" data-image="{{ asset('storage/' . $image->image) }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Product Info -->
            <div class="w-full lg:w-1/2">
                <div class="space-y-6">
                    <!-- Product Info -->
                    <div class="bg-white rounded-2xl shadow-md p-8">
                        <!-- Category & Rating -->
                        <div class="flex items-center justify-between mb-3">
                            <a href="{{ route('products.category', $product->category->slug) }}" class="text-sm text-accent hover:text-accent-dark font-medium transition-colors">
                                {{ $product->category->name }}
                            </a>
                        </div>

                        <!-- Product name -->
                        <h1 class="text-3xl md:text-4xl font-display text-primary mb-3">{{ $product->name }}</h1>

                        <!-- Price -->
                        <div class="flex items-center mb-6">
                            @if($product->discount_percentage > 0)
                            <span class="text-2xl font-bold text-accent mr-3">{{ \App\Helpers\SettingsHelper::formatPrice($product->final_price) }}</span>
                            <span class="text-lg text-gray-500 line-through">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</span>
                            <span class="ml-3 px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-md">{{ is_rtl() ? 'خصم' : 'SAVE' }} {{ $product->discount_percentage }}%</span>
                            @else
                            <span class="text-2xl font-bold text-accent">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</span>
                            @endif
                        </div>

                        <!-- Description -->
                        <div class="prose max-w-none text-gray-700 mb-8">
                            {!! $product->description !!}
                        </div>

                        <!-- Add to Cart -->
                        <div class="flex flex-col md:flex-row gap-4">
                            <div class="flex items-center border border-gray-300 rounded-lg w-32 h-12 shadow-sm overflow-hidden">
                                <button id="quantity-decrease" class="quantity-btn w-10 h-full flex items-center justify-center bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-accent transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" id="quantity" name="quantity" min="1" value="1" class="w-full h-full py-2 px-0 text-center border-x border-gray-300 focus:outline-none focus:ring-0 focus:border-accent text-gray-700" style="appearance: none; -moz-appearance: textfield; -webkit-appearance: none; -webkit-inner-spin-button: none; -webkit-outer-spin-button: none; margin: 0;">
                                <button id="quantity-increase" class="quantity-btn w-10 h-full flex items-center justify-center bg-gray-50 text-gray-600 hover:bg-gray-100 hover:text-accent transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="quantity" id="cart-quantity" value="1">
                                <button type="submit" id="add-to-cart-btn" class="w-full bg-accent hover:bg-accent-dark text-white py-3 px-6 rounded-lg font-semibold transition-colors duration-300 flex items-center justify-center add-to-cart" data-action="add-to-cart">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ is_rtl() ? 'أضف إلى السلة' : 'Add to Cart' }}
                                </button>
                            </form>
                            
                            @auth
                                @php
                                    $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                                        ->where('product_id', $product->id)
                                        ->exists();
                                @endphp
                                <form action="{{ $inWishlist ? route('wishlist.remove', $product) : route('wishlist.add', $product) }}" method="POST">
                                    @csrf
                                    @if($inWishlist)
                                        @method('DELETE')
                                    @endif
                                    <button type="submit" class="h-12 w-12 flex items-center justify-center rounded-lg border {{ $inWishlist ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }} hover:bg-gray-100 transition-colors">
                                        <svg class="w-6 h-6 {{ $inWishlist ? 'text-red-500' : 'text-gray-400' }}" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="h-12 w-12 flex items-center justify-center rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors" title="{{ __('common.sign_in_to_add_to_wishlist', 'Sign in to add to wishlist') }}">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </a>
                            @endauth
                        </div>

                        <!-- Stock Status & SKU -->
                        <div class="flex flex-wrap items-center gap-x-8 gap-y-2 mt-6 pt-6 border-t border-gray-200 text-sm">
                            <div class="flex items-center">
                                <span class="text-gray-600 mr-2">{{ is_rtl() ? 'التوفر:' : 'Availability:' }}</span>
                                @if($product->in_stock)
                                    @if(\App\Helpers\SettingsHelper::get('show_stock_to_client', '1') == '1')
                                    <span class="text-green-600 font-medium">{{ is_rtl() ? 'متوفر' : 'In Stock' }} ({{ $product->stock }} {{ is_rtl() ? 'متاح' : 'available' }})</span>
                                    @else
                                    <span class="text-green-600 font-medium">{{ is_rtl() ? 'متوفر' : 'In Stock' }}</span>
                                    @endif
                                @else
                                <span class="text-red-600 font-medium">{{ is_rtl() ? 'غير متوفر' : 'Out of Stock' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info Tabs -->
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="border-b border-gray-200">
                            <div class="tabs flex">
                                <button class="tab-btn active px-6 py-4 text-sm font-medium text-accent border-b-2 border-accent focus:outline-none" data-tab="details">{{ is_rtl() ? 'التفاصيل' : 'Details' }}</button>
                                <button class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="ingredients">{{ is_rtl() ? 'المكونات' : 'Ingredients' }}</button>
                                <button class="tab-btn px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none" data-tab="how-to-use">{{ is_rtl() ? 'كيفية الاستخدام' : 'How to Use' }}</button>
                            </div>
                        </div>
                        <div class="p-6">
                            <div id="details-content" class="tab-content block">
                                <div class="prose max-w-none">
                                    <h3 class="text-lg font-medium text-primary mb-3">{{ is_rtl() ? 'تفاصيل المنتج' : 'Product Details' }}</h3>
                                    <p>{!! $product->details !!}</p>
                                </div>
                            </div>
                            <div id="ingredients-content" class="tab-content hidden">
                                <div class="prose max-w-none">
                                    <h3 class="text-lg font-medium text-primary mb-3">{{ is_rtl() ? 'المكونات' : 'Ingredients' }}</h3>
                                    <p>{!! $product->ingredients !!}</p>
                                </div>
                            </div>
                            <div id="how-to-use-content" class="tab-content hidden">
                                <div class="prose max-w-none">
                                    <h3 class="text-lg font-medium text-primary mb-3">{{ is_rtl() ? 'كيفية الاستخدام' : 'How to Use' }}</h3>
                                    <p>{!! $product->how_to_use !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if(count($relatedProducts) > 0)
        <div class="mt-16">
            <h2 class="text-2xl font-display text-primary mb-8 text-center">{{ is_rtl() ? 'منتجات ذات صلة' : 'Related Products' }}</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                <x-product-card :product="$relatedProduct" />
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity controls
        const quantityInput = document.getElementById('quantity');
        const cartQuantityInput = document.getElementById('cart-quantity');
        const decreaseBtn = document.getElementById('quantity-decrease');
        const increaseBtn = document.getElementById('quantity-increase');
        
        // Sync the two quantity inputs
        function syncQuantity() {
            cartQuantityInput.value = quantityInput.value;
        }
        
        // Update quantity
        function updateQuantity(newValue) {
            const value = parseInt(newValue);
            if (isNaN(value) || value < 1) {
                quantityInput.value = 1;
            } else {
                quantityInput.value = value;
            }
            syncQuantity();
        }
        
        // Event listeners for quantity controls
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                updateQuantity(currentValue - 1);
            }
        });
        
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(quantityInput.value);
            updateQuantity(currentValue + 1);
        });
        
        quantityInput.addEventListener('change', function() {
            updateQuantity(this.value);
        });
        
        // Tabs functionality
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabId = this.dataset.tab;
                
                // Remove active class from all buttons and hide all contents
                tabBtns.forEach(btn => btn.classList.remove('active', 'text-accent', 'border-accent'));
                tabBtns.forEach(btn => btn.classList.add('text-gray-500'));
                tabContents.forEach(content => content.classList.add('hidden'));
                
                // Add active class to current button and show current content
                this.classList.add('active', 'text-accent', 'border-accent');
                this.classList.remove('text-gray-500');
                document.getElementById(`${tabId}-content`).classList.remove('hidden');
            });
        });
        
        // Product image gallery
        const mainImage = document.getElementById('main-image').querySelector('img');
        const thumbnails = document.querySelectorAll('.thumbnail-item img');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Update main image
                mainImage.src = this.dataset.image;
                
                // Update active thumbnail
                thumbnails.forEach(thumb => {
                    thumb.parentElement.classList.remove('border-accent');
                    thumb.parentElement.classList.add('border-transparent');
                });
                this.parentElement.classList.remove('border-transparent');
                this.parentElement.classList.add('border-accent');
            });
        });
    });
</script>
@endpush
@endsection 