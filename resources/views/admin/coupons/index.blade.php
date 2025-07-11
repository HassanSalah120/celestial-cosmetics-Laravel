@php
    use Illuminate\Support\Facades\Auth;
@endphp

@extends('layouts.admin')

@push('styles')
<style>
    /* AG Grid cell content styling */
    .ag-cell-wrapper {
        width: 100%;
    }
    
    .ag-cell-value {
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    
    /* Fix for tooltip positioning */
    .relative.group {
        position: relative;
    }
    
    /* Make sure status pills don't overflow */
    .ag-theme-alpine .ag-cell .rounded-full {
        display: inline-block;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 font-display">Marketing Coupons</h2>
        
        <div class="flex space-x-3">
            <a href="{{ route('admin.coupons.create') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">
                <i class="fas fa-plus mr-2"></i>Add New Coupon
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
            <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">All Statuses</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <!-- Discount Type Filter -->
                <div>
                    <label for="type-filter" class="block text-sm font-medium text-gray-700 mb-1">Discount Type</label>
                    <select id="type-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">All Types</option>
                        <option value="percentage">Percentage</option>
                        <option value="fixed_amount">Fixed Amount</option>
                    </select>
                </div>
                
                <!-- Period Filter -->
                <div>
                    <label for="period-filter" class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <select id="period-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last7days">Last 7 Days</option>
                        <option value="thismonth">This Month</option>
                        <option value="lastmonth">Last Month</option>
                        <option value="custom">Custom Range</option>
                    </select>
                </div>
                
                <!-- Search Input -->
                <div>
                    <label for="search-filter" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" id="search-filter" placeholder="Search coupons..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Custom Date Range (initially hidden) -->
            <div id="custom-date-range" class="mb-4 grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                <div>
                    <label for="custom-start-date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="custom-start-date" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
                <div>
                    <label for="custom-end-date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <div class="flex space-x-2">
                        <input type="date" id="custom-end-date" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <button id="apply-custom-date" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">Apply</button>
                    </div>
                </div>
            </div>

            <!-- AG Grid Table -->
            <div id="coupons-grid" class="ag-theme-alpine w-full h-[650px] md:h-[600px] xl:h-[650px]"></div>
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
        return;
    }

    // Format currency
    const formatCurrency = (value) => {
        if (value === null || value === undefined) return '-';
        
        // Use server-side currency formatting to match system settings
        return fetch(`{{ route('admin.format-price') }}?price=${value}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.text())
        .catch(error => {
            return value;
        });
    };

    // Discount value renderer
    const discountValueRenderer = (params) => {
        if (params.value === null || params.value === undefined) return '-';
        
        const cellElement = document.createElement('div');
        
        if (params.data.discount_type === 'percentage') {
            cellElement.innerHTML = `<span class="font-medium">${params.value}%</span>`;
            
            // Show max discount if available
            if (params.data.maximum_discount_amount > 0) {
                const maxDiscountDiv = document.createElement('div');
                maxDiscountDiv.className = 'text-xs text-gray-500 mt-1';
                maxDiscountDiv.innerText = `Max: {{ \App\Helpers\SettingsHelper::formatPrice(0) }}`.replace('0', params.data.maximum_discount_amount);
                
                // Fetch formatted max discount
                fetch(`{{ route('admin.format-price') }}?price=${params.data.maximum_discount_amount}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(formattedPrice => {
                    maxDiscountDiv.innerText = `Max: ${formattedPrice}`;
                })
                .catch(error => {
                    // Handle error silently
                });
                
                cellElement.appendChild(maxDiscountDiv);
            }
        } else {
            // Fixed amount discount
            cellElement.innerHTML = `<span class="font-medium">{{ \App\Helpers\SettingsHelper::formatPrice(0) }}</span>`.replace('0', params.value);
            
            // Fetch formatted discount amount
            fetch(`{{ route('admin.format-price') }}?price=${params.value}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.text())
            .then(formattedPrice => {
                cellElement.querySelector('span').innerText = formattedPrice;
            })
            .catch(error => {
                // Handle error silently
            });
        }
        
        return cellElement;
    };

    // Code renderer
    const codeRenderer = (params) => {
        if (!params.value) return '';
        
        const container = document.createElement('div');
        container.className = 'relative group';
        
        // Coupon code
        const codeSpan = document.createElement('span');
        codeSpan.className = 'font-mono bg-gray-100 px-2 py-1 rounded text-sm';
        codeSpan.textContent = params.value;
        container.appendChild(codeSpan);
        
        // Copy button
        const copyButton = document.createElement('button');
        copyButton.className = 'absolute -top-1 -right-1 bg-primary text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity';
        copyButton.innerHTML = '<i class="fas fa-copy text-xs"></i>';
        copyButton.title = 'Copy to clipboard';
        copyButton.onclick = (e) => {
            e.stopPropagation();
            navigator.clipboard.writeText(params.value)
                .then(() => {
                    // Show success indicator
                    copyButton.innerHTML = '<i class="fas fa-check text-xs"></i>';
                    setTimeout(() => {
                        copyButton.innerHTML = '<i class="fas fa-copy text-xs"></i>';
                    }, 1000);
                })
                .catch(err => {
                    // Handle error silently
                });
        };
        container.appendChild(copyButton);
        
        return container;
    };

    // Status renderer
    const statusRenderer = (params) => {
        const isActive = params.value;
        const statusDiv = document.createElement('div');
        
        if (isActive) {
            statusDiv.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800';
            statusDiv.innerHTML = '<span class="w-2 h-2 mr-1.5 rounded-full bg-green-500"></span>Active';
        } else {
            statusDiv.className = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800';
            statusDiv.innerHTML = '<span class="w-2 h-2 mr-1.5 rounded-full bg-gray-500"></span>Inactive';
        }
        
        return statusDiv;
    };

    // Date formatter
    const formatDate = (params) => {
        if (!params.value) return '-';
        
        const date = new Date(params.value);
        return date.toLocaleDateString();
    };

    // Valid period renderer
    const validPeriodRenderer = (params) => {
        if (!params.data) return '-';
        
        const startDate = params.data.valid_from ? new Date(params.data.valid_from) : null;
        const endDate = params.data.valid_to ? new Date(params.data.valid_to) : null;
        
        const container = document.createElement('div');
        
        if (startDate && endDate) {
            container.innerHTML = `
                <div class="text-xs">
                    <div><span class="font-medium">From:</span> ${startDate.toLocaleDateString()}</div>
                    <div><span class="font-medium">To:</span> ${endDate.toLocaleDateString()}</div>
                </div>
            `;
        } else if (startDate) {
            container.innerHTML = `
                <div class="text-xs">
                    <div><span class="font-medium">From:</span> ${startDate.toLocaleDateString()}</div>
                    <div><span class="font-medium">To:</span> No end date</div>
                </div>
            `;
        } else if (endDate) {
            container.innerHTML = `
                <div class="text-xs">
                    <div><span class="font-medium">From:</span> Always</div>
                    <div><span class="font-medium">To:</span> ${endDate.toLocaleDateString()}</div>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="text-xs">
                    <div>Always valid</div>
                </div>
            `;
        }
        
        return container;
    };

    // Restrictions renderer
    const restrictionsRenderer = (params) => {
        if (!params.data) return '-';
        
        const container = document.createElement('div');
        const restrictions = [];
        
        if (params.data.product_ids && params.data.product_ids.length > 0) {
            restrictions.push(`${params.data.product_ids.length} products`);
        }
        
        if (params.data.category_ids && params.data.category_ids.length > 0) {
            restrictions.push(`${params.data.category_ids.length} categories`);
        }
        
        if (params.data.user_ids && params.data.user_ids.length > 0) {
            restrictions.push(`${params.data.user_ids.length} users`);
        }
        
        if (restrictions.length > 0) {
            container.innerHTML = `
                <div class="text-xs">
                    <div class="font-medium">Limited to:</div>
                    <div>${restrictions.join(', ')}</div>
                </div>
            `;
        } else {
            container.innerHTML = `<span class="text-xs text-gray-500">No restrictions</span>`;
        }
        
        return container;
    };

    // Actions renderer
    const actionsRenderer = (params) => {
        if (!params.data) return '';
        
        const container = document.createElement('div');
        container.className = 'flex space-x-2';
        
        // Edit button
        const editButton = document.createElement('a');
        editButton.href = `{{ url('/admin/coupons') }}/${params.data.id}/edit`;
        editButton.className = 'text-blue-600 hover:text-blue-900';
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.title = 'Edit';
        container.appendChild(editButton);
        
        // Toggle status button
        const toggleButton = document.createElement('button');
        toggleButton.className = params.data.is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900';
        toggleButton.innerHTML = params.data.is_active ? '<i class="fas fa-toggle-off"></i>' : '<i class="fas fa-toggle-on"></i>';
        toggleButton.title = params.data.is_active ? 'Deactivate' : 'Activate';
        toggleButton.onclick = (e) => {
            e.stopPropagation();
            window.toggleCouponStatus(params.data.id, params.data.is_active);
        };
        container.appendChild(toggleButton);
        
        // Duplicate button
        const duplicateButton = document.createElement('button');
        duplicateButton.className = 'text-purple-600 hover:text-purple-900';
        duplicateButton.innerHTML = '<i class="fas fa-copy"></i>';
        duplicateButton.title = 'Duplicate';
        duplicateButton.onclick = (e) => {
            e.stopPropagation();
            window.duplicateCoupon(params.data.id);
        };
        container.appendChild(duplicateButton);
        
        // Delete button
        const deleteButton = document.createElement('button');
        deleteButton.className = 'text-red-600 hover:text-red-900';
        deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
        deleteButton.title = 'Delete';
        deleteButton.onclick = (e) => {
            e.stopPropagation();
            window.deleteCoupon(params.data.id);
        };
        container.appendChild(deleteButton);
        
        return container;
    };

    // Column definitions
    const columnDefs = [
        {
            field: 'code',
            headerName: 'Coupon Code',
            minWidth: 150,
            flex: 1,
            cellRenderer: codeRenderer,
            sortable: true,
            filter: true
        },
        {
            field: 'discount_value',
            headerName: 'Discount',
            minWidth: 100,
            cellRenderer: discountValueRenderer,
            sortable: true,
            filter: true
        },
        {
            field: 'minimum_order_amount',
            headerName: 'Min. Order',
            minWidth: 100,
            valueFormatter: params => {
                if (params.value === null || params.value === 0) return 'None';
                return '{{ \App\Helpers\SettingsHelper::formatPrice(0) }}'.replace('0', params.value);
            },
            sortable: true,
            filter: true
        },
        {
            headerName: 'Valid Period',
            minWidth: 140,
            cellRenderer: validPeriodRenderer,
            sortable: false,
            filter: false
        },
        {
            field: 'is_active',
            headerName: 'Status',
            minWidth: 100,
            cellRenderer: statusRenderer,
            sortable: true,
            filter: true
        },
        {
            field: 'usage_count',
            headerName: 'Uses',
            minWidth: 80,
            sortable: true,
            filter: true
        },
        {
            field: 'usage_limit_per_user',
            headerName: 'Per User',
            minWidth: 80,
            valueFormatter: params => {
                if (params.value === null || params.value === 0) return 'Unlimited';
                return params.value;
            },
            sortable: true,
            filter: true
        },
        {
            headerName: 'Restrictions',
            minWidth: 100,
            cellRenderer: restrictionsRenderer,
            sortable: false,
            filter: false
        },
        {
            headerName: 'Actions',
            minWidth: 120,
            cellRenderer: actionsRenderer,
            sortable: false,
            filter: false,
            pinned: 'right'
        }
    ];

    // Grid options
    const gridOptions = {
        columnDefs: columnDefs,
        defaultColDef: {
            resizable: true,
            wrapText: true,
            autoHeight: true,
            autoHeaderHeight: true,
            cellClass: 'py-1' // Reduce padding to avoid overflow
        },
        rowHeight: 52, // Increase row height slightly to accommodate description
        headerHeight: 40, // Adjust header height
        animateRows: true,
        enableCellTextSelection: true,
        suppressCellFocus: true,
        rowSelection: 'multiple',
        rowMultiSelectWithClick: true,
        suppressRowClickSelection: true,
        suppressContextMenu: false,
        enableBrowserTooltips: true,
        // Ensure horizontal scrolling is enabled if needed
        suppressHorizontalScroll: false
    };

    // Create grid
    const gridDiv = document.querySelector('#coupons-grid');
    const grid = new agGrid.Grid(gridDiv, gridOptions);
    
    // Store grid instance for potential external access
    window.gridInstance = grid;

    // Filters state
    let currentStatus = '';
    let currentType = '';
    let currentPeriod = '';
    let customStartDate = '';
    let customEndDate = '';

    // Show/hide custom date range based on period selection
    document.getElementById('period-filter').addEventListener('change', function() {
        const customDateRange = document.getElementById('custom-date-range');
        if (this.value === 'custom') {
            customDateRange.classList.remove('hidden');
        } else {
            customDateRange.classList.add('hidden');
            currentPeriod = this.value;
            applyFilters();
        }
    });

    // Custom date range apply button
    document.getElementById('apply-custom-date').addEventListener('click', function() {
        customStartDate = document.getElementById('custom-start-date').value;
        customEndDate = document.getElementById('custom-end-date').value;
        
        if (!customStartDate || !customEndDate) {
            alert('Please select both start and end dates');
            return;
        }
        
        currentPeriod = 'custom';
        applyFilters();
    });

    // Status filter event
    document.getElementById('status-filter').addEventListener('change', function() {
        currentStatus = this.value;
        applyFilters();
    });

    // Type filter event
    document.getElementById('type-filter').addEventListener('change', function() {
        currentType = this.value;
        applyFilters();
    });

    // Search with debounce
    let searchTimeout;
    document.getElementById('search-filter').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, 300);
    });

    // Apply all filters
    const applyFilters = () => {
        const search = document.getElementById('search-filter').value;
        fetchData(currentStatus, currentType, currentPeriod, search, customStartDate, customEndDate);
    };

    // Fetch data from server
    const fetchData = (status = '', type = '', period = '', search = '', startDate = '', endDate = '') => {
        let url = '{{ route('admin.coupons.index') }}?format=json';
        if (status) {
            url += `&status=${status}`;
        }
        if (type) {
            url += `&type=${type}`;
        }
        if (period) {
            url += `&period=${period}`;
        }
        if (period === 'custom' && startDate && endDate) {
            url += `&start_date=${startDate}&end_date=${endDate}`;
        }
        if (search) {
            url += `&search=${search}`;
        }
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Ensure data is an array before setting row data
            if (data && Array.isArray(data)) {
                gridOptions.api.setRowData(data);
            } else if (data && typeof data === 'object' && data.data && Array.isArray(data.data)) {
                // If data is wrapped in a data property (common Laravel API pattern)
                gridOptions.api.setRowData(data.data);
            } else {
                gridOptions.api.setRowData([]);
            }
            
            // Force grid to refresh
            gridOptions.api.redrawRows();
        })
        .catch(error => {
            gridOptions.api.setRowData([]);
        });
    };

    // Initial data load
    fetchData();

    // Export buttons
    document.getElementById('export-csv').addEventListener('click', function() {
        gridOptions.api.exportDataAsCsv();
    });
    
    document.getElementById('export-excel').addEventListener('click', function() {
        gridOptions.api.exportDataAsExcel();
    });

    // Helper functions for coupon actions
    window.toggleCouponStatus = function(id, currentStatus) {
        if (confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this coupon?`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('/admin/coupons') }}/${id}/toggle-status`;
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    window.duplicateCoupon = function(id) {
        if (confirm('Are you sure you want to duplicate this coupon?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('/admin/coupons') }}/${id}/duplicate`;
            form.innerHTML = `@csrf`;
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    window.deleteCoupon = function(id) {
        if (confirm('Are you sure you want to delete this coupon? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('/admin/coupons') }}/${id}`;
            form.innerHTML = `
                @csrf
                @method('DELETE')
            `;
            document.body.appendChild(form);
            form.submit();
        }
    };
});
</script>
@endpush 