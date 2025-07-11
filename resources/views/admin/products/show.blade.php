@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Product Details</h2>
            <p class="text-gray-500">Viewing details for {{ $product->name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition">
                <i class="fas fa-edit mr-2"></i>Edit Product
            </a>
            <a href="{{ route('admin.print.product', $product->id) }}" target="_blank" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition">
                <i class="fas fa-print mr-2"></i>Print Product
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Product Image -->
        <div class="md:col-span-1 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Product Image</h3>
                <div class="rounded-lg overflow-hidden border border-gray-200">
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full object-cover">
                    @else
                        <div class="bg-gray-100 h-64 flex items-center justify-center">
                            <i class="fas fa-image text-gray-400 text-4xl"></i>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="md:col-span-2 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Product Information</h3>
                <div class="space-y-4">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Name</label>
                            <div class="mt-1 text-gray-900">{{ $product->name }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                            <div class="mt-1 text-gray-900">{{ $product->slug }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price</label>
                            <div class="mt-1 text-gray-900">
                                @if($product->discount_percent > 0)
                                    <span class="line-through text-gray-500">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</span>
                                    <span class="text-green-600 font-medium ml-1">{{ \App\Helpers\SettingsHelper::formatPrice($product->price * (1 - $product->discount_percent / 100)) }}</span>
                                @else
                                    {{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Discount</label>
                            <div class="mt-1 text-gray-900">
                                @if($product->discount_percent > 0)
                                    <span class="text-green-600 font-medium">{{ $product->discount_percent }}%</span>
                                @else
                                    -
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <div class="mt-1 text-gray-900">{{ $product->category ? $product->category->name : 'Uncategorized' }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock</label>
                            <div class="mt-1">
                                @if($product->stock > 0)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">In Stock ({{ $product->stock }})</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Out of Stock</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <div class="mt-1">
                                @if($product->is_visible)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Featured</label>
                            <div class="mt-1">
                                @if($product->is_featured)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Featured</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Not Featured</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <div class="mt-1 prose max-w-full">
                            {!! $product->description !!}
                        </div>
                    </div>

                    <!-- Ingredients -->
                    @if($product->ingredients)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Ingredients</label>
                        <div class="mt-1 prose max-w-full">
                            {!! $product->ingredients !!}
                        </div>
                    </div>
                    @endif

                    <!-- How to Use -->
                    @if($product->how_to_use)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">How to Use</label>
                        <div class="mt-1 prose max-w-full">
                            {!! $product->how_to_use !!}
                        </div>
                    </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Created</label>
                            <div class="mt-1 text-gray-900">{{ $product->created_at->format('F j, Y g:i A') }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                            <div class="mt-1 text-gray-900">{{ $product->updated_at->format('F j, Y g:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Images -->
        @if($product->images->count() > 0)
        <div class="md:col-span-3 bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Gallery Images</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($product->images as $image)
                    <div class="rounded-lg overflow-hidden border border-gray-200">
                        <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover">
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 