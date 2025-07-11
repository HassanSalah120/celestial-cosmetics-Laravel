@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
@endphp

@section('content')
<div class="bg-background min-h-screen pt-16 pb-24">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="p-8 text-center border-b border-gray-200">
                    <div class="rounded-full bg-green-100 h-20 w-20 flex items-center justify-center mx-auto mb-4">
                        <svg class="h-10 w-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-display text-primary mb-2">{{ TranslationHelper::get('thank_you_order', 'Thank You for Your Order!') }}</h1>
                    <p class="text-xl text-gray-700 mb-4">{{ TranslationHelper::get('order_placed_successfully', 'Your order has been placed successfully.') }}</p>
                    <p class="text-gray-600">{{ TranslationHelper::get('order_number', 'Order #') }}: <span class="font-semibold">{{ $order->id }}</span></p>
                    <p class="text-gray-600">{{ TranslationHelper::get('confirmation_email_sent', 'A confirmation email has been sent to') }} <span class="font-semibold">{{ $order->shipping_address['email'] }}</span></p>
                </div>
                
                <div class="px-8 py-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ TranslationHelper::get('order_summary', 'Order Summary') }}</h2>
                    
                    <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ TranslationHelper::get('order_information', 'Order Information') }}</h3>
                            <div class="bg-gray-50 rounded p-4 space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ TranslationHelper::get('date_placed', 'Date Placed') }}:</span>
                                    <span class="font-medium">{{ $order->created_at->format('M d, Y, g:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ TranslationHelper::get('order_status', 'Order Status') }}:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ TranslationHelper::get('payment_method', 'Payment Method') }}:</span>
                                    <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ TranslationHelper::get('payment_status', 'Payment Status') }}:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                                @if($order->tracking_number)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">{{ TranslationHelper::get('tracking_number', 'Tracking #') }}:</span>
                                    <span class="font-medium">{{ $order->tracking_number }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ TranslationHelper::get('shipping_address', 'Shipping Address') }}</h3>
                            <div class="bg-gray-50 rounded p-4 space-y-1">
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
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Instructions for specific payment methods -->
                    @php
                        $paymentConfig = null;
                        try {
                            if (class_exists('\App\Models\PaymentConfig')) {
                                $paymentConfig = \App\Models\PaymentConfig::first();
                            }
                        } catch(\Exception $e) {
                            // Ignore if payment config doesn't exist
                        }
                    @endphp
                    
                    @if($order->payment_status !== 'paid')
                        @if($order->payment_method == 'bank_transfer' && $paymentConfig)
                            <div class="mb-6 bg-blue-50 border border-blue-100 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-800 mb-2">{{ TranslationHelper::get('bank_transfer_instructions', 'Bank Transfer Instructions') }}</h3>
                                <div class="space-y-3 text-sm text-blue-700">
                                    <p class="font-medium">{{ TranslationHelper::get('account_details', 'Account Details') }}:</p>
                                    <pre class="whitespace-pre-wrap">{{ $paymentConfig->bank_account_details }}</pre>
                                    
                                    <p class="font-medium mt-2">{{ TranslationHelper::get('instructions', 'Instructions') }}:</p>
                                    <p>{{ $paymentConfig->bank_transfer_instructions }}</p>
                                    
                                    <p class="bg-blue-100 p-2 rounded text-blue-900 mt-2">
                                        <strong>{{ TranslationHelper::get('important', 'Important') }}:</strong> 
                                        {{ TranslationHelper::get('reference_order_number', 'Please use your Order Number as payment reference') }}: 
                                        <strong>{{ $order->order_number }}</strong>
                                    </p>
                                </div>
                            </div>
                        @elseif($order->payment_method == 'fawry' && $paymentConfig && $paymentConfig->fawry_code)
                            <div class="mb-6 bg-orange-50 border border-orange-100 rounded-lg p-4">
                                <h3 class="font-semibold text-orange-800 mb-2">{{ TranslationHelper::get('fawry_payment_instructions', 'Fawry Payment Instructions') }}</h3>
                                <div class="space-y-3 text-sm text-orange-700">
                                    <p>{{ TranslationHelper::get('fawry_instructions', 'Please visit any Fawry terminal or use the Fawry app and use the following code to complete your payment:') }}</p>
                                    <p class="bg-white p-3 text-center rounded border border-orange-200">
                                        <span class="text-lg font-bold">{{ $paymentConfig->fawry_code }}</span>
                                    </p>
                                    <p>{{ TranslationHelper::get('payment_confirmation', 'After making your payment, please contact us with your order number to confirm your payment.') }}</p>
                                </div>
                            </div>
                        @elseif(in_array($order->payment_method, ['stc_pay', 'benefit_pay']) && $paymentConfig)
                            @php
                                $colorClass = $order->payment_method == 'stc_pay' ? 'purple' : 'green';
                                $number = $order->payment_method == 'stc_pay' ? $paymentConfig->stc_pay_number : $paymentConfig->benefit_pay_number;
                                $methodName = $order->payment_method == 'stc_pay' ? 'STC Pay' : 'Benefit Pay';
                            @endphp
                            <div class="mb-6 bg-{{ $colorClass }}-50 border border-{{ $colorClass }}-100 rounded-lg p-4">
                                <h3 class="font-semibold text-{{ $colorClass }}-800 mb-2">{{ $methodName }} {{ TranslationHelper::get('payment_instructions', 'Payment Instructions') }}</h3>
                                <div class="space-y-3 text-sm text-{{ $colorClass }}-700">
                                    <p>{{ TranslationHelper::get('transfer_instructions', 'Please transfer the total amount to the following number:') }}</p>
                                    <p class="bg-white p-3 text-center rounded border border-{{ $colorClass }}-200">
                                        <span class="text-lg font-bold">{{ $number }}</span>
                                    </p>
                                    <p>{{ TranslationHelper::get('payment_reference', 'Please include your Order Number as payment reference') }}: <strong>{{ $order->order_number }}</strong></p>
                                    <p>{{ TranslationHelper::get('payment_confirmation', 'After making your payment, please contact us with your order number to confirm your payment.') }}</p>
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">{{ TranslationHelper::get('order_items', 'Order Items') }}</h3>
                    <div class="border rounded-lg overflow-hidden mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('product', 'Product') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('price', 'Price') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('quantity', 'Quantity') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('subtotal', 'Subtotal') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0">
                                                    @if($item->product && $item->product->featured_image)
                                                        <img class="h-10 w-10 object-cover rounded" src="{{ asset('storage/' . $item->product->featured_image) }}" alt="{{ $item->product->name }}">
                                                    @else
                                                        <div class="h-10 w-10 bg-gray-200 rounded"></div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        @if($item->product)
                                                            {{ $item->product->name }}
                                                        @else
                                                            {{ TranslationHelper::get('product_not_available', 'Product Not Available') }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($item->price, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $item->quantity }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($item->subtotal, 2) }}</div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('subtotal', 'Subtotal') }}:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($order->subtotal, 2) }}</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-green-600">{{ TranslationHelper::get('discount', 'Discount') }}{{ $order->coupon_code ? ' ('.$order->coupon_code.')' : '' }}:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">-{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($order->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('shipping', 'Shipping') }} ({{ ucfirst($order->shipping_method) }}):</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($order->shipping_fee, 2) }}</td>
                                </tr>
                                @if($order->cod_fee > 0)
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('cod_fee', 'Cash on Delivery Fee') }}:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($order->cod_fee, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-right text-sm font-medium text-gray-900">{{ TranslationHelper::get('total', 'Total') }}:</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-primary">{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }} {{ number_format($order->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:justify-between items-center space-y-4 sm:space-y-0">
                        <a href="{{ route('orders.show', $order->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            {{ TranslationHelper::get('view_order_details', 'View Order Details') }}
                        </a>
                        <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg class="h-5 w-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            {{ TranslationHelper::get('view_all_orders', 'View All Orders') }}
                        </a>
                        <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                            {{ TranslationHelper::get('continue_shopping', 'Continue Shopping') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 