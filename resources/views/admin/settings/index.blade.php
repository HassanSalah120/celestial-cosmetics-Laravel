@extends('layouts.admin')

@section('content')
    <div class="pb-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Application Settings</h1>
            
            @include('admin.partials.alerts')
            
            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- General Settings Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-primary text-white mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">General Settings</h3>
                            <p class="text-sm text-gray-500 mb-4">Configure site name, logo, favicon, and registration settings.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.settings.general') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Manage
                                </a>
                </div>
                        </div>
                    </div>
                    
                    <!-- Currency Settings Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-emerald-500 text-white mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Currency Settings</h3>
                            <p class="text-sm text-gray-500 mb-4">Configure currency code, symbol, decimal places, and formatting.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.settings.currency') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-500 hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                                    Manage
                                </a>
                </div>
            </div>
        </div>

                    <!-- Language Settings Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                                </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Language Settings</h3>
                            <p class="text-sm text-gray-500 mb-4">Configure language switcher, available languages, and default language.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.settings.language') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Manage
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SEO Settings Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-purple-500 text-white mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                    </svg>
                </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">SEO Settings</h3>
                            <p class="text-sm text-gray-500 mb-4">Configure SEO defaults, meta tags, structured data, and sitemaps.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.seo.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-500 hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                    Manage
                                </a>
                            </div>
                        </div>
                                        </div>
                                        
                    <!-- Shipping Settings Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                        <div class="p-6">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-yellow-500 text-white mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                </svg>
                                </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Shipping Settings</h3>
                            <p class="text-sm text-gray-500 mb-4">Configure shipping methods, rates, and country-specific fees.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.shipping.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-yellow-500 hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    Manage
                                </a>
            </div>
        </div>
    </div>
    
                    <!-- Homepage Content Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300">
                    <div class="p-6">
                            <div class="flex items-center justify-center h-12 w-12 rounded-md bg-red-500 text-white mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                        </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Homepage Content</h3>
                            <p class="text-sm text-gray-500 mb-4">Configure homepage sections, featured products, and promotional content.</p>
                            <div class="mt-4">
                                <a href="{{ route('admin.homepage-content') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-500 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Manage
                                </a>
                            </div>
            </div>
        </div>
    </div>
    
                @if(isset($normalizedTablesExist) && !$normalizedTablesExist)
                <div class="mt-8 rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Normalized Settings Tables Not Found</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>You're currently using the legacy settings table. Please run migrations to create normalized settings tables for improved performance and functionality.</p>
                                <p class="mt-2">Command: <code class="bg-yellow-100 px-2 py-1 rounded">php artisan migrate</code></p>
                            </div>
                        </div>
                    </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection