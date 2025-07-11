// Common admin functionality
document.addEventListener('DOMContentLoaded', function() {
    // CSRF token for AJAX requests
    window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    // Check if jQuery is properly loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
        // Try to load jQuery as fallback
        var jqScript = document.createElement('script');
        jqScript.src = 'https://code.jquery.com/jquery-3.7.1.min.js';
        document.head.appendChild(jqScript);
    }
    
    // Only check for AG Grid in admin pages
    const isAdminPage = document.querySelector('.admin-panel') !== null || 
                       window.location.pathname.includes('/admin');
    
    if (isAdminPage && typeof agGrid === 'undefined') {
        console.error('AG Grid is not loaded!');
        // Try to load AG Grid as fallback
        var agGridScript = document.createElement('script');
        agGridScript.src = 'https://cdn.jsdelivr.net/npm/ag-grid-community/dist/ag-grid-community.min.js';
        document.head.appendChild(agGridScript);
        
        // Add AG Grid styles
        var agGridStyles = document.createElement('link');
        agGridStyles.rel = 'stylesheet';
        agGridStyles.href = 'https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-grid.css';
        document.head.appendChild(agGridStyles);
        
        var agGridTheme = document.createElement('link');
        agGridTheme.rel = 'stylesheet';
        agGridTheme.href = 'https://cdn.jsdelivr.net/npm/ag-grid-community/styles/ag-theme-alpine.css';
        document.head.appendChild(agGridTheme);
    }
    
    // Setup common event listeners
    setupCommonEventListeners();
});

/**
 * Setup common admin event listeners
 */
function setupCommonEventListeners() {
    // Add any common admin event listeners here
    
    // Example: Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert, [role="alert"]');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
}

// Utility functions that might be needed across admin pages
const AdminUtils = {
    /**
     * Format a number as currency
     * @param {number} amount - The amount to format
     * @param {string} currency - The currency code (default: USD)
     * @return {string} Formatted currency string
     */
    formatCurrency: function(amount, currency = 'USD') {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },
    
    /**
     * Format a date in a user-friendly format
     * @param {string|Date} date - The date to format
     * @return {string} Formatted date string
     */
    formatDate: function(date) {
        const dateObj = new Date(date);
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }).format(dateObj);
    },
    
    /**
     * Send an AJAX request with CSRF token
     * @param {string} url - The URL to send the request to
     * @param {Object} options - Fetch API options
     * @return {Promise} Fetch promise
     */
    ajaxRequest: function(url, options = {}) {
        // Ensure headers exist
        options.headers = options.headers || {};
        
        // Add CSRF token
        options.headers['X-CSRF-TOKEN'] = window.csrfToken;
        
        // Set default content type if method is POST
        if ((options.method === 'POST' || options.method === 'PUT' || options.method === 'PATCH') && 
            !options.headers['Content-Type'] && 
            !(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/json';
        }
        
        return fetch(url, options).then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        });
    }
};

// Export for use in other files
export default AdminUtils; 