@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Product Performance Report</h2>
            <p class="mt-1 text-sm text-gray-600">Detailed analysis of your product sales and inventory</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Reports
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Report
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.reports.products') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}" 
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}" 
                           class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-3xl font-bold text-primary">{{ $totalProducts }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $activeProducts }} active products</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Units Sold</p>
                    <p class="text-3xl font-bold text-secondary">{{ $totalUnitsSold }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $salesPerDay }} units per day</p>
                </div>
                <div class="p-3 bg-secondary/10 rounded-full">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Revenue</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($totalRevenue) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Avg. {{ \App\Helpers\SettingsHelper::formatPrice($averageProductRevenue) }} per product</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Low Stock</p>
                    <p class="text-3xl font-bold text-amber-600">{{ $lowStockCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $outOfStockCount }} out of stock</p>
                </div>
                <div class="p-3 bg-amber-100 rounded-full">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Selling Products Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Selling Products</h3>
            <div class="h-80">
                <canvas id="topProductsChart"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales by Category</h3>
            <div class="h-80">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Inventory Status Chart -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory Status</h3>
        <div class="h-96">
            <canvas id="inventoryStatusChart"></canvas>
        </div>
    </div>

    <!-- Product Performance Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Product Performance</h3>
                <div class="flex space-x-2">
                    <button id="exportCSV" class="px-3 py-1 bg-green-50 text-green-700 text-sm rounded border border-green-200 hover:bg-green-100 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export CSV
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="productTable">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit Margin</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">#{{ $product->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                {{ $product->category ? $product->category->name : ($product->category_id ? 'Category #'.$product->category_id : "Uncategorized") }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($product->discount_percent > 0)
                                <span class="line-through text-gray-400">{{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}</span>
                                <span class="text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($product->price * (1 - $product->discount_percent/100)) }}</span>
                            @else
                                {{ \App\Helpers\SettingsHelper::formatPrice($product->price) }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->quantity_sold }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($product->revenue) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stock > 10 ? 'bg-green-100 text-green-800' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $product->stock }}
                                </span>
                                @if($product->stock <= 5 && $product->stock > 0)
                                    <span class="ml-2 text-xs text-yellow-600">Low</span>
                                @elseif($product->stock == 0)
                                    <span class="ml-2 text-xs text-red-600">Out of stock</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($product->profit_margin, 1) }}%</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_visible ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $product->is_visible ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No product data available for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/papaparse@5.3.0/papaparse.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to format price according to settings
        function formatPrice(price) {
            const currencySymbol = '{!! \App\Helpers\SettingsHelper::get('currency_symbol', 'ج.م') !!}';
            const currencyPosition = '{!! \App\Helpers\SettingsHelper::get('currency_position', 'right') !!}';
            const thousandSeparator = '{!! \App\Helpers\SettingsHelper::get('thousand_separator', ',') !!}';
            const decimalSeparator = '{!! \App\Helpers\SettingsHelper::get('decimal_separator', '.') !!}';
            const decimalDigits = {!! \App\Helpers\SettingsHelper::get('decimal_digits', 2) !!};
            
            // Format number with proper separators
            let formattedNumber = Number(price).toFixed(decimalDigits);
            
            // Add thousand separators if needed
            if (thousandSeparator) {
                const parts = formattedNumber.split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
                formattedNumber = parts.join(decimalSeparator);
            }
            
            // Position currency symbol according to settings
            switch (currencyPosition) {
                case 'left':
                    return currencySymbol + formattedNumber;
                case 'right':
                    return formattedNumber + currencySymbol;
                case 'left_with_space':
                    return currencySymbol + ' ' + formattedNumber;
                case 'right_with_space':
                    return formattedNumber + ' ' + currencySymbol;
                default:
                    return formattedNumber + currencySymbol;
            }
        }
        
        // Top Products Chart
        const topProductsData = {!! json_encode($topProductsChart) !!};
        const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
        
        new Chart(topProductsCtx, {
            type: 'bar',
            data: {
                labels: topProductsData.map(item => item.name),
                datasets: [{
                    label: 'Units Sold',
                    data: topProductsData.map(item => item.units_sold),
                    backgroundColor: '#2B5B6C',
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            display: true
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += formatPrice(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Category Sales Chart
        const categoryData = {!! json_encode($categorySalesChart) !!};
        const categorySalesCtx = document.getElementById('categorySalesChart').getContext('2d');
        
        new Chart(categorySalesCtx, {
            type: 'pie',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    data: categoryData.map(item => item.revenue),
                    backgroundColor: [
                        '#2B5B6C',
                        '#1A3F4C',
                        '#D4AF37',
                        '#E5C65C',
                        '#10B981',
                        '#374151',
                        '#6B7280',
                        '#9CA3AF',
                        '#4B5563',
                        '#1F2937'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += formatPrice(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Inventory Status Chart
        const inventoryData = {!! json_encode($inventoryStatusChart) !!};
        const inventoryCtx = document.getElementById('inventoryStatusChart').getContext('2d');
        
        new Chart(inventoryCtx, {
            type: 'bar',
            data: {
                labels: inventoryData.map(item => item.name),
                datasets: [
                    {
                        label: 'Stock',
                        data: inventoryData.map(item => item.stock),
                        backgroundColor: inventoryData.map(item => {
                            if (item.stock === 0) return '#EF4444'; // Red for out of stock
                            if (item.stock <= 5) return '#F59E0B'; // Yellow for low stock
                            return '#10B981'; // Green for healthy stock
                        }),
                        borderWidth: 0
                    },
                    {
                        label: 'Units Sold',
                        data: inventoryData.map(item => item.units_sold),
                        backgroundColor: 'rgba(43, 91, 108, 0.4)',
                        borderWidth: 0
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: false,
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        stacked: false,
                        beginAtZero: true,
                        grid: {
                            display: true
                        },
                        ticks: {
                            precision: 0,
                            callback: function(value) {
                                return formatPrice(value);
                            }
                        }
                    }
                }
            }
        });

        // CSV Export
        document.getElementById('exportCSV').addEventListener('click', function() {
            const table = document.getElementById('productTable');
            const rows = table.querySelectorAll('tbody tr');
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            
            const data = [headers];
            
            rows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('td')).map(td => {
                    // Get only the text content, ignoring any HTML
                    const text = td.textContent.trim().replace(/\s+/g, ' ');
                    return text;
                });
                
                if (rowData.length > 0) {
                    data.push(rowData);
                }
            });
            
            const csv = Papa.unparse(data);
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            
            const link = document.createElement('a');
            link.setAttribute('href', url);
            link.setAttribute('download', 'product_report_{{ date("Y-m-d") }}.csv');
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    });
</script>
@endpush

<style>
@media print {
    header, nav, footer, form, button:not(.print-include) {
        display: none !important;
    }
    
    .rounded-lg {
        border-radius: 0 !important;
    }
    
    .shadow-sm {
        box-shadow: none !important;
    }
    
    body {
        background-color: white !important;
    }
    
    h2 {
        font-size: 22pt !important;
        margin-bottom: 20px !important;
    }
    
    .space-y-6 > div {
        margin-bottom: 30px !important;
    }
    
    canvas {
        max-height: 300px !important;
    }
    
    table {
        font-size: 10pt !important;
    }
    
    .px-6 {
        padding-left: 12px !important;
        padding-right: 12px !important;
    }
    
    .py-4 {
        padding-top: 8px !important;
        padding-bottom: 8px !important;
    }
}
</style>
@endsection 
