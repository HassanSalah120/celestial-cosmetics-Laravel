@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 font-display">Orders</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.orders.create') }}" class="px-4 py-2 bg-primary text-white rounded-md shadow-sm hover:bg-primary-dark flex items-center">
                <i class="fas fa-plus mr-2"></i> Add New Order
            </a>
        </div>
    </div>

    <!-- All in one card -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6 space-y-6">
            <!-- Two column grid layout for filters -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status Filters -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Status</h3>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="all" title="Show all orders">All Orders</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="pending" title="Show only pending orders">Pending</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="processing" title="Show only processing orders">Processing</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="shipped" title="Show only shipped orders">Shipped</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="delivered" title="Show only delivered orders">Delivered</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="cancelled" title="Show only cancelled orders">Cancelled</button>
                        <button type="button" class="status-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="refunded" title="Show only refunded orders">Refunded</button>
                    </div>
                </div>

                <!-- Period Filters -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Filter by Period</h3>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="all" title="Show all orders">All Time</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="today" title="Show only orders placed today">Today</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="yesterday" title="Show only orders placed yesterday">Yesterday</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="last7days" title="Show only orders placed in the last 7 days">Last 7 Days</button>
                        <button type="button" class="period-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="last30days" title="Show only orders placed in the last 30 days">Last 30 Days</button>
                    </div>
                </div>

                <!-- Special Filters -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Special Filters</h3>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="special-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="attention" title="Show orders that require immediate attention">Needs Attention</button>
                        <button type="button" class="special-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="high-value" title="Show high-value orders">High Value</button>
                        <button type="button" class="special-filter px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="new-customers" title="Show orders from first-time customers">New Customers</button>
                    </div>
                </div>

                <!-- Search Box -->
                <div>
                    <h3 class="text-sm font-medium text-gray-500 uppercase mb-2">Search</h3>
                    <div class="relative">
                        <input id="searchBox" type="text" placeholder="Search by order #, customer name or email..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-sm">
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
            <div id="orders-grid" class="ag-theme-alpine w-full h-[600px]"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Add direct script import to ensure AG Grid is loaded -->
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@30.0.6/dist/ag-grid-community.min.js"></script>
<style>
    .price-loading {
        opacity: 0.7;
        position: relative;
    }
    .price-loading::after {
        content: '';
        position: absolute;
        width: 12px;
        height: 12px;
        top: 0;
        right: -16px;
        border-radius: 50%;
        border: 2px solid rgba(0, 0, 0, 0.1);
        border-top-color: #3490dc;
        animation: spinner 0.6s linear infinite;
    }
    @keyframes spinner {
        to {transform: rotate(360deg);}
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check if AG Grid is available
        if (typeof agGrid === 'undefined') {
            console.error('AG Grid is not loaded!');
            return;
        }

        // Format currency
        const formatCurrency = (value) => {
            if (value === null || value === undefined) return '-';
            
            // Use AJAX to get formatted price from the server
            return fetch(`{{ route('admin.format-price') }}?price=${value}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.text())
            .catch(error => {
                console.error('Error formatting price:', error);
                return value;
            });
        };
        
        // Create a custom cell renderer for formatted prices
        const currencyRenderer = (params) => {
            if (params.value === null || params.value === undefined) return '-';
            
            // Initial display while we fetch the formatted price
            const cellElement = document.createElement('span');
            cellElement.innerText = `{{ \App\Helpers\SettingsHelper::formatPrice(0) }}`.replace('0', params.value);
            
            // Add a loading state
            cellElement.classList.add('price-loading');
            
            // Fetch the formatted price from the server
            fetch(`{{ route('admin.format-price') }}?price=${params.value}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.text())
            .then(formattedPrice => {
                cellElement.innerText = formattedPrice;
                cellElement.classList.remove('price-loading');
            })
            .catch(error => {
                console.error('Error formatting price:', error);
                cellElement.classList.remove('price-loading');
            });
            
            return cellElement;
        };

        // Format date
        const formatDate = (value) => {
            if (!value) return '';
            const date = new Date(value);
            return new Intl.DateTimeFormat('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(date);
        };

        // Column Definitions
        const columnDefs = [
            { 
                field: 'id', 
                headerName: 'Order #', 
                sortable: true, 
                filter: true, 
                width: 110,
                flex: 0.5,
                minWidth: 100,
                cellRenderer: params => {
                    if (!params.value) return '';
                    return `<a href="/admin/orders/${params.value}" class="text-primary hover:text-primary-dark font-medium">#${params.value}</a>`;
                }
            },
            { 
                field: 'customer_name', 
                headerName: 'Customer', 
                sortable: true, 
                filter: true,
                width: 140,
                flex: 0.8,
                minWidth: 120
            },
            {
                field: 'email', 
                headerName: 'Email', 
                sortable: true, 
                filter: true,
                flex: 1,
                minWidth: 180
            },
            {
                field: 'total', 
                headerName: 'Total', 
                sortable: true, 
                filter: true,
                width: 110,
                flex: 0,
                minWidth: 100,
                maxWidth: 120,
                cellRenderer: currencyRenderer
            },
            { 
                field: 'status', 
                headerName: 'Status', 
                sortable: true, 
                filter: true,
                width: 140,
                flex: 0.7,
                minWidth: 120,
                cellRenderer: params => {
                    if (!params.value) return '';
                    
                    const status = String(params.value).toLowerCase();
                    const statusClasses = {
                        'pending': 'bg-blue-100 text-blue-800',
                        'processing': 'bg-amber-100 text-amber-800',
                        'completed': 'bg-green-100 text-green-800',
                        'delivered': 'bg-green-100 text-green-800',
                        'shipped': 'bg-indigo-100 text-indigo-800',
                        'cancelled': 'bg-red-100 text-red-800',
                        'refunded': 'bg-purple-100 text-purple-800'
                    };
                    const className = statusClasses[status] || 'bg-gray-100 text-gray-800';
                    
                    return `<span class="px-2 py-1 text-xs font-medium rounded-full ${className}">${params.value}</span>`;
                }
            },
            {
                field: 'payment_method', 
                headerName: 'Payment', 
                sortable: true, 
                filter: true,
                flex: 0.7,
                minWidth: 120
            },
            {
                field: 'created_at', 
                headerName: 'Date', 
                sortable: true, 
                filter: true,
                flex: 1,
                minWidth: 160,
                cellRenderer: params => formatDate(params.value)
            },
            {
                headerName: 'Actions',
                sortable: false,
                filter: false,
                width: 120,
                flex: 0,
                minWidth: 120,
                cellRenderer: params => {
                    const id = params.data.id;
                    
                    return `
                        <div class="flex items-center space-x-2">
                            <a href="/admin/orders/${id}" class="text-blue-500 hover:text-blue-700" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="/admin/orders/${id}/edit" class="text-amber-500 hover:text-amber-700" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button onclick="deleteOrder(${id})" class="text-red-500 hover:text-red-700" title="Delete Order">
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
        const gridDiv = document.querySelector('#orders-grid');
        const grid = new agGrid.Grid(gridDiv, gridOptions);
        
        // For debugging
        console.log("AG Grid version:", agGrid.version);
        window.gridInstance = grid;
        
        // Define custom field getters
        gridOptions.getRowId = (params) => params.data.id;
        
        // Current filter state
        let currentSearch = '';
        
        // Filter state object
        let filters = {
            status: [],
            period: [],
            special: []
        };

        // Function to load data with current filters
        function loadData() {
            const url = new URL('{{ route("admin.orders.index") }}', window.location.origin);
            url.searchParams.append('format', 'json');
            
            // Add structured filters
            if (filters.status.length > 0 || filters.period.length > 0 || filters.special.length > 0) {
                // Add as comma-separated values
                url.searchParams.append('filters[status]', filters.status.join(','));
                url.searchParams.append('filters[period]', filters.period.join(','));
                url.searchParams.append('filters[special]', filters.special.join(','));
            }
            
            // Add search param if present
            if (currentSearch) {
                url.searchParams.append('search', currentSearch);
            }

            // Fetch data
            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Ensure data is in the correct format
                if (data && Array.isArray(data)) {
                    gridOptions.api.setRowData(data);
                } else if (data && typeof data === 'object' && data.data && Array.isArray(data.data)) {
                    gridOptions.api.setRowData(data.data);
                } else {
                    console.error('Expected array of orders but got:', data);
                    gridOptions.api.setRowData([]);
                }
            })
            .catch(error => {
                console.error('Error loading order data:', error);
                gridOptions.api.setRowData([]);
            });
        }

        // Initial data load
        loadData();

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

        // Set up special filter button clicks with multi-select
        document.querySelectorAll('.special-filter').forEach(button => {
            button.addEventListener('click', function() {
                const filterValue = this.dataset.filter;
                
                // Toggle this button's state
                if (this.classList.contains('active')) {
                    // Deactivate
                    this.classList.remove('active', 'bg-primary', 'text-white');
                    this.classList.add('bg-gray-200', 'text-gray-700');
                    
                    // Remove from filters
                    const index = filters.special.indexOf(filterValue);
                    if (index > -1) {
                        filters.special.splice(index, 1);
                    }
                } else {
                    // Activate
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('active', 'bg-primary', 'text-white');
                    
                    // Add to filters if not already present
                    if (!filters.special.includes(filterValue)) {
                        filters.special.push(filterValue);
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
                fileName: 'orders_export.csv'
            });
        };

        window.onBtExportExcel = function() {
            gridOptions.api.exportDataAsExcel({
                fileName: 'orders_export.xlsx'
            });
        };
    });

    // Delete order function
    function deleteOrder(id) {
        if (confirm('Are you sure you want to delete this order? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/orders/${id}`;
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