@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
@endphp

@section('content')
<div class="bg-background min-h-screen pt-16 pb-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-display text-primary">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</h1>
                <a href="{{ route('orders.index') }}" class="inline-flex items-center text-sm font-medium text-accent hover:text-accent-dark">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ TranslationHelper::get('back_to_orders', 'Back to Orders') }}
                </a>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-200">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('order_information', 'Order Information') }}</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('order_date', 'Order Date') }}:</span>
                                <span class="font-medium">{{ $order->created_at->format('F j, Y, g:i a') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('order_status', 'Order Status') }}:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ TranslationHelper::get('status_' . $order->status, ucfirst($order->status)) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('payment_method', 'Payment Method') }}:</span>
                                <span class="font-medium">{{ TranslationHelper::get('payment_method_' . $order->payment_method, ucfirst(str_replace('_', ' ', $order->payment_method))) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('payment_status', 'Payment Status') }}:</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status == 'refunded') bg-purple-100 text-purple-800
                                    @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                    {{ TranslationHelper::get('payment_status_' . $order->payment_status, ucfirst($order->payment_status)) }}
                                </span>
                            </div>
                            @if($order->tracking_number)
                                <div class="flex justify-between items-center border-t border-gray-100 pt-3 mt-3">
                                    <span class="text-gray-600 font-medium">{{ TranslationHelper::get('tracking_number', 'Tracking Number') }}:</span>
                                    <div class="relative">
                                        <div class="flex items-center">
                                            <span id="tracking-number" class="font-medium bg-indigo-50 text-indigo-700 px-2 py-1 rounded border border-indigo-200">{{ $order->tracking_number }}</span>
                                            <button onclick="copyTrackingNumber()" class="ml-2 text-gray-500 hover:text-primary focus:outline-none" title="{{ TranslationHelper::get('copy_tracking_number', 'Copy tracking number') }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                                </svg>
                                            </button>
                                            <span id="copy-tooltip" class="hidden absolute right-0 -mt-8 px-2 py-1 bg-gray-800 text-white text-xs rounded shadow-lg">{{ TranslationHelper::get('copied', 'Copied!') }}</span>
                                        </div>
                                    </div>
                                </div>
                                @if($order->status == 'shipped' || $order->status == 'delivered')
                                <div class="mt-3 text-sm bg-blue-50 p-3 rounded-md text-blue-700">
                                    <p><span class="font-semibold">{{ TranslationHelper::get('shipping_info', 'Shipping Info') }}:</span> 
                                    {{ TranslationHelper::get('your_order_' . $order->status, 'Your order ' . ($order->status == 'delivered' ? 'was delivered' : 'has shipped') . '!') }} 
                                    {{ $order->status == 'shipped' ? TranslationHelper::get('track_package_info', 'You can track your package with the tracking number above.') : '' }}</p>
                                </div>
                                @endif
                                <script>
                                    function copyTrackingNumber() {
                                        const trackingNumber = document.getElementById('tracking-number').innerText;
                                        navigator.clipboard.writeText(trackingNumber)
                                            .then(() => {
                                                const tooltip = document.getElementById('copy-tooltip');
                                                tooltip.classList.remove('hidden');
                                                setTimeout(() => {
                                                    tooltip.classList.add('hidden');
                                                }, 2000);
                                            })
                                            .catch(err => {
                                                console.error('Failed to copy text: ', err);
                                            });
                                    }
                                </script>
                            @endif
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('customer_information', 'Customer Information') }}</h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('name', 'Name') }}:</span>
                                <span class="font-medium">{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('email', 'Email') }}:</span>
                                <span class="font-medium">{{ $order->shipping_address['email'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">{{ TranslationHelper::get('phone', 'Phone') }}:</span>
                                <span class="font-medium">{{ $order->shipping_address['phone'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('shipping_address', 'Shipping Address') }}</h2>
                    <div class="text-sm space-y-1">
                        <p>{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}</p>
                        <p>{{ $order->shipping_address['address_line1'] }}</p>
                        @if(isset($order->shipping_address['address_line2']) && !empty($order->shipping_address['address_line2']))
                            <p>{{ $order->shipping_address['address_line2'] }}</p>
                        @endif
                        <p>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }} {{ $order->shipping_address['postal_code'] }}</p>
                        <p>{{ $order->shipping_address['country'] }}</p>
                        <p>{{ $order->shipping_address['phone'] }}</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('billing_address', 'Billing Address') }}</h2>
                    <div class="text-sm space-y-1">
                        <p>{{ $order->billing_address['first_name'] }} {{ $order->billing_address['last_name'] }}</p>
                        <p>{{ $order->billing_address['address_line1'] }}</p>
                        @if(isset($order->billing_address['address_line2']) && !empty($order->billing_address['address_line2']))
                            <p>{{ $order->billing_address['address_line2'] }}</p>
                        @endif
                        <p>{{ $order->billing_address['city'] }}, {{ $order->billing_address['state'] }} {{ $order->billing_address['postal_code'] }}</p>
                        <p>{{ $order->billing_address['country'] }}</p>
                        <p>{{ $order->billing_address['phone'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('payment_information', 'Payment Information') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <span class="text-gray-600">{{ TranslationHelper::get('payment_method', 'Payment Method') }}:</span>
                        <span class="font-medium ml-2">{{ TranslationHelper::get('payment_method_' . $order->payment_method, ucfirst(str_replace('_', ' ', $order->payment_method))) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">{{ TranslationHelper::get('payment_status', 'Payment Status') }}:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-2
                            @if($order->payment_status == 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status == 'refunded') bg-purple-100 text-purple-800
                            @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ TranslationHelper::get('payment_status_' . $order->payment_status, ucfirst($order->payment_status)) }}
                        </span>
                    </div>
                </div>
                
                @if($order->payment_method == 'cod' && $order->payment_status == 'pending')
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mt-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ TranslationHelper::get('cod_instructions', 'Cash on Delivery Instructions') }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">{{ TranslationHelper::get('cod_instructions_text', 'Please have the exact amount of') }} {{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }} {{ TranslationHelper::get('cod_instructions_text_2', 'ready when our delivery personnel arrives with your order. The payment will be collected at the time of delivery.') }}</p>
                    </div>
                @elseif($order->payment_method == 'instapay' && $order->payment_status == 'pending')
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mt-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ TranslationHelper::get('instapay_instructions', 'InstaPay Payment Instructions') }}</span>
                        </div>
                        <div class="mt-2 space-y-3 text-sm">
                            <p class="text-gray-600">{{ TranslationHelper::get('payment_instructions_text', 'Please complete your payment using the following details:') }}</p>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('payment_method', 'Payment Method') }}:</span> 
                                <span class="ml-2">InstaPay</span>
                            </div>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('instapay_number', 'InstaPay Number') }}:</span> 
                                <span class="ml-2">{{ \App\Helpers\SettingsHelper::get('instapay_number', 'Contact Customer Service') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('reference_number', 'Reference Number') }}:</span> 
                                <span class="ml-2">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('amount', 'Amount') }}:</span> 
                                <span class="ml-2">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</span>
                            </div>
                            <p class="text-gray-500 italic">{{ \App\Helpers\SettingsHelper::get('payment_confirmation_instructions', 'After making the payment, please contact our customer service team with your payment details for faster processing.') }}</p>
                            <p class="text-gray-600 whitespace-pre-line">{{ \App\Helpers\SettingsHelper::get('payment_confirmation_contact', 'Contact customer service for assistance.') }}</p>
                        </div>
                    </div>
                @elseif($order->payment_method == 'vodafone' && $order->payment_status == 'pending')
                    <div class="bg-gray-50 p-4 rounded-md border border-gray-200 mt-2">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">{{ TranslationHelper::get('vodafone_instructions', 'Vodafone Cash Payment Instructions') }}</span>
                        </div>
                        <div class="mt-2 space-y-3 text-sm">
                            <p class="text-gray-600">{{ TranslationHelper::get('payment_instructions_text', 'Please complete your payment using the following details:') }}</p>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('payment_method', 'Payment Method') }}:</span> 
                                <span class="ml-2">{{ TranslationHelper::get('vodafone_cash', 'Vodafone Cash') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('vodafone_number', 'Vodafone Cash Number') }}:</span> 
                                <span class="ml-2">{{ \App\Helpers\SettingsHelper::get('vodafone_cash_number', 'Contact Customer Service') }}</span>
                            </div>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('reference_number', 'Reference Number') }}:</span> 
                                <span class="ml-2">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</span>
                            </div>
                            <div>
                                <span class="font-medium">{{ TranslationHelper::get('amount', 'Amount') }}:</span> 
                                <span class="ml-2">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</span>
                            </div>
                            <p class="text-gray-500 italic">{{ \App\Helpers\SettingsHelper::get('payment_confirmation_instructions', 'After making the payment, please contact our customer service team with your payment details for faster processing.') }}</p>
                            <p class="text-gray-600 whitespace-pre-line">{{ \App\Helpers\SettingsHelper::get('payment_confirmation_contact', 'Contact customer service for assistance.') }}</p>
                        </div>
                    </div>
                @endif
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 bg-primary">
                    <h2 class="text-lg font-semibold text-white">{{ TranslationHelper::get('order_items', 'Order Items') }}</h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('product', 'Product') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('price', 'Price') }}</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('quantity', 'Quantity') }}</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('subtotal', 'Subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($order->items as $item)
                                @if($item->offer_id)
                                    <tr>
                                        <td class="py-4 px-4 border-b border-gray-200">
                                            <div class="flex items-center">
                                                @if($item->offer && $item->offer->image)
                                                    <img src="{{ asset('storage/' . $item->offer->image) }}" alt="{{ $item->offer->title }}" class="h-16 w-16 object-cover rounded">
                                                @else
                                                    <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center">
                                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $item->offer->title }}</div>
                                                    <div class="text-xs text-primary font-medium mt-1">
                                                        <a href="{{ route('orders.bundle-details', ['orderId' => $order->id, 'itemId' => $item->id]) }}" class="flex items-center hover:text-primary-dark">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                            {{ TranslationHelper::get('view_bundle_details', 'View Bundle Details') }}
                                                        </a>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                            {{ TranslationHelper::get('bundle', 'Bundle') }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 border-b border-gray-200 text-sm text-right">
                                            {{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}
                                        </td>
                                        <td class="py-4 px-4 border-b border-gray-200 text-sm text-right">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="py-4 px-4 border-b border-gray-200 text-sm text-right font-medium">
                                            {{ \App\Helpers\SettingsHelper::formatPrice($item->price * $item->quantity) }}
                                        </td>
                                    </tr>
                                @else
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    @if($item->type == 'product' && $item->product && $item->product->featured_image)
                                                        <img class="h-10 w-10 object-cover rounded" src="{{ asset('storage/' . $item->product->featured_image) }}" alt="{{ $item->product->name }}">
                                                    @elseif($item->type == 'offer' && $item->offer && $item->offer->image)
                                                        <img class="h-10 w-10 object-cover rounded" src="{{ asset('storage/' . str_replace('storage/', '', $item->offer->image)) }}" alt="{{ $item->offer->title }}">
                                                    @else
                                                        <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        @if($item->type == 'product')
                                                            {{ $item->product ? $item->product->name : $item->name }}
                                                        @elseif($item->type == 'offer')
                                                            {{ $item->offer ? $item->offer->title : $item->name }}
                                                            <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-accent text-white">
                                                                {{ TranslationHelper::get('special_offer', 'Special Offer') }}
                                                            </span>
                                                        @else
                                                            {{ $item->name ?? TranslationHelper::get('item_not_available', 'Item Not Available') }}
                                                        @endif
                                                    </div>
                                                    @if($item->type == 'product' && $item->product && $item->product->sku)
                                                    <div class="text-xs text-gray-500">
                                                        {{ TranslationHelper::get('sku', 'SKU') }}: {{ $item->product->sku }}
                                                    </div>
                                                    @endif
                                                    @if($item->type == 'offer' && $item->offer && $item->offer->subtitle)
                                                    <div class="text-xs text-gray-500">
                                                        {{ $item->offer->subtitle }}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <div class="text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($item->subtotal) }}</div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('subtotal', 'Subtotal') }}:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->subtotal) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-green-600">{{ TranslationHelper::get('discount', 'Discount') }}{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600">-{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->discount_amount) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('shipping', 'Shipping') }} ({{ ucfirst($order->shipping_method) }}):</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->shipping_fee) }}</td>
                            </tr>
                            @if($order->cod_fee > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('cod_fee', 'Cash on Delivery Fee') }}:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->cod_fee) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-gray-100">
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">{{ TranslationHelper::get('total', 'Total') }}:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-primary">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="flex justify-between items-center">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    {{ TranslationHelper::get('back_to_orders', 'Back to Orders') }}
                </a>
                
                @if($order->status != 'cancelled')
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                        {{ TranslationHelper::get('shop_more_products', 'Shop More Products') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 