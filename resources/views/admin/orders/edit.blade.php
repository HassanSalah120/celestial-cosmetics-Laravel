@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-700">
            Edit Order #{{ $order->id }}
        </h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.orders.show', $order) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                View Order
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                Back to Orders
            </a>
        </div>
    </div>
    
    <!-- Order Update Form -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-6 border-b pb-2">Update Order Status</h3>
        
        <form action="{{ route('admin.orders.update', $order) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Order Status</label>
                    <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @foreach($orderStatuses as $status)
                            <option value="{{ $status }}" @if($order->status === $status) selected @endif>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Payment Status -->
                <div>
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select name="payment_status" id="payment_status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @foreach($paymentStatuses as $status)
                            <option value="{{ $status }}" @if($order->payment_status === $status) selected @endif>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('payment_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Tracking Number -->
                <div class="md:col-span-2">
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 mb-2">Tracking Number</label>
                    <div class="flex space-x-3">
                        <input type="text" name="tracking_number" id="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Enter tracking number if available">
                    </div>
                    <div class="mt-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="generate_tracking" id="generate_tracking" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                            <label for="generate_tracking" class="ml-2 text-sm text-gray-700">Generate random tracking number (will overwrite any manually entered number)</label>
                        </div>
                    </div>
                    @error('tracking_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-3 bg-primary text-white rounded-md hover:bg-primary-dark">
                    Update Order
                </button>
            </div>
        </form>
    </div>
    
    <!-- Order Information (Read-only) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Order Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Order ID:</span>
                    <span>#{{ $order->id }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Order Date:</span>
                    <span>{{ $order->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Payment Method:</span>
                    <span>{{ ucfirst($order->payment_method) }}</span>
                </div>
                <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
                    <span class="font-medium">Total Amount:</span>
                    <span class="font-bold">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Customer Information</h3>
            
            @if($order->user)
                <div class="flex items-center mb-4">
                    <div class="relative w-10 h-10 mr-3 rounded-full">
                        @if($order->user->profile_image)
                            <img src="{{ asset('storage/' . $order->user->profile_image) }}" alt="{{ $order->user->name }}" class="object-cover w-full h-full rounded-full">
                        @else
                            <div class="absolute inset-0 rounded-full shadow-inner bg-primary text-white flex items-center justify-center">
                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="font-semibold text-gray-700">{{ $order->user->name }}</p>
                        <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('admin.users.show', $order->user) }}" class="text-blue-500 hover:underline text-sm">
                        View Customer Profile
                    </a>
                </div>
            @else
                <p class="text-gray-600">Guest customer or user data unavailable</p>
            @endif
        </div>
    </div>
    
    <!-- Order Items (Read-only) -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <h3 class="text-lg font-semibold text-gray-700 p-6 border-b">Order Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-6 py-3">Product</th>
                        <th class="px-6 py-3">Unit Price</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y bg-white">
                    @foreach($order->items as $item)
                        <tr class="text-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="relative hidden w-12 h-12 mr-3 md:block">
                                        @if($item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="object-cover w-full h-full rounded">
                                        @else
                                            <div class="absolute inset-0 rounded shadow-inner bg-gray-200 flex items-center justify-center text-gray-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $item->product ? $item->product->name : 'Product not available' }}</p>
                                        @if($item->product)
                                            <p class="text-xs text-gray-600">
                                                {{ $item->product->category ? $item->product->category->name : 'No category' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ \App\Helpers\SettingsHelper::formatPrice($item->price) }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                {{ \App\Helpers\SettingsHelper::formatPrice($item->subtotal) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    @if($order->cod_fee > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold">Subtotal</td>
                        <td class="px-6 py-4 font-semibold">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount - $order->cod_fee) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold">Cash on Delivery Fee</td>
                        <td class="px-6 py-4 font-semibold">{{ \App\Helpers\SettingsHelper::formatPrice($order->cod_fee) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-semibold">Total</td>
                        <td class="px-6 py-4 font-bold text-lg">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection 