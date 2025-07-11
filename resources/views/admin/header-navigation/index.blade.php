@extends('layouts.admin')

@section('title', 'Header Navigation')

@push('styles')
<style>
    .nav-item {
        cursor: move;
    }
    .sortable-ghost {
        opacity: 0.4;
        background-color: #f3f4f6;
    }
</style>
@endpush

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Header Management</h1>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
        <div class="py-4">
            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Tabs -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="#navigation-tab" class="tab-link border-primary text-primary hover:text-primary-dark hover:border-primary-dark px-1 py-4 font-medium text-sm border-b-2 active" onclick="showTab('navigation-tab')">
                        Navigation Items
                    </a>
                    <a href="#settings-tab" class="tab-link border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 px-1 py-4 font-medium text-sm border-b-2" onclick="showTab('settings-tab')">
                        Header Settings
                    </a>
                </nav>
            </div>

            <!-- Navigation Items Tab -->
            <div id="navigation-tab" class="tab-content py-4">
                <!-- Add New Navigation Item Button -->
                <div class="mb-6">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" onclick="openAddModal()">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Navigation Item
                    </button>
                </div>

                <!-- Navigation Items List -->
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col">
                            <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arabic Name</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route/URL</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                                    <th scope="col" class="relative px-6 py-3">
                                                        <span class="sr-only">Actions</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200" id="navigation-items">
                                                @foreach($navigationItems as $item)
                                                <tr data-id="{{ $item->id }}" class="nav-item">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $item->name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $item->name_ar }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $item->route ?: $item->url }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $item->sort_order }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                        <button type="button" class="text-primary hover:text-primary-dark mr-3" onclick="openEditModal({{ $item->id }})">
                                                            Edit
                                                        </button>
                                                        <button type="button" class="text-red-600 hover:text-red-900" onclick="deleteItem({{ $item->id }})">
                                                            Delete
                                                        </button>
                                                    </td>
                                                </tr>
                                                @if($item->has_dropdown && $item->children->count() > 0)
                                                    @foreach($item->children as $child)
                                                    <tr data-id="{{ $child->id }}" class="nav-item bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 pl-12">
                                                            ↳ {{ $child->name }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $child->name_ar }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $child->route ?: $child->url }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                                {{ $child->is_active ? 'Active' : 'Inactive' }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $child->sort_order }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <button type="button" class="text-primary hover:text-primary-dark mr-3" onclick="openEditModal({{ $child->id }})">
                                                                Edit
                                                            </button>
                                                            <button type="button" class="text-red-600 hover:text-red-900" onclick="deleteItem({{ $child->id }})">
                                                                Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Header Settings Tab -->
            <div id="settings-tab" class="tab-content py-4 hidden">
                <div class="bg-white shadow rounded-lg">
                    <div class="p-6">
                        <form action="{{ route('admin.header-navigation.settings') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                <div class="sm:col-span-3">
                                    <label for="header_style" class="block text-sm font-medium text-gray-700">Header Style</label>
                                    <select id="header_style" name="header_style" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                                        <option value="default" {{ $headerSettings->header_style == 'default' ? 'selected' : '' }}>Default</option>
                                        <option value="centered" {{ $headerSettings->header_style == 'centered' ? 'selected' : '' }}>Centered</option>
                                        <option value="minimal" {{ $headerSettings->header_style == 'minimal' ? 'selected' : '' }}>Minimal</option>
                                        <option value="full-width" {{ $headerSettings->header_style == 'full-width' ? 'selected' : '' }}>Full Width</option>
                                    </select>
                                </div>

                                <div class="sm:col-span-6">
                                    <div class="mt-4 space-y-4">
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_logo" name="show_logo" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_logo ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_logo" class="font-medium text-gray-700">Show Logo</label>
                                                <p class="text-gray-500">Display the site logo in the header</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_profile" name="show_profile" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_profile ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_profile" class="font-medium text-gray-700">Show Profile Link</label>
                                                <p class="text-gray-500">Display the user profile/account link in the header</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_store_hours" name="show_store_hours" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_store_hours ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_store_hours" class="font-medium text-gray-700">Show Store Hours</label>
                                                <p class="text-gray-500">Display store hours in the header</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_search" name="show_search" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_search ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_search" class="font-medium text-gray-700">Show Search</label>
                                                <p class="text-gray-500">Display the search box in the header</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_cart" name="show_cart" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_cart ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_cart" class="font-medium text-gray-700">Show Cart</label>
                                                <p class="text-gray-500">Display the shopping cart in the header</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_language_switcher" name="show_language_switcher" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_language_switcher ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_language_switcher" class="font-medium text-gray-700">Show Language Switcher</label>
                                                <p class="text-gray-500">Display the language switcher in the header</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="sticky_header" name="sticky_header" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->sticky_header ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="sticky_header" class="font-medium text-gray-700">Sticky Header</label>
                                                <p class="text-gray-500">Make the header stick to the top when scrolling</p>
                                            </div>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="flex items-center h-5">
                                                <input id="show_auth_links" name="show_auth_links" type="checkbox" value="1" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded" {{ $headerSettings->show_auth_links ? 'checked' : '' }}>
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label for="show_auth_links" class="font-medium text-gray-700">Show Auth Links</label>
                                                <p class="text-gray-500">Display login/register links for guests</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="navigationModal">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="navigationForm" method="POST" onsubmit="return submitForm(event)">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" onkeyup="suggestRoutes(this.value)" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="name_ar" class="block text-sm font-medium text-gray-700">Arabic Name</label>
                        <input type="text" name="name_ar" id="name_ar" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="route" class="block text-sm font-medium text-gray-700">Route</label>
                        <select name="route" id="route" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="">Select a route</option>
                            @foreach($routes as $route)
                            <option value="{{ $route }}">{{ $route }}</option>
                            @endforeach
                        </select>
                        <div id="route-suggestions" class="mt-2 text-sm text-gray-600"></div>
                    </div>
                    <div class="mb-4">
                        <label for="url" class="block text-sm font-medium text-gray-700">URL (if no route selected)</label>
                        <input type="text" name="url" id="url" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="translation_key" class="block text-sm font-medium text-gray-700">Translation Key</label>
                        <input type="text" name="translation_key" id="translation_key" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="mb-4">
                        <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Item</label>
                        <select name="parent_id" id="parent_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                            <option value="">None (Top Level)</option>
                            @foreach($navigationItems as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" class="mt-1 focus:ring-primary focus:border-primary block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="is_active" id="is_active" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="is_active" class="font-medium text-gray-700">Active</label>
                        </div>
                    </div>
                    <div class="flex items-start mb-4">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="has_dropdown" id="has_dropdown" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="has_dropdown" class="font-medium text-gray-700">Has Dropdown</label>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="open_in_new_tab" id="open_in_new_tab" class="focus:ring-primary h-4 w-4 text-primary border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="open_in_new_tab" class="font-medium text-gray-700">Open in New Tab</label>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm">
                        Save
                    </button>
                    <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Sortable.js library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Store all routes in a JavaScript array for searching
    const allRoutes = [
        @foreach($routes as $route)
            "{{ $route }}",
        @endforeach
    ];

    // Check for hash in URL and show the appropriate tab
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash === '#settings-tab') {
            showTab('settings-tab');
        }
        
        // Check for success message in URL
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success')) {
            // Create and show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4';
            alertDiv.role = 'alert';
            alertDiv.innerHTML = `
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"> Navigation item saved successfully.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <svg onclick="this.parentElement.parentElement.remove()" class="fill-current h-6 w-6 text-green-500 cursor-pointer" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            `;
            document.querySelector('.container').insertBefore(alertDiv, document.querySelector('.container').firstChild);
            
            // Remove success parameter from URL without page reload
            const url = new URL(window.location);
            url.searchParams.delete('success');
            window.history.replaceState({}, '', url);
        }
        
        // Initialize Sortable for the navigation items table
        const navItemsTable = document.getElementById('navigation-items');
        if (navItemsTable) {
            new Sortable(navItemsTable, {
                animation: 150,
                handle: '.nav-item',
                onEnd: function() {
                    const items = Array.from(document.querySelectorAll('.nav-item')).map((el, index) => ({
                        id: el.dataset.id,
                        sort_order: index + 1,
                        parent_id: el.dataset.parentId || null
                    }));

                    fetch('{{ route('admin.header-navigation.update-order') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ items })
                    });
                }
            });
        }
    });

    function suggestRoutes(name) {
        if (!name || name.length < 2) {
            document.getElementById('route-suggestions').innerHTML = '';
            return;
        }
        
        // Convert name to lowercase for case-insensitive matching
        const nameLower = name.toLowerCase();
        
        // Find matching routes
        const matchingRoutes = allRoutes.filter(route => {
            const routeLower = route.toLowerCase();
            return routeLower.includes(nameLower) || 
                   // Check for words in the name matching parts of the route
                   nameLower.split(' ').some(word => word.length > 2 && routeLower.includes(word));
        }).slice(0, 5); // Limit to top 5 matches
        
        if (matchingRoutes.length > 0) {
            let html = '<p class="font-medium">Suggested routes:</p><ul class="mt-1 space-y-1">';
            matchingRoutes.forEach(route => {
                html += `<li><a href="#" onclick="selectRoute('${route}'); return false;" class="text-primary hover:underline">${route}</a></li>`;
            });
            html += '</ul>';
            document.getElementById('route-suggestions').innerHTML = html;
        } else {
            document.getElementById('route-suggestions').innerHTML = '';
        }
    }
    
    function selectRoute(route) {
        document.getElementById('route').value = route;
        document.getElementById('route-suggestions').innerHTML = '';
    }

    function submitForm(event) {
        event.preventDefault(); // Prevent default form submission
        
        // Get form data
        const form = event.target;
        const formData = new FormData(form);
        
        // Determine if this is a create or update operation
        const isUpdate = form.querySelector('input[name="_method"]')?.value === 'PUT';
        
        // Handle checkboxes - convert to boolean values
        const checkboxFields = ['open_in_new_tab', 'is_active', 'has_dropdown'];
        checkboxFields.forEach(field => {
            // Remove any existing value for the field
            if (formData.has(field)) {
                formData.delete(field);
                formData.append(field, '1'); // If checkbox is checked, set to 1 (true)
            } else {
                formData.append(field, '0'); // If checkbox is not checked, set to 0 (false)
            }
        });
        
        // Debug: Log form data
        console.log('Form action:', form.action);
        console.log('Form method:', isUpdate ? 'POST (with _method=PUT)' : form.method);
        formData.forEach((value, key) => {
            console.log(`${key}: ${value}`);
        });
        
        // Get the CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        // Send the form data via fetch
        fetch(form.action, {
            method: isUpdate ? 'POST' : form.method,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (response.ok) {
                // Redirect to the index page with a success message
                window.location.href = "{{ route('admin.header-navigation.index') }}?success=1";
            } else {
                return response.json().then(errors => {
                    console.error('Server errors:', errors);
                    throw new Error(JSON.stringify(errors));
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving. Please check the console for details.');
        });
        
        return false;
    }

    function openAddModal() {
        document.getElementById('navigationForm').reset();
        document.getElementById('navigationForm').action = "{{ route('admin.header-navigation.store') }}";
        // Remove any existing method override inputs
        const methodInputs = document.querySelectorAll('input[name="_method"]');
        methodInputs.forEach(input => input.remove());
        document.getElementById('navigationModal').classList.remove('hidden');
    }

    function openEditModal(id) {
        // Reset form and remove any existing method override inputs
        document.getElementById('navigationForm').reset();
        const methodInputs = document.querySelectorAll('input[name="_method"]');
        methodInputs.forEach(input => input.remove());
        
        // Fetch item data and populate form
        fetch(`{{ url('admin/header-navigation') }}/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('name').value = data.name;
                document.getElementById('name_ar').value = data.name_ar || '';
                document.getElementById('route').value = data.route || '';
                document.getElementById('url').value = data.url || '';
                document.getElementById('translation_key').value = data.translation_key || '';
                document.getElementById('parent_id').value = data.parent_id || '';
                document.getElementById('sort_order').value = data.sort_order;
                document.getElementById('is_active').checked = Boolean(data.is_active);
                document.getElementById('has_dropdown').checked = Boolean(data.has_dropdown);
                document.getElementById('open_in_new_tab').checked = Boolean(data.open_in_new_tab);
                
                document.getElementById('navigationForm').action = `{{ url('admin/header-navigation') }}/${id}`;
                document.getElementById('navigationForm').insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PUT">');
                document.getElementById('navigationModal').classList.remove('hidden');
            });
    }

    function closeModal() {
        document.getElementById('navigationModal').classList.add('hidden');
    }

    function deleteItem(id) {
        if (confirm('Are you sure you want to delete this navigation item?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('admin/header-navigation') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }

    function showTab(tabId) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.add('hidden');
        });

        // Remove active class from all tab links
        document.querySelectorAll('.tab-link').forEach(link => {
            link.classList.remove('border-primary', 'text-primary');
            link.classList.add('border-transparent', 'text-gray-500');
        });

        // Show the selected tab content
        document.getElementById(tabId).classList.remove('hidden');

        // Add active class to the clicked tab link
        document.querySelector(`[href="#${tabId}"]`).classList.remove('border-transparent', 'text-gray-500');
        document.querySelector(`[href="#${tabId}"]`).classList.add('border-primary', 'text-primary');
    }
</script>
@endpush
