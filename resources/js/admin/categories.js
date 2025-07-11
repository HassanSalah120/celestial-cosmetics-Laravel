// Categories Management Scripts

document.addEventListener('DOMContentLoaded', function() {
    initializeCategoriesGrid();
    setupCategoryEventListeners();
});

/**
 * Initialize the categories grid
 */
function initializeCategoriesGrid() {
    // Check if AG Grid is available
    if (typeof agGrid === 'undefined') {
        console.error('AG Grid is not loaded!');
        return;
    }

    // Image renderer
    const imageRenderer = (params) => {
        if (!params.value) return '<div class="flex justify-center"><div class="w-10 h-10 bg-gray-100 rounded-md flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div></div>';
        return `<div class="flex justify-center"><img src="${params.value}" alt="Category" class="w-10 h-10 object-cover rounded-md" /></div>`;
    };

    // Status renderer
    const statusRenderer = (params) => {
        if (!params.value) return '';
        
        const status = params.value.toLowerCase();
        const className = status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
        
        return `<span class="px-2 py-1 text-xs font-medium rounded-full ${className}">${params.value}</span>`;
    };

    // Count renderer
    const countRenderer = (params) => {
        if (params.value === null || params.value === undefined) return '0';
        return `<span class="font-medium">${params.value}</span>`;
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
            headerName: 'Category Name', 
            sortable: true, 
            filter: true,
            flex: 2,
            minWidth: 200,
            cellRenderer: params => {
                if (!params.value) return '';
                return `<a href="/admin/categories/${params.data.id}/edit" class="text-primary hover:text-primary-dark font-medium">${params.value}</a>`;
            }
        },
        {
            field: 'slug', 
            headerName: 'Slug', 
            sortable: true, 
            filter: true,
            flex: 1,
            minWidth: 140
        },
        {
            field: 'product_count', 
            headerName: 'Products', 
            sortable: true, 
            filter: true,
            width: 120,
            minWidth: 100,
            maxWidth: 140,
            cellRenderer: countRenderer,
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
                        <a href="/admin/categories/${id}" class="text-blue-500 hover:text-blue-700" title="View">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="/admin/categories/${id}/edit" class="text-amber-500 hover:text-amber-700" title="Edit">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                        </a>
                        <button onclick="deleteCategory(${id})" class="text-red-500 hover:text-red-700" title="Delete">
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
    const gridDiv = document.querySelector('#categories-grid');
    if (!gridDiv) return;
    
    const grid = new agGrid.Grid(gridDiv, gridOptions);
    
    // Store grid options globally to access in other functions
    window.categoriesGridOptions = gridOptions;
    
    // Fetch initial data
    fetchCategoriesData();
}

/**
 * Fetch categories data from the server
 */
function fetchCategoriesData(status = '', search = '') {
    if (!window.categoriesGridOptions) return;
    
    let url = '/admin/categories?format=json';
    if (status) {
        url += `&status=${status}`;
    }
    if (search) {
        url += `&search=${search}`;
    }
    
    console.log("Fetching categories from URL:", url);
    
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
        console.log("Received categories data:", data);
        
        // Ensure data is an array before setting row data
        if (data && Array.isArray(data)) {
            console.log(`Setting ${data.length} categories to grid`);
            window.categoriesGridOptions.api.setRowData(data);
        } else if (data && typeof data === 'object' && data.data && Array.isArray(data.data)) {
            // If data is wrapped in a data property (common Laravel API pattern)
            console.log(`Using data from data.data property, found ${data.data.length} items`);
            window.categoriesGridOptions.api.setRowData(data.data);
        } else {
            console.error('Expected array of categories but got:', data);
            window.categoriesGridOptions.api.setRowData([]);
        }
        
        // Force grid to refresh
        window.categoriesGridOptions.api.sizeColumnsToFit();
    })
    .catch(error => {
        console.error('Error fetching categories:', error);
        alert('Failed to load categories. Please try again.');
    });
}

/**
 * Set up event listeners for category page
 */
function setupCategoryEventListeners() {
    // Status filter
    const statusFilter = document.getElementById('status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const searchFilter = document.getElementById('search-filter');
            fetchCategoriesData(
                this.value, 
                searchFilter ? searchFilter.value : ''
            );
        });
    }
    
    // Search filter
    const searchFilter = document.getElementById('search-filter');
    if (searchFilter) {
        let debounceTimeout;
        searchFilter.addEventListener('input', function() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const statusFilter = document.getElementById('status-filter');
                fetchCategoriesData(
                    statusFilter ? statusFilter.value : '',
                    this.value
                );
            }, 500);
        });
    }
    
    // Export buttons
    const exportCsvBtn = document.getElementById('export-csv');
    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', function() {
            if (!window.categoriesGridOptions) return;
            
            window.categoriesGridOptions.api.exportDataAsCsv({
                fileName: 'categories_export.csv'
            });
        });
    }
    
    const exportExcelBtn = document.getElementById('export-excel');
    if (exportExcelBtn) {
        exportExcelBtn.addEventListener('click', function() {
            if (!window.categoriesGridOptions) return;
            
            window.categoriesGridOptions.api.exportDataAsExcel({
                fileName: 'categories_export.xlsx'
            });
        });
    }
}

/**
 * Delete a category
 * @param {number} id - The category ID to delete
 */
window.deleteCategory = function(id) {
    if (!confirm('Are you sure you want to delete this category?')) {
        return;
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/admin/categories/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
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
            // Show success message
            alert(data.message || 'Category deleted successfully.');
            
            // Refresh the grid
            fetchCategoriesData(
                document.getElementById('status-filter')?.value || '',
                document.getElementById('search-filter')?.value || ''
            );
        } else {
            alert(data.message || 'Failed to delete category.');
        }
    })
    .catch(error => {
        console.error('Error deleting category:', error);
        alert('An error occurred while deleting the category.');
    });
}; 