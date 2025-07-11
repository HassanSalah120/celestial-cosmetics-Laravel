@extends('layouts.admin')

@section('title', $pageTitle)

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-900 mt-4">Edit Schema Markup</h1>
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
                    <a href="{{ route('admin.structured-data') }}" class="ml-1 text-sm font-medium text-primary hover:text-primary-dark md:ml-2">Structured Data</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Schema</span>
                </div>
            </li>
        </ol>
    </nav>
    
    @if(session('success'))
        <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4">
            <p>{{ session('success') }}</p>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="mb-6 bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 flex justify-between items-center">
            <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">Edit Schema Markup</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Update structured data for this page.</p>
            </div>
        </div>
        
        <div class="border-t border-gray-200 p-4">
            <form action="{{ route('admin.update-structured-data', $schema->id) }}" method="POST" id="schemaForm" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="page_url" class="block text-sm font-medium text-gray-700">Page URL</label>
                        <div class="mt-1">
                            <input type="text" name="page_url" id="page_url" placeholder="/page-path" value="{{ old('page_url', $schema->page_url) }}" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">The URL path of the page (without domain)</p>
                        @error('page_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="schema_type" class="block text-sm font-medium text-gray-700">Schema Type</label>
                        <div class="mt-1">
                            <select name="schema_type" id="schema_type" class="shadow-sm focus:ring-primary focus:border-primary block w-full sm:text-sm border-gray-300 rounded-md">
                                <option value="WebPage" {{ old('schema_type', $schema->schema_type) == 'WebPage' ? 'selected' : '' }}>WebPage</option>
                                <option value="Article" {{ old('schema_type', $schema->schema_type) == 'Article' ? 'selected' : '' }}>Article</option>
                                <option value="FAQPage" {{ old('schema_type', $schema->schema_type) == 'FAQPage' ? 'selected' : '' }}>FAQ Page</option>
                                <option value="Organization" {{ old('schema_type', $schema->schema_type) == 'Organization' ? 'selected' : '' }}>Organization</option>
                                <option value="LocalBusiness" {{ old('schema_type', $schema->schema_type) == 'LocalBusiness' ? 'selected' : '' }}>Local Business</option>
                                <option value="BreadcrumbList" {{ old('schema_type', $schema->schema_type) == 'BreadcrumbList' ? 'selected' : '' }}>BreadcrumbList</option>
                                <option value="Custom" {{ old('schema_type', $schema->schema_type) == 'Custom' ? 'selected' : '' }}>Custom</option>
                            </select>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Select the schema type (or custom for advanced users)</p>
                        @error('schema_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div id="customFields">
                    <div>
                        <label for="custom_schema" class="block text-sm font-medium text-gray-700">Schema Data (JSON-LD)</label>
                        <textarea name="custom_schema" id="custom_schema" rows="10" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md font-mono">{{ old('custom_schema', json_encode(json_decode($schema->schema_data), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Enter JSON-LD code. Make sure it's valid JSON.</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4 mt-4">
                    <label for="is_active" class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $schema->is_active) ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
                
                <div class="pt-3">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Update Schema
                    </button>
                    <a href="{{ route('admin.structured-data') }}" class="ml-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure we're loading with the correct values
        document.getElementById('custom_schema').value = JSON.stringify(
            JSON.parse(document.getElementById('custom_schema').value), 
            null, 
            2
        );
    });
</script>
@endsection
@endsection 