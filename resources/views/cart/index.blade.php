@extends('layouts.app')

@php
use Illuminate\Support\Str;
use App\Helpers\TranslationHelper;
use App\Helpers\SettingsHelper as Settings;
@endphp

@section('meta_tags')
    <x-seo :title="Settings::get('cart_meta_title') ?? (is_rtl() ? 'سلة التسوق' : 'Shopping Cart') . ' | ' . config('app.name')"
           :description="Settings::get('cart_meta_description') ?? (is_rtl() ? 'مراجعة عناصر سلة التسوق الخاصة بك والمتابعة إلى الدفع. إدارة الكميات ومشاهدة إجمالي طلبك.' : 'Review your shopping cart items and proceed to checkout. Manage quantities and see your order total.')"
           :keywords="Settings::get('cart_meta_keywords') ?? (is_rtl() ? 'سلة التسوق، الدفع، الطلب' : 'shopping cart, checkout, order') . ', ' . config('app.name')"
           :ogImage="Settings::get('cart_og_image')"
           type="website" />
@endsection

@section('content')
<div class="bg-background min-h-screen py-16">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-display text-primary mb-2">{{ is_rtl() ? 'سلة التسوق الخاصة بك' : 'Your Shopping Cart' }}</h1>
            <p class="text-gray-600 max-w-2xl mx-auto">{{ is_rtl() ? 'مراجعة العناصر الخاصة بك والمتابعة إلى الدفع' : 'Review your items and proceed to checkout' }}</p>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
                <svg class="h-5 w-5 mr-2 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center shadow-sm" role="alert">
                <svg class="h-5 w-5 mr-2 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        
        @if(count($products) > 0 || count($offers) > 0)
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Cart Items -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full" id="cart-table">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'المنتج' : 'Product' }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'السعر' : 'Price' }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'الكمية' : 'Quantity' }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'المجموع الفرعي' : 'Subtotal' }}</th>
                                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'الإجراءات' : 'Actions' }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <!-- Products in cart -->
                                    @foreach($products as $item)
                                        <tr class="product-row" data-product-id="{{ $item['product']->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-20 w-20 flex-shrink-0 rounded-lg overflow-hidden shadow-sm">
                                                        @if($item['product']->featured_image)
                                                            <img class="h-20 w-20 object-cover" src="{{ asset('storage/' . $item['product']->featured_image) }}" alt="{{ $item['product']->name }}">
                                                        @else
                                                            <div class="h-20 w-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="ml-4">
                                                        <a href="{{ route('products.show', $item['product']->slug) }}" class="text-gray-900 font-medium hover:text-accent transition-colors">
                                                            {{ $item['product']->name }}
                                                        </a>
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            {{ is_rtl() ? 'الفئة' : 'Category' }}: <a href="{{ route('products.category', $item['product']->category->slug) }}" class="text-accent hover:text-accent-dark transition-colors">{{ $item['product']->category->name }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-sm text-gray-900">
                                                    {{ Settings::formatPrice($item['product']->price) }} <span class="unit-price hidden">{{ $item['product']->price }}</span>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <button class="decrement-quantity px-2 py-1 border border-gray-300 rounded-l-md bg-gray-50">-</button>
                                                    <input type="number" min="1" value="{{ $item['quantity'] }}" class="quantity-input w-12 text-center border-t border-b border-gray-300 py-1">
                                                    <button class="increment-quantity px-2 py-1 border border-gray-300 rounded-r-md bg-gray-50">+</button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <span class="font-medium text-gray-900 item-subtotal">
                                                    {{ Settings::formatPrice($item['product']->price * $item['quantity']) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button" class="remove-product text-red-600 hover:text-red-900 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Offers in cart -->
                                    @foreach($offers as $item)
                                        <tr class="offer-row" data-offer-id="{{ $item['offer']->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($item['offer']->image)
                                                        <div class="flex-shrink-0 h-10 w-10">
                                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . str_replace('storage/', '', $item['offer']->image)) }}" alt="{{ $item['offer']->title }}">
                                                        </div>
                                                    @endif
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $item['offer']->title }}
                                                            @if(isset($item['special_offer']) && $item['special_offer'])
                                                                <span class="bg-accent text-white text-xs px-2 py-1 rounded-full">{{ is_rtl() ? 'عرض خاص' : 'Special Offer' }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ Str::limit($item['offer']->description, 50) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ Settings::formatPrice($item['price']) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <button class="decrement-offer-quantity px-2 py-1 border border-gray-300 rounded-l-md bg-gray-50">-</button>
                                                    <input type="number" min="1" value="{{ $item['quantity'] }}" class="offer-quantity-input w-12 text-center border-t border-b border-gray-300 py-1">
                                                    <button class="increment-offer-quantity px-2 py-1 border border-gray-300 rounded-r-md bg-gray-50">+</button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900 offer-subtotal">{{ Settings::formatPrice($item['price'] * $item['quantity']) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button class="remove-offer text-red-600 hover:text-red-900">{{ is_rtl() ? 'إزالة' : 'Remove' }}</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="w-full lg:w-1/3">
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden sticky top-24">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-display text-primary flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                                {{ is_rtl() ? 'ملخص الطلب' : 'Order Summary' }}
                            </h2>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">{{ is_rtl() ? 'المجموع الفرعي' : 'Subtotal' }}</span>
                                    <span class="text-gray-900">{{ Settings::formatPrice($total) }} <span id="cart-subtotal" class="hidden">{{ $total }}</span></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">{{ is_rtl() ? 'الشحن' : 'Shipping' }}</span>
                                    <span class="text-gray-900">{{ Settings::formatPrice(0) }}</span>
                                </div>
                                <div class="border-t border-gray-200 pt-4 flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">{{ is_rtl() ? 'المجموع' : 'Total' }}</span>
                                    <span class="text-lg font-semibold text-accent">{{ Settings::formatPrice($total) }} <span id="cart-total" class="hidden">{{ $total }}</span></span>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <a href="{{ route('checkout.index') }}" class="block w-full text-center bg-accent hover:bg-accent-dark text-white py-3 px-4 rounded-lg font-semibold transition-colors duration-300 shadow-sm hover:shadow">
                                    {{ is_rtl() ? 'المتابعة إلى الدفع' : 'Proceed to Checkout' }}
                                </a>
                                <a href="{{ route('products.index') }}" class="block w-full text-center bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 px-4 rounded-lg font-medium transition-colors duration-300">
                                    {{ is_rtl() ? 'مواصلة التسوق' : 'Continue Shopping' }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-md p-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="rounded-full bg-gray-100 p-6 mb-6">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-display text-primary mb-4">{{ is_rtl() ? 'سلة التسوق فارغة' : 'Your cart is empty' }}</h2>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">{{ is_rtl() ? 'يبدو أنك لم تضف أي منتجات إلى سلة التسوق الخاصة بك بعد. تصفح مجموعتنا لاكتشاف الجمال المستوحى من السماء.' : 'Looks like you haven\'t added any products to your cart yet. Browse our collection to discover celestial-inspired beauty.' }}</p>
                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-accent hover:bg-accent-dark md:py-4 md:text-lg md:px-8 transition-colors">
                        {{ is_rtl() ? 'تصفح المنتجات' : 'Browse Products' }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Get currency settings for JavaScript use
        const currencySymbol = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', '$') }}';
        const currencyPosition = '{{ \App\Helpers\SettingsHelper::get('currency_position', 'left') }}';
        const thousandSeparator = '{{ \App\Helpers\SettingsHelper::get('thousand_separator', ',') }}';
        const decimalSeparator = '{{ \App\Helpers\SettingsHelper::get('decimal_separator', '.') }}';
        const decimalDigits = {{ \App\Helpers\SettingsHelper::get('decimal_digits', 2) }};
        
        // Function to extract numeric price from formatted price string (regardless of currency symbol)
        function extractPrice(priceString) {
            // Remove currency symbol and separators
            return parseFloat(priceString.replace(currencySymbol, '')
                            .replace(new RegExp('[^0-9' + decimalSeparator + ']', 'g'), '')
                            .replace(decimalSeparator, '.')
                            .trim());
        }
        
        // Function to format price with currency symbol
        function formatPrice(price) {
            // Format number with proper separators
            let formattedNumber = price.toFixed(decimalDigits);
            
            // Add thousand separators if needed
            if (thousandSeparator) {
                const parts = formattedNumber.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
                formattedNumber = parts.join(decimalSeparator);
            }
            
            // Position currency symbol according to settings
            switch (currencyPosition) {
                case 'left':
                    return currencySymbol + formattedNumber;
                case 'right':
                    return formattedNumber + currencySymbol;
                case 'left_with_space':
                    return currencySymbol + ' ' + formattedNumber;
                case 'right_with_space':
                    return formattedNumber + ' ' + currencySymbol;
                default:
                    return currencySymbol + formattedNumber;
            }
        }
        
        // Handle product quantity changes
        document.querySelectorAll('.product-row .quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                updateProductQuantity(this);
            });
        });
        
        document.querySelectorAll('.product-row .increment-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.flex').querySelector('.quantity-input');
                input.value = parseInt(input.value) + 1;
                updateProductQuantity(input);
            });
        });
        
        document.querySelectorAll('.product-row .decrement-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.flex').querySelector('.quantity-input');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateProductQuantity(input);
                }
            });
        });
        
        // Handle offer quantity changes
        document.querySelectorAll('.offer-row .offer-quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                updateOfferQuantity(this);
            });
        });
        
        document.querySelectorAll('.offer-row .increment-offer-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.flex').querySelector('.offer-quantity-input');
                input.value = parseInt(input.value) + 1;
                updateOfferQuantity(input);
            });
        });
        
        document.querySelectorAll('.offer-row .decrement-offer-quantity').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.closest('.flex').querySelector('.offer-quantity-input');
                if (parseInt(input.value) > 1) {
                    input.value = parseInt(input.value) - 1;
                    updateOfferQuantity(input);
                }
            });
        });
        
        // Handle product removal
        document.querySelectorAll('.remove-product').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.product-row');
                const productId = row.dataset.productId;
                
                fetch(`/cart/remove/${productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        row.remove();
                        updateCartTotal();
                        // Update cart count in header
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }
                });
            });
        });
        
        // Handle offer removal
        document.querySelectorAll('.remove-offer').forEach(button => {
            button.addEventListener('click', function() {
                const row = this.closest('.offer-row');
                const offerId = row.dataset.offerId;
                
                fetch(`/cart/remove-offer/${offerId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        row.remove();
                        updateCartTotal();
                        // Update cart count in header
                        const cartCount = document.getElementById('cart-count');
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                        }
                    }
                });
            });
        });
        
        function updateProductQuantity(input) {
            const row = input.closest('.product-row');
            const productId = row.dataset.productId;
            const quantity = parseInt(input.value);
            
            if (isNaN(quantity) || quantity < 1) {
                input.value = 1;
                return;
            }
            
            fetch(`/cart/update/${productId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity: quantity })
            })
            .then(response => response.json())
            .then(data => {
                // Get the price from the price cell
                const priceCell = row.querySelector('td:nth-child(2) .text-sm');
                const price = extractPrice(priceCell.textContent);
                
                // Update the subtotal cell with formatted price
                const subtotalCell = row.querySelector('.item-subtotal');
                subtotalCell.textContent = formatPrice(price * quantity);
                
                // Update the cart total
                updateCartTotal();
                
                // Update cart count in header if available
                if (data.cart_count) {
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                    }
                }
            })
            .catch(error => {
                console.error('Error updating quantity:', error);
            });
        }
        
        function updateOfferQuantity(input) {
            const row = input.closest('.offer-row');
            const offerId = row.dataset.offerId;
            const quantity = parseInt(input.value);
            
            if (isNaN(quantity) || quantity < 1) {
                input.value = 1;
                return;
            }
            
            // Get the price from the price cell
            const priceCell = row.querySelector('td:nth-child(2) .text-sm');
            const price = extractPrice(priceCell.textContent);
            
            // Update the subtotal cell with formatted price
            const subtotalCell = row.querySelector('.offer-subtotal');
            subtotalCell.textContent = formatPrice(price * quantity);
            
            // Update the cart total
            updateCartTotal();
            
            // In a full implementation, you would add an API endpoint to update the offer quantity
        }
        
        function updateCartTotal() {
            let total = 0;
            
            // Sum product subtotals
            document.querySelectorAll('.item-subtotal').forEach(el => {
                total += extractPrice(el.textContent);
            });
            
            // Sum offer subtotals
            document.querySelectorAll('.offer-subtotal').forEach(el => {
                total += extractPrice(el.textContent);
            });
            
            // Update the total
            const totalElement = document.querySelector('.text-lg .font-semibold.text-accent');
            if (totalElement) {
                totalElement.textContent = formatPrice(total);
            }
            
            // If cart is empty, reload the page to show empty cart message
            if (document.querySelectorAll('.product-row, .offer-row').length === 0) {
                window.location.reload();
            }
        }
    });
</script>
@endpush
@endsection 