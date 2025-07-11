@extends('layouts.admin')

@section('title', 'SEO Management')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">SEO Management</h1>
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
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">SEO Management</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1">
        <div class="col-span-1 bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center">
                <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Global SEO Settings</h3>
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
                
                <form action="{{ route('admin.seo.update-settings') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <div class="mb-4">
                                <label for="default_meta_title" class="block text-sm font-medium text-gray-700 mb-1">Default Meta Title</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="default_meta_title" name="default_meta_title" 
                                       value="{{ $seoSettings['default_meta_title']->value ?? '' }}" maxlength="70">
                                <p class="mt-1 text-sm text-gray-500">Recommended length: 50-60 characters</p>
                            </div>
                            
                            <div class="mb-4">
                                <label for="default_meta_description" class="block text-sm font-medium text-gray-700 mb-1">Default Meta Description</label>
                                <textarea class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                          id="default_meta_description" name="default_meta_description" 
                                          rows="3" maxlength="160">{{ $seoSettings['default_meta_description']->value ?? '' }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Recommended length: 150-160 characters</p>
                            </div>
                            
                            <div class="mb-4">
                                <label for="default_meta_keywords" class="block text-sm font-medium text-gray-700 mb-1">Default Meta Keywords</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="default_meta_keywords" name="default_meta_keywords" 
                                       value="{{ $seoSettings['default_meta_keywords']->value ?? '' }}">
                                <p class="mt-1 text-sm text-gray-500">Separate keywords with commas</p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <label for="og_site_name" class="block text-sm font-medium text-gray-700 mb-1">Open Graph Site Name</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="og_site_name" name="og_site_name" 
                                       value="{{ $seoSettings['og_site_name']->value ?? '' }}">
                            </div>
                            
                            <div class="mb-4">
                                <label for="og_default_image" class="block text-sm font-medium text-gray-700 mb-1">Default Open Graph Image</label>
                                <input type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-primary-dark" 
                                       accept="image/*"
                                       id="og_default_image" name="og_default_image">
                                @if(!empty($seoSettings['og_default_image']->value))
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $seoSettings['og_default_image']->value) }}" 
                                             alt="Default OG Image" class="h-24 w-auto object-cover rounded-md border border-gray-200">
                                    </div>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <label for="twitter_site" class="block text-sm font-medium text-gray-700 mb-1">Twitter Site (@username)</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="twitter_site" name="twitter_site" 
                                       value="{{ $seoSettings['twitter_site']->value ?? '' }}">
                            </div>
                            
                            <div class="mb-4">
                                <label for="twitter_creator" class="block text-sm font-medium text-gray-700 mb-1">Twitter Creator (@username)</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="twitter_creator" name="twitter_creator" 
                                       value="{{ $seoSettings['twitter_creator']->value ?? '' }}">
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Robots & Indexing</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" 
                                           id="enable_robots_txt" name="enable_robots_txt" 
                                           value="1" {{ ($seoSettings['enable_robots_txt']->value ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_robots_txt" class="font-medium text-gray-700">Enable robots.txt</label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="robots_txt_content" class="block text-sm font-medium text-gray-700 mb-1">robots.txt Content</label>
                                <textarea class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md font-mono" 
                                          id="robots_txt_content" name="robots_txt_content" rows="6">{{ $seoSettings['robots_txt_content']->value ?? "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: " . url('sitemap.xml') }}</textarea>
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" 
                                           id="enable_sitemap" name="enable_sitemap" 
                                           value="1" {{ ($seoSettings['enable_sitemap']->value ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_sitemap" class="font-medium text-gray-700">Enable XML Sitemap</label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <a href="{{ route('admin.seo.generate-sitemap') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Generate Sitemap
                                </a>
                                @if(file_exists(public_path('sitemap.xml')))
                                    <a href="{{ url('sitemap.xml') }}" target="_blank" class="ml-2 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        View Sitemap
                                    </a>
                                @endif
                            </div>
                            
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" 
                                           id="no_index_admin" name="no_index_admin" 
                                           value="1" {{ ($seoSettings['no_index_admin']->value ?? 1) == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="no_index_admin" class="font-medium text-gray-700">Prevent indexing of admin pages</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-medium text-gray-900 mb-3">Structured Data</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" 
                                           id="enable_organization_schema" name="enable_organization_schema" 
                                           value="1" {{ ($seoSettings['enable_organization_schema']->value ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_organization_schema" class="font-medium text-gray-700">Enable Organization Schema</label>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1">Organization Name</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="organization_name" name="organization_name" 
                                       value="{{ $seoSettings['organization_name']->value ?? '' }}">
                            </div>
                            
                            <div class="mb-4">
                                <label for="organization_logo" class="block text-sm font-medium text-gray-700 mb-1">Organization Logo URL</label>
                                <input type="text" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" 
                                       id="organization_logo" name="organization_logo" 
                                       value="{{ $seoSettings['organization_logo']->value ?? '' }}">
                            </div>
                        </div>
                        
                        <div>
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" 
                                           id="enable_product_schema" name="enable_product_schema" 
                                           value="1" {{ ($seoSettings['enable_product_schema']->value ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_product_schema" class="font-medium text-gray-700">Enable Product Schema</label>
                                </div>
                            </div>
                            
                            <div class="mb-4 flex items-start">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" 
                                           id="enable_breadcrumb_schema" name="enable_breadcrumb_schema" 
                                           value="1" {{ ($seoSettings['enable_breadcrumb_schema']->value ?? 0) == 1 ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="enable_breadcrumb_schema" class="font-medium text-gray-700">Enable Breadcrumb Schema</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Add SEO Tools section -->
                    <div class="bg-white shadow overflow-hidden sm:rounded-md mt-4 mb-8">
                        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center justify-between">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">SEO Tools</h3>
                        </div>
                        <ul class="divide-y divide-gray-200">
                            <li>
                                <a href="{{ route('admin.health-checker') }}" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                                <p class="ml-3 text-sm font-medium text-primary">SEO Health Checker</p>
                                            </div>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    View Report
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    Check your site's SEO health and find issues to improve
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.seo.suggestion-tool') }}" class="block hover:bg-gray-50">
                                    <div class="px-4 py-4 sm:px-6">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <svg class="flex-shrink-0 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                                <p class="ml-3 text-sm font-medium text-primary">SEO Suggestion Tool</p>
                                            </div>
                                            <div class="ml-2 flex-shrink-0 flex">
                                                <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    New
                                                </p>
                                            </div>
                                        </div>
                                        <div class="mt-2 sm:flex sm:justify-between">
                                            <div class="sm:flex">
                                                <p class="flex items-center text-sm text-gray-500">
                                                    Generate optimized SEO metadata for products and categories
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            Save Global SEO Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Homepage SEO Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Homepage SEO</dt>
                            <dd class="text-lg font-medium text-gray-900">Main Page Settings</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Optimize your homepage's meta tags, social cards, and structured data for better visibility.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.seo.edit-homepage') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Homepage SEO
                    </a>
                </div>
            </div>
        </div>

        <!-- Product SEO Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-emerald-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-emerald-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Product SEO</dt>
                            <dd class="text-lg font-medium text-gray-900">Product Page Settings</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Manage SEO settings for individual products to improve their search engine visibility.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.seo.products') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manage Product SEO
                    </a>
                </div>
            </div>
        </div>

        <!-- Category SEO Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-amber-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-amber-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Category SEO</dt>
                            <dd class="text-lg font-medium text-gray-900">Category Page Settings</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Optimize category pages with meta tags and structured data for better search rankings.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.seo.categories') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-amber-600 hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Manage Category SEO
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Second row of SEO feature cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-5">
        <!-- Health Checker Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">SEO Health</dt>
                            <dd class="text-lg font-medium text-gray-900">Health Checker</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Scan your site for SEO issues and get recommendations to improve your search engine performance.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.health-checker') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Run Health Check
                    </a>
                </div>
            </div>
        </div>

        <!-- Robots.txt Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Crawler Control</dt>
                            <dd class="text-lg font-medium text-gray-900">Robots.txt Editor</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Control how search engines crawl your site by configuring robots.txt rules and directives.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.robots-txt') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Robots.txt
                    </a>
                </div>
            </div>
        </div>

        <!-- Redirects Management Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">URL Management</dt>
                            <dd class="text-lg font-medium text-gray-900">Redirects</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Create and manage URL redirects to maintain SEO value when pages move or change.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.redirects') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                        Manage Redirects
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Third row of SEO feature cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mt-5">
        <!-- Structured Data Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-pink-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rich Results</dt>
                            <dd class="text-lg font-medium text-gray-900">Structured Data</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Enhance your search listings with structured data to display rich results in search engines.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.structured-data') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-pink-600 hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
                        </svg>
                        Configure Schema
                    </a>
                </div>
            </div>
        </div>

        <!-- Sitemap Viewer Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">XML Sitemap</dt>
                            <dd class="text-lg font-medium text-gray-900">Sitemap Viewer</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    View and manage your sitemap.xml file to help search engines discover and crawl your pages.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.sitemap-viewer') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                        </svg>
                        View Sitemap
                    </a>
                </div>
            </div>
        </div>

        <!-- SEO Component Docs Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-gray-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Documentation</dt>
                            <dd class="text-lg font-medium text-gray-900">SEO Component Docs</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4 text-sm text-gray-600">
                    Learn how to use the SEO component to optimize your custom pages for search engines.
                </div>
                <div class="mt-5">
                    <a href="{{ route('admin.seo.docs.component') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 w-full justify-center">
                        <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        View Documentation
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-4 py-5 border-b border-gray-200 sm:px-6 flex items-center">
            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            <h3 class="text-lg leading-6 font-medium text-gray-900">SEO Tips & Best Practices</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Meta Titles & Descriptions</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Keep meta titles between 50-60 characters to avoid truncation in search results.</li>
                        <li>Write meta descriptions between 150-160 characters that accurately summarize the page content.</li>
                        <li>Include relevant keywords in titles and descriptions, but avoid keyword stuffing.</li>
                        <li>Each page should have a unique meta title and description.</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Social Media Optimization</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Use high-quality images for Open Graph and Twitter Cards (recommended size: 1200×630 pixels).</li>
                        <li>Customize social titles and descriptions for better engagement on social platforms.</li>
                        <li>Test your social media cards using preview tools before publishing.</li>
                    </ul>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">Structured Data</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Use product schema to enhance product listings in search results.</li>
                        <li>Implement breadcrumb schema to improve site navigation in search results.</li>
                        <li>Test your structured data using Google's Structured Data Testing Tool.</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-base font-medium text-gray-800 mb-2">URL Structure</h4>
                    <ul class="space-y-1 text-sm text-gray-600 list-disc pl-5">
                        <li>Use descriptive URLs that include relevant keywords.</li>
                        <li>Keep URLs short and avoid unnecessary parameters.</li>
                        <li>Use hyphens (-) to separate words in URLs, not underscores.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 