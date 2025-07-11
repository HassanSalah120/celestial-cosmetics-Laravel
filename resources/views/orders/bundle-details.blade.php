@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Bundle Details</h1>
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Order
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ $orderItem->offer->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                            <p><strong>Bundle Price:</strong> {{ \App\Helpers\SettingsHelper::formatPrice($orderItem->price) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Quantity:</strong> {{ $orderItem->quantity }}</p>
                            <p><strong>Subtotal:</strong> {{ \App\Helpers\SettingsHelper::formatPrice($orderItem->price * $orderItem->quantity) }}</p>
                        </div>
                    </div>
                    
                    @if($orderItem->offer->description)
                    <div class="mb-4">
                        <h5>Bundle Description</h5>
                        <p>{{ $orderItem->offer->description }}</p>
                    </div>
                    @endif
                    
                    <h5>Products in this Bundle</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity per Bundle</th>
                                    <th>Total Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orderItem->offer->products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($product->featured_image)
                                            <img src="{{ asset('storage/' . $product->featured_image) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="img-thumbnail me-3" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                                @if($product->sku)
                                                <br><small class="text-muted">SKU: {{ $product->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $product->pivot->quantity }}</td>
                                    <td>{{ $product->pivot->quantity * $orderItem->quantity }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 