@extends('layouts.app')

@php
function debug_var($var) {
    if (is_array($var)) {
        return json_encode($var);
    } elseif (is_object($var) && !method_exists($var, '__toString')) {
        return get_class($var);
    } else {
        return (string)$var;
    }
}
@endphp

@section('meta_tags')
    <title>Category - {{ debug_var($category->name ?? 'Not Found') }}</title>
    <meta name="description" content="Category page">
@endsection

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ debug_var($category->name ?? 'Category') }}</h1>
    
    @if(config('app.debug'))
    <div class="bg-red-100 p-4 rounded mb-6">
        <h2 class="font-bold mb-2">Debug Information:</h2>
        <ul class="list-disc pl-5">
            <li>Products type: {{ gettype($products) }}</li>
            <li>Products class: {{ is_object($products) ? get_class($products) : 'Not an object' }}</li>
            <li>Products count: {{ is_object($products) && method_exists($products, 'count') ? $products->count() : 'N/A' }}</li>
            <li>Products total: {{ is_object($products) && method_exists($products, 'total') ? debug_var($products->total()) : 'N/A' }}</li>
            <li>Category type: {{ gettype($category) }}</li>
            <li>Category class: {{ is_object($category) ? get_class($category) : 'Not an object' }}</li>
        </ul>
        
        <h3 class="font-bold mt-4 mb-2">Category Properties:</h3>
        <ul class="list-disc pl-5">
            @if(is_object($category))
                @foreach($category->getAttributes() as $key => $value)
                    <li>{{ $key }}: {{ gettype($value) }} {{ is_array($value) ? '(Array)' : debug_var($value) }}</li>
                @endforeach
            @endif
        </ul>
    </div>
    @endif
    
    <div class="bg-white shadow rounded-lg p-6">
        @if(is_object($products) && method_exists($products, 'count') && $products->count() > 0)
            <p class="mb-4">Found {{ is_object($products) && method_exists($products, 'total') ? debug_var($products->total()) : '0' }} products</p>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $product)
                    <div class="border rounded p-4">
                        <h3 class="font-bold">{{ debug_var($product->name ?? 'Product') }}</h3>
                        <p>Price: {{ debug_var($product->price ?? '0.00') }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p>No products found in this category.</p>
        @endif
    </div>
</div>
@endsection 