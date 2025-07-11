@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Sales Report</h2>
            <p class="mt-1 text-sm text-gray-600">Detailed analysis of your store's sales performance</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Reports
            </a>
            <a href="{{ route('admin.print.sales-report') }}?start_date={{ request('start_date', $startDate->format('Y-m-d')) }}&end_date={{ request('end_date', $endDate->format('Y-m-d')) }}&status={{ request('status', '') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print PDF Report
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('admin.reports.sales') }}" method="GET" class="space-y-4">
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
                    <label for="status" class="block text-sm font-medium text-gray-700">Order Status</label>
                    <select name="status" id="status" 
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="">All Statuses</option>
                        @foreach($orderStatuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
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

    <!-- Sales Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-primary">{{ \App\Helpers\SettingsHelper::formatPrice($totalRevenue) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $orderCount }} orders · Avg. {{ \App\Helpers\SettingsHelper::formatPrice($averageOrderValue) }} per order</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Items Sold</p>
                    <p class="text-3xl font-bold text-secondary">{{ $totalItemsSold }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $uniqueProductsSold }} unique products</p>
                </div>
                <div class="p-3 bg-secondary/10 rounded-full">
                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Tax Collected</p>
                    <p class="text-3xl font-bold text-gray-700">{{ \App\Helpers\SettingsHelper::formatPrice($totalTax) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Avg. tax rate: {{ number_format($averageTaxRate, 1) }}%</p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-8 h-8 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trend</h3>
            <div class="h-72">
                <canvas id="revenueTrendChart"></canvas>
            </div>
        </div>

        <!-- Order Status Distribution -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
            <div class="h-72">
                <canvas id="orderStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Orders in Period</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#{{ $order->id }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $order->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status == 'shipped' ? 'bg-blue-100 text-blue-800' : 
                                   ($order->status == 'processing' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $order->items->sum('quantity') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600">
                            {{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:text-primary-dark">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No orders found for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Top Selling Products</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topProducts as $product)
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->quantity_sold }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($product->revenue) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-primary h-2.5 rounded-full" style="width: {{ $product->percentage }}%"></div>
                                </div>
                                <span>{{ number_format($product->percentage, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No product data available for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Selling Offers -->
    @if(isset($topOffers) && count($topOffers) > 0)
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Top Selling Offers</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offer</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topOffers as $offer)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <img src="{{ asset('storage/' . str_replace('storage/', '', $offer->image)) }}" alt="{{ $offer->title }}" class="w-10 h-10 rounded-full mr-3 object-cover">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $offer->title }}</div>
                                    <div class="text-sm text-gray-500">#{{ $offer->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $offer->quantity_sold }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($offer->revenue) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="flex items-center">
                                <div class="w-full bg-gray-200 rounded-full h-2.5 mr-2">
                                    <div class="bg-amber-500 h-2.5 rounded-full" style="width: {{ $offer->percentage }}%"></div>
                                </div>
                                <span>{{ number_format($offer->percentage, 1) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payment Methods -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Methods</h3>
            <div class="h-64">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales by Customer Location</h3>
            <div class="h-64">
                <canvas id="locationChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        
        // Revenue Trend Chart
        const revenueTrendData = {!! json_encode($revenueTrend) !!};
        const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        
        new Chart(revenueTrendCtx, {
            type: 'line',
            data: {
                labels: revenueTrendData.map(item => item.date),
                datasets: [{
                    label: 'Revenue',
                    data: revenueTrendData.map(item => item.revenue),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatPrice(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
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

        // Order Status Chart
        const orderStatusData = {!! json_encode($orderStatusData) !!};
        const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
        
        new Chart(orderStatusCtx, {
            type: 'pie',
            data: {
                labels: orderStatusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
                datasets: [{
                    data: orderStatusData.map(item => item.count),
                    backgroundColor: [
                        '#10B981', // Completed (green)
                        '#3B82F6', // Shipped (blue)
                        '#F59E0B', // Processing (yellow)
                        '#EF4444', // Cancelled (red)
                        '#6B7280'  // Other (gray)
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
                    }
                }
            }
        });

        // Payment Method Chart
        const paymentMethodData = {!! json_encode($paymentMethods) !!};
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        
        new Chart(paymentMethodCtx, {
            type: 'bar',
            data: {
                labels: paymentMethodData.map(item => item.method),
                datasets: [{
                    label: 'Orders',
                    data: paymentMethodData.map(item => item.count),
                    backgroundColor: '#2B5B6C',
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Location Chart
        const locationData = {!! json_encode($locationData) !!};
        const locationCtx = document.getElementById('locationChart').getContext('2d');
        
        new Chart(locationCtx, {
            type: 'bar',
            data: {
                labels: locationData.map(item => item.location),
                datasets: [{
                    label: 'Revenue',
                    data: locationData.map(item => item.revenue),
                    backgroundColor: '#D4AF37',
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
                        ticks: {
                            callback: function(value) {
                                return formatPrice(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
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
