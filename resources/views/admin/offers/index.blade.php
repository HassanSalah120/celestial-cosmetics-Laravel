@extends('layouts.admin')

@php
use App\Facades\Settings;
@endphp

@section('styles')
<style>
    :root {
        --ag-border-color: #e5e7eb;
        --ag-header-background-color: #f9fafb;
        --ag-odd-row-background-color: #ffffff;
        --ag-row-border-color: #e5e7eb;
        --ag-row-hover-color: rgba(249, 250, 251, 0.4);
    }
    
    .ag-theme-alpine {
        --ag-font-family: 'Inter', ui-sans-serif, system-ui;
        --ag-font-size: 14px;
        --ag-grid-size: 5px;
        --ag-borders: none;
        --ag-borders-critical: none;
        --ag-borders-secondary: none;
        --ag-row-border-style: solid;
        --ag-row-border-width: 1px;
        --ag-cell-horizontal-border: none;
    }
    
    .ag-header-cell {
        color: #4b5563;
        font-weight: 600;
        padding-left: 16px;
        padding-right: 16px;
    }
    
    .ag-header-cell .ag-header-cell-text {
        font-weight: 600;
        color: #4b5563;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    
    .ag-header-row {
        height: 48px !important;
    }
    
    .ag-header {
        border-bottom: 1px solid var(--ag-border-color);
    }
    
    .ag-row {
        height: auto !important;
        min-height: 48px !important;
    }
    
    .ag-cell {
        display: flex;
        align-items: center;
        padding-top: 8px !important;
        padding-bottom: 8px !important;
        line-height: 1.4;
    }
    
    .ag-cell-wrapper {
        width: 100%;
    }
    
    .text-center .ag-cell-wrapper {
        justify-content: center;
    }
    
    .title-cell {
        max-width: 250px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .title-text {
        font-weight: 500;
        color: #111827;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .description-text {
        font-size: 13px;
        color: #6b7280;
        margin-top: 4px;
    }
    
    .product-cell {
        max-width: 150px;
        width: 100%;
        white-space: normal;
        word-break: break-word;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .price-cell {
        font-weight: 500;
    }
    
    .badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .badge-danger {
        background-color: #fee2e2;
        color: #b91c1c;
    }
    
    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .wrap-text-cell {
        white-space: normal;
        align-items: flex-start !important;
    }
    
    /* Complete unification of table appearance */
    .ag-cell-first-right-pinned {
        border-left: none !important;
    }
    
    .ag-pinned-right-header {
        border-left: none !important;
        box-shadow: none !important;
        background-color: var(--ag-header-background-color) !important;
    }
    
    .ag-pinned-right-cols-container {
        border-left: none !important;
        box-shadow: none !important;
    }
    
    .ag-pinned-right-header .ag-header-row {
        background-color: var(--ag-header-background-color) !important;
    }
    
    .ag-pinned-right-cols-container .ag-row-even {
        background-color: var(--ag-odd-row-background-color) !important;
    }
    
    .ag-pinned-right-cols-container .ag-row-odd {
        background-color: var(--ag-background-color) !important;
    }
    
    .ag-horizontal-right-spacer {
        border-left: none !important;
    }
    
    /* Ensure no dividing lines anywhere */
    .ag-root :is(.ag-side-bar, .ag-tool-panel-wrapper, .ag-tool-panel, .ag-pinned-left-cols-container, .ag-pinned-right-cols-container) {
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Fix the overall table appearance */
    .ag-layout-auto-height .ag-center-cols-clipper,
    .ag-layout-auto-height .ag-center-cols-container,
    .ag-layout-auto-height .ag-body-vertical-scroll-viewport {
        min-height: unset !important;
    }
    
    /* Force consistent row appearance */
    .ag-theme-alpine .ag-ltr .ag-cell {
        border-right: 1px solid var(--ag-border-color) !important;
    }
    
    /* Remove all dividing lines between pinned and regular columns */
    .ag-theme-alpine .ag-header,
    .ag-theme-alpine .ag-header-row,
    .ag-theme-alpine .ag-header-cell,
    .ag-theme-alpine .ag-cell,
    .ag-theme-alpine .ag-pinned-right-header,
    .ag-theme-alpine .ag-horizontal-right-spacer {
        border: none !important;
        box-shadow: none !important;
    }
    
    /* Only show horizontal borders between rows */
    .ag-theme-alpine .ag-row {
        border-bottom: 1px solid var(--ag-row-border-color) !important;
        border-top: none !important;
    }
    
    /* Hide all scrollbars */
    .ag-body-horizontal-scroll-viewport::-webkit-scrollbar,
    .ag-body-viewport::-webkit-scrollbar {
        display: none !important;
    }
    
    /* Additional unified styling */
    .ag-theme-alpine {
        --ag-grid-size: 5px;
        --ag-borders: none;
        --ag-borders-critical: none;
        --ag-borders-secondary: none;
        --ag-row-border-style: solid;
        --ag-row-border-width: 1px;
        --ag-cell-horizontal-border: none;
    }
    
    /* Force uniform cell sizing and alignment */
    .ag-header-cell, .ag-cell {
        display: flex !important;
        align-items: center !important;
    }
    
    /* Ensure actions column integrates seamlessly */
    .ag-header-cell[col-id="3"], .ag-cell[col-id="3"] {
        border: none !important;
        background-color: inherit !important;
    }
    
    .actions-cell {
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        color: #374151;
        transition: all 0.2s;
    }
    
    .action-btn:hover {
        background-color: #f9fafb;
        border-color: #d1d5db;
    }
    
    .action-btn svg {
        width: 16px;
        height: 16px;
    }
    
    .action-btn-edit:hover {
        color: #1d4ed8;
    }
    
    .action-btn-delete:hover {
        color: #dc2626;
    }
    
    /* Product tooltip styles - completely rewritten */
    .product-count {
        font-weight: 500;
    }
    
    .product-count-number {
        color: #4f46e5;
        font-weight: 600;
    }
    
    /* Custom tooltip container (used by the grid's tooltip component) */
    .custom-tooltip {
        position: absolute;
        background: white;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
        width: 220px;
        padding: 0;
        overflow: hidden;
        z-index: 9999;
    }
    
    .custom-tooltip-image {
        height: 120px;
        width: 100%;
        background-size: cover;
        background-position: center;
        background-color: #f3f4f6;
    }
    
    .custom-tooltip-content {
        padding: 12px;
    }
    
    .custom-tooltip-title {
        font-weight: 600;
        font-size: 14px;
        color: #111827;
        margin-bottom: 4px;
    }
    
    .custom-tooltip-price {
        font-weight: 500;
        font-size: 14px;
        color: #047857;
    }
</style>
@endsection

@section('content')
<!-- Tooltip Container (Fixed) -->
<div id="tooltip-container" class="fixed hidden z-50" style="pointer-events: none;"></div>

<!-- Floating Action Button for Adding New Offers -->
<div class="fixed bottom-8 right-8 z-50">
    <a href="{{ route('admin.offers.create') }}" class="flex items-center justify-center w-16 h-16 bg-primary text-white rounded-full shadow-lg hover:bg-primary-dark transition-all transform hover:scale-110">
        <i class="fas fa-plus text-xl"></i>
    </a>
</div>

<div class="space-y-6">
    <div class="flex justify-between items-center bg-white p-4 rounded-lg shadow-sm mb-4">
        <h2 class="text-2xl font-bold text-gray-900 font-display">Marketing Offers</h2>
        <div class="flex space-x-3">
            <a href="{{ route('admin.offers.create') }}" class="px-5 py-3 bg-primary text-white rounded-md hover:bg-primary-dark transition flex items-center shadow-md font-medium text-lg">
                <i class="fas fa-plus mr-2"></i>Add New Offer
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <!-- Export Buttons -->
            <div class="mb-4 flex space-x-2">
                <button class="export-csv px-4 py-2 bg-primary text-white rounded-md flex items-center">
                    <i class="fas fa-file-csv mr-2"></i> Export CSV
                </button>
                <button class="export-excel px-4 py-2 bg-green-600 text-white rounded-md flex items-center">
                    <i class="fas fa-file-excel mr-2"></i> Export Excel
                </button>
            </div>
            
            <!-- Filters -->
            <div class="mb-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="status" data-value="">All</button>
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="status" data-value="active">Active</button>
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="status" data-value="inactive">Inactive</button>
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="status" data-value="expired">Expired</button>
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="status" data-value="scheduled">Scheduled</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm active bg-primary text-white" data-filter="product" data-value="">All</button>
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="product" data-value="with-product">With Product</button>
                        <button type="button" class="filter-btn px-2.5 py-1.5 rounded-md text-sm bg-gray-200 text-gray-700" data-filter="product" data-value="without-product">Without Product</button>
                    </div>
                </div>
                <div>
                    <label for="search-filter" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" class="search-input w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Search offers...">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <button class="search-clear text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- AG Grid Table -->
            <div id="offers-grid" class="ag-theme-alpine w-full h-[600px]"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Force loading a specific version of AG Grid -->
<script src="https://cdn.jsdelivr.net/npm/ag-grid-community@29.3.5/dist/ag-grid-community.min.js"></script>
<script>
    // Add CSRF token to all fetch requests
    const csrfToken = '{{ csrf_token() }}';
    
    // Global variables
    let filterStatus = '';
    let filterProduct = '';
    let searchQuery = '';
    let gridApi;
    
    // Cell renderers
    const imageRenderer = function(params) {
        if (!params.value) {
            return '<div class="flex justify-center"><div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div></div>';
        }
        
        // Ensure the image path starts with a slash and includes storage if needed
        let imagePath = params.value;
        if (!imagePath.startsWith('/')) {
            imagePath = '/' + imagePath;
        }
        
        return `<div class="flex justify-center"><img src="${imagePath}" alt="Offer" class="w-10 h-10 object-cover rounded-md" /></div>`;
    };
    
    const titleRenderer = function(params) {
        if (!params.value) return '-';
        return `<div class="title-cell">
            <span class="title-text">${params.value}</span>
            ${params.data.description ? `<div class="text-sm text-gray-500 mt-1">${params.data.description.substring(0, 60)}${params.data.description.length > 60 ? '...' : ''}</div>` : ''}
        </div>`;
    };
    
    const priceRenderer = function(params) {
        if (params.value === null || params.value === undefined) return '-';
        return `<div class="font-medium">${formatCurrency(params.value)}</div>`;
    };
    
    const discountedPriceRenderer = function(params) {
        if (params.value === null || params.value === undefined) return '-';
        return `<div class="font-medium">${formatCurrency(params.value)}</div>`;
    };
    
    const combinedPriceRenderer = function(params) {
        if (!params.data) return '-';
        
        const originalPrice = params.data.original_price;
        const discountedPrice = params.data.discounted_price;
        
        if (!originalPrice && !discountedPrice) return '-';
        
        let html = '<div class="flex flex-col">';
        
        if (originalPrice && discountedPrice && originalPrice !== discountedPrice) {
            // Both prices exist and are different - show original with strikethrough
            html += `<div class="text-red-600 line-through text-sm">${formatCurrency(originalPrice)}</div>`;
            html += `<div class="text-green-600 font-medium">${formatCurrency(discountedPrice)}</div>`;
        } else if (originalPrice && (!discountedPrice || originalPrice === discountedPrice)) {
            // Only original price or both prices are the same
            html += `<div class="text-gray-800 font-medium">${formatCurrency(originalPrice)}</div>`;
        } else if (!originalPrice && discountedPrice) {
            // Only discounted price exists
            html += `<div class="text-green-600 font-medium">${formatCurrency(discountedPrice)}</div>`;
        }
        
        html += '</div>';
        return html;
    };
    
    const stockRenderer = function(params) {
        if (!params.data || params.data.stock === null || params.data.stock === undefined) return '-';
        
        const stock = parseInt(params.data.stock);
        const lowStockThreshold = params.data.low_stock_threshold || 5;
        
        if (stock <= 0) {
            return '<div class="text-red-600 font-medium">Out of Stock</div>';
        } else if (stock <= lowStockThreshold) {
            return `<div class="text-amber-600 font-medium">Low Stock (${stock})</div>`;
        } else {
            return `<div class="text-green-600 font-medium">${stock} in stock</div>`;
        }
    };
    
    const discountRenderer = function(params) {
        if (params.data && params.data.discount !== null && params.data.discount !== undefined) {
            const discount = parseFloat(params.data.discount);
            if (!isNaN(discount)) {
                const formattedDiscount = discount.toFixed(discount % 1 === 0 ? 0 : 2);
                return `<div class="font-semibold text-green-600">${formattedDiscount}%</div>`;
            }
        }
        
        // Calculate discount from prices if not directly provided
        if (params.data && params.data.original_price && params.data.discounted_price) {
            const originalPrice = parseFloat(params.data.original_price);
            const discountedPrice = parseFloat(params.data.discounted_price);
            
            if (!isNaN(originalPrice) && !isNaN(discountedPrice) && originalPrice > discountedPrice) {
                const calculatedDiscount = ((originalPrice - discountedPrice) / originalPrice * 100).toFixed(0);
                return `<div class="font-semibold text-green-600">${calculatedDiscount}%</div>`;
            }
        }
        
        return '-';
    };
    
    const productRenderer = function(params) {
        let productCount = 0;
        let tooltipData = {
            name: '',
            image: '',
            price: '',
            count: 0
        };
        
        // Helper function to format image URL with fallback to placeholder
        const formatImageUrl = (imgPath) => {
            // If no image path provided, use placeholder
            if (!imgPath) return 'https://placehold.co/300x300/EFEFEF/AAAAAA&text=No+Image';
            
            // Check if it's already a full URL
            if (imgPath.startsWith('http://') || imgPath.startsWith('https://')) {
                return imgPath;
            }
            
            // For relative paths, construct the full URL
            return `{{ asset('storage') }}/${imgPath}`;
        };
        
        // Process data
        if (params.data) {
            // Check for the new products relationship
            if (params.data.products && Array.isArray(params.data.products)) {
                tooltipData.count = productCount = params.data.products.length;
                if (productCount > 0) {
                    const firstProduct = params.data.products[0];
                    if (typeof firstProduct === 'object') {
                        tooltipData.name = firstProduct.name || 'Product';
                        tooltipData.image = formatImageUrl(firstProduct.image);
                        tooltipData.price = firstProduct.price || '';
                    }
                }
            }
            // Fallback to old product field for backward compatibility
            else if (params.data.product) {
                if (Array.isArray(params.data.product)) {
                    tooltipData.count = productCount = params.data.product.length;
                    if (productCount > 0) {
                        const firstProduct = params.data.product[0];
                        if (typeof firstProduct === 'object') {
                            tooltipData.name = firstProduct.name || 'Product';
                            tooltipData.image = formatImageUrl(firstProduct.image);
                            tooltipData.price = firstProduct.price || '';
                        } else {
                            tooltipData.name = firstProduct || 'Product';
                            tooltipData.image = '/images/placeholder-product.jpg';
                        }
                    }
                } else if (typeof params.data.product === 'object' && params.data.product.name) {
                    tooltipData.count = productCount = 1;
                    tooltipData.name = params.data.product.name;
                    tooltipData.image = formatImageUrl(params.data.product.image);
                    tooltipData.price = params.data.product.price || params.data.discounted_price || params.data.original_price || '';
                } else if (typeof params.data.product === 'string') {
                    tooltipData.count = productCount = 1;
                    tooltipData.name = params.data.product;
                    tooltipData.image = formatImageUrl(params.data.product_image);
                    tooltipData.price = params.data.product_price || params.data.discounted_price || params.data.original_price || '';
                } else if (params.data.product_name) {
                    tooltipData.count = productCount = 1;
                    tooltipData.name = params.data.product_name;
                    tooltipData.image = formatImageUrl(params.data.product_image);
                    tooltipData.price = params.data.product_price || params.data.discounted_price || params.data.original_price || '';
                }
            }
        }
        
        // Save tooltip data
        const tooltipJson = encodeURIComponent(JSON.stringify(tooltipData));
        
        // Return cell content with just the count and data attribute
        if (productCount > 0) {
            return `<span class="product-count" 
                         data-tooltip="${tooltipJson}" 
                         onmouseover="showProductTooltip(event, this)" 
                         onmouseout="hideProductTooltip()">
                <span class="product-count-number">${productCount}</span> Product${productCount > 1 ? 's' : ''}
            </span>`;
        }
        
        return '<span class="text-gray-400 italic">No product</span>';
    };
    
    // Global tooltip functions
    function showProductTooltip(event, element) {
        const tooltipData = JSON.parse(decodeURIComponent(element.getAttribute('data-tooltip')));
        const tooltipContainer = document.getElementById('tooltip-container');
        
        if (tooltipData && tooltipData.name) {
            const tooltipHTML = `
                <div class="bg-white rounded-lg shadow-xl overflow-hidden" style="width: 200px;">
                    <div class="p-3">
                        <div class="font-semibold text-gray-900">${tooltipData.name}</div>
                        ${tooltipData.price ? `<div class="text-green-600 font-medium">${formatCurrency(tooltipData.price)}</div>` : ''}
                        ${tooltipData.count > 1 ? `<div class="mt-1 text-xs text-gray-500">+ ${tooltipData.count-1} more product${tooltipData.count > 2 ? 's' : ''}</div>` : ''}
                    </div>
                </div>
            `;
            
            tooltipContainer.innerHTML = tooltipHTML;
            tooltipContainer.classList.remove('hidden');
            
            // Position the tooltip above the mouse
            const x = event.pageX;
            const y = event.pageY;
            
            tooltipContainer.style.left = `${x - 100}px`; // Center horizontally (width/2)
            tooltipContainer.style.top = `${y - 100}px`;  // Position above cursor
        }
    }
    
    function hideProductTooltip() {
        const tooltipContainer = document.getElementById('tooltip-container');
        tooltipContainer.classList.add('hidden');
    }
    
    const statusRenderer = function(params) {
        const status = params.value || 'inactive';
        let statusClass = '';
        
        switch(status) {
            case 'active':
                statusClass = 'bg-green-100 text-green-800';
                break;
            case 'inactive':
                statusClass = 'bg-gray-100 text-gray-800';
                break;
            case 'expired':
                statusClass = 'bg-red-100 text-red-800';
                break;
            case 'scheduled':
                statusClass = 'bg-blue-100 text-blue-800';
                break;
            default:
                statusClass = 'bg-gray-100 text-gray-800';
        }
        
        return `<div class="inline-flex justify-center px-3 py-1 ${statusClass} rounded-full text-xs font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</div>`;
    };
    
    const dateRenderer = function(params) {
        if (!params.value) return '-';
        const date = new Date(params.value);
        return `<div class="text-gray-600">${date.toLocaleDateString()}</div>`;
    };
    
    const offersActionsRenderer = function(params) {
        return `<div class="flex items-center justify-center space-x-2">
            <a href="{{ url('admin/offers') }}/${params.data.id}/edit" class="text-blue-600 hover:text-blue-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
            <button onclick="deleteOffer(${params.data.id})" class="text-red-600 hover:text-red-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>`;
    };
    
    // AG Grid initialization
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, checking AG Grid availability');
        
        if (typeof agGrid === 'undefined') {
            console.error('AG Grid library not available');
            document.querySelector('#offers-grid').innerHTML = `
                <div class="alert alert-danger m-3">
                    <strong>Error:</strong> AG Grid component failed to load.
                    <br><small>Please check your internet connection and refresh the page.</small>
                </div>
            `;
            return;
        }
        
        console.log('AG Grid available, version:', agGrid.version);
        
        const gridOptions = {
            columnDefs: [
                { 
                    field: 'id', 
                    headerName: 'ID', 
                    sortable: true, 
                    filter: true, 
                    width: 70,
                    flex: 0,
                    minWidth: 60,
                    maxWidth: 80,
                    cellClass: 'text-center'
                },
                { 
                    field: 'image', 
                    headerName: 'Image', 
                    width: 90,
                    flex: 0,
                    minWidth: 80,
                    maxWidth: 100,
                    cellRenderer: imageRenderer,
                    cellClass: 'text-center',
                    sortable: false,
                    filter: false
                },
                { 
                    field: 'title', 
                    headerName: 'Title', 
                    sortable: true, 
                    filter: true,
                    flex: 3,
                    minWidth: 220,
                    cellRenderer: titleRenderer,
                    cellClass: 'wrap-text-cell',
                    autoHeight: true,
                    wrapText: true
                },
                { 
                    field: 'price', 
                    headerName: 'Price', 
                    sortable: true, 
                    filter: true,
                    width: 140,
                    flex: 1,
                    minWidth: 130,
                    maxWidth: 160,
                    cellRenderer: combinedPriceRenderer
                },
                { 
                    headerName: 'Discount', 
                    sortable: true, 
                    filter: true,
                    width: 90,
                    flex: 0.5,
                    minWidth: 80,
                    maxWidth: 100,
                    cellRenderer: discountRenderer
                },
                { 
                    headerName: 'Product', 
                    sortable: true, 
                    filter: true,
                    flex: 2,
                    minWidth: 150,
                    maxWidth: 200,
                    cellRenderer: productRenderer,
                    cellClass: 'wrap-text-cell',
                    autoHeight: true,
                    wrapText: true
                },
                { 
                    field: 'stock', 
                    headerName: 'Stock', 
                    sortable: true, 
                    filter: true,
                    width: 120,
                    flex: 1,
                    minWidth: 100,
                    maxWidth: 140,
                    cellRenderer: stockRenderer
                },
                { 
                    field: 'status',
                    headerName: 'Status', 
                    sortable: true, 
                    filter: true,
                    width: 110,
                    flex: 0.5,
                    minWidth: 100,
                    maxWidth: 120,
                    cellRenderer: statusRenderer,
                    cellClass: 'text-center'
                },
                { 
                    field: 'expires_at', 
                    headerName: 'Expires', 
                    sortable: true, 
                    filter: true,
                    width: 140,
                    flex: 1,
                    minWidth: 130,
                    maxWidth: 160,
                    cellRenderer: dateRenderer
                },
                {
                    headerName: 'Actions',
                    sortable: false,
                    filter: false,
                    width: 100,
                    flex: 0,
                    minWidth: 100,
                    maxWidth: 130,
                    cellRenderer: offersActionsRenderer,
                    pinned: false,
                    suppressMenu: true
                }
            ],
            defaultColDef: {
                flex: 1,
                minWidth: 100,
                resizable: true,
                sortable: true,
                wrapHeaderText: true,
                autoHeaderHeight: true
            },
            suppressColumnVirtualisation: true,
            animateRows: true,
            enableCellTextSelection: true,
            suppressCellFocus: true,
            rowSelection: 'multiple',
            rowMultiSelectWithClick: true,
            suppressRowClickSelection: true,
            suppressContextMenu: false,
            suppressMovableColumns: true,
            enableBrowserTooltips: true,
            pagination: true,
            paginationPageSize: 10,
            domLayout: 'autoHeight',
            getRowHeight: function(params) {
                return params.data && (params.data.title && params.data.title.length > 30 || 
                       params.data.product && params.data.product.name && params.data.product.name.length > 25) ? 80 : 50;
            }
        };
        
        // Initialize the grid
        const gridDiv = document.querySelector('#offers-grid');
        
        // Fix for AG Grid initialization
        if (typeof agGrid.createGrid === 'function') {
            // New AG Grid API (version 30+)
            gridApi = agGrid.createGrid(gridDiv, gridOptions);
            console.log("Grid initialized with createGrid method");
        } else if (typeof agGrid.Grid === 'function') {
            // Old AG Grid API
            new agGrid.Grid(gridDiv, gridOptions);
            gridApi = gridOptions.api;
            console.log("Grid initialized with Grid constructor method");
                        } else {
            // Fallback initialization if both methods fail
            console.error('AG Grid initialization failed - library not loaded correctly');
            document.querySelector('#offers-grid').innerHTML = `
                <div class="alert alert-danger m-3">
                    <strong>Error:</strong> Failed to initialize grid component.
                    <br><small>Please refresh the page to try again or contact support.</small>
                </div>
            `;
            return;
        }
        
        // Check if grid API is available
        if (!gridApi) {
            console.error("Grid API not available after initialization");
            document.querySelector('#offers-grid').innerHTML = `
                <div class="alert alert-danger m-3">
                    <strong>Error:</strong> Grid API initialization failed.
                    <br><small>Please refresh the page to try again or contact support.</small>
                </div>
            `;
            return;
        }
        
        console.log("Grid API initialized successfully:", gridApi);
        
        // Fetch data
        fetchData();
        
        // Setup event listeners
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const filterType = this.dataset.filter;
                const value = this.dataset.value;
                
                // Update active state for buttons in the same group
                document.querySelectorAll(`.filter-btn[data-filter="${filterType}"]`).forEach(btn => {
                    btn.classList.remove('active', 'bg-primary', 'text-white');
                    btn.classList.add('bg-gray-200', 'text-gray-700');
                });
                this.classList.remove('bg-gray-200', 'text-gray-700');
                this.classList.add('active', 'bg-primary', 'text-white');
                
                // Update filter values
                if (filterType === 'status') {
                    filterStatus = value;
                } else if (filterType === 'product') {
                    filterProduct = value;
                }
                
                fetchData();
            });
        });

        // Search input with debounce
        const searchInput = document.querySelector('.search-input');
        let debounceTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                searchQuery = this.value.trim();
                fetchData();
            }, 300);
        });
        
        // Clear search
        document.querySelector('.search-clear').addEventListener('click', function() {
            searchInput.value = '';
            searchQuery = '';
            fetchData();
        });
        
        // Export buttons
        document.querySelector('.export-csv').addEventListener('click', function() {
            gridApi.exportDataAsCsv();
        });
        
        document.querySelector('.export-excel').addEventListener('click', function() {
            gridApi.exportDataAsExcel();
                });
            });
            
    // Data fetching function
    function fetchData() {
        const params = new URLSearchParams();
        if (filterStatus) params.append('status', filterStatus);
        if (filterProduct) params.append('product', filterProduct);
        if (searchQuery) params.append('search', searchQuery);
        params.append('format', 'json');
        
        const url = `{{ route('admin.offers.index') }}?${params.toString()}`;
        console.log('Fetching data from:', url);
        
        fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.error || `HTTP error! Status: ${response.status}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (gridApi) {
                    gridApi.setRowData(data);
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                // Show error in the grid area
                document.querySelector('#offers-grid').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <strong>Error loading data:</strong> ${error.message || 'Unknown error occurred'}
                        <br><small>Please refresh the page to try again or contact support.</small>
                    </div>
                `;
            });
    }
    
    // Toggle offer status function
    function toggleOfferStatus(id) {
        fetch(`{{ url('admin/offers') }}/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchData();
            } else {
                alert(data.message || 'Failed to update offer status');
            }
        })
        .catch(error => {
            console.error('Error toggling status:', error);
            alert('Failed to update offer status. Please try again.');
        });
    }
    
    // Duplicate offer function
    function duplicateOffer(id) {
        if (!confirm('Are you sure you want to duplicate this offer?')) {
            return;
        }
        
        fetch(`{{ url('admin/offers') }}/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchData();
            } else {
                alert(data.message || 'Failed to duplicate offer');
            }
        })
        .catch(error => {
            console.error('Error duplicating offer:', error);
            alert('Failed to duplicate offer. Please try again.');
        });
    }
    
    // Delete offer function
    function deleteOffer(id) {
        if (!confirm('Are you sure you want to delete this offer? This action cannot be undone.')) {
            return;
        }
        
        fetch(`{{ url('admin/offers') }}/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
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
            if (data.success) {
                fetchData();
            } else {
                alert(data.message || 'Failed to delete offer');
            }
        })
        .catch(error => {
            console.error('Error deleting offer:', error);
            alert('Failed to delete offer. Please try again.');
        });
    }
    
    function formatCurrency(value) {
        if (value === null || value === undefined) return '';
        const formattedValue = parseFloat(value).toFixed(2);
        return '{{ Settings::get('currency_symbol', '$') }} ' + formattedValue;
    }
</script>
@endpush 