@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
@endphp

@section('meta_tags')
    <x-seo :title="Settings::get('order_confirmation_meta_title') ?? 'Order Confirmation | ' . config('app.name')"
           :description="Settings::get('order_confirmation_meta_description') ?? 'Thank you for your order! Your purchase has been confirmed.'"
           :keywords="Settings::get('order_confirmation_meta_keywords') ?? 'order confirmation, thank you, purchase, ' . config('app.name')"
           :ogImage="Settings::get('order_confirmation_og_image')"
           type="website" />
@endsection

@section('content')
<div class="bg-background min-h-screen pt-16 pb-24">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto">
            <div class="text-center mb-12">
                <div class="bg-white p-6 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6 shadow-md">
                    <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-display text-primary mb-2">{{ TranslationHelper::get('thank_you_order', 'Thank You for Your Order!') }}</h1>
                <p class="text-gray-600 mb-2">{{ TranslationHelper::get('order_placed_successfully', 'Your order has been placed successfully.') }}</p>
                <p class="text-gray-600 font-medium">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="bg-primary px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">{{ TranslationHelper::get('order_summary', 'Order Summary') }}</h2>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ TranslationHelper::get('order_details', 'Order Details') }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">{{ TranslationHelper::get('order_date', 'Order Date') }}:</p>
                                <p class="font-medium">{{ $order->created_at->format('F j, Y, g:i a') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">{{ TranslationHelper::get('order_status', 'Order Status') }}:</p>
                                <p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->status == 'processing') bg-blue-100 text-blue-800
                                    @elseif($order->status == 'completed') bg-green-100 text-green-800
                                    @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                        {{ TranslationHelper::get('status_' . $order->status, ucfirst($order->status)) }}
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">{{ TranslationHelper::get('payment_method', 'Payment Method') }}:</p>
                                <p class="font-medium">{{ TranslationHelper::get('payment_method_' . $order->payment_method, ucfirst(str_replace('_', ' ', $order->payment_method))) }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">{{ TranslationHelper::get('payment_status', 'Payment Status') }}:</p>
                                <p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($order->payment_status == 'paid') bg-green-100 text-green-800
                                    @elseif($order->payment_status == 'refunded') bg-purple-100 text-purple-800
                                    @elseif($order->payment_status == 'failed') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800
                                    @endif">
                                        {{ TranslationHelper::get('payment_status_' . $order->payment_status, ucfirst($order->payment_status)) }}
                                    </span>
                                </p>
                            </div>
                            @if($order->tracking_number)
                            <div>
                                <p class="text-gray-600">{{ TranslationHelper::get('tracking_number', 'Tracking Number') }}:</p>
                                <p class="font-medium text-indigo-700">{{ $order->tracking_number }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ TranslationHelper::get('shipping_address', 'Shipping Address') }}</h3>
                        <div class="text-sm bg-gray-50 p-4 rounded-md">
                            <p class="font-medium">{{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}</p>
                            <p>{{ $order->shipping_address['address_line1'] }}</p>
                            @if(isset($order->shipping_address['address_line2']) && !empty($order->shipping_address['address_line2']))
                                <p>{{ $order->shipping_address['address_line2'] }}</p>
                            @endif
                            <p>
                                {{ $order->shipping_address['city'] }}
                                @if(isset($order->shipping_address['state']) && !empty($order->shipping_address['state']))
                                , {{ $order->shipping_address['state'] }}
                                @endif
                                @if(isset($order->shipping_address['postal_code']) && !empty($order->shipping_address['postal_code']))
                                {{ $order->shipping_address['postal_code'] }}
                                @endif
                            </p>
                            <p>{{ \App\Helpers\CountryHelper::getCountryName($order->shipping_address['country']) }}</p>
                            <p>{{ $order->shipping_address['phone'] }}</p>
                            <p>{{ $order->shipping_address['email'] }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ TranslationHelper::get('order_items', 'Order Items') }}</h3>
                        <div class="overflow-x-auto border rounded-lg">
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
                                        <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('shipping', 'Shipping') }} ({{ TranslationHelper::get($order->shipping_method, ucfirst($order->shipping_method)) }}):</td>
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
                    
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3">{{ TranslationHelper::get('payment_information', 'Payment Information') }}</h3>
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200">
                            @if($order->payment_method == 'cod')
                                <div class="flex items-center space-x-3 text-sm">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ TranslationHelper::get('cash_on_delivery', 'Cash on Delivery') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">{{ TranslationHelper::get('cod_instructions_text', 'Please have the exact amount of') }} {{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }} {{ TranslationHelper::get('cod_instructions_text_2', 'ready for our delivery personnel upon arrival.') }}</p>
                            @elseif($order->payment_method == 'instapay')
                                <div class="flex items-center space-x-3 text-sm">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <span class="font-medium">{{ TranslationHelper::get('instapay', 'Instapay') }}</span>
                                </div>
                                <div class="mt-2 space-y-2 text-sm">
                                    <p class="text-gray-600">{{ TranslationHelper::get('payment_instructions_text', 'Please complete your payment using the following Instapay details:') }}</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('account_name', 'Account Name') }}:</p>
                                            <p class="font-medium">{{ \App\Helpers\SettingsHelper::get('company_name', 'Celestial Cosmetics') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('account_number', 'Account Number') }}:</p>
                                            <p class="font-medium">{{ \App\Helpers\SettingsHelper::get('instapay_number', 'Contact Customer Service') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('reference_number', 'Reference Number') }}:</p>
                                            <p class="font-medium">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('amount', 'Amount') }}:</p>
                                            <p class="font-medium">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-500 italic">{{ \App\Helpers\SettingsHelper::get('payment_confirmation_instructions', 'After making the payment, please contact our customer service team with your payment details for faster processing.') }}</p>
                                </div>
                            @elseif($order->payment_method == 'vodafone')
                                <div class="flex items-center space-x-3 text-sm">
                                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="font-medium">{{ TranslationHelper::get('vodafone_cash', 'Vodafone Cash') }}</span>
                                </div>
                                <div class="mt-2 space-y-2 text-sm">
                                    <p class="text-gray-600">{{ TranslationHelper::get('payment_instructions_text', 'Please complete your payment using the following Vodafone Cash details:') }}</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('phone_number', 'Phone Number') }}:</p>
                                            <p class="font-medium">{{ \App\Helpers\SettingsHelper::get('vodafone_cash_number', 'Contact Customer Service') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('reference', 'Reference') }}:</p>
                                            <p class="font-medium">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">{{ TranslationHelper::get('amount', 'Amount') }}:</p>
                                            <p class="font-medium">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-500 italic">{{ \App\Helpers\SettingsHelper::get('payment_confirmation_instructions', 'After making the payment, please contact our customer service team with your payment details for faster processing.') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center space-y-4">
                <p class="text-gray-600">A confirmation email has been sent to <span class="font-medium">{{ $order->shipping_address['email'] }}</span></p>
                
                <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('orders.show', $order->id) }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        View Order Details
                    </a>
                    <a href="{{ route('orders.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                        View All Orders
                    </a>
                    <a href="{{ route('products.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 