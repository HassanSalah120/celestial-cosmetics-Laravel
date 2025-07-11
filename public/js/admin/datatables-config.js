function initializeDataTable(tableId, options = {}) {
    const defaultConfig = {
        processing: true,
        pageLength: 10,
        dom: "<'flex flex-col sm:flex-row items-center justify-between mb-4'<'flex items-center space-x-2'B><'mt-2 sm:mt-0'f>>" +
             "<'overflow-x-auto'tr>" +
             "<'flex flex-col sm:flex-row items-center justify-between mt-4'<'text-sm text-gray-600'i><'flex items-center space-x-2'p>>",
        buttons: [
            {
                extend: 'copy',
                className: 'dt-button',
                text: '<span><i class="fas fa-copy"></i> Copy</span>'
            },
            {
                extend: 'csv',
                className: 'dt-button',
                text: '<span><i class="fas fa-file-csv"></i> CSV</span>'
            },
            {
                extend: 'excel',
                className: 'dt-button',
                text: '<span><i class="fas fa-file-excel"></i> Excel</span>'
            },
            {
                extend: 'pdf',
                className: 'dt-button',
                text: '<span><i class="fas fa-file-pdf"></i> PDF</span>'
            }
        ],
        order: [[0, 'desc']],
        language: {
            search: "",
            searchPlaceholder: "Search...",
            lengthMenu: "_MENU_ per page",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                first: '<i class="fas fa-angle-double-left"></i>',
                last: '<i class="fas fa-angle-double-right"></i>',
                next: '<i class="fas fa-angle-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>'
            }
        }
    };

    // Merge default config with provided options
    const config = { ...defaultConfig, ...options };

    // Destroy existing DataTable if it exists
    if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }

    // Initialize new DataTable
    return $(tableId).DataTable(config);
} 