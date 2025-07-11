@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900 font-display">Categories</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">
                <i class="fas fa-plus mr-2"></i>Add New Category
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <!-- Export Buttons -->
            <div class="mb-4 flex space-x-2">
                <button id="export-csv" class="px-4 py-2 bg-primary text-white rounded-md flex items-center">
                    <i class="fas fa-file-csv mr-2"></i> Export CSV
                </button>
                <button id="export-excel" class="px-4 py-2 bg-green-600 text-white rounded-md flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
            </div>
            
            <!-- Filters -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div>
                    <label for="search-filter" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search-filter" placeholder="Search categories..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>
            
            <!-- AG Grid Table -->
            <div id="categories-grid" class="ag-theme-alpine w-full h-[500px]"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- AG Grid CDN as a backup -->
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@30.0.6/dist/ag-grid-community.min.js"></script>

<!-- Import categories management JavaScript -->
@vite(['resources/js/admin/categories.js'])
@endpush