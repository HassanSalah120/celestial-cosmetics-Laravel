@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
            <h2 class="text-2xl font-bold text-gray-900 font-display">Products</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.products.create') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition">
                <i class="fas fa-plus mr-2"></i>Add New Product
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
            <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="category-filter" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                    </select>
                </div>
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
                    <input type="text" id="search-filter" placeholder="Search by product name or slug..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>
            </div>
            
            <!-- AG Grid Table -->
            <div id="products-grid" class="ag-theme-alpine w-full h-[600px]"></div>
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
                console.error('Error formatting price:', error);
                return value;
            });
        };

        // Create a custom cell renderer for formatted prices with discount
        const discountedPriceRenderer = (params) => {
            if (params.value === null || params.value === undefined) return '-';
            
            // Create container element
            const cellElement = document.createElement('div');
            
            // If product has a discount
            if (params.data.discount_percent > 0) {
                // Calculate discounted price
                const discountedPrice = params.value * (1 - params.data.discount_percent / 100);
                
                // Create elements for original and discounted prices
                const originalPriceSpan = document.createElement('span');
                originalPriceSpan.className = 'line-through text-gray-500';
                originalPriceSpan.innerText = `{{ \App\Helpers\SettingsHelper::formatPrice(0) }}`.replace('0', params.value);
                
                const discountedPriceSpan = document.createElement('span');
                discountedPriceSpan.className = 'text-green-600 font-medium ml-1';
                discountedPriceSpan.innerText = `{{ \App\Helpers\SettingsHelper::formatPrice(0) }}`.replace('0', discountedPrice);
                
                // Add elements to container
                cellElement.appendChild(originalPriceSpan);
                cellElement.appendChild(discountedPriceSpan);
                
                // Fetch formatted original price
                fetch(`{{ route('admin.format-price') }}?price=${params.value}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(formattedPrice => {
                    originalPriceSpan.innerText = formattedPrice;
                })
                .catch(error => {
                    console.error('Error formatting price:', error);
                });
                
                // Fetch formatted discounted price
                fetch(`{{ route('admin.format-price') }}?price=${discountedPrice}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.text())
                .then(formattedPrice => {
                    discountedPriceSpan.innerText = formattedPrice;
                })
                .catch(error => {
                    console.error('Error formatting price:', error);
                });
            } else {
                // For regular prices without discount
                cellElement.innerText = `{{ \App\Helpers\SettingsHelper::formatPrice(0) }}`.replace('0', params.value);
                
                // Fetch formatted price
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
                })
                .catch(error => {
                    console.error('Error formatting price:', error);
                });
            }
            
            return cellElement;
        };

        // Image renderer
        const imageRenderer = (params) => {
            if (!params.value) return '<div class="flex justify-center"><div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div></div>';
            return `<div class="flex justify-center"><img src="${params.value}" alt="Product" class="w-10 h-10 object-cover rounded-md" /></div>`;
        };

        // Stock status renderer
        const stockStatusRenderer = (params) => {
            if (params.value === null || params.value === undefined) return '';
            
            const inStock = parseInt(params.value) > 0;
            const className = inStock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            const text = inStock ? 'In Stock' : 'Out of Stock';
            
            return `<span class="px-2 py-1 text-xs font-medium rounded-full ${className}">${text}</span>`;
        };

        // Status renderer
        const statusRenderer = (params) => {
            if (!params.value) return '';
            
            const status = params.value.toLowerCase();
            const className = status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
            
            return `<span class="px-2 py-1 text-xs font-medium rounded-full ${className}">${params.value}</span>`;
        };

        // Column Definitions
        const columnDefs = [
            { 
                field: 'id', 
                headerName: 'ID', 
                sortable: true, 
                filter: true, 
                width: 80,
                minWidth: 80,
                maxWidth: 100
            },
            {
                field: 'image', 
                headerName: 'Image', 
                sortable: false, 
                filter: false,
                width: 100,
                minWidth: 100,
                maxWidth: 120,
                cellRenderer: imageRenderer,
                cellClass: 'text-center'
            },
            { 
                field: 'name', 
                headerName: 'Product Name', 
                sortable: true, 
                filter: true,
                flex: 2,
                minWidth: 200,
                cellRenderer: params => {
                    if (!params.value) return '';
                    return `<a href="/admin/products/${params.data.id}/edit" class="text-primary hover:text-primary-dark font-medium">${params.value}</a>`;
                }
            },
            {
                field: 'discount_percent', 
                headerName: 'Discount', 
                sortable: true, 
                filter: true,
                width: 100,
                minWidth: 90,
                maxWidth: 120,
                cellRenderer: params => {
                    if (params.value === null || params.value === undefined || params.value === 0) return '-';
                    return `<span class="text-green-600 font-medium">${params.value}%</span>`;
                }
            },
            {
                field: 'category', 
                headerName: 'Category', 
                sortable: true, 
                filter: true,
                flex: 1,
                minWidth: 150
            },
            {
                field: 'price', 
                headerName: 'Price', 
                sortable: true, 
                filter: true,
                flex: 1,
                minWidth: 150,
                cellRenderer: discountedPriceRenderer
            },
            {
                field: 'stock', 
                headerName: 'Stock', 
                sortable: true, 
                filter: true,
                width: 100,
                minWidth: 80,
                maxWidth: 120,
                cellRenderer: stockStatusRenderer,
                cellClass: 'text-center'
            },
            { 
                field: 'status', 
                headerName: 'Status', 
                sortable: true, 
                filter: true,
                width: 120,
                minWidth: 100,
                maxWidth: 140,
                cellRenderer: statusRenderer,
                cellClass: 'text-center'
            },
            {
                headerName: 'Actions',
                sortable: false,
                filter: false,
                width: 140,
                minWidth: 120,
                maxWidth: 160,
                cellRenderer: params => {
                    const id = params.data.id;
                    
                    return `
                        <div class="flex items-center space-x-2">
                            <a href="/admin/products/${id}" class="text-blue-500 hover:text-blue-700" title="View">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="/admin/products/${id}/edit" class="text-amber-500 hover:text-amber-700" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <button onclick="deleteProduct(${id})" class="text-red-500 hover:text-red-700" title="Delete">
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
        const gridDiv = document.querySelector('#products-grid');
        const grid = new agGrid.Grid(gridDiv, gridOptions);
        
        // For debugging
        console.log("AG Grid version:", agGrid.version);
        window.gridInstance = grid;

        // Fetch data from server
        const fetchData = (category = '', status = '', search = '') => {
            let url = '{{ route('admin.products.index') }}?format=json';
            if (category) {
                url += `&category=${category}`;
            }
            if (status) {
                url += `&status=${status}`;
            }
            if (search) {
                url += `&search=${search}`;
            }
            
            console.log("Fetching products from URL:", url);
            
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
                console.log("Response received, parsing JSON...");
                return response.json();
            })
            .then(data => {
                console.log("Received products data:", data);
                
                // Ensure data is an array before setting row data
                if (data && Array.isArray(data)) {
                    console.log(`Setting ${data.length} products to grid`);
                    gridOptions.api.setRowData(data);
                } else if (data && typeof data === 'object' && data.data && Array.isArray(data.data)) {
                    // If data is wrapped in a data property (common Laravel API pattern)
                    console.log(`Using data from data.data property, found ${data.data.length} items`);
                    gridOptions.api.setRowData(data.data);
                } else {
                    console.error('Expected array of products but got:', data);
                    gridOptions.api.setRowData([]);
                }
                
                // Force grid to refresh
                gridOptions.api.redrawRows();
                
                // Check if we have data in the grid
                const rowCount = gridOptions.api.getDisplayedRowCount();
                console.log("Products grid now has", rowCount, "visible rows");
            })
            .catch(error => {
                console.error('Error loading product data:', error);
                gridOptions.api.setRowData([]);
            });
        };

        // Initial data load
        fetchData();

        // Filter functionality
        const applyFilters = () => {
            const category = document.getElementById('category-filter').value;
            const status = document.getElementById('status-filter').value;
            const search = document.getElementById('search-filter').value;
            fetchData(category, status, search);
        };

        document.getElementById('category-filter').addEventListener('change', applyFilters);
        document.getElementById('status-filter').addEventListener('change', applyFilters);
        
        // Add debounce for search input
        let searchTimeout;
        document.getElementById('search-filter').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 300);
        });

        // Set up export buttons
        document.getElementById('export-csv').addEventListener('click', function() {
            gridOptions.api.exportDataAsCsv();
        });
        
        document.getElementById('export-excel').addEventListener('click', function() {
            gridOptions.api.exportDataAsExcel();
        });
    });

    // Delete product function
    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${id}`;
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