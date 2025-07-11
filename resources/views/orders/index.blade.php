@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
@endphp

@section('content')
<div class="bg-background min-h-screen pt-16 pb-24">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-display text-primary">{{ TranslationHelper::get('my_orders', 'My Orders') }}</h1>
                <a href="{{ route('products.index') }}" class="inline-flex items-center text-sm font-medium text-accent hover:text-accent-dark">
                    <svg class="w-4 h-4 {{ is_rtl() ? 'ml-1' : 'mr-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    {{ TranslationHelper::get('continue_shopping', 'Continue Shopping') }}
                </a>
            </div>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            
            @if(count($orders) > 0)
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('order_details', 'Order Details') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('payment_info', 'Payment Info') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('items', 'Items') }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ TranslationHelper::get('actions', 'Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900 mb-1">{{ TranslationHelper::get('order_number', 'Order #') }}{{ $order->id }}</div>
                                            <div class="text-sm text-gray-500 mb-1">{{ $order->created_at->format('M d, Y') }} {{ TranslationHelper::get('at', 'at') }} {{ $order->created_at->format('g:i A') }}</div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                                {{ TranslationHelper::get('status_' . $order->status, ucfirst($order->status)) }}
                                            </span>
                                            <div class="text-sm font-medium text-gray-900 mt-2">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 mb-2">
                                                <span class="font-medium">{{ TranslationHelper::get('method', 'Method') }}:</span> {{ TranslationHelper::get('payment_method_' . $order->payment_method, ucfirst(str_replace('_', ' ', $order->payment_method))) }}
                                            </div>
                                            <div class="text-sm text-gray-900">
                                                <span class="font-medium">{{ TranslationHelper::get('status', 'Status') }}:</span> 
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
                                                <div class="text-sm text-gray-900 mt-2">
                                                    <span class="font-medium">{{ TranslationHelper::get('tracking', 'Tracking') }}:</span> {{ $order->tracking_number }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 mb-2">{{ $order->items->sum('quantity') }} {{ TranslationHelper::get('items', 'items') }}</div>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($order->items->take(3) as $item)
                                                    <div class="relative group">
                                                        @if($item->type == 'product' && $item->product && $item->product->featured_image)
                                                            <img src="{{ asset('storage/' . $item->product->featured_image) }}" alt="{{ $item->product->name }}" class="h-10 w-10 object-cover rounded-md">
                                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 absolute bottom-full left-0 transform mb-2 w-48 bg-gray-900 text-white text-xs rounded p-2 pointer-events-none">
                                                                {{ $item->product->name }} ({{ $item->quantity }}x)
                                                                <div class="text-xs text-gray-300">{{ TranslationHelper::get('price', 'Price') }}:</div>
                                                                <div class="text-xs font-medium">{{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}</div>
                                                            </div>
                                                        @elseif($item->type == 'offer' && $item->offer && $item->offer->image)
                                                            <img src="{{ asset('storage/' . str_replace('storage/', '', $item->offer->image)) }}" alt="{{ $item->offer->title }}" class="h-10 w-10 object-cover rounded-md">
                                                            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 absolute bottom-full left-0 transform mb-2 w-48 bg-gray-900 text-white text-xs rounded p-2 pointer-events-none">
                                                                {{ $item->offer->title }} ({{ $item->quantity }}x)
                                                                <div class="text-xs text-gray-300">{{ TranslationHelper::get('special_offer', 'Special Offer') }}</div>
                                                                <div class="text-xs font-medium">{{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}</div>
                                                            </div>
                                                        @else
                                                            <div class="h-10 w-10 bg-gray-200 rounded-md flex items-center justify-center">
                                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if($order->items->count() > 3)
                                                    <div class="h-10 w-10 bg-gray-100 rounded-md flex items-center justify-center text-xs text-gray-500">
                                                        +{{ $order->items->count() - 3 }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('orders.show', $order->id) }}" class="text-accent hover:text-accent-dark inline-flex items-center">
                                                <span>{{ TranslationHelper::get('view_details', 'View Details') }}</span>
                                                <svg class="w-4 h-4 {{ is_rtl() ? 'mr-1' : 'ml-1' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md overflow-hidden p-8 text-center">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <svg class="h-20 w-20 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-gray-900">{{ TranslationHelper::get('no_orders_yet', 'You haven\'t placed any orders yet') }}</h2>
                        <p class="text-gray-600 max-w-md">{{ TranslationHelper::get('start_shopping_description', 'Start shopping our collection of celestial-inspired cosmetics to see your orders here.') }}</p>
                        <a href="{{ route('products.index') }}" class="mt-2 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-accent hover:bg-accent-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                            {{ TranslationHelper::get('browse_products', 'Browse Products') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 