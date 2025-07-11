@extends('layouts.app')

@section('content')
<div class="bg-gray-50 py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <!-- Success Header -->
            <div class="bg-green-50 p-6 border-b border-green-100">
                <div class="flex items-center justify-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-extrabold text-gray-900">Order Successful!</h1>
                </div>
                <p class="mt-4 text-center text-gray-600">Your order has been placed successfully. Thank you for shopping with us!</p>
                
                @if(session('coupon_warning'))
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                {{ session('coupon_warning') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Order Details -->
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Order Details</h2>
                <div class="border border-gray-200 rounded-md overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between">
                        <div class="text-sm font-medium text-gray-500">Order Number</div>
                        <div class="text-sm font-bold text-gray-900">{{ $order->order_number }}</div>
                    </div>
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between">
                        <div class="text-sm font-medium text-gray-500">Date</div>
                        <div class="text-sm text-gray-900">{{ $order->created_at->format('F j, Y, g:i a') }}</div>
                    </div>
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between">
                        <div class="text-sm font-medium text-gray-500">Status</div>
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ ucfirst($order->status) }}
                        </div>
                    </div>
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between">
                        <div class="text-sm font-medium text-gray-500">Payment Status</div>
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($order->payment_status == 'paid')
                                bg-green-100 text-green-800
                            @elseif($order->payment_status == 'awaiting_payment')
                                bg-yellow-100 text-yellow-800
                            @elseif($order->payment_status == 'failed')
                                bg-red-100 text-red-800
                            @else
                                bg-gray-100 text-gray-800
                            @endif
                        ">
                            {{ ucwords(str_replace('_', ' ', $order->payment_status)) }}
                        </div>
                    </div>
                    <div class="px-4 py-3 border-b border-gray-200 flex justify-between">
                        <div class="text-sm font-medium text-gray-500">Payment Method</div>
                        <div class="text-sm text-gray-900">
                            @php
                                $paymentMethod = str_replace('_', ' ', $order->payment_method);
                                $paymentMethod = ucwords($paymentMethod);
                            @endphp
                            {{ $paymentMethod }}
                        </div>
                    </div>
                    <div class="px-4 py-3 flex justify-between">
                        <div class="text-sm font-medium text-gray-500">Total</div>
                        <div class="text-sm font-bold text-gray-900">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</div>
                    </div>
                </div>

                <!-- What's Next -->
                <div class="mt-8 bg-blue-50 p-4 rounded-md">
                    <h3 class="text-md font-medium text-blue-800 mb-2">What's Next?</h3>
                    <ul class="text-sm text-blue-700 space-y-2">
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            You will receive an order confirmation email shortly.
                        </li>
                        
                        @if($order->payment_status == 'awaiting_payment')
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Please complete your payment using the details provided. Your order will be processed once payment is confirmed.
                        </li>
                        @endif
                        
                        <!-- Payment method specific instructions -->
                        @if($order->payment_status == 'awaiting_payment')
                            @if($order->payment_method == 'bank_transfer')
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Please make your bank transfer using the bank details provided during checkout and include your order number as a reference.
                            </li>
                            @elseif(in_array($order->payment_method, ['instapay', 'vodafone_cash', 'stc_pay', 'benefit_pay']))
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                Send your payment to the number provided and include your order number {{ $order->order_number }} in the transaction notes.
                            </li>
                            @elseif($order->payment_method == 'fawry')
                            <li class="flex items-start">
                                <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                </svg>
                                Visit any Fawry outlet to complete your payment using the reference code provided during checkout.
                            </li>
                            @endif
                        @endif
                        
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                            Our team will process your order and update you on shipment.
                        </li>
                        @auth
                        <li class="flex items-start">
                            <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            You can track your order status in your account.
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>

            <!-- Payment Instructions (for awaiting payment) -->
            @if($order->payment_status == 'awaiting_payment')
            <div class="mt-6 p-4 border border-yellow-200 bg-yellow-50 rounded-lg">
                <h3 class="text-lg font-medium text-yellow-800 mb-2">Payment Instructions</h3>
                <p class="text-sm text-yellow-700 mb-3">Please complete your payment to process your order.</p>
                
                @if($order->payment_method == 'bank_transfer')
                <div class="bg-white p-3 rounded border border-yellow-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">Bank Transfer Instructions:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Make a transfer to the bank account provided during checkout</li>
                        <li>Include your order number <span class="font-medium">{{ $order->order_number }}</span> as reference</li>
                        <li>After making the payment, please allow up to 24 hours for verification</li>
                    </ul>
                </div>
                @elseif(in_array($order->payment_method, ['instapay', 'vodafone_cash']))
                <div class="bg-white p-3 rounded border border-yellow-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">Mobile Payment Instructions:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Send the payment to the number provided during checkout</li>
                        <li>Include your order number <span class="font-medium">{{ $order->order_number }}</span> in the payment notes</li>
                        <li>Take a screenshot of your payment confirmation for your records</li>
                    </ul>
                </div>
                @elseif($order->payment_method == 'fawry')
                <div class="bg-white p-3 rounded border border-yellow-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">Fawry Payment Instructions:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Visit any Fawry outlet with your reference code</li>
                        <li>Make the payment of {{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</li>
                        <li>Keep your receipt for reference</li>
                    </ul>
                </div>
                @elseif(in_array($order->payment_method, ['stc_pay', 'benefit_pay']))
                <div class="bg-white p-3 rounded border border-yellow-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">Payment Instructions:</p>
                    <ul class="text-sm text-gray-600 list-disc list-inside space-y-1">
                        <li>Open your payment app and make a transfer to the account details provided</li>
                        <li>Use your order number <span class="font-medium">{{ $order->order_number }}</span> as the reference</li>
                        <li>Your order will be processed once payment is confirmed</li>
                    </ul>
                </div>
                @endif
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 flex flex-col sm:flex-row sm:justify-between space-y-3 sm:space-y-0">
                @auth
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="h-5 w-5 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    View Order History
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="h-5 w-5 mr-2 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Login to Track Orders
                </a>
                @endauth
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 