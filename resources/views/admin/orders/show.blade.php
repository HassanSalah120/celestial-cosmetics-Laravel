@extends('layouts.admin')

@section('content')
<div class="container px-6 mx-auto grid">
    <div class="flex justify-between items-center my-6">
        <h2 class="text-2xl font-semibold text-gray-700">
            Order #{{ !empty($order->order_number) ? $order->order_number : $order->id }}
        </h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                Back to Orders
            </a>
            <a href="{{ route('admin.orders.edit', $order) }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                Edit Order
            </a>
            <a href="{{ route('admin.print.order', $order->id) }}" target="_blank" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700">
                <i class="fas fa-print mr-1"></i> Print Order
            </a>
            @if($order->tracking_number)
            <a href="{{ route('admin.orders.shipping-label', $order) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Print Shipping Label
            </a>
            @endif
            @if(!$order->tracking_number)
            <form method="POST" action="{{ route('admin.orders.generate-tracking', $order) }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Generate Tracking
                </button>
            </form>
            @endif
        </div>
    </div>
    
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 col-span-2">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Order Summary</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Order ID</p>
                    <p class="text-gray-800">#{{ !empty($order->order_number) ? $order->order_number : $order->id }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Order Date</p>
                    <p class="text-gray-800">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Order Status</p>
                    <p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'processing' => 'bg-blue-100 text-blue-800',
                                'shipped' => 'bg-indigo-100 text-indigo-800',
                                'delivered' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                            $statusColor = $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Payment Status</p>
                    <p>
                        @php
                            $paymentStatusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'paid' => 'bg-green-100 text-green-800',
                                'failed' => 'bg-red-100 text-red-800',
                                'refunded' => 'bg-gray-100 text-gray-800',
                            ];
                            $paymentStatusColor = $paymentStatusColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $paymentStatusColor }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Payment Method</p>
                    <p class="text-gray-800">{{ ucfirst($order->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Tracking Number</p>
                    @if($order->tracking_number)
                        <p class="text-gray-800 font-semibold bg-green-50 p-2 rounded border border-green-200">
                            {{ $order->tracking_number }}
                        </p>
                    @else
                        <p class="text-gray-500 italic">Not available</p>
                    @endif
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
                <p class="text-gray-600">Guest customer</p>
                @if(is_array($order->shipping_address))
                <div class="mt-2">
                    <p class="font-semibold text-gray-700">{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_address['email'] ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_address['phone'] ?? '' }}</p>
                </div>
                @else
                <div class="mt-2">
                    <p class="font-semibold text-gray-700">{{ $order->shipping_first_name ?? '' }} {{ $order->shipping_last_name ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_email ?? '' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->shipping_phone ?? '' }}</p>
                </div>
                @endif
            @endif
        </div>
    </div>
    
    <!-- Shipping Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Shipping Information</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Shipping Address</h4>
                @if(is_array($order->shipping_address))
                <p>{{ $order->shipping_address['first_name'] ?? '' }} {{ $order->shipping_address['last_name'] ?? '' }}</p>
                <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                @if(isset($order->shipping_address['address_line2']) && $order->shipping_address['address_line2'])
                    <p>{{ $order->shipping_address['address_line2'] }}</p>
                @endif
                <p>{{ $order->shipping_address['city'] ?? '' }}{{ isset($order->shipping_address['state']) && $order->shipping_address['state'] ? ', ' . $order->shipping_address['state'] : '' }} {{ $order->shipping_address['postal_code'] ?? '' }}</p>
                <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                <p class="mt-2">{{ $order->shipping_address['phone'] ?? '' }}</p>
                <p>{{ $order->shipping_address['email'] ?? '' }}</p>
                @else
                <p>{{ $order->shipping_first_name ?? '' }} {{ $order->shipping_last_name ?? '' }}</p>
                <p>{{ $order->shipping_address_line1 ?? '' }}</p>
                @if($order->shipping_address_line2)
                    <p>{{ $order->shipping_address_line2 }}</p>
                @endif
                <p>{{ $order->shipping_city ?? '' }}{{ $order->shipping_state ? ', ' . $order->shipping_state : '' }} {{ $order->shipping_postal_code ?? '' }}</p>
                <p>{{ $order->shipping_country ?? '' }}</p>
                <p class="mt-2">{{ $order->shipping_phone ?? '' }}</p>
                <p>{{ $order->shipping_email ?? '' }}</p>
                @endif
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Shipping Details</h4>
                <p><span class="text-gray-600">Method:</span> {{ ucfirst($order->shipping_method) }}</p>
                <p><span class="text-gray-600">Cost:</span> {{ \App\Helpers\SettingsHelper::formatPrice(
                    !empty($order->shipping_cost) ? $order->shipping_cost : ($order->shipping_fee ?? 0)
                ) }}</p>
                @if($order->tracking_number)
                    <p class="mt-4"><span class="text-gray-600">Tracking Number:</span></p>
                    <p class="font-medium bg-blue-50 p-2 rounded border border-blue-200 mt-1">{{ $order->tracking_number }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Order Items -->
    <div class="bg-white rounded-lg shadow-md mb-8">
        <h3 class="text-lg font-semibold text-gray-700 p-6 border-b">Order Items</h3>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-6 py-3">Item</th>
                        <th class="px-6 py-3">Type</th>
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
                                        @if($item->type === 'product' && $item->product && $item->product->image)
                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="object-cover w-full h-full rounded">
                                        @elseif($item->type === 'offer' && $item->offer && $item->offer->image)
                                            <img src="{{ asset('storage/' . $item->offer->image) }}" alt="{{ $item->offer->title }}" class="object-cover w-full h-full rounded">
                                        @else
                                            <div class="absolute inset-0 rounded shadow-inner bg-gray-200 flex items-center justify-center text-gray-500">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        @if($item->type === 'product')
                                            <p class="font-semibold">{{ $item->product ? $item->product->name : $item->name }}</p>
                                            @if($item->product && $item->product->category)
                                                <p class="text-xs text-gray-600">
                                                    {{ $item->product->category->name }}
                                                </p>
                                            @endif
                                        @elseif($item->type === 'offer')
                                            <p class="font-semibold">{{ $item->offer ? $item->offer->title : $item->name }}</p>
                                            <p class="text-xs text-gray-600">Special Offer</p>
                                        @else
                                            <p class="font-semibold">{{ $item->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $item->type === 'product' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ ucfirst($item->type) }}
                                </span>
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
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold">Subtotal</td>
                        <td class="px-6 py-4 font-semibold">{{ \App\Helpers\SettingsHelper::formatPrice(
                            !empty($order->subtotal) ? $order->subtotal : \App\Http\Controllers\Admin\OrderController::getOrderSubtotal($order)
                        ) }}</td>
                    </tr>
                    @php
                        $discount = !empty($order->discount) ? $order->discount : ($order->discount_amount ?? 0);
                    @endphp
                    @if($discount > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold">Discount
                            @if($order->coupon_code)
                            <span class="text-xs text-gray-500 ml-1">(Coupon: {{ $order->coupon_code }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-green-600">-{{ \App\Helpers\SettingsHelper::formatPrice($discount) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold">Shipping</td>
                        <td class="px-6 py-4 font-semibold">{{ \App\Helpers\SettingsHelper::formatPrice(
                            !empty($order->shipping_cost) ? $order->shipping_cost : ($order->shipping_fee ?? 0)
                        ) }}</td>
                    </tr>
                    @if($order->payment_fee > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold">Payment Fee ({{ ucfirst($order->payment_method) }})</td>
                        <td class="px-6 py-4 font-semibold">{{ \App\Helpers\SettingsHelper::formatPrice($order->payment_fee) }}</td>
                    </tr>
                    @endif
                    @if($order->cod_fee > 0)
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right font-semibold">COD Fee</td>
                        <td class="px-6 py-4 font-semibold">{{ \App\Helpers\SettingsHelper::formatPrice($order->cod_fee) }}</td>
                    </tr>
                    @endif
                    <tr class="border-t-2 border-gray-300">
                        <td colspan="4" class="px-6 py-4 text-right font-bold text-lg">Total</td>
                        <td class="px-6 py-4 font-bold text-lg">{{ \App\Helpers\SettingsHelper::formatPrice(
                            !empty($order->total) ? $order->total : ($order->total_amount ?? 0)
                        ) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <!-- Addresses -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Shipping Address -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Shipping Address</h3>
            
            @if(is_array($order->shipping_address))
                <address class="not-italic">
                    <p class="font-medium">{{ $order->shipping_address['name'] ?? 'Name not available' }}</p>
                    <p>{{ $order->shipping_address['address_line1'] ?? '' }}</p>
                    @if(isset($order->shipping_address['address_line2']) && $order->shipping_address['address_line2'])
                        <p>{{ $order->shipping_address['address_line2'] }}</p>
                    @endif
                    <p>
                        {{ $order->shipping_address['city'] ?? '' }}, 
                        {{ $order->shipping_address['state'] ?? '' }} 
                        {{ $order->shipping_address['postal_code'] ?? '' }}
                    </p>
                    <p>{{ $order->shipping_address['country'] ?? '' }}</p>
                    @if(isset($order->shipping_address['phone']) && $order->shipping_address['phone'])
                        <p class="mt-2">Phone: {{ $order->shipping_address['phone'] }}</p>
                    @endif
                </address>
            @else
                <p class="text-gray-600">Shipping address data unavailable</p>
            @endif
        </div>
        
        <!-- Billing Address -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 border-b pb-2">Billing Address</h3>
            
            @if(is_array($order->billing_address))
                <address class="not-italic">
                    <p class="font-medium">{{ $order->billing_address['name'] ?? 'Name not available' }}</p>
                    <p>{{ $order->billing_address['address_line1'] ?? '' }}</p>
                    @if(isset($order->billing_address['address_line2']) && $order->billing_address['address_line2'])
                        <p>{{ $order->billing_address['address_line2'] }}</p>
                    @endif
                    <p>
                        {{ $order->billing_address['city'] ?? '' }}, 
                        {{ $order->billing_address['state'] ?? '' }} 
                        {{ $order->billing_address['postal_code'] ?? '' }}
                    </p>
                    <p>{{ $order->billing_address['country'] ?? '' }}</p>
                    @if(isset($order->billing_address['phone']) && $order->billing_address['phone'])
                        <p class="mt-2">Phone: {{ $order->billing_address['phone'] }}</p>
                    @endif
                </address>
            @else
                <p class="text-gray-600">Billing address data unavailable</p>
            @endif
        </div>
    </div>
</div>
@endsection 