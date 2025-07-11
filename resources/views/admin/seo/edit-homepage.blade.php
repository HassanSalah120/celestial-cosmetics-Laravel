@extends('layouts.admin')

@section('title', 'Edit Homepage SEO')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Homepage SEO Settings</h1>
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
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Homepage SEO</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center">
            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <h3 class="text-lg leading-6 font-medium text-gray-900">Homepage SEO Settings</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif
            
            <p class="mb-6 text-gray-600">
                Customize SEO settings specifically for your website's homepage. These settings will override the global default SEO settings.
            </p>
            
            <form action="{{ route('admin.seo.update-homepage') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="mb-4">
                            <label for="homepage_meta_title" class="block text-sm font-medium text-gray-700 mb-1">Homepage Meta Title</label>
                            <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   id="homepage_meta_title" name="homepage_meta_title" 
                                   value="{{ $homepageSeo['homepage_meta_title']->value ?? '' }}" maxlength="70">
                            <p class="mt-1 text-sm text-gray-500">Recommended length: 50-60 characters</p>
                            @error('homepage_meta_title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="homepage_meta_description" class="block text-sm font-medium text-gray-700 mb-1">Homepage Meta Description</label>
                            <textarea class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                      id="homepage_meta_description" name="homepage_meta_description" 
                                      rows="3" maxlength="160">{{ $homepageSeo['homepage_meta_description']->value ?? '' }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">Recommended length: 150-160 characters</p>
                            @error('homepage_meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="homepage_meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Homepage Meta Keywords</label>
                            <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                   id="homepage_meta_keywords" name="homepage_meta_keywords" 
                                   value="{{ $homepageSeo['homepage_meta_keywords']->value ?? '' }}">
                            <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                            @error('homepage_meta_keywords')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <div>
                        <div class="mb-4">
                            <label for="homepage_twitter_card_type" class="block text-sm font-medium text-gray-700 mb-1">Twitter Card Type</label>
                            <select class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    id="homepage_twitter_card_type" name="homepage_twitter_card_type">
                                <option value="summary" {{ ($homepageSeo['homepage_twitter_card_type']->value ?? '') == 'summary' ? 'selected' : '' }}>Summary</option>
                                <option value="summary_large_image" {{ ($homepageSeo['homepage_twitter_card_type']->value ?? '') == 'summary_large_image' ? 'selected' : '' }}>Summary with Large Image</option>
                            </select>
                            @error('homepage_twitter_card_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="homepage_og_image" class="block text-sm font-medium text-gray-700 mb-1">Homepage Open Graph Image</label>
                            <input type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-primary-dark" 
                                   accept="image/*"
                                   id="homepage_og_image" name="homepage_og_image">
                            @if(!empty($homepageSeo['homepage_og_image']->value))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $homepageSeo['homepage_og_image']->value) }}" 
                                         alt="Homepage OG Image" class="h-24 w-auto object-cover rounded-md border border-gray-200">
                                </div>
                            @endif
                            @error('homepage_og_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Recommended size: 1200×630 pixels</p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Save Homepage SEO Settings
                    </button>
                    <a href="{{ route('admin.seo.index') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </a>
                </div>
            </form>
            
            <div class="mt-10 pt-6 border-t border-gray-200">
                <h4 class="text-lg font-medium text-gray-900 mb-4">SEO Preview</h4>
                
                <div class="bg-white border border-gray-300 rounded-lg p-4 max-w-2xl">
                    <h5 class="text-blue-600 text-xl font-medium mb-1" id="preview-title">
                        {{ $homepageSeo['homepage_meta_title']->value ?? config('app.name') }}
                    </h5>
                    <div class="text-green-700 text-sm mb-2">{{ config('app.url') }}</div>
                    <p class="text-gray-600" id="preview-description">
                        {{ $homepageSeo['homepage_meta_description']->value ?? 'Your website description appears here. Make sure it accurately describes your website and encourages users to visit.' }}
                    </p>
                </div>
                
                <div class="mt-4">
                    <h5 class="text-md font-medium text-gray-900 mb-2">Social Media Preview</h5>
                    
                    <div class="bg-gray-100 border border-gray-300 rounded-lg p-4 max-w-md">
                        @if(!empty($homepageSeo['homepage_og_image']->value))
                            <div class="bg-gray-300 h-40 w-full rounded-t-md overflow-hidden">
                                <img src="{{ asset('storage/' . $homepageSeo['homepage_og_image']->value) }}" 
                                     alt="Social preview" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="bg-gray-300 h-40 w-full rounded-t-md flex items-center justify-center">
                                <svg class="h-16 w-16 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <div class="p-3 bg-white rounded-b-md">
                            <div class="text-gray-500 text-xs mb-1">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>
                            <h6 class="text-gray-900 font-medium mb-1" id="social-preview-title">
                                {{ $homepageSeo['homepage_meta_title']->value ?? config('app.name') }}
                            </h6>
                            <p class="text-gray-600 text-sm" id="social-preview-description">
                                {{ $homepageSeo['homepage_meta_description']->value ?? 'Your website description appears here.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Update SEO Preview
        const titleInput = document.getElementById('homepage_meta_title');
        const descInput = document.getElementById('homepage_meta_description');
        const previewTitle = document.getElementById('preview-title');
        const previewDesc = document.getElementById('preview-description');
        const socialTitle = document.getElementById('social-preview-title');
        const socialDesc = document.getElementById('social-preview-description');
        
        titleInput.addEventListener('input', function() {
            previewTitle.textContent = this.value || '{{ config('app.name') }}';
            socialTitle.textContent = this.value || '{{ config('app.name') }}';
        });
        
        descInput.addEventListener('input', function() {
            previewDesc.textContent = this.value || 'Your website description appears here. Make sure it accurately describes your website and encourages users to visit.';
            socialDesc.textContent = this.value || 'Your website description appears here.';
        });
    });
</script>
@endpush 