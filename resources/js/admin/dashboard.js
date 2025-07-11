// Dashboard Charts and Functionality
import { Chart } from 'chart.js';
import { registerables } from 'chart.js';
Chart.register(...registerables);

// Global variables for charts
let salesChart, productsChart, categoryChart, timeChart;

// Color palette for consistent styling
const colors = [
    '#0EA5E9', '#EC4899', '#14B8A6', '#F97316', '#6366F1'
];

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboardCharts();
    setupEventListeners();
});

/**
 * Initialize all dashboard charts
 */
function initializeDashboardCharts() {
    initializeSalesChart();
    initializeProductsChart();
    initializeCategoryChart();
    initializeTimeChart();
}

/**
 * Initialize sales chart
 */
function initializeSalesChart() {
    const salesCtx = document.getElementById('salesChart')?.getContext('2d');
    if (!salesCtx) return;
    
    // The salesData is declared in the Blade template and available globally
    salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.date),
            datasets: [{
                label: 'Revenue',
                data: salesData.map(item => item.amount),
                borderColor: colors[0],
                backgroundColor: colors[0] + '40',
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

/**
 * Initialize products chart
 */
function initializeProductsChart() {
    const productsCtx = document.getElementById('productsChart')?.getContext('2d');
    if (!productsCtx) return;
    
    // Ensure we have data, even if it's empty
    if (!productsData || !Array.isArray(productsData) || productsData.length === 0) {
        // Fallback data for demonstration if no real data exists
        productsData = [
            { name: 'Sample Product 1', revenue: 1200, sales_count: 24 },
            { name: 'Sample Product 2', revenue: 900, sales_count: 18 },
            { name: 'Sample Product 3', revenue: 600, sales_count: 12 },
            { name: 'Sample Product 4', revenue: 400, sales_count: 8 },
            { name: 'Sample Product 5', revenue: 200, sales_count: 4 }
        ];
        console.log("Using fallback product data");
    } else {
        console.log("Using real product data:", productsData);
    }

    // Get the top 5 products by revenue
    const labels = productsData.slice(0, 5).map(product => product.name);
    const data = productsData.slice(0, 5).map(product => product.revenue);
    
    console.log("Chart labels:", labels);
    console.log("Chart data:", data);
    
    // Create a simple doughnut chart with minimal options
    productsChart = new Chart(productsCtx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#0694a2', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444'
                ],
                borderWidth: 2,
                borderColor: '#FFFFFF'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
                padding: {
                    top: 10,
                    right: 10,
                    bottom: 10,
                    left: 10
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
}

/**
 * Initialize category chart
 */
function initializeCategoryChart() {
    const categoryCtx = document.getElementById('categoryChart')?.getContext('2d');
    if (!categoryCtx || !categoryData) return;
    
    const labels = categoryData.map(category => category.name);
    const data = categoryData.map(category => category.products_count);
    
    categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Products',
                data: data,
                backgroundColor: colors[1]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

/**
 * Initialize time chart
 */
function initializeTimeChart() {
    const timeCtx = document.getElementById('timeChart')?.getContext('2d');
    if (!timeCtx || !timeData) return;
    
    const data = timeData['day'] || [];
    const labels = data.map(item => item.label);
    const counts = data.map(item => item.count);
    
    timeChart = new Chart(timeCtx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Orders',
                data: counts,
                backgroundColor: colors[2]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

/**
 * Setup event listeners for dashboard controls
 */
function setupEventListeners() {
    // Time range filter
    document.getElementById('timeRange')?.addEventListener('change', function() {
        const timeRange = this.value;
        
        fetch(`/admin/dashboard/data?timeRange=${timeRange}`)
            .then(response => response.json())
            .then(data => {
                // Update charts with new data
                if (data.salesData && salesChart) {
                    salesChart.data.labels = data.salesData.map(item => item.date);
                    salesChart.data.datasets[0].data = data.salesData.map(item => item.amount);
                    salesChart.update();
                }
                
                if (data.topProducts && productsChart) {
                    const labels = data.topProducts.slice(0, 5).map(product => product.name);
                    const chartData = data.topProducts.slice(0, 5).map(product => product.revenue);
                    productsChart.data.labels = labels;
                    productsChart.data.datasets[0].data = chartData;
                    productsChart.update();
                }
                
                if (data.categoryData && categoryChart) {
                    categoryChart.data.labels = data.categoryData.map(category => category.name);
                    categoryChart.data.datasets[0].data = data.categoryData.map(category => category.products_count);
                    categoryChart.update();
                }
                
                if (data.timeData && timeChart) {
                    const timeData = data.timeData['day'] || [];
                    timeChart.data.labels = timeData.map(item => item.label);
                    timeChart.data.datasets[0].data = timeData.map(item => item.count);
                    timeChart.update();
                }
            })
            .catch(error => console.error('Error fetching dashboard data:', error));
    });
    
    // Product metric selector
    document.getElementById('productMetric')?.addEventListener('change', function() {
        const metric = this.value;
        
        if (!productsChart) return;
        
        // Update data based on selected metric
        productsChart.data.datasets[0].data = productsData.slice(0, 5).map(product => 
            metric === 'revenue' ? product.revenue : product.sales_count
        );
        
        productsChart.update();
    });
}

// Export functions if needed for other components
export {
    initializeDashboardCharts,
    setupEventListeners
}; 