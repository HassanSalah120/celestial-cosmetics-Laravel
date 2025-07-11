@extends('layouts.app')

@php
use App\Helpers\TranslationHelper;
use App\Helpers\SettingsHelper;
@endphp

@section('content')
<div class="bg-background min-h-screen py-12">
    <div class="container mx-auto px-4">
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 font-display mb-2">{{ is_rtl() ? 'مرحبًا بعودتك' : 'Welcome back' }}, {{ auth()->user()->name }}!</h1>
            <p class="text-gray-600">{{ is_rtl() ? 'هنا نظرة عامة على حسابك ونشاطك الأخير.' : 'Here\'s an overview of your account and recent activity.' }}</p>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column - Order Stats -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Orders Card -->
                    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg p-4 border border-indigo-100 transform transition-all duration-200 hover:scale-105">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-indigo-900">{{ is_rtl() ? 'الطلبات' : 'Orders' }}</h3>
                            <div class="bg-indigo-100 rounded-full p-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-indigo-600">
                                    <path d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-indigo-800">{{ $orderCount ?? 0 }}</p>
                        <div class="flex items-center text-xs text-indigo-600 mt-1">
                            <span>{{ $recentOrders ?? 0 }} {{ is_rtl() ? 'طلبات هذا الشهر' : 'orders this month' }}</span>
                            @if(isset($orderGrowth) && $orderGrowth > 0)
                            <span class="flex items-center ml-2 text-emerald-600">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                                {{ $orderGrowth }}%
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Total Spent Card -->
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4 border border-emerald-100 transform transition-all duration-200 hover:scale-105">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-emerald-900">{{ is_rtl() ? 'إجمالي الإنفاق' : 'Total Spent' }}</h3>
                            <div class="bg-emerald-100 rounded-full p-1.5">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-500 text-sm">{{ is_rtl() ? 'إجمالي الإنفاق' : 'Total Spent' }}</p>
                            <p class="text-3xl font-bold text-emerald-800">{{ \App\Helpers\SettingsHelper::formatPrice($totalSpent ?? 0) }}</p>
                            <p class="text-xs text-gray-600 mt-1">
                                <span>{{ \App\Helpers\SettingsHelper::formatPrice($recentSpent ?? 0) }} {{ is_rtl() ? 'هذا الشهر' : 'this month' }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Points Card -->
                    <div class="bg-gradient-to-br from-accent/5 to-accent/10 rounded-lg p-4 border border-accent/20 transform transition-all duration-200 hover:scale-105">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-primary-dark">{{ is_rtl() ? 'نقاط المكافآت' : 'Reward Points' }}</h3>
                            <div class="bg-accent/20 rounded-full p-1.5">
                                <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-primary-dark">{{ $rewardPoints ?? 0 }}</p>
                        <div class="flex items-center text-xs text-accent-dark mt-1">
                            <span>{{ $pointsToNextReward ?? 0 }} {{ is_rtl() ? 'نقطة للمكافأة التالية' : 'points to next reward' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order History Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ is_rtl() ? 'سجل الطلبات' : 'Order History' }}</h3>
                        <div class="flex items-center space-x-2">
                            <button class="order-chart-toggle active px-3 py-1 text-sm rounded-md bg-primary text-white" data-chart="amount">{{ is_rtl() ? 'المبلغ' : 'Amount' }}</button>
                            <button class="order-chart-toggle px-3 py-1 text-sm rounded-md bg-gray-100 text-gray-600 hover:bg-gray-200" data-chart="count">{{ is_rtl() ? 'العدد' : 'Count' }}</button>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="orderHistoryChart"></canvas>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">{{ is_rtl() ? 'الطلبات الأخيرة' : 'Recent Orders' }}</h3>
                        <a href="{{ route('orders.index') }}" class="text-primary hover:text-primary-dark">{{ is_rtl() ? 'عرض الكل' : 'View All' }}</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'رقم الطلب' : 'Order #' }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'التاريخ' : 'Date' }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'الحالة' : 'Status' }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'المجموع' : 'Total' }}</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ is_rtl() ? 'الإجراءات' : 'Actions' }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentOrders ?? [] as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M j, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($order->status == 'completed') bg-green-100 text-green-800
                                            @elseif($order->status == 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">{{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('orders.show', $order->id) }}" class="text-primary hover:text-primary-dark">{{ is_rtl() ? 'عرض التفاصيل' : 'View Details' }}</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ is_rtl() ? 'لم يتم العثور على طلبات' : 'No orders found' }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column - Account & Quick Actions -->
            <div class="space-y-6">
                <!-- Account Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="bg-primary/10 rounded-full p-3">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">{{ is_rtl() ? 'تفاصيل الحساب' : 'Account Details' }}</h3>
                            <p class="text-sm text-gray-600">{{ is_rtl() ? 'إدارة ملفك الشخصي وتفضيلاتك' : 'Manage your profile and preferences' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ is_rtl() ? 'البريد الإلكتروني' : 'Email' }}</p>
                            <p class="text-sm text-gray-900">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ is_rtl() ? 'عضو منذ' : 'Member Since' }}</p>
                            <p class="text-sm text-gray-900">{{ auth()->user()->created_at->format('F j, Y') }}</p>
                        </div>
                        <div class="pt-4 border-t border-gray-200">
                            <a href="{{ route('profile.edit') }}" class="inline-flex items-center text-sm font-medium text-primary hover:text-primary-dark">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                {{ is_rtl() ? 'تعديل الملف الشخصي' : 'Edit Profile' }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ is_rtl() ? 'إجراءات سريعة' : 'Quick Actions' }}</h3>
                    <div class="space-y-3">
                        <a href="{{ route('products.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="bg-primary/10 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ is_rtl() ? 'تسوق الآن' : 'Shop Now' }}</p>
                                <p class="text-xs text-gray-600">{{ is_rtl() ? 'تصفح أحدث منتجاتنا' : 'Browse our latest products' }}</p>
                            </div>
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="bg-pink-50 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ is_rtl() ? 'قائمة الرغبات' : 'Wishlist' }}</p>
                                <p class="text-xs text-gray-600">{{ is_rtl() ? 'عرض العناصر المحفوظة' : 'View your saved items' }}</p>
                            </div>
                        </a>
                        <a href="{{ route('support') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <div class="bg-blue-50 rounded-full p-2 mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ is_rtl() ? 'الدعم' : 'Support' }}</p>
                                <p class="text-xs text-gray-600">{{ is_rtl() ? 'احصل على مساعدة بخصوص طلباتك' : 'Get help with your orders' }}</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Reward Progress -->
                @if(isset($rewardPoints))
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ is_rtl() ? 'تقدم المكافآت' : 'Rewards Progress' }}</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="font-medium text-gray-900">{{ $rewardPoints }} {{ is_rtl() ? 'نقطة' : 'points' }}</span>
                                <span class="text-gray-600">{{ is_rtl() ? 'المكافأة التالية عند' : 'Next reward at' }} {{ $nextRewardThreshold }} {{ is_rtl() ? 'نقطة' : 'points' }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-primary h-2 rounded-full" style="width: {{ ($rewardPoints / $nextRewardThreshold) * 100 }}%"></div>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ is_rtl() ? 'اكسب' : 'Earn' }} {{ $pointsToNextReward }} {{ is_rtl() ? 'نقطة إضافية لفتح مكافأتك التالية!' : 'more points to unlock your next reward!' }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let orderChart;
        const currencySymbol = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'EGP') }}';
        const orderData = {!! json_encode($orderHistory ?? []) !!};

        function initOrderChart(type = 'amount') {
            if (orderChart) orderChart.destroy();

            const data = type === 'amount' 
                ? orderData.map(item => item.amount)
                : orderData.map(item => item.count);
            const label = type === 'amount' ? 'Order Amount' : 'Order Count';
            const color = type === 'amount' ? '#0694a2' : '#2B5B6C';

            orderChart = new Chart(document.getElementById('orderHistoryChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: orderData.map(item => item.date),
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        backgroundColor: `${color}20`,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function(context) {
                                    let value = context.raw;
                                    return type === 'amount'
                                        ? currencySymbol + value.toLocaleString()
                                        : value + ' orders';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return type === 'amount'
                                        ? currencySymbol + value
                                        : value;
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        }

        // Initialize chart
        initOrderChart('amount');

        // Event listeners
        document.querySelectorAll('.order-chart-toggle').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.order-chart-toggle').forEach(b => {
                    b.classList.remove('active', 'bg-primary', 'text-white');
                    b.classList.add('bg-gray-100', 'text-gray-600');
                });
                this.classList.add('active', 'bg-primary', 'text-white');
                this.classList.remove('bg-gray-100', 'text-gray-600');
                initOrderChart(this.dataset.chart);
            });
        });

        // Function to format price according to settings
        function formatPrice(price) {
            const currencySymbol = '{{ \App\Helpers\SettingsHelper::get('currency_symbol', 'ج.م') }}';
            const currencyPosition = '{{ \App\Helpers\SettingsHelper::get('currency_position', 'right') }}';
            const thousandSeparator = '{{ \App\Helpers\SettingsHelper::get('thousand_separator', ',') }}';
            const decimalSeparator = '{{ \App\Helpers\SettingsHelper::get('decimal_separator', '.') }}';
            const decimalDigits = {{ \App\Helpers\SettingsHelper::get('decimal_digits', 2) }};
            
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
        
        // Use formatPrice to update any dynamically rendered prices in the dashboard
        // For example, if you're fetching and displaying order data via AJAX
    });
</script>
@endpush

<style>
.order-chart-toggle.active {
    font-weight: 500;
}
</style>
@endsection 