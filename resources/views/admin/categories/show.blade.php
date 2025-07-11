@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">{{ $category->name }}</h2>
            <p class="mt-1 text-sm text-gray-600">Category created {{ $category->created_at->diffForHumans() }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Category
            </a>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Categories
            </a>
        </div>
    </div>

    <!-- Category Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Category Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-1">
            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Category Information</h3>
            
            @if($category->image)
                <div class="mb-6">
                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="w-full h-48 object-cover rounded-lg shadow-sm">
                </div>
            @endif
            
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Category Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Slug</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded inline-block">{{ $category->slug }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Products</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $products->total() }} products in this category</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('F j, Y \a\t g:i a') }}</dd>
                </div>
                
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last Modified</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('F j, Y \a\t g:i a') }}</dd>
                </div>
            </dl>
            
            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Description</h4>
                <div class="text-sm text-gray-600 prose max-w-none">
                    {!! $category->description ? nl2br(e($category->description)) : '<p class="text-gray-400 italic">No description provided</p>' !!}
                </div>
            </div>
        </div>

        <!-- Products in this Category -->
        <div class="bg-white rounded-lg shadow-sm p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h3 class="text-lg font-medium text-gray-900">Products in this Category</h3>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-3 py-1 bg-accent hover:bg-accent-light text-white text-sm rounded-md transition-colors duration-200">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Product
                </a>
            </div>
            
            @if($products->count() > 0)
                <div class="space-y-4">
                    @foreach($products as $product)
                        <div class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                            <div class="flex-shrink-0 h-12 w-12 bg-gray-200 rounded-md overflow-hidden">
                                @if($product->featured_image)
                                    <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-gray-400">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                <div class="mt-1 flex items-center">
                                    <p class="text-sm text-gray-500">{{ $product->price_formatted }}</p>
                                    @if($product->is_featured)
                                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-primary-light text-primary rounded-full">Featured</span>
                                    @endif
                                    @if(!$product->is_active)
                                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('admin.products.edit', $product) }}" class="text-primary hover:text-primary-dark transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                    
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <div class="py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h4 class="mt-2 text-sm font-medium text-gray-900">No products in this category</h4>
                    <p class="mt-1 text-sm text-gray-500">Add products to this category to see them listed here.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-md transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create a New Product
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 