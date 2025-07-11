@props(['product', 'style' => 'default'])
@php
use Illuminate\Support\Str;
@endphp

@if($style === 'default')
<div class="group bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 h-full flex flex-col transform hover:-translate-y-1">
    <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-square">
        <div class="w-full h-full bg-gray-100">
            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500" loading="lazy">
        </div>
        @if($product->is_new)
            <span class="absolute top-3 {{ is_rtl() ? 'right-3' : 'left-3' }} bg-accent text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">{{ __('messages.new') }}</span>
        @endif
        @if($product->discount_percent > 0)
            <span class="absolute bottom-3 {{ is_rtl() ? 'right-3' : 'left-3' }} bg-primary text-white text-xs font-semibold px-3 py-1 rounded-full shadow-sm">
                {{ $product->discount_percent }}% {{ __('messages.off', ['default' => 'OFF']) }}
            </span>
        @endif
        
        <!-- Quick View Button -->
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black bg-opacity-20">
            <button type="button"
                class="absolute top-2 right-2 w-8 h-8 bg-white bg-opacity-75 rounded-full flex items-center justify-center text-primary hover:bg-opacity-100 transition-opacity"
                data-product-id="{{ $product->id }}"
                data-product-slug="{{ $product->slug }}"
                data-product-name="{{ $product->name }}"
                data-product-price="{{ $product->price }}"
                data-product-final-price="{{ $product->final_price }}"
                data-product-image="{{ asset('storage/' . $product->image) }}"
                data-product-description="{{ Str::limit(strip_tags($product->description), 100) }}"
                data-product-category="{{ $product->category->name }}"
                onclick="event.preventDefault(); window.quickViewProduct(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
            
            @auth
                @php
                    $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                        ->where('product_id', $product->id)
                        ->exists();
                @endphp
                <form action="{{ $inWishlist ? route('wishlist.remove', $product) : route('wishlist.add', $product) }}" method="POST" class="absolute top-2 left-2">
                    @csrf
                    @if($inWishlist)
                        @method('DELETE')
                    @endif
                    <button type="submit" class="w-8 h-8 bg-white bg-opacity-75 rounded-full flex items-center justify-center hover:bg-opacity-100 transition-opacity">
                        <svg class="w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-600' }}" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="absolute top-2 left-2 w-8 h-8 bg-white bg-opacity-75 rounded-full flex items-center justify-center hover:bg-opacity-100 transition-opacity" title="{{ __('common.sign_in_to_add_to_wishlist') }}">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>
            @endauth
        </div>
    </a>
    <div class="p-5 flex-grow flex flex-col">
        <a href="{{ route('products.category', $product->category->slug) }}" class="text-xs text-gray-500 hover:text-accent mb-1 transition-colors duration-200">{{ $product->category->name }}</a>
        <a href="{{ route('products.show', $product->slug) }}" class="text-primary hover:text-accent font-medium text-lg mb-3 line-clamp-2 flex-grow transition-colors duration-200">{{ $product->name }}</a>
        <div class="flex items-center justify-between mt-2 {{ is_rtl() ? 'flex-row-reverse' : '' }}">
            <div>
                @if($product->discount_percent > 0)
                    <span class="text-sm line-through text-gray-400 {{ is_rtl() ? 'ml-2' : 'mr-2' }}">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</span>
                    <span class="text-lg font-semibold text-accent">{{ \App\Helpers\SettingsHelper::formatPrice($product->final_price) }}</span>
                @else
                    <span class="text-lg font-semibold text-accent">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</span>
                @endif
            </div>
            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" 
                    class="add-to-cart bg-primary hover:bg-primary-dark text-white p-2.5 rounded-full transition-colors duration-300 shadow-sm hover:shadow-md transform hover:scale-105" 
                    data-action="add-to-cart"
                    aria-label="{{ __('messages.add_to_cart') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </button>
            </form>
        </div>
        <!-- View Details Link -->
        <div class="flex justify-end items-center mt-4">
            <a href="{{ route('products.show', $product->slug) }}" class="text-primary hover:text-primary-dark font-medium flex items-center">
                {{ is_rtl() ? 'عرض التفاصيل' : 'View details' }}
                <svg class="ml-1 w-4 h-4 rtl-flip" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@else
