@extends('layouts.admin')

@php
use Illuminate\Support\Str;
@endphp

@section('title', 'Edit Product SEO - ' . $product->name)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Edit Product SEO</h1>
    <nav class="flex mb-5" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-primary hover:text-primary-dark">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('admin.seo.index') }}" class="ml-1 text-sm font-medium text-primary hover:text-primary-dark md:ml-2">SEO Management</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('admin.seo.products') }}" class="ml-1 text-sm font-medium text-primary hover:text-primary-dark md:ml-2">Product SEO</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $product->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex flex-wrap items-center justify-between">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                </svg>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit SEO for: {{ $product->name }}</h3>
            </div>
            <div>
                <a href="{{ route('admin.seo.products') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Products
                </a>
            </div>
        </div>
        <div class="px-4 py-5 sm:p-6">
            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="mb-6">
                <div class="bg-gray-50 rounded-lg shadow-sm p-4">
                    <div class="flex flex-col md:flex-row">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                 class="h-24 w-24 object-cover rounded-md flex-shrink-0">
                        @else
                            <div class="bg-white h-24 w-24 rounded-md flex items-center justify-center flex-shrink-0">
                                <svg class="h-10 w-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="mt-4 md:mt-0 md:ml-6">
                            <h4 class="text-lg font-medium text-gray-900">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-500 mb-2">{{ $product->slug }}</p>
                            <div class="flex space-x-2 mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_visible ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $product->is_visible ? 'Visible' : 'Hidden' }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $product->stock_status === 'in_stock' ? 'bg-green-100 text-green-800' : 
                                        ($product->stock_status === 'out_of_stock' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst(str_replace('_', ' ', $product->stock_status)) }}
                                </span>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.products.edit', $product->id) }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Product
                                </a>
                                <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    View Product
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('admin.seo.update-product', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 pb-2 border-b border-gray-200 mb-4">Search Engine Optimization</h3>
                        
                        <div class="mb-4">
                            <label for="meta_title" class="block text-sm font-medium text-gray-700 mb-1">Meta Title</label>
                            <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   id="meta_title" name="meta_title" 
                                   value="{{ old('meta_title', $product->meta_title) }}" maxlength="70">
                            <div id="titleCounter" class="mt-1 text-sm text-gray-500">
                                <span>Recommended: 50-60 characters. </span>
                                <span class="chars-count">{{ strlen(old('meta_title', $product->meta_title)) }}</span>/70
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                Default: {{ $product->seo_title }}
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-1">Meta Description</label>
                            <textarea class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                      id="meta_description" name="meta_description" 
                                      rows="3" maxlength="160">{{ old('meta_description', $product->meta_description) }}</textarea>
                            <div id="descriptionCounter" class="mt-1 text-sm text-gray-500">
                                <span>Recommended: 150-160 characters. </span>
                                <span class="chars-count">{{ strlen(old('meta_description', $product->meta_description)) }}</span>/160
                            </div>
                            <div class="mt-1 text-sm text-gray-500">
                                Default: {{ Str::limit($product->seo_description, 100) }}
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Meta Keywords</label>
                            <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   id="meta_keywords" name="meta_keywords" 
                                   value="{{ old('meta_keywords', $product->meta_keywords) }}">
                            <div class="mt-1 text-sm text-gray-500">Separate keywords with commas</div>
                        </div>
                        
                        <div class="mt-6 mb-4">
                            <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-700">Search Preview</h3>
                                </div>
                                <div class="p-4">
                                    <div id="seoPreview">
                                        <div class="text-blue-700 text-base font-medium mb-1" id="previewTitle">
                                            {{ $product->meta_title ?: $product->seo_title }}
                                        </div>
                                        <div class="text-green-700 text-sm mb-1" id="previewUrl">
                                            {{ route('products.show', $product->slug) }}
                                        </div>
                                        <div class="text-gray-600 text-sm" id="previewDescription">
                                            {{ $product->meta_description ?: $product->seo_description }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 pb-2 border-b border-gray-200 mb-4">Social Media Optimization</h3>
                        
                        <div class="mb-4">
                            <label for="og_image" class="block text-sm font-medium text-gray-700 mb-1">Open Graph Image</label>
                            <input type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-primary-dark" 
                                   accept="image/*"
                                   id="og_image" name="og_image">
                            <div class="mt-1 text-sm text-gray-500">Recommended size: 1200×630 pixels</div>
                            
                            @if($product->og_image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->og_image) }}" alt="OG Image" 
                                         class="h-32 w-auto object-cover rounded-md border border-gray-200">
                                    <div class="mt-1 text-sm text-gray-500">Current Open Graph image</div>
                                </div>
                            @elseif($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" 
                                         class="h-32 w-auto object-cover rounded-md border border-gray-200">
                                    <div class="mt-1 text-sm text-gray-500">Default: Using product image</div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <label for="twitter_card_type" class="block text-sm font-medium text-gray-700 mb-1">Twitter Card Type</label>
                            <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md" 
                                    id="twitter_card_type" name="twitter_card_type">
                                <option value="">Select Card Type</option>
                                <option value="summary" {{ old('twitter_card_type', $product->twitter_card_type) === 'summary' ? 'selected' : '' }}>
                                    Summary Card
                                </option>
                                <option value="summary_large_image" {{ old('twitter_card_type', $product->twitter_card_type) === 'summary_large_image' ? 'selected' : '' }}>
                                    Summary Card with Large Image
                                </option>
                            </select>
                            <div class="mt-1 text-sm text-gray-500">Default: Summary Card with Large Image</div>
                        </div>
                        
                        <div class="mt-6 mb-4">
                            <div class="bg-white border border-gray-200 rounded-md overflow-hidden">
                                <div class="bg-gray-50 px-4 py-2 border-b border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-700">Social Media Preview</h3>
                                </div>
                                <div class="p-4">
                                    <div id="socialPreview" class="max-w-md mx-auto border border-gray-200 rounded-md overflow-hidden">
                                        <div class="h-52 bg-gray-100 flex items-center justify-center overflow-hidden">
                                            @if($product->og_image)
                                                <img src="{{ asset('storage/' . $product->og_image) }}" alt="OG Image" class="w-full h-full object-cover">
                                            @elseif($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="bg-white p-3">
                                            <div class="text-blue-600 font-semibold text-sm" id="socialTitle">
                                                {{ $product->meta_title ?: $product->seo_title }}
                                            </div>
                                            <div class="text-gray-600 text-xs mt-1" id="socialDescription">
                                                {{ $product->meta_description ?: $product->seo_description }}
                                            </div>
                                            <div class="text-gray-500 text-xs mt-2 uppercase">
                                                {{ parse_url(route('products.show', $product->slug), PHP_URL_HOST) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex items-center space-x-3">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Save SEO Settings
                    </button>
                    <a href="{{ route('admin.seo.products') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Character counter for meta title
        $('#meta_title').on('input', function() {
            var count = $(this).val().length;
            $('#titleCounter .chars-count').text(count);
            $('#previewTitle').text($(this).val() || '{{ $product->seo_title }}');
            $('#socialTitle').text($(this).val() || '{{ $product->seo_title }}');
            
            if (count > 60) {
                $('#titleCounter .chars-count').addClass('text-yellow-500');
            } else {
                $('#titleCounter .chars-count').removeClass('text-yellow-500');
            }
        });
        
        // Character counter for meta description
        $('#meta_description').on('input', function() {
            var count = $(this).val().length;
            $('#descriptionCounter .chars-count').text(count);
            $('#previewDescription').text($(this).val() || '{{ $product->seo_description }}');
            $('#socialDescription').text($(this).val() || '{{ $product->seo_description }}');
            
            if (count > 150) {
                $('#descriptionCounter .chars-count').addClass('text-yellow-500');
            } else {
                $('#descriptionCounter .chars-count').removeClass('text-yellow-500');
            }
        });
        
        // Preview image upload
        $('#og_image').on('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    $('#socialPreview img').attr('src', e.target.result);
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endsection
@endsection