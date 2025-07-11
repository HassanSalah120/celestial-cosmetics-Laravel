@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
use App\Helpers\SettingsHelper as Settings;
@endphp

@section('meta_tags')
    <x-seo :title="Settings::get('wishlist_meta_title') ?? __('common.wishlist') . ' | ' . config('app.name')"
           :description="Settings::get('wishlist_meta_description') ?? 'Save your favorite products to your wishlist for easy access later.'"
           :keywords="Settings::get('wishlist_meta_keywords') ?? 'wishlist, favorites, saved items'"
           :ogImage="Settings::get('wishlist_og_image')"
           type="website" />
@endsection

@section('content')
<!-- Hero Section -->
<div class="bg-gradient-to-r from-primary-dark to-primary pt-16 pb-24 relative overflow-hidden">
    <!-- Animated stars background -->
    <div class="absolute inset-0 opacity-40 overflow-hidden">
        <div class="stars-container h-full w-full">
            <span class="star-icon text-xl sm:text-2xl animate-twinkle hidden sm:block" style="top: 15%; left: 10%; animation-delay: 0.5s;">✦</span>
            <span class="star-icon text-2xl sm:text-3xl animate-twinkle" style="top: 25%; left: 20%; animation-delay: 1.2s;">✧</span>
            <span class="star-icon text-lg sm:text-xl animate-twinkle hidden sm:block" style="top: 10%; left: 30%; animation-delay: 2.3s;">✦</span>
            <span class="star-icon text-xl sm:text-2xl animate-twinkle" style="top: 30%; left: 40%; animation-delay: 0.8s;">✧</span>
            <span class="star-icon text-lg sm:text-xl animate-twinkle" style="top: 20%; left: 50%; animation-delay: 1.6s;">✦</span>
            <span class="star-icon text-2xl sm:text-3xl animate-twinkle hidden md:block" style="top: 15%; left: 60%; animation-delay: 2.5s;">✧</span>
        </div>
    </div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="text-center">
            <h1 class="text-4xl sm:text-5xl font-display font-bold text-accent mb-4 drop-shadow-md" data-aos="fade-down" data-aos-delay="100">{{ __('common.wishlist') }}</h1>
            <p class="text-white text-opacity-90 text-lg sm:text-xl max-w-3xl mx-auto" data-aos="fade-up" data-aos-delay="200">
                {{ is_rtl() ? 'قائمة المنتجات المفضلة لديك للرجوع إليها بسهولة في وقت لاحق.' : 'Your favorite products saved for easy access later.' }}
            </p>
            
            <div class="w-16 sm:w-20 md:w-24 h-1 bg-accent mx-auto mt-6 sm:mt-8 rounded-full" data-aos="zoom-in" data-aos-delay="300"></div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        @if(session('success'))
        <div class="bg-green-100 border border-green-200 text-green-800 px-4 py-3 rounded mb-6 flex items-center shadow-sm" role="alert">
            <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-100 border border-red-200 text-red-800 px-4 py-3 rounded mb-6 flex items-center shadow-sm" role="alert">
            <svg class="h-5 w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
            </svg>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
        @endif
        
        @if(auth()->check())
            @if(count($wishlistItems) > 0)
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-display text-primary">
                        {{ __('common.wishlist') }} <span class="text-gray-500 text-lg">({{ count($wishlistItems) }} {{ count($wishlistItems) == 1 ? (is_rtl() ? 'منتج' : 'item') : (is_rtl() ? 'منتجات' : 'items') }})</span>
                    </h2>
                    <form action="{{ route('wishlist.clear') }}" method="POST" class="clear-wishlist-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800 flex items-center transition-colors duration-200">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('common.clear_wishlist') }}
                        </button>
                    </form>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($wishlistItems as $item)
                        @if($item->product)
                        <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden relative group" data-product-id="{{ $item->product->id }}">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-100 relative">
                                @if($item->product->image)
                                <a href="{{ route('products.show', $item->product->slug) }}">
                                    <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-500">
                                </a>
                                @else
                                <div class="flex items-center justify-center h-full bg-gray-100">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @endif
                                
                                @if($item->product->discount_percent > 0)
                                <span class="absolute bottom-3 {{ is_rtl() ? 'right-3' : 'left-3' }} bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                                    {{ $item->product->discount_percent }}% {{ is_rtl() ? 'خصم' : 'OFF' }}
                                </span>
                                @endif
                                
                                <form action="{{ route('wishlist.remove', $item->product) }}" method="POST" class="absolute top-3 right-3">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-white p-2 rounded-full shadow hover:bg-gray-100 transition-colors duration-200 wishlist-remove-btn" title="{{ __('common.remove_from_wishlist') }}">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            <div class="p-5">
                                <a href="{{ route('products.category', $item->product->category->slug) }}" class="text-xs text-gray-500 hover:text-accent mb-1 transition-colors duration-200">{{ $item->product->category->name }}</a>
                                <h3 class="font-medium text-lg mb-2">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="text-primary hover:text-accent transition-colors duration-200">{{ $item->product->name }}</a>
                                </h3>
                                <div class="flex justify-between items-center mt-4">
                                    <div>
                                        @if($item->product->discount_percent > 0)
                                            <span class="text-sm line-through text-gray-400 {{ is_rtl() ? 'ml-2' : 'mr-2' }}">{{ Settings::formatPrice($item->product->price) }}</span>
                                            <span class="text-lg font-semibold text-accent">{{ Settings::formatPrice($item->product->final_price) }}</span>
                                        @else
                                            <span class="text-lg font-semibold text-accent">{{ Settings::formatPrice($item->product->price) }}</span>
                                        @endif
                                    </div>
                                    <form action="{{ route('cart.add', $item->product) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-primary hover:bg-primary-dark text-white p-2.5 rounded-full transition-colors duration-300 shadow-sm hover:shadow-md transform hover:scale-105 add-to-cart" data-action="add-to-cart" aria-label="{{ __('common.add_to_cart') }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <!-- View Details Link -->
                                <div class="flex justify-end items-center mt-4">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="text-primary hover:text-primary-dark font-medium flex items-center">
                                        {{ is_rtl() ? 'عرض التفاصيل' : 'View details' }}
                                        <svg class="ml-1 w-4 h-4 rtl-flip" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="rounded-full bg-gray-100 p-6 mb-6">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-display text-primary mb-4">{{ __('common.wishlist_empty') }}</h2>
                        <p class="text-gray-600 mb-8 max-w-md mx-auto">{{ __('common.wishlist_empty_message') }}</p>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent hover:bg-accent-dark md:py-4 md:text-lg md:px-8 transition-colors">
                            {{ __('common.browse_products') }}
                        </a>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="rounded-full bg-gray-100 p-6 mb-6">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-display text-primary mb-4">{{ __('common.sign_in_to_view_wishlist') }}</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">{{ __('common.wishlist_requires_login') }}</p>
                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-6">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary hover:bg-primary-dark transition-colors">
                            {{ __('common.sign_in') }}
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                            {{ __('common.create_account') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // AJAX for removing items from wishlist
    const wishlistRemoveBtns = document.querySelectorAll('.wishlist-remove-btn');
    wishlistRemoveBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const url = form.getAttribute('action');
            const productCard = this.closest('[data-product-id]');
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Fade out and remove the product card
                    productCard.classList.add('opacity-0');
                    setTimeout(() => {
                        productCard.remove();
                        
                        // Check if there are any items left
                        const remainingItems = document.querySelectorAll('[data-product-id]');
                        if (remainingItems.length === 0) {
                            // Refresh the page to show empty state
                            window.location.reload();
                        }
                    }, 300);
                    
                    // Show success toast
                    if (window.showToast) {
                        window.showToast(data.message || '{{ __("common.removed_from_wishlist") }}', 'success');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showToast) {
                    window.showToast('Error removing item from wishlist', 'error');
                }
            });
        });
    });
    
    // AJAX for clearing the entire wishlist
    const clearWishlistForm = document.querySelector('.clear-wishlist-form');
    if (clearWishlistForm) {
        clearWishlistForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = this.getAttribute('action');
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success toast
                    if (window.showToast) {
                        window.showToast(data.message || '{{ __("common.wishlist_cleared") }}', 'success');
                    }
                    
                    // Refresh the page to show empty state
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (window.showToast) {
                    window.showToast('Error clearing wishlist', 'error');
                }
            });
        });
    }
    
    // Note: Add to cart functionality is now handled by the global cart.js script
    // that we fixed earlier, so we don't need to duplicate that code here
});
</script>
@endpush 