<div class="group relative bg-white rounded-2xl shadow-md overflow-hidden transition-all duration-300 hover:shadow-xl transform hover:-translate-y-1">
    <div class="flex justify-between absolute top-0 left-0 right-0 z-10">
        <div class="m-3">
            @if($product->is_new)
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary text-white shadow-sm">
                {{ __('messages.new') }}
            </span>
            @endif
        </div>
        <div class="m-3">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-primary text-white shadow-sm">
                {{ $product->category->name }}
            </span>
        </div>
    </div>
    
    <div class="relative pb-[100%] overflow-hidden">
        <div class="w-full h-full bg-gray-100">
            <img 
                src="{{ asset('storage/' . $product->image) }}" 
                alt="{{ $product->name }}" 
                class="absolute inset-0 w-full h-full object-cover transform transition-transform duration-500 group-hover:scale-105"
                loading="lazy"
            >
        </div>
        
        <!-- Quick View Button -->
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black bg-opacity-20">
            <button type="button"
                class="absolute top-2 right-2 w-8 h-8 bg-white bg-opacity-75 rounded-full flex items-center justify-center text-primary hover:bg-opacity-100 transition-opacity"
                data-product-id="{{ $product->id }}"
                data-product-slug="{{ $product->slug }}"
                data-product-name="{{ $product->name }}"
                data-product-price="{{ $product->price }}"
                data-product-final-price="{{ $product->final_price }}"
                data-product-image="{{ asset('storage/' . $product->image) }}"
                data-product-description="{{ Str::limit(strip_tags($product->description), 100) }}"
                data-product-category="{{ $product->category->name }}"
                onclick="event.preventDefault(); window.quickViewProduct(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
            
            @auth
                @php
                    $inWishlist = \App\Models\Wishlist::where('user_id', auth()->id())
                        ->where('product_id', $product->id)
                        ->exists();
                @endphp
                <form action="{{ $inWishlist ? route('wishlist.remove', $product) : route('wishlist.add', $product) }}" method="POST" class="absolute top-2 left-2">
                    @csrf
                    @if($inWishlist)
                        @method('DELETE')
                    @endif
                    <button type="submit" class="w-8 h-8 bg-white bg-opacity-75 rounded-full flex items-center justify-center hover:bg-opacity-100 transition-opacity">
                        <svg class="w-5 h-5 {{ $inWishlist ? 'text-red-500' : 'text-gray-600' }}" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="absolute top-2 left-2 w-8 h-8 bg-white bg-opacity-75 rounded-full flex items-center justify-center hover:bg-opacity-100 transition-opacity" title="{{ __('common.sign_in_to_add_to_wishlist') }}">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
    
    <div class="p-5 bg-white">
        <h3 class="font-display text-xl mb-2 text-primary group-hover:text-accent transition-colors duration-300 truncate">{{ $product->name }}</h3>
        <p class="text-sm text-gray-600 h-10 line-clamp-2 mb-4">{{ $product->description }}</p>
        <div class="flex items-center justify-between">
            <p class="text-accent font-semibold text-lg">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</p>
            <form action="{{ route('cart.add', $product) }}" method="POST">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" 
                    class="add-to-cart flex items-center justify-center w-10 h-10 rounded-full bg-primary text-white hover:bg-accent transition-colors duration-300 shadow-md transform hover:scale-105"
                    data-action="add-to-cart">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </button>
            </form>
        </div>
        <!-- View Details Link -->
        <div class="flex justify-end items-center mt-4">
            <a href="{{ route('products.show', $product->slug) }}" class="text-primary hover:text-primary-dark font-medium flex items-center">
                {{ is_rtl() ? 'عرض التفاصيل' : 'View details' }}
                <svg class="ml-1 w-4 h-4 rtl-flip" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
@endif 