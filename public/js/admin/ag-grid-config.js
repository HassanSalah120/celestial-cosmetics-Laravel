/**
 * Initialize an AG Grid instance with default configuration.
 * 
 * @param {string} containerId - The ID of the container element
 * @param {Array} columnDefs - Column definitions
 * @param {string} apiUrl - API URL to fetch data from
 * @param {Object} options - Additional AG Grid options
 * @returns {Object} - AG Grid API
 */
function initializeAgGrid(containerId, columnDefs, apiUrl, options = {}) {
    // Default AG Grid options
    const defaultOptions = {
        columnDefs: columnDefs,
        pagination: true,
        paginationPageSize: 10,
        domLayout: 'autoHeight',
        defaultColDef: {
            flex: 1,
            minWidth: 100,
            resizable: true,
            sortable: true,
            filter: true
        },
        animateRows: true,
        enableCellTextSelection: true,
        suppressCellFocus: true,
        rowSelection: 'multiple',
        rowMultiSelectWithClick: true,
        suppressRowClickSelection: true,
        suppressContextMenu: false,
        enableBrowserTooltips: true,
        // Theme styling
        rowClass: 'hover:bg-gray-50',
        headerHeight: 48,
        rowHeight: 48
    };

    // Merge default options with provided options
    const gridOptions = { ...defaultOptions, ...options };

    // Create the grid
    const gridDiv = document.querySelector(containerId);
    const grid = new agGrid.Grid(gridDiv, gridOptions);

    // Fetch data from server
    fetch(apiUrl, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (Array.isArray(data)) {
            gridOptions.api.setRowData(data);
        } else if (data.data && Array.isArray(data.data)) {
            // Handle standard Laravel paginated responses
            gridOptions.api.setRowData(data.data);
        } else {
            console.error('Invalid data format received from server:', data);
            gridOptions.api.setRowData([]);
        }
    })
    .catch(error => {
        console.error('Error loading data:', error);
        gridOptions.api.setRowData([]);
    });

    return gridOptions.api;
}

/**
 * Create a status badge cell renderer
 * 
 * @param {Object} statusClasses - Mapping of status values to CSS classes
 * @returns {Function} - Cell renderer function
 */
function statusBadgeRenderer(statusClasses) {
    return params => {
        if (!params.value) return '';
        
        const status = String(params.value).toLowerCase();
        const className = statusClasses[status] || 'bg-gray-100 text-gray-800';
        
        return `<span class="px-2 py-1 text-xs font-medium rounded-full ${className}">${params.value}</span>`;
    };
}

/**
 * Create actions column renderer with view, edit, delete buttons
 * 
 * @param {string} baseUrl - Base URL for actions (e.g., '/admin/users')
 * @param {string} idField - Name of the ID field in the data
 * @param {Function} deleteCallback - Function to call when delete is clicked
 * @returns {Function} - Cell renderer function
 */
function actionsRenderer(baseUrl, idField, deleteCallback) {
    return params => {
        const id = params.data[idField];
        
        return `
            <div class="flex items-center space-x-2">
                <a href="${baseUrl}/${id}" class="text-blue-500 hover:text-blue-700" title="View">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </a>
                <a href="${baseUrl}/${id}/edit" class="text-amber-500 hover:text-amber-700" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </a>
                <button onclick="${deleteCallback}(${id})" class="text-red-500 hover:text-red-700" title="Delete">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </div>
        `;
    };
}

/**
 * Setup export buttons for AG Grid
 * 
 * @param {string} buttonId - ID of the button to trigger export
 * @param {Object} gridApi - AG Grid API instance
 * @param {string} exportType - Type of export (csv or excel)
 */
function setupExportButton(buttonId, gridApi, exportType = 'csv') {
    document.getElementById(buttonId).addEventListener('click', function() {
        if (exportType === 'csv') {
            gridApi.exportDataAsCsv();
        } else if (exportType === 'excel') {
            gridApi.exportDataAsExcel();
        }
    });
} 