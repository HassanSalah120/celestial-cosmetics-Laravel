@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Reports & Analytics</h2>
            <p class="mt-1 text-sm text-gray-600">Insights into your store's performance</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.reports.sales') }}" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark transition-colors duration-200">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Sales Report
                </span>
            </a>
            <a href="{{ route('admin.reports.products') }}" class="px-4 py-2 bg-secondary text-white rounded-md hover:bg-secondary-dark transition-colors duration-200">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    Product Report
                </span>
            </a>
            <a href="{{ route('admin.reports.customers') }}" class="px-4 py-2 bg-accent text-white rounded-md hover:bg-accent-light transition-colors duration-200">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Customer Report
                </span>
            </a>
            <a href="{{ route('admin.print.inventory') }}" target="_blank" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 transition-colors duration-200">
                <span class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Inventory
                </span>
            </a>
        </div>
    </div>

    <!-- Monthly Performance Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Orders Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monthly Orders</p>
                    <div class="flex items-end mt-1">
                        <p class="text-2xl font-bold text-primary">{{ $currentMonthOrders }}</p>
                        <span class="ml-2 text-sm {{ $orderChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="flex items-center">
                                @if($orderChange >= 0)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                @endif
                                {{ abs(round($orderChange)) }}%
                            </span>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">vs. last month: {{ $lastMonthOrders }}</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                    <div class="flex items-end mt-1">
                        <p class="text-2xl font-bold text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($currentMonthRevenue) }}</p>
                        <span class="ml-2 text-sm {{ $revenueChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="flex items-center">
                                @if($revenueChange >= 0)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                @endif
                                {{ abs(round($revenueChange)) }}%
                            </span>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">vs. last month: {{ \App\Helpers\SettingsHelper::formatPrice($lastMonthRevenue) }}</p>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- New Users Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">New Customers</p>
                    <div class="flex items-end mt-1">
                        <p class="text-2xl font-bold text-accent">{{ $currentMonthUsers }}</p>
                        <span class="ml-2 text-sm {{ $userChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            <span class="flex items-center">
                                @if($userChange >= 0)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                @endif
                                {{ abs(round($userChange)) }}%
                            </span>
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">vs. last month: {{ $lastMonthUsers }}</p>
                </div>
                <div class="p-3 bg-accent/10 rounded-full">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Daily Sales Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Sales This Month</h3>
            <div class="h-64">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <!-- Category Performance -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Sales by Category</h3>
            <div class="h-64">
                <canvas id="categorySalesChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Products and Offers Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Products Card -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Top Selling Products</h3>
                <a href="{{ route('admin.reports.products') }}" class="text-sm font-medium text-primary hover:text-primary-dark">View Full Report</a>
            </div>
            <div class="space-y-4">
                @forelse($topProducts as $product)
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-full object-cover">
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-gray-900 truncate">{{ $product->name }}</div>
                                <div class="text-sm font-medium text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($product->revenue) }}</div>
                            </div>
                            <div class="flex mt-1">
                                <div class="flex-1">
                                    <div class="text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-primary/10 text-primary">
                                            {{ $product->category ? $product->category->name : ($product->category_id ? 'Category #'.$product->category_id : 'Uncategorized') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $product->sales_count }} units</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-sm text-gray-500">No products sold in this period</div>
                @endforelse
            </div>
        </div>

        <!-- Top Offers Card -->
        @if(isset($topOffers) && count($topOffers) > 0)
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Top Selling Offers</h3>
                <a href="{{ route('admin.reports.sales') }}" class="text-sm font-medium text-primary hover:text-primary-dark">View Full Report</a>
            </div>
            <div class="space-y-4">
                @foreach($topOffers as $offer)
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ asset('storage/' . str_replace('storage/', '', $offer->image)) }}" alt="{{ $offer->title }}" class="w-10 h-10 rounded-full object-cover">
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <div class="text-sm font-medium text-gray-900 truncate">{{ $offer->title }}</div>
                                <div class="text-sm font-medium text-amber-600">{{ \App\Helpers\SettingsHelper::formatPrice($offer->revenue) }}</div>
                            </div>
                            <div class="flex mt-1">
                                <div class="flex-1">
                                    <div class="text-xs text-gray-500">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                            Special Offer
                                        </span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $offer->sales_count }} units</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Daily Sales Chart
        const dailySalesData = {!! json_encode($dailySales) !!};
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        
        new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: dailySalesData.map(item => new Date(item.date).toLocaleDateString()),
                datasets: [
                    {
                        label: 'Revenue',
                        data: dailySalesData.map(item => item.revenue),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        yAxisID: 'y',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Orders',
                        data: dailySalesData.map(item => item.order_count),
                        borderColor: '#2B5B6C',
                        backgroundColor: 'transparent',
                        yAxisID: 'y1',
                        borderDashed: [5, 5],
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Revenue ($)'
                        },
                        beginAtZero: true
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Order Count'
                        },
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
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
                                if (context.dataset.yAxisID === 'y') {
                                    label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.raw);
                                } else {
                                    label += context.raw;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // Category Sales Chart
        const categoryData = {!! json_encode($categoryData) !!};
        const categorySalesCtx = document.getElementById('categorySalesChart').getContext('2d');
        
        new Chart(categorySalesCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(item => item.name),
                datasets: [{
                    label: 'Revenue by Category',
                    data: categoryData.map(item => item.revenue || 0),
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
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(context.raw);
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
@endsection 
