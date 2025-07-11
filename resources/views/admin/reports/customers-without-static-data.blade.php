@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Customer Analysis Report</h2>
            <p class="mt-1 text-sm text-gray-600">Understand your customer base and purchasing behaviors</p>
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
        <form action="{{ route('admin.reports.customers') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                <div class="flex items-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        Apply Filters
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Customers</p>
                    <p class="text-3xl font-bold text-accent">{{ $totalCustomers }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $newCustomers }} new in this period</p>
                </div>
                <div class="p-3 bg-accent/10 rounded-full">
                    <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-sm font-medium text-gray-600">Average Order Value</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($avgOrderValue) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $totalOrders }} orders in period</p>
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
                    <p class="text-sm font-medium text-gray-600">Returning Customers</p>
                    <p class="text-3xl font-bold text-primary">{{ number_format($returningCustomerRate, 1) }}%</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $repeatedPurchaseCustomers }} customers with 2+ orders</p>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Customer Growth Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Growth</h3>
            <div class="h-80">
                <canvas id="customerGrowthChart"></canvas>
            </div>
        </div>

        <!-- Order Frequency Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Frequency Distribution</h3>
            <div class="h-80">
                <canvas id="orderFrequencyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Customers Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Top Customers by Spend</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Joined</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Order Value</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Order</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($topCustomers as $customer)
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($customer->profile_image)
                                        <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $customer->profile_image) }}" alt="{{ $customer->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-primary text-white flex items-center justify-center">
                                            <span class="text-sm font-medium">{{ strtoupper(substr($customer->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $customer->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ is_string($customer->created_at) ? $customer->created_at : $customer->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $customer->orders_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-emerald-600">{{ \App\Helpers\SettingsHelper::formatPrice($customer->total_spent) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ \App\Helpers\SettingsHelper::formatPrice($customer->average_order) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $customer->last_order_date ? (is_string($customer->last_order_date) ? $customer->last_order_date : $customer->last_order_date->format('M d, Y')) : 'N/A' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            No customer data available for the selected period
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $topCustomers->withQueryString()->links() }}
        </div>
    </div>

    <!-- Customer Segments -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Customer Segments</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
            <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-green-800">VIP Customers</h4>
                    <span class="text-sm font-medium px-2 py-1 bg-green-100 text-green-800 rounded-md">{{ number_format($vipPercentage, 1) }}%</span>
                </div>
                <p class="text-sm text-green-700 mb-2">Customers who spent over {{ \App\Helpers\SettingsHelper::formatPrice($vipThreshold) }}</p>
                <div class="flex justify-between text-sm">
                    <span class="text-green-600">{{ $vipCustomers }} customers</span>
                    <span class="font-semibold text-green-800">{{ \App\Helpers\SettingsHelper::formatPrice($vipRevenue) }} revenue</span>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-blue-800">Regular Customers</h4>
                    <span class="text-sm font-medium px-2 py-1 bg-blue-100 text-blue-800 rounded-md">{{ number_format($regularPercentage, 1) }}%</span>
                </div>
                <p class="text-sm text-blue-700 mb-2">Customers with 2-3 orders</p>
                <div class="flex justify-between text-sm">
                    <span class="text-blue-600">{{ $regularCustomers }} customers</span>
                    <span class="font-semibold text-blue-800">{{ \App\Helpers\SettingsHelper::formatPrice($regularRevenue) }} revenue</span>
                </div>
            </div>
            
            <div class="bg-amber-50 rounded-lg p-4 border border-amber-100">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-amber-800">New Customers</h4>
                    <span class="text-sm font-medium px-2 py-1 bg-amber-100 text-amber-800 rounded-md">{{ number_format($newCustomerPercentage, 1) }}%</span>
                </div>
                <p class="text-sm text-amber-700 mb-2">Customers with only 1 order</p>
                <div class="flex justify-between text-sm">
                    <span class="text-amber-600">{{ $newCustomersWithOneOrder }} customers</span>
                    <span class="font-semibold text-amber-800">{{ \App\Helpers\SettingsHelper::formatPrice($newCustomerRevenue) }} revenue</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Customer Growth Chart
        const growthData = {!! json_encode($customerGrowthData) !!};
        const growthCtx = document.getElementById('customerGrowthChart').getContext('2d');
        
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: growthData.map(item => item.date),
                datasets: [
                    {
                        label: 'New Customers',
                        data: growthData.map(item => item.new_customers),
                        borderColor: '#D4AF37',
                        backgroundColor: 'rgba(212, 175, 55, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Cumulative Customers',
                        data: growthData.map(item => item.total_customers),
                        borderColor: '#2B5B6C',
                        backgroundColor: 'rgba(43, 91, 108, 0.05)',
                        fill: true,
                        tension: 0.4
                    }
                ]
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

        // Order Frequency Chart
        const frequencyData = {!! json_encode($orderFrequencyData) !!};
        const frequencyCtx = document.getElementById('orderFrequencyChart').getContext('2d');
        
        new Chart(frequencyCtx, {
            type: 'bar',
            data: {
                labels: frequencyData.map(item => item.frequency === 1 ? '1 order' : 
                                              (item.frequency > 5 ? '5+ orders' : item.frequency + ' orders')),
                datasets: [{
                    label: 'Number of Customers',
                    data: frequencyData.map(item => item.count),
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
