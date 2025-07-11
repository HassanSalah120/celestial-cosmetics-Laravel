@extends('layouts.admin')

@section('styles')
<!-- Ensure AG Grid styles are loaded -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-alpine.css">
<style>
    /* AG Grid styling */
    .ag-theme-alpine {
        --ag-font-size: 0.875rem;
        --ag-font-family: 'Inter', sans-serif;
        --ag-header-background-color: #f9fafb;
        --ag-odd-row-background-color: #ffffff;
        --ag-row-hover-color: #f3f4f6;
        --ag-selected-row-background-color: rgba(59, 130, 246, 0.1);
    }
    
    /* Fix for CSS variables */
    :root {
        --color-primary-rgb: 43, 91, 108;  /* Replace with your primary color */
        --color-accent-rgb: 210, 85, 85;   /* Replace with your accent color */
    }
    
    .ag-header-cell-text {
        font-weight: 600;
        color: #4b5563;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .permission-badge {
        display: inline-block;
        padding: 0.125rem 0.5rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
        background-color: #dbeafe;
        color: #1e40af;
        margin: 0.125rem;
    }
    
    .permission-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.2s;
    }
    
    .user-cell {
        display: flex;
        align-items: center;
    }
    
    .user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 9999px;
        object-fit: cover;
        margin-right: 0.75rem;
    }
    
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .role-badge-admin {
        background-color: rgba(var(--color-primary-rgb), 0.2);
        color: var(--color-primary);
    }
    
    .role-badge-staff {
        background-color: rgba(var(--color-accent-rgb), 0.2);
        color: var(--color-accent);
    }
    
    .role-badge-customer {
        background-color: #f3f4f6;
        color: #374151;
    }
    
    /* Action buttons */
    .action-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    
    .action-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 1.5rem;
        height: 1.5rem;
    }
    
    /* Filter buttons styling */
    .filter-btn {
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .filter-btn.active {
        background-color: var(--color-primary) !important;
        color: white !important;
    }
    
    /* Style overrides for export buttons */
    #export-csv, #export-excel {
        display: inline-flex;
        align-items: center;
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Roles & Permissions Management</h2>
            <p class="mt-1 text-sm text-gray-600">Manage user roles and specific permissions</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                All Users
            </a>
            <a href="{{ route('admin.roles.create') }}" class="inline-flex items-center px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Assign New Role
            </a>
        </div>
    </div>

    <!-- Role Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Admin Count -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-primary/10 rounded-full p-3">
                    <svg class="h-6 w-6 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-medium text-gray-900">Administrators</h3>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">{{ $userCounts['admin'] }}</p>
                        <p class="ml-2 text-sm text-gray-600">users</p>
                    </div>
                </div>
            </div>
            <p class="mt-3 text-sm text-gray-600">Full access to all system features and settings</p>
        </div>

        <!-- Staff Count -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-accent/10 rounded-full p-3">
                    <svg class="h-6 w-6 text-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-medium text-gray-900">Staff Members</h3>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">{{ $userCounts['staff'] }}</p>
                        <p class="ml-2 text-sm text-gray-600">users</p>
                    </div>
                </div>
            </div>
            <p class="mt-3 text-sm text-gray-600">Limited access based on assigned permissions</p>
        </div>

        <!-- Customer Count -->
        <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-gray-100 rounded-full p-3">
                    <svg class="h-6 w-6 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="ml-5">
                    <h3 class="text-lg font-medium text-gray-900">Customers</h3>
                    <div class="mt-1 flex items-baseline">
                        <p class="text-3xl font-semibold text-gray-900">{{ $userCounts['user'] }}</p>
                        <p class="ml-2 text-sm text-gray-600">users</p>
                    </div>
                </div>
            </div>
            <p class="mt-3 text-sm text-gray-600">Standard accounts with no admin privileges</p>
        </div>
    </div>

    <!-- Users with Roles AG Grid -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Users with Special Roles</h3>
            <p class="mt-1 text-sm text-gray-600">Admin and staff users with elevated privileges</p>
        </div>
        
        <div class="p-6">
            <div class="mb-4 flex space-x-2">
                <button id="export-csv" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md flex items-center">
                    <i class="fas fa-file-csv mr-2"></i> Export CSV
                </button>
                <button id="export-excel" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
            </div>
            
            <!-- Filters -->
            <div class="mb-6">
                <!-- Filter sections in a grid layout -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Role filters -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Role</h3>
                        <div class="flex flex-wrap gap-1">
                            <button type="button" class="filter-btn role-filter active bg-primary text-white px-2.5 py-1.5 rounded-md text-sm" data-filter="all" data-type="role">
                                All Roles
                            </button>
                            <button type="button" class="filter-btn role-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="admin" data-type="role">
                                Admins
                            </button>
                            <button type="button" class="filter-btn role-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="staff" data-type="role">
                                Staff
                            </button>
                        </div>
                    </div>
                    
                    <!-- Date filters -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Date</h3>
                        <div class="flex flex-wrap gap-1">
                            <button type="button" class="filter-btn date-filter active bg-primary text-white px-2.5 py-1.5 rounded-md text-sm" data-filter="all" data-type="date">
                                All Time
                            </button>
                            <button type="button" class="filter-btn date-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="today" data-type="date">
                                Today
                            </button>
                            <button type="button" class="filter-btn date-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="yesterday" data-type="date">
                                Yesterday
                            </button>
                            <button type="button" class="filter-btn date-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="last7days" data-type="date">
                                Last 7 Days
                            </button>
                            <button type="button" class="filter-btn date-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="last30days" data-type="date">
                                Last 30 Days
                            </button>
                        </div>
                    </div>
                    
                    <!-- Server-side filtering options -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Advanced Options</h3>
                        <div class="flex flex-wrap gap-1">
                            <label class="inline-flex items-center px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700">
                                <input type="checkbox" id="server-filter-toggle" class="mr-2 rounded text-primary border-gray-300">
                                <span>Server-side filtering</span>
                            </label>
                            <button id="clear-filters" class="px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700">
                                <i class="fas fa-times-circle mr-1.5"></i> Clear filters
                            </button>
                        </div>
                    </div>
                    
                    <!-- Search Box -->
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Search</h3>
                        <div class="relative">
                            <input id="search-filter" type="text" placeholder="Search by name or email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                <button class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Standard HTML table instead of AG Grid -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
                <thead>
                    <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">User</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Email</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Role</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Permissions</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Last Updated</th>
                            <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Actions</th>
                    </tr>
                </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($staffUsers as $user)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($user->profile_image)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                                    @else
                                        <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=2B5B6C&color=ffffff&size=100" alt="{{ $user->name }}">
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <a href="{{ route('admin.users.show', $user) }}" class="hover:text-primary hover:underline">
                                            {{ $user->name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                @if($user->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/20 text-primary">
                                        Administrator
                                    </span>
                                @elseif($user->role === 'staff')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent/20 text-accent">
                                        Staff
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Customer
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1 max-w-md">
                                @php
                                    $permissions = is_string($user->permissions) ? json_decode($user->permissions, true) : $user->permissions;
                                    $permissions = is_array($permissions) ? $permissions : [];
                                @endphp
                                
                                @if(count($permissions) > 0)
                                    @foreach($permissions as $permission)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 permission-badge">
                                            {{ $permission }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-gray-500 text-sm italic">No permissions assigned</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">
                                @if($user->updated_at)
                                    {{ $user->updated_at->diffForHumans() }}
                                @else
                                    Not available
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex justify-end space-x-3">
                                <a href="{{ route('admin.roles.edit', $user) }}" class="text-accent hover:text-accent-dark">
                                    <span class="sr-only">Edit</span>
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-800">
                                    <span class="sr-only">View</span>
                                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $user) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to reset this user to a standard customer account? This will remove all special permissions.')">
                                        <span class="sr-only">Reset Role</span>
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p>No users with administrative roles found.</p>
                                        <p class="mt-2 text-sm">
                                            <a href="{{ route('admin.roles.create') }}" class="text-primary hover:underline">Assign your first role</a>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                </tbody>
            </table>
                
                <!-- Empty message when filters return no results -->
                <div class="empty-message py-8 text-center text-gray-500 italic hidden">
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-gray-400 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                        </svg>
                        <p>No users match the current filters.</p>
                        <p class="mt-2 text-sm">
                            <button id="clear-filters-empty" class="text-primary hover:underline">Clear all filters</button>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Keep hidden AG Grid for reference -->
            <div id="roles-grid" class="ag-theme-alpine w-full h-[0px] hidden"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Store active filters
        let activeRoleFilter = 'all';
        let activeDateFilter = 'all';
        let searchTerm = '';
        
        try {
            // Get all filter buttons and elements
            const roleButtons = document.querySelectorAll('.role-filter');
            const dateButtons = document.querySelectorAll('.date-filter');
            const searchInput = document.getElementById('search-filter');
            const searchButton = document.querySelector('.relative button');
            const clearFiltersBtn = document.getElementById('clear-filters');
            const clearFiltersEmptyBtn = document.getElementById('clear-filters-empty');
            const tableRows = document.querySelectorAll('tbody tr');
            const emptyMessage = document.querySelector('.empty-message');
            
            // Add event listeners to role filter buttons
            roleButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    // Prevent default behavior
                    event.preventDefault();
                    
                    // Get filter value
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Update active filter
                    activeRoleFilter = filterValue;
                    
                    // Update button styles
                    roleButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('active', 'bg-primary', 'text-white');
                    
                    // Apply filters
                    applyFilters();
                });
            });
            
            // Add event listeners to date filter buttons
            dateButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    // Prevent default behavior
                    event.preventDefault();
                    
                    // Get filter value
                    const filterValue = this.getAttribute('data-filter');
                    
                    // Update active filter
                    activeDateFilter = filterValue;
                    
                    // Update button styles
                    dateButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('active', 'bg-primary', 'text-white');
                    
                    // Apply filters
                    applyFilters();
                });
            });
            
            // Add event listener to search input
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    // Get search term
                    searchTerm = this.value.toLowerCase().trim();
                    
                    // Apply filters
                    applyFilters();
                });
                
                // Also add keypress event for Enter key
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchTerm = this.value.toLowerCase().trim();
                        applyFilters();
                    }
                });
            }
            
            // Add event listener to search button
            if (searchButton) {
                searchButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // Get search term
                    searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
                    
                    // Apply filters
                    applyFilters();
                });
            }
            
            // Add event listener to clear filters button
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Reset active filters
                    activeRoleFilter = 'all';
                    activeDateFilter = 'all';
                    searchTerm = '';
                    
                    // Reset button styles
                    roleButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                        
                        if (btn.getAttribute('data-filter') === 'all') {
                            btn.classList.remove('bg-gray-200', 'text-gray-700');
                            btn.classList.add('active', 'bg-primary', 'text-white');
                        }
                    });
                    
                    dateButtons.forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                        
                        if (btn.getAttribute('data-filter') === 'all') {
                            btn.classList.remove('bg-gray-200', 'text-gray-700');
                            btn.classList.add('active', 'bg-primary', 'text-white');
                        }
                    });
                    
                    // Reset search input
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    
                    // Apply filters
                    applyFilters();
                });
            }
            
            // Add event listener to empty message clear filters button
            if (clearFiltersEmptyBtn) {
                clearFiltersEmptyBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (clearFiltersBtn) {
                        clearFiltersBtn.click();
                    }
                });
            }
            
            // Function to apply filters
            function applyFilters() {
                // Get all table rows to ensure we have the latest
                const rows = document.querySelectorAll('tbody tr');
                
                let visibleRows = 0;
                
                // Apply filters to each row
                rows.forEach((row) => {
                    try {
                        // Directly extract text content from the role cell (3rd column) and name/email cells
                        const roleCell = row.querySelector('td:nth-child(3)');
                        const nameCell = row.querySelector('td:nth-child(1)');
                        const emailCell = row.querySelector('td:nth-child(2)');
                        const dateCell = row.querySelector('td:nth-child(5)');
                        
                        // Fix: Directly target the span inside the role cell
                        const roleSpan = roleCell ? roleCell.querySelector('span') : null;
                        const roleText = roleSpan ? roleSpan.textContent.trim().toLowerCase() : '';
                        
                        // Get name considering the structure (it's inside a div inside the cell)
                        const nameLink = nameCell ? nameCell.querySelector('a') : null;
                        const nameText = nameLink ? nameLink.textContent.trim().toLowerCase() : '';
                        
                        // Get email from the div inside the email cell
                        const emailDiv = emailCell ? emailCell.querySelector('div') : null;
                        const emailText = emailDiv ? emailDiv.textContent.trim().toLowerCase() : '';
                        
                        // Get date text
                        const dateDiv = dateCell ? dateCell.querySelector('div') : null;
                        const dateText = dateDiv ? dateDiv.textContent.trim().toLowerCase() : '';
                        
                        // Check if row should be visible
                        let isVisible = true;
                        
                        // Apply role filter
                        if (activeRoleFilter !== 'all') {
                            if (activeRoleFilter === 'admin') {
                                // Check if it contains administrator
                                isVisible = isVisible && roleText.includes('administrator');
                            } else if (activeRoleFilter === 'staff') {
                                // Check if it's staff but not admin
                                isVisible = isVisible && roleText.includes('staff') && !roleText.includes('administrator');
                }
            }
                        
                        // Apply date filter
                        if (activeDateFilter !== 'all' && isVisible) {
                            switch (activeDateFilter) {
                                case 'today':
                                    isVisible = dateText.includes('hour') || 
                                              dateText.includes('minute') || 
                                              dateText.includes('second') ||
                                              dateText.includes('just now');
                                    break;
                                case 'yesterday':
                                    isVisible = dateText.includes('day ago') && 
                                              !dateText.includes('days ago');
                                    break;
                                case 'last7days':
                                    isVisible = dateText.includes('hour') || 
                                              dateText.includes('minute') || 
                                              dateText.includes('second') ||
                                              dateText.includes('just now') ||
                                              dateText.includes('day ago') ||
                                              (dateText.includes('days ago') && 
                                               parseInt(dateText.match(/\d+/)?.[0] || '99') <= 7);
                                    break;
                                case 'last30days':
                                    isVisible = dateText.includes('hour') || 
                                              dateText.includes('minute') || 
                                              dateText.includes('second') ||
                                              dateText.includes('just now') ||
                                              dateText.includes('day ago') ||
                                              dateText.includes('days ago') ||
                                              (dateText.includes('week') && 
                                               parseInt(dateText.match(/\d+/)?.[0] || '99') <= 4);
                                    break;
                            }
                        }
                        
                        // Apply search filter if still visible
                        if (searchTerm && isVisible) {
                            isVisible = nameText.includes(searchTerm) || 
                                      emailText.includes(searchTerm) || 
                                      roleText.includes(searchTerm);
                        }
                        
                        // Update row visibility
                        row.style.display = isVisible ? '' : 'none';
                        
                        // Count visible rows
                        if (isVisible) {
                            visibleRows++;
                        }
                    } catch (err) {
                        // Silent error handling to avoid breaking the filter
                    }
                });
                
                // Show/hide empty message
                if (emptyMessage) {
                    emptyMessage.style.display = visibleRows === 0 ? 'block' : 'none';
                }
            }
            
            // Initial call to filtering
            applyFilters();
            
        } catch (err) {
            // Silent error handling to avoid console errors
        }
    });
</script>
@endpush 