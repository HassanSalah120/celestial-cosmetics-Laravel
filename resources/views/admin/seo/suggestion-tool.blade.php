@extends('layouts.admin')

@section('title', 'SEO Suggestion Tool')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">SEO Suggestion Tool</h1>
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">SEO Suggestion Tool</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="bg-white shadow-md rounded-lg border border-gray-200 mb-6">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex items-center">
            <svg class="w-5 h-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
            </svg>
            <h2 class="text-lg font-medium text-gray-800">Generate Optimized SEO Metadata</h2>
        </div>
        <div class="px-4 py-4">
            <p class="text-gray-700 mb-4">Select a product or category to generate optimized SEO metadata based on its content.</p>
            
            <div class="flex items-center mb-4">
                <span class="text-sm font-medium text-gray-700 mr-3">Enable Bulk Suggestions:</span>
                <label for="bulk_toggle" class="inline-flex relative items-center cursor-pointer">
                    <input type="checkbox" id="bulk_toggle" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
            
            <form id="suggestionForm" class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                    <div class="md:col-span-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Item Type</label>
                        <select id="type" name="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                            <option value="">Select Type</option>
                            <option value="product">Product</option>
                            <option value="category">Category</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-8">
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1" id="product_label">Select Product</label>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1 hidden" id="category_label">Select Category</label>
                        
                        <select id="product_id" name="product_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md">
                            <option value="">Select a Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        
                        <select id="category_id" name="category_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md hidden">
                            <option value="">Select a Category</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Bulk selection for products -->
                        <select id="product_ids" name="product_ids[]" multiple class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md hidden" size="8">
                            @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        
                        <!-- Bulk selection for categories -->
                        <select id="category_ids" name="category_ids[]" multiple class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-md hidden" size="8">
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        
                        <div class="mt-2 text-xs text-gray-500 hidden" id="bulk_help">
                            Hold Ctrl (Windows) or Cmd (Mac) to select multiple items.
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                    Generate Suggestions
                </button>
            </form>
            
            <div id="loading" class="hidden">
                <div class="flex justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Individual item results -->
            <div id="results" class="hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">SEO Metadata Suggestions</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-blue-200">
                            <div class="px-4 py-4 border-b border-blue-200 bg-blue-50 flex items-center">
                                <svg class="h-5 w-5 text-blue-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-md font-medium text-blue-800">Item Information</h3>
                            </div>
                            <div class="px-4 py-4">
                                <div id="item_info" class="text-sm"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="bg-white shadow overflow-hidden sm:rounded-lg border border-green-200">
                            <div class="px-4 py-4 border-b border-green-200 bg-green-50 flex items-center">
                                <svg class="h-5 w-5 text-green-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <h3 class="text-md font-medium text-green-800">SERP Preview</h3>
                            </div>
                            <div class="px-4 py-4">
                                <div class="serp-preview-container">
                                    <div class="serp-title" id="serp_title"></div>
                                    <div class="serp-url" id="serp_url"></div>
                                    <div class="serp-description" id="serp_description"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white shadow overflow-hidden sm:rounded-lg mb-6">
                    <div id="suggestionsAccordion">
                        <!-- Meta Title Section -->
                        <div class="border-b border-gray-200">
                            <button type="button" class="accordion-button w-full px-4 py-3 text-left text-gray-800 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 flex justify-between items-center" data-target="#collapseOne" aria-expanded="true">
                                <span class="text-md font-medium">Meta Title</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform accordion-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="collapseOne" class="px-4 py-4 accordion-content">
                                <div class="flex space-x-2">
                                    <input type="text" id="meta_title" class="flex-grow block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" readonly>
                                    <button class="copy-btn inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" type="button" data-clipboard-target="#meta_title">
                                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="text-xs text-gray-500 flex justify-between">
                                        <span>Character Count: <span id="title_length">0</span>/60</span>
                                    </div>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                        <div id="title_progress" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Meta Description Section -->
                        <div class="border-b border-gray-200">
                            <button type="button" class="accordion-button w-full px-4 py-3 text-left text-gray-800 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 flex justify-between items-center" data-target="#collapseTwo" aria-expanded="false">
                                <span class="text-md font-medium">Meta Description</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform accordion-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="collapseTwo" class="px-4 py-4 accordion-content hidden">
                                <div class="flex space-x-2">
                                    <textarea id="meta_description" class="flex-grow block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" rows="3" readonly></textarea>
                                    <button class="copy-btn inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary h-10" type="button" data-clipboard-target="#meta_description">
                                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="mt-2">
                                    <div class="text-xs text-gray-500 flex justify-between">
                                        <span>Character Count: <span id="description_length">0</span>/160</span>
                                    </div>
                                    <div class="mt-1 w-full bg-gray-200 rounded-full h-2">
                                        <div id="description_progress" class="bg-green-500 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Meta Keywords Section -->
                        <div class="border-b border-gray-200">
                            <button type="button" class="accordion-button w-full px-4 py-3 text-left text-gray-800 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 flex justify-between items-center" data-target="#collapseThree" aria-expanded="false">
                                <span class="text-md font-medium">Meta Keywords</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform accordion-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="collapseThree" class="px-4 py-4 accordion-content hidden">
                                <div class="flex space-x-2">
                                    <textarea id="meta_keywords" class="flex-grow block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" rows="2" readonly></textarea>
                                    <button class="copy-btn inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary h-10" type="button" data-clipboard-target="#meta_keywords">
                                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Structured Data Section -->
                        <div>
                            <button type="button" class="accordion-button w-full px-4 py-3 text-left text-gray-800 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 flex justify-between items-center" data-target="#collapseFour" aria-expanded="false">
                                <span class="text-md font-medium">Structured Data (JSON-LD)</span>
                                <svg class="h-5 w-5 text-gray-500 transform transition-transform accordion-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div id="collapseFour" class="px-4 py-4 accordion-content hidden">
                                <div class="flex space-x-2">
                                    <textarea id="structured_data" class="flex-grow block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 bg-gray-50 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm font-mono" rows="15" readonly></textarea>
                                    <button class="copy-btn inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary h-10" type="button" data-clipboard-target="#structured_data">
                                        <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <button type="button" id="apply_suggestions" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Apply These Suggestions
                    </button>
                </div>
            </div>
            
            <!-- Bulk results -->
            <div id="bulk_results" class="hidden">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bulk SEO Metadata Suggestions</h3>
                
                <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200 mb-6">
                    <div class="px-4 py-5 border-b border-gray-200 bg-gray-50 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Generated Suggestions
                        </h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">
                            <span id="bulk_count">0</span> items processed successfully
                        </p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Meta Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Meta Description
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="bulk_table_body">
                                <!-- Table rows will be inserted dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-4 py-4 bg-gray-50 sm:px-6 flex justify-between">
                        <button type="button" id="download_csv" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download CSV
                        </button>
                        
                        <button type="button" id="apply_bulk_suggestions" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Apply All Suggestions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .serp-preview-container {
        font-family: Arial, sans-serif;
        max-width: 600px;
        padding: 15px;
        border: 1px solid #dfe1e5;
        border-radius: 8px;
        background-color: white;
        box-shadow: 0 1px 6px rgba(32, 33, 36, 0.28);
    }
    
    .serp-title {
        font-size: 18px;
        line-height: 1.3;
        color: #1a0dab;
        margin-bottom: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .serp-url {
        font-size: 14px;
        line-height: 1.5;
        color: #006621;
        margin-bottom: 3px;
    }
    
    .serp-description {
        font-size: 14px;
        line-height: 1.58;
        color: #4d5156;
    }
    
    /* Custom accordion styling */
    .accordion-icon {
        transition: transform 0.2s ease;
    }
    
    .accordion-button[aria-expanded="true"] .accordion-icon {
        transform: rotate(180deg);
    }
    
    .accordion-content {
        transition: all 0.2s ease-out;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize clipboard.js
        new ClipboardJS('.copy-btn');
        
        // Type selector change handler
        const typeSelect = document.getElementById('type');
        const productSelect = document.getElementById('product_id');
        const categorySelect = document.getElementById('category_id');
        const productLabel = document.getElementById('product_label');
        const categoryLabel = document.getElementById('category_label');
        
        // Bulk selectors
        const bulkProductSelect = document.getElementById('product_ids');
        const bulkCategorySelect = document.getElementById('category_ids');
        const bulkToggle = document.getElementById('bulk_toggle');
        const bulkHelp = document.getElementById('bulk_help');
        const bulkResults = document.getElementById('bulk_results');
        const regularResults = document.getElementById('results');
        
        // Initialize the form state
        // Remove required from all selects initially
        productSelect.removeAttribute('required');
        categorySelect.removeAttribute('required');
        
        // We're doing manual validation, so no need to set required attributes
        // Instead just make sure hidden elements remain hidden
        if (typeSelect.value === 'product') {
            categorySelect.classList.add('hidden');
            bulkCategorySelect.classList.add('hidden');
            
            // Show either single or bulk based on toggle
            if (bulkToggle.checked) {
                productSelect.classList.add('hidden');
                bulkProductSelect.classList.remove('hidden');
                bulkHelp.classList.remove('hidden');
            } else {
                productSelect.classList.remove('hidden');
                bulkProductSelect.classList.add('hidden');
                bulkHelp.classList.add('hidden');
            }
        } else if (typeSelect.value === 'category') {
            productSelect.classList.add('hidden');
            bulkProductSelect.classList.add('hidden');
            
            // Show either single or bulk based on toggle
            if (bulkToggle.checked) {
                categorySelect.classList.add('hidden');
                bulkCategorySelect.classList.remove('hidden');
                bulkHelp.classList.remove('hidden');
            } else {
                categorySelect.classList.remove('hidden');
                bulkCategorySelect.classList.add('hidden');
                bulkHelp.classList.add('hidden');
            }
        } else {
            // No type selected yet, hide all selectors except type
            productSelect.classList.add('hidden');
            categorySelect.classList.add('hidden');
            bulkProductSelect.classList.add('hidden');
            bulkCategorySelect.classList.add('hidden');
            bulkHelp.classList.add('hidden');
        }
        
        // Toggle bulk mode
        bulkToggle.addEventListener('change', function() {
            if (this.checked) {
                // Show bulk selectors, hide individual selectors
                if (typeSelect.value === 'product') {
                    productSelect.classList.add('hidden');
                    bulkProductSelect.classList.remove('hidden');
                    bulkHelp.classList.remove('hidden');
                } else if (typeSelect.value === 'category') {
                    categorySelect.classList.add('hidden');
                    bulkCategorySelect.classList.remove('hidden');
                    bulkHelp.classList.remove('hidden');
                }
            } else {
                // Show individual selectors, hide bulk selectors
                if (typeSelect.value === 'product') {
                    productSelect.classList.remove('hidden');
                    bulkProductSelect.classList.add('hidden');
                    bulkHelp.classList.add('hidden');
                } else if (typeSelect.value === 'category') {
                    categorySelect.classList.remove('hidden');
                    bulkCategorySelect.classList.add('hidden');
                    bulkHelp.classList.add('hidden');
                }
            }
        });
        
        typeSelect.addEventListener('change', function() {
            if (this.value === 'product') {
                productSelect.classList.remove('hidden');
                categorySelect.classList.add('hidden');
                productLabel.classList.remove('hidden');
                categoryLabel.classList.add('hidden');
                
                // Handle bulk selectors based on current bulk mode
                if (bulkToggle.checked) {
                    productSelect.classList.add('hidden');
                    bulkProductSelect.classList.remove('hidden');
                    bulkCategorySelect.classList.add('hidden');
                    bulkHelp.classList.remove('hidden');
                } else {
                    bulkProductSelect.classList.add('hidden');
                    bulkCategorySelect.classList.add('hidden');
                    bulkHelp.classList.add('hidden');
                }
            } else if (this.value === 'category') {
                productSelect.classList.add('hidden');
                categorySelect.classList.remove('hidden');
                productLabel.classList.add('hidden');
                categoryLabel.classList.remove('hidden');
                
                // Handle bulk selectors based on current bulk mode
                if (bulkToggle.checked) {
                    categorySelect.classList.add('hidden');
                    bulkCategorySelect.classList.remove('hidden');
                    bulkProductSelect.classList.add('hidden');
                    bulkHelp.classList.remove('hidden');
                } else {
                    bulkProductSelect.classList.add('hidden');
                    bulkCategorySelect.classList.add('hidden');
                    bulkHelp.classList.add('hidden');
                }
            } else {
                productSelect.classList.add('hidden');
                categorySelect.classList.add('hidden');
                productLabel.classList.add('hidden');
                categoryLabel.classList.add('hidden');
                bulkProductSelect.classList.add('hidden');
                bulkCategorySelect.classList.add('hidden');
                bulkHelp.classList.add('hidden');
            }
        });
        
        // Custom accordion functionality
        document.querySelectorAll('.accordion-button').forEach(function(button) {
            button.addEventListener('click', function() {
                const target = document.querySelector(this.getAttribute('data-target'));
                const expanded = this.getAttribute('aria-expanded') === 'true';
                
                if (expanded) {
                    this.setAttribute('aria-expanded', 'false');
                    target.classList.add('hidden');
                } else {
                    this.setAttribute('aria-expanded', 'true');
                    target.classList.remove('hidden');
                }
            });
        });
        
        // Form submission handler
        const form = document.getElementById('suggestionForm');
        const loading = document.getElementById('loading');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const type = typeSelect.value;
            const isBulkMode = bulkToggle.checked;
            
            // Perform manual validation instead of relying on HTML5 validation
            if (!type) {
                alert('Please select a type');
                return;
            }
            
            let isValid = true;
            let errorMessage = '';
            
            if (isBulkMode) {
                let selectedCount = 0;
                if (type === 'product') {
                    // Count selected products
                    for (let option of bulkProductSelect.options) {
                        if (option.selected) selectedCount++;
                    }
                    if (selectedCount === 0) {
                        isValid = false;
                        errorMessage = 'Please select at least one product';
                    }
                } else if (type === 'category') {
                    // Count selected categories
                    for (let option of bulkCategorySelect.options) {
                        if (option.selected) selectedCount++;
                    }
                    if (selectedCount === 0) {
                        isValid = false;
                        errorMessage = 'Please select at least one category';
                    }
                }
            } else {
                // Individual item mode
                if (type === 'product' && !productSelect.value) {
                    isValid = false;
                    errorMessage = 'Please select a product';
                } else if (type === 'category' && !categorySelect.value) {
                    isValid = false;
                    errorMessage = 'Please select a category';
                }
            }
            
            if (!isValid) {
                alert(errorMessage);
                return;
            }
            
            // If validation passes, continue with the form submission
            // Show loading
            loading.classList.remove('hidden');
            regularResults.classList.add('hidden');
            bulkResults.classList.add('hidden');
            
            if (isBulkMode) {
                // Bulk mode
                let ids = [];
                
                if (type === 'product') {
                    // Get selected product IDs
                    for (let option of bulkProductSelect.options) {
                        if (option.selected) {
                            ids.push(option.value);
                        }
                    }
                } else if (type === 'category') {
                    // Get selected category IDs
                    for (let option of bulkCategorySelect.options) {
                        if (option.selected) {
                            ids.push(option.value);
                        }
                    }
                }
                
                if (ids.length === 0) {
                    alert('Please select at least one item');
                    loading.classList.add('hidden');
                    return;
                }
                
                // Fetch bulk suggestions
                processBulkSuggestions(type, ids);
            } else {
                // Individual item mode
                let id;
                
                if (type === 'product') {
                    id = productSelect.value;
                } else if (type === 'category') {
                    id = categorySelect.value;
                }
                
                if (!id) {
                    alert('Please select an item');
                    loading.classList.add('hidden');
                    return;
                }
                
                // Fetch individual suggestion
                fetch('/admin/seo/generate-suggestions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type, id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Hide loading
                        loading.classList.add('hidden');
                        
                        // Show results
                        regularResults.classList.remove('hidden');
                        
                        // Populate item info
                        const item = data.item;
                        let itemInfoHtml = '';
                        
                        if (type === 'product') {
                            itemInfoHtml = `
                                <div class="mb-2"><strong class="text-gray-700">Product:</strong> ${item.name}</div>
                                ${item.brand ? `<div class="mb-2"><strong class="text-gray-700">Brand:</strong> ${item.brand}</div>` : ''}
                                ${item.category ? `<div class="mb-2"><strong class="text-gray-700">Category:</strong> ${item.category.name}</div>` : ''}
                                <div class="mb-2"><strong class="text-gray-700">Price:</strong> ${item.price}</div>
                                ${item.sku ? `<div class="mb-2"><strong class="text-gray-700">SKU:</strong> ${item.sku}</div>` : ''}
                            `;
                        } else {
                            itemInfoHtml = `
                                <div class="mb-2"><strong class="text-gray-700">Category:</strong> ${item.name}</div>
                                <div class="mb-2"><strong class="text-gray-700">Products Count:</strong> ${item.products_count || 0}</div>
                            `;
                        }
                        
                        document.getElementById('item_info').innerHTML = itemInfoHtml;
                        
                        // Populate metadata fields
                        const metadata = data.metadata;
                        
                        // Meta title
                        const metaTitleInput = document.getElementById('meta_title');
                        metaTitleInput.value = metadata.meta_title;
                        updateTitleCounter(metadata.meta_title);
                        
                        // Meta description
                        const metaDescInput = document.getElementById('meta_description');
                        metaDescInput.value = metadata.meta_description;
                        updateDescriptionCounter(metadata.meta_description);
                        
                        // Meta keywords
                        document.getElementById('meta_keywords').value = metadata.meta_keywords;
                        
                        // Structured data
                        document.getElementById('structured_data').value = metadata.structured_data;
                        
                        // SERP preview
                        document.getElementById('serp_title').textContent = metadata.meta_title;
                        document.getElementById('serp_url').textContent = window.location.origin + '/' + (type === 'product' ? 'products/' : 'categories/') + (item.slug || 'sample-item');
                        document.getElementById('serp_description').textContent = metadata.meta_description;
                        
                        // Apply button handler
                        const applyBtn = document.getElementById('apply_suggestions');
                        applyBtn.onclick = function() {
                            applySuggestion(type, id, metadata);
                        };
                    } else {
                        loading.classList.add('hidden');
                        alert('Error: ' + (data.message || 'Failed to generate suggestions'));
                    }
                })
                .catch(error => {
                    loading.classList.add('hidden');
                    console.error('Error:', error);
                    alert('An error occurred while fetching suggestions.');
                });
            }
        });
        
        // Process bulk suggestions
        function processBulkSuggestions(type, ids) {
            // Clear previous bulk results
            const bulkTableBody = document.getElementById('bulk_table_body');
            bulkTableBody.innerHTML = '';
            
            // Array to collect all suggestions
            const allSuggestions = [];
            
            // Counter for completed requests
            let completed = 0;
            
            // Process each ID
            ids.forEach(id => {
                fetch('/admin/seo/generate-suggestions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ type, id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Add to suggestions array
                        allSuggestions.push({
                            id: id,
                            type: type,
                            name: data.item.name,
                            metadata: data.metadata
                        });
                        
                        // Add row to table
                        const row = document.createElement('tr');
                        
                        // Truncate long text
                        const truncate = (text, length) => {
                            return text.length > length ? text.substring(0, length) + '...' : text;
                        };
                        
                        row.innerHTML = `
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ${data.item.name}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${type === 'product' ? 'Product' : 'Category'}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${truncate(data.metadata.meta_title, 40)}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ${truncate(data.metadata.meta_description, 60)}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <button type="button" class="text-indigo-600 hover:text-indigo-900 view-details" data-id="${id}" data-type="${type}">
                                    View Details
                                </button>
                                <button type="button" class="ml-3 text-green-600 hover:text-green-900 apply-single" data-id="${id}" data-type="${type}">
                                    Apply
                                </button>
                            </td>
                        `;
                        
                        bulkTableBody.appendChild(row);
                        
                        // Add event listeners to the new buttons
                        const viewBtn = row.querySelector('.view-details');
                        const applyBtn = row.querySelector('.apply-single');
                        
                        viewBtn.addEventListener('click', function() {
                            showItemDetails(type, id);
                        });
                        
                        applyBtn.addEventListener('click', function() {
                            applySuggestion(type, id, data.metadata);
                        });
                    }
                    
                    // Update completed count
                    completed++;
                    
                    // Update counter text
                    document.getElementById('bulk_count').textContent = allSuggestions.length;
                    
                    // If all requests are done
                    if (completed === ids.length) {
                        // Hide loading
                        loading.classList.add('hidden');
                        
                        // Show bulk results
                        bulkResults.classList.remove('hidden');
                        
                        // Apply all button handler
                        document.getElementById('apply_bulk_suggestions').onclick = function() {
                            applyAllSuggestions(allSuggestions);
                        };
                        
                        // Download CSV button handler
                        document.getElementById('download_csv').onclick = function() {
                            downloadCSV(allSuggestions);
                        };
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    // Update completed count even on error
                    completed++;
                    
                    // If all requests are done
                    if (completed === ids.length) {
                        // Hide loading
                        loading.classList.add('hidden');
                        
                        // Show bulk results if we have any
                        if (allSuggestions.length > 0) {
                            bulkResults.classList.remove('hidden');
                        } else {
                            alert('Failed to generate any suggestions.');
                        }
                    }
                });
            });
        }
        
        // Show details for a specific item in the individual view
        function showItemDetails(type, id) {
            // Hide bulk results
            bulkResults.classList.add('hidden');
            
            // Show loading
            loading.classList.remove('hidden');
            
            // Fetch individual suggestion
            fetch('/admin/seo/generate-suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ type, id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Hide loading
                    loading.classList.add('hidden');
                    
                    // Show regular results
                    regularResults.classList.remove('hidden');
                    
                    // Populate item info
                    const item = data.item;
                    let itemInfoHtml = '';
                    
                    if (type === 'product') {
                        itemInfoHtml = `
                            <div class="mb-2"><strong class="text-gray-700">Product:</strong> ${item.name}</div>
                            ${item.brand ? `<div class="mb-2"><strong class="text-gray-700">Brand:</strong> ${item.brand}</div>` : ''}
                            ${item.category ? `<div class="mb-2"><strong class="text-gray-700">Category:</strong> ${item.category.name}</div>` : ''}
                            <div class="mb-2"><strong class="text-gray-700">Price:</strong> ${item.price}</div>
                            ${item.sku ? `<div class="mb-2"><strong class="text-gray-700">SKU:</strong> ${item.sku}</div>` : ''}
                        `;
                    } else {
                        itemInfoHtml = `
                            <div class="mb-2"><strong class="text-gray-700">Category:</strong> ${item.name}</div>
                            <div class="mb-2"><strong class="text-gray-700">Products Count:</strong> ${item.products_count || 0}</div>
                        `;
                    }
                    
                    document.getElementById('item_info').innerHTML = itemInfoHtml;
                    
                    // Populate metadata fields
                    const metadata = data.metadata;
                    
                    // Meta title
                    const metaTitleInput = document.getElementById('meta_title');
                    metaTitleInput.value = metadata.meta_title;
                    updateTitleCounter(metadata.meta_title);
                    
                    // Meta description
                    const metaDescInput = document.getElementById('meta_description');
                    metaDescInput.value = metadata.meta_description;
                    updateDescriptionCounter(metadata.meta_description);
                    
                    // Meta keywords
                    document.getElementById('meta_keywords').value = metadata.meta_keywords;
                    
                    // Structured data
                    document.getElementById('structured_data').value = metadata.structured_data;
                    
                    // SERP preview
                    document.getElementById('serp_title').textContent = metadata.meta_title;
                    document.getElementById('serp_url').textContent = window.location.origin + '/' + (type === 'product' ? 'products/' : 'categories/') + (item.slug || 'sample-item');
                    document.getElementById('serp_description').textContent = metadata.meta_description;
                    
                    // Apply button handler
                    const applyBtn = document.getElementById('apply_suggestions');
                    applyBtn.onclick = function() {
                        applySuggestion(type, id, metadata);
                    };
                } else {
                    loading.classList.add('hidden');
                    alert('Error: ' + (data.message || 'Failed to generate suggestions'));
                }
            })
            .catch(error => {
                loading.classList.add('hidden');
                console.error('Error:', error);
                alert('An error occurred while fetching suggestions.');
            });
        }
        
        // Apply suggestion for a single item
        function applySuggestion(type, id, metadata) {
            // You can implement this based on your application's API
            fetch('/admin/seo/apply-suggestions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    type, 
                    id,
                    meta_title: metadata.meta_title,
                    meta_description: metadata.meta_description,
                    meta_keywords: metadata.meta_keywords,
                    structured_data: metadata.structured_data
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Suggestions applied successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to apply suggestions'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while applying suggestions.');
            });
        }
        
        // Apply all suggestions
        function applyAllSuggestions(suggestions) {
            if (confirm(`Are you sure you want to apply SEO suggestions to ${suggestions.length} items?`)) {
                fetch('/admin/seo/apply-bulk-suggestions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ suggestions })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(`Successfully applied suggestions to ${data.applied_count} items!`);
                    } else {
                        alert('Error: ' + (data.message || 'Failed to apply suggestions'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while applying suggestions.');
                });
            }
        }
        
        // Download CSV of suggestions
        function downloadCSV(suggestions) {
            // Create CSV content
            let csvContent = 'data:text/csv;charset=utf-8,';
            
            // Add headers
            csvContent += 'Item Name,Type,Meta Title,Meta Description,Meta Keywords\n';
            
            // Add data rows
            suggestions.forEach(item => {
                csvContent += `"${item.name}","${item.type}","${item.metadata.meta_title}","${item.metadata.meta_description}","${item.metadata.meta_keywords}"\n`;
            });
            
            // Create download link
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'seo_suggestions.csv');
            document.body.appendChild(link);
            
            // Trigger download
            link.click();
            
            // Clean up
            document.body.removeChild(link);
        }
        
        // Meta title character counter
        function updateTitleCounter(text) {
            const length = text.length;
            const titleLength = document.getElementById('title_length');
            const titleProgress = document.getElementById('title_progress');
            
            titleLength.textContent = length;
            
            const percentage = Math.min(100, (length / 60) * 100);
            titleProgress.style.width = percentage + '%';
            
            if (length > 60) {
                titleProgress.classList.remove('bg-green-500', 'bg-yellow-500');
                titleProgress.classList.add('bg-red-500');
            } else if (length > 50) {
                titleProgress.classList.remove('bg-green-500', 'bg-red-500');
                titleProgress.classList.add('bg-yellow-500');
            } else {
                titleProgress.classList.remove('bg-yellow-500', 'bg-red-500');
                titleProgress.classList.add('bg-green-500');
            }
        }
        
        // Meta description character counter
        function updateDescriptionCounter(text) {
            const length = text.length;
            const descLength = document.getElementById('description_length');
            const descProgress = document.getElementById('description_progress');
            
            descLength.textContent = length;
            
            const percentage = Math.min(100, (length / 160) * 100);
            descProgress.style.width = percentage + '%';
            
            if (length > 160) {
                descProgress.classList.remove('bg-green-500', 'bg-yellow-500');
                descProgress.classList.add('bg-red-500');
            } else if (length > 140) {
                descProgress.classList.remove('bg-green-500', 'bg-red-500');
                descProgress.classList.add('bg-yellow-500');
            } else {
                descProgress.classList.remove('bg-yellow-500', 'bg-red-500');
                descProgress.classList.add('bg-green-500');
            }
        }
    });
</script>
@endpush 