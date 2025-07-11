@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 font-display">Customers</h2>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">
            <i class="fas fa-plus mr-2"></i>Add New Customer
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Role Filters - Keep this separate at the top -->
            <div>
                <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Role</h3>
                <div class="flex flex-wrap gap-1">
                    <button type="button" class="role-filter px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="all" title="Show all customers">All Customers</button>
                    <button type="button" class="role-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="admin" title="Show only admin customers">Admins</button>
                    <button type="button" class="role-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="staff" title="Show only staff customers">Staff</button>
                    <button type="button" class="role-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="user" title="Show only regular customers">Regular</button>
                </div>
            </div>
            
            <!-- Two column grid layout for filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status Filters -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Status</h3>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="all">All Status</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="verified">Verified</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="unverified">Unverified</button>
                    </div>
                </div>
                
                <!-- Period Filters -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Period</h3>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="all">All Time</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="yesterday">Yesterday</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="last7days">Last 7 Days</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="recent">Last 30 Days</button>
                    </div>
                </div>
                
                <!-- More filters (placeholder for special filters in the future) -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Special Filters</h3>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="new">New Customers</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="inactive">Inactive</button>
                    </div>
                            </div>
                
                <!-- Search Box -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Search</h3>
                    <div class="relative">
                        <input id="searchBox" type="text" placeholder="Search by customer name or email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button id="searchButton" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Buttons -->
            <div class="flex justify-end gap-2">
                <button onclick="onBtExportCSV()" class="px-3 py-1.5 bg-primary text-white rounded-md flex items-center text-sm">
                    <i class="fas fa-file-csv mr-1.5"></i> Export CSV
                </button>
                <button onclick="onBtExportExcel()" class="px-3 py-1.5 bg-green-600 text-white rounded-md flex items-center text-sm">
                    <i class="fas fa-file-excel mr-1.5"></i> Export Excel
                                    </button>
                            </div>

            <!-- Divider -->
            <div class="border-t border-gray-200"></div>

            <!-- AG Grid Table -->
            <div id="users-grid" class="ag-theme-alpine w-full h-[600px]"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Add direct script import to ensure AG Grid is loaded -->
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@30.0.6/dist/ag-grid-community.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if AG Grid is available
        if (typeof agGrid === 'undefined') {
            console.error('AG Grid is not loaded!');
            return;
        }

        // Column Definitions
        const columnDefs = [
            { field: 'id', headerName: 'ID', sortable: true, filter: true, width: 80, flex: 0, minWidth: 60 },
            { 
                field: 'profile_image', 
                headerName: 'Avatar', 
                width: 90,
                flex: 0,
                minWidth: 80,
                cellRenderer: params => {
                    if (params.value) {
                        return `<img src="${params.value}" alt="Avatar" class="h-10 w-10 rounded-full">`;
                    }
                    return `<div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                              <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                              </svg>
                            </div>`;
                },
                filter: false,
                sortable: false
            },
            { field: 'name', headerName: 'Name', sortable: true, filter: true, flex: 1, minWidth: 120 },
            { field: 'email', headerName: 'Email', sortable: true, filter: true, flex: 2, minWidth: 180 },
            { 
                field: 'role', 
                headerName: 'Role', 
                sortable: true, 
                filter: true,
                width: 110,
                flex: 0.5,
                minWidth: 90,
                cellRenderer: params => {
                    if (!params.value) return '';
                    
                    const status = String(params.value).toLowerCase();
                    const statusClasses = {
                        'admin': 'bg-red-100 text-red-800',
                        'manager': 'bg-blue-100 text-blue-800',
                        'staff': 'bg-green-100 text-green-800',
                        'user': 'bg-gray-100 text-gray-800'
                    };
                    const className = statusClasses[status] || 'bg-gray-100 text-gray-800';
                    
                    return `<span class="px-2 py-1 text-xs font-medium rounded-full ${className}">${params.value}</span>`;
                }
            },
            { 
                field: 'email_verified_at', 
                headerName: 'Verified', 
                sortable: true,
                filter: true,
                width: 90,
                flex: 0,
                minWidth: 80,
                cellRenderer: params => {
                    return params.value ? 
                        '<span class="text-green-500"><i class="fas fa-check-circle"></i></span>' : 
                        '<span class="text-red-500"><i class="fas fa-times-circle"></i></span>';
                }
            },
            {
                headerName: 'Actions',
                sortable: false,
                filter: false,
                width: 120,
                flex: 0,
                minWidth: 110,
                cellRenderer: params => {
                    const id = params.data.id;
                    
                    return `
                        <div class="flex items-center space-x-2">
                            <a href="/admin/users/${id}" class="text-blue-500 hover:text-blue-700" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="/admin/users/${id}/edit" class="text-amber-500 hover:text-amber-700" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button onclick="deleteUser(${id})" class="text-red-500 hover:text-red-700" title="Delete">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                }
            }
        ];

        // Grid Options
        const gridOptions = {
            columnDefs: columnDefs,
            rowData: [],
            pagination: true,
            paginationPageSize: 10,
            domLayout: 'autoHeight',
            defaultColDef: {
                flex: 1,
                minWidth: 100,
                maxWidth: 500, // Prevent columns from getting too wide
                resizable: true,
                sortable: true,
                wrapHeaderText: true,
                autoHeaderHeight: true
            },
            animateRows: true,
            enableCellTextSelection: true,
            suppressCellFocus: true,
            rowSelection: 'multiple',
            rowMultiSelectWithClick: true,
            suppressRowClickSelection: true,
            suppressContextMenu: false,
            enableBrowserTooltips: true
        };

        // Create grid
        const gridDiv = document.querySelector('#users-grid');
        const grid = new agGrid.Grid(gridDiv, gridOptions);
        
        // For debugging
        console.log("AG Grid version:", agGrid.version);
        window.gridInstance = grid;

        // Current filter state
        let currentFilter = 'all';
        let currentSearch = '';
        
        // NEW: Add filter state object
        let filters = {
            role: [],
            status: [],
            period: []
        };

        // Function to load data with current filters
        function loadData() {
            const url = new URL('{{ route("admin.users.index") }}', window.location.origin);
            
            // Add structured filters
            if (filters.role.length > 0 || filters.status.length > 0 || filters.period.length > 0) {
                // Add as JSON string and handle in backend
                url.searchParams.append('filters[role]', filters.role.join(','));
                url.searchParams.append('filters[status]', filters.status.join(','));
                url.searchParams.append('filters[period]', filters.period.join(','));
            } 
            // Backward compatibility
            else if (currentFilter !== 'all') {
                url.searchParams.append('filter', currentFilter);
            }
            
            // Add search param if present
            if (currentSearch) {
                url.searchParams.append('search', currentSearch);
            }

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                gridOptions.api.setRowData(data);
            })
            .catch(error => {
                console.error('Error loading data:', error);
            });
        }

        // Initial data load
        loadData();

        // Set up role filter button clicks with multi-select
        document.querySelectorAll('.role-filter').forEach(button => {
            button.addEventListener('click', function() {
                const filterValue = this.dataset.filter;
                
                // If "all" is clicked, clear all role filters
                if (filterValue === 'all') {
                    document.querySelectorAll('.role-filter').forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('active', 'bg-primary', 'text-white');
                    filters.role = [];
                } else {
                    // Remove active status from "all" button
                    const allButton = document.querySelector('.role-filter[data-filter="all"]');
                    if (allButton) {
                        allButton.classList.remove('active', 'bg-primary', 'text-white');
                        allButton.classList.add('bg-gray-200', 'text-gray-700');
                    }
                    
                    // Toggle this button's state
                    if (this.classList.contains('active')) {
                        // Deactivate
                        this.classList.remove('active', 'bg-primary', 'text-white');
                        this.classList.add('bg-gray-200', 'text-gray-700');
                        
                        // Remove from filters
                        const index = filters.role.indexOf(filterValue);
                        if (index > -1) {
                            filters.role.splice(index, 1);
                        }
                        
                        // If no filters active, activate "all"
                        if (filters.role.length === 0 && allButton) {
                            allButton.classList.remove('bg-gray-200', 'text-gray-700');
                            allButton.classList.add('active', 'bg-primary', 'text-white');
                        }
                    } else {
                        // Activate
                        this.classList.remove('bg-gray-200', 'text-gray-700');
                        this.classList.add('active', 'bg-primary', 'text-white');
                        
                        // Add to filters if not already present
                        if (!filters.role.includes(filterValue)) {
                            filters.role.push(filterValue);
                        }
                    }
                }
                
                // For backward compatibility
                currentFilter = filters.role.length > 0 ? filters.role[0] : 'all';
                
                // Load data with updated filter
                loadData();
            });
        });

        // Set up status filter button clicks with multi-select
        document.querySelectorAll('.status-filter').forEach(button => {
            button.addEventListener('click', function() {
                const filterValue = this.dataset.filter;
                
                // If "all" is clicked, clear all status filters
                if (filterValue === 'all') {
                    document.querySelectorAll('.status-filter').forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('active', 'bg-primary', 'text-white');
                    filters.status = [];
                } else {
                    // Remove active status from "all" button
                    const allButton = document.querySelector('.status-filter[data-filter="all"]');
                    if (allButton) {
                        allButton.classList.remove('active', 'bg-primary', 'text-white');
                        allButton.classList.add('bg-gray-200', 'text-gray-700');
                    }
                    
                    // Toggle this button's state
                    if (this.classList.contains('active')) {
                        // Deactivate
                        this.classList.remove('active', 'bg-primary', 'text-white');
                        this.classList.add('bg-gray-200', 'text-gray-700');
                        
                        // Remove from filters
                        const index = filters.status.indexOf(filterValue);
                        if (index > -1) {
                            filters.status.splice(index, 1);
                        }
                        
                        // If no filters active, activate "all"
                        if (filters.status.length === 0 && allButton) {
                            allButton.classList.remove('bg-gray-200', 'text-gray-700');
                            allButton.classList.add('active', 'bg-primary', 'text-white');
                        }
                    } else {
                        // Activate
                        this.classList.remove('bg-gray-200', 'text-gray-700');
                        this.classList.add('active', 'bg-primary', 'text-white');
                        
                        // Add to filters if not already present
                        if (!filters.status.includes(filterValue)) {
                            filters.status.push(filterValue);
                        }
                    }
                }
                
                // Load data with updated filter
                loadData();
            });
        });

        // Set up period filter button clicks with multi-select
        document.querySelectorAll('.period-filter').forEach(button => {
            button.addEventListener('click', function() {
                const filterValue = this.dataset.filter;
                
                // If "all" is clicked, clear all period filters
                if (filterValue === 'all') {
                    document.querySelectorAll('.period-filter').forEach(btn => {
                        btn.classList.remove('active', 'bg-primary', 'text-white');
                        btn.classList.add('bg-gray-200', 'text-gray-700');
                    });
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('active', 'bg-primary', 'text-white');
                    filters.period = [];
                } else {
                    // Remove active status from "all" button
                    const allButton = document.querySelector('.period-filter[data-filter="all"]');
                    if (allButton) {
                        allButton.classList.remove('active', 'bg-primary', 'text-white');
                        allButton.classList.add('bg-gray-200', 'text-gray-700');
                    }
                    
                    // Toggle this button's state
                    if (this.classList.contains('active')) {
                        // Deactivate
                        this.classList.remove('active', 'bg-primary', 'text-white');
                        this.classList.add('bg-gray-200', 'text-gray-700');
                        
                        // Remove from filters
                        const index = filters.period.indexOf(filterValue);
                        if (index > -1) {
                            filters.period.splice(index, 1);
                        }
                        
                        // If no filters active, activate "all"
                        if (filters.period.length === 0 && allButton) {
                            allButton.classList.remove('bg-gray-200', 'text-gray-700');
                            allButton.classList.add('active', 'bg-primary', 'text-white');
                        }
                    } else {
                        // Activate
                        this.classList.remove('bg-gray-200', 'text-gray-700');
                        this.classList.add('active', 'bg-primary', 'text-white');
                        
                        // Add to filters if not already present
                        if (!filters.period.includes(filterValue)) {
                            filters.period.push(filterValue);
                        }
                    }
                }
                
                // Load data with updated filter
                loadData();
            });
        });

        // Set up search functionality
        document.getElementById('searchButton').addEventListener('click', function() {
            currentSearch = document.getElementById('searchBox').value.trim();
            loadData();
        });

        // Allow search on Enter key
        document.getElementById('searchBox').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                currentSearch = this.value.trim();
                loadData();
            }
        });

        // Export functions
        window.onBtExportCSV = function() {
            gridOptions.api.exportDataAsCsv({
                fileName: 'users_export.csv'
            });
        };

        window.onBtExportExcel = function() {
            gridOptions.api.exportDataAsExcel({
                fileName: 'users_export.xlsx'
            });
        };
    });

    // Delete user function
    function deleteUser(id) {
        if (confirm('Are you sure you want to delete this user?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/users/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush