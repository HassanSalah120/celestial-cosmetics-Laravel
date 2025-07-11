@extends('layouts.admin')

@php
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 font-display">Admin Dashboard</h2>
            <p class="mt-1 text-sm text-gray-600">Manage your store and monitor performance</p>
        </div>
        <div class="flex space-x-3">
            <select id="timeRange" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                <option value="today" {{ $timeRange === 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ $timeRange === 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ $timeRange === 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year" {{ $timeRange === 'year' ? 'selected' : '' }}>This Year</option>
            </select>
        </div>
    </div>

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @if((Auth::user()->hasPermission('manage_products') || Auth::user()->isAdmin()) && $show_products_stat)
        <div class="bg-white rounded-lg shadow-sm p-6 transform transition-all duration-200 hover:scale-105 relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-primary mt-1" data-stat="totalProducts">{{ $totalProducts }}</p>
                    <div class="flex items-center mt-1">
                        <p class="text-xs text-gray-500" data-stat="activeProducts">{{ $activeProducts ?? 0 }} active</p>
                        @php
                            $productGrowth = \App\Models\Product::where('created_at', '>=', now()->subWeek())->count();
                            $productGrowthPercent = $totalProducts > 0 ? round(($productGrowth / $totalProducts) * 100, 1) : 0;
                        @endphp
                        <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full {{ $productGrowthPercent > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-{{ $productGrowthPercent >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>{{ abs($productGrowthPercent) }}%
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
            </div>
            <!-- Mini sparkline - we'll add a small background chart as a decorative element -->
            <div class="absolute bottom-0 left-0 w-full h-10 opacity-20">
                <svg viewBox="0 0 100 20" class="w-full h-full text-primary">
                    <path fill="none" stroke="currentColor" stroke-width="2" d="M0,10 Q25,20 50,10 T100,10"></path>
                </svg>
            </div>
        </div>
        @endif

        @if((Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('view_orders') || Auth::user()->isAdmin()) && $show_orders_stat)
        <div class="bg-white rounded-lg shadow-sm p-6 transform transition-all duration-200 hover:scale-105 relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Orders</p>
                    <p class="text-2xl font-bold text-secondary mt-1" data-stat="totalOrders">{{ $totalOrders }}</p>
                    <div class="flex items-center mt-1">
                        <p class="text-xs text-gray-500" data-stat="pendingOrders">{{ $pendingOrders ?? 0 }} pending</p>
                        @php
                            // Calculate order growth using a comparison between current and previous period
                            $previousPeriodOrders = \App\Models\Order::whereBetween('created_at', [
                                now()->subDays(2 * 7), now()->subDays(7)
                            ])->count();
                            $currentPeriodOrders = \App\Models\Order::whereBetween('created_at', [
                                now()->subDays(7), now()
                            ])->count();
                            $orderGrowthPercent = $previousPeriodOrders > 0 
                                ? round((($currentPeriodOrders - $previousPeriodOrders) / $previousPeriodOrders) * 100, 1)
                                : ($currentPeriodOrders > 0 ? 100 : 0);
                        @endphp
                        <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full {{ $orderGrowthPercent > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-{{ $orderGrowthPercent >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>{{ abs($orderGrowthPercent) }}%
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-secondary/10 rounded-full">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
            </div>
            <!-- Mini sparkline background -->
            <div class="absolute bottom-0 left-0 w-full h-10 opacity-20">
                <svg viewBox="0 0 100 20" class="w-full h-full text-secondary">
                    <path fill="none" stroke="currentColor" stroke-width="2" d="M0,15 Q30,5 60,12 T100,8"></path>
                </svg>
            </div>
        </div>
        @endif

        @if((Auth::user()->hasPermission('manage_users') || Auth::user()->hasPermission('view_customers') || Auth::user()->isAdmin()) && $show_users_stat)
        <div class="bg-white rounded-lg shadow-sm p-6 transform transition-all duration-200 hover:scale-105 relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-bold text-accent mt-1" data-stat="totalUsers">{{ $totalUsers }}</p>
                    <div class="flex items-center mt-1">
                        <p class="text-xs text-gray-500" data-stat="newUsers">{{ $newUsers ?? 0 }} new this month</p>
                        @php
                            $previousMonthUsers = \App\Models\User::where('created_at', '<', now()->subMonth())->count();
                            $userGrowthPercent = $previousMonthUsers > 0 
                                ? round((($totalUsers - $previousMonthUsers) / $previousMonthUsers) * 100, 1)
                                : ($totalUsers > 0 ? 100 : 0);
                        @endphp
                        <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full {{ $userGrowthPercent > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-{{ $userGrowthPercent >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>{{ abs($userGrowthPercent) }}%
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-accent/10 rounded-full">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- Mini sparkline background -->
            <div class="absolute bottom-0 left-0 w-full h-10 opacity-20">
                <svg viewBox="0 0 100 20" class="w-full h-full text-accent">
                    <path fill="none" stroke="currentColor" stroke-width="2" d="M0,10 L20,8 L40,16 L60,7 L80,12 L100,5"></path>
                </svg>
            </div>
        </div>
        @endif

        @if((Auth::user()->hasPermission('view_reports') || Auth::user()->isAdmin()) && $show_revenue_stat)
        <div class="bg-white rounded-lg shadow-sm p-6 transform transition-all duration-200 hover:scale-105 relative overflow-hidden">
            <div class="flex items-center justify-between relative z-10">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-emerald-600" data-stat="totalRevenue">{{ \App\Helpers\SettingsHelper::formatPrice($totalRevenue ?? 0) }}</p>
                    <div class="flex items-center mt-1">
                        <p class="text-xs text-gray-500" data-stat="revenueGrowth">{{ $revenueGrowth ?? 0 }}% vs last period</p>
                        <span class="ml-2 px-1.5 py-0.5 text-xs rounded-full {{ ($revenueGrowth ?? 0) > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            <i class="fas fa-{{ ($revenueGrowth ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>{{ abs($revenueGrowth ?? 0) }}%
                        </span>
                    </div>
                </div>
                <div class="p-3 bg-emerald-100 rounded-full">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <!-- Mini sparkline background -->
            <div class="absolute bottom-0 left-0 w-full h-10 opacity-20">
                <svg viewBox="0 0 100 20" class="w-full h-full text-emerald-600">
                    <path fill="none" stroke="currentColor" stroke-width="2" d="M0,15 L20,10 L40,5 L60,15 L80,5 L100,10"></path>
                </svg>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ $elements_per_row }} gap-6">
        @if(Auth::user()->hasPermission('manage_products') && $show_add_product_card)
        <a href="{{ route('admin.products.create') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-primary/10 rounded-full">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Add Product</h3>
                    <p class="text-sm text-gray-500">Create a new product listing</p>
                </div>
            </div>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_products') && $show_categories_card)
        <a href="{{ route('admin.categories.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-secondary/10 rounded-full">
                    <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Categories</h3>
                    <p class="text-sm text-gray-500">Manage product categories</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('view_orders')) && $show_orders_card)
        <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-accent/10 rounded-full">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Orders</h3>
                    <p class="text-sm text-gray-500">View and manage orders</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('manage_users') || Auth::user()->hasPermission('view_customers')) && $show_users_card)
        <a href="{{ route('admin.users.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-emerald-100 rounded-full">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Users</h3>
                    <p class="text-sm text-gray-500">Manage user accounts</p>
                </div>
            </div>
        </a>
        @endif

        @if(Auth::user()->hasPermission('manage_contact_messages') && $show_messages_card)
        <a href="{{ route('admin.contact-messages.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-emerald-100 rounded-full">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">
                        Contact Messages
                        @php
                            try {
                                $newMessagesCount = \App\Models\ContactMessage::where('status', 'new')->count();
                            } catch (\Exception $e) {
                                $newMessagesCount = 0;
                            }
                        @endphp
                        @if($newMessagesCount > 0)
                            <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs leading-none text-white bg-primary rounded-full">
                                {{ $newMessagesCount }} new
                            </span>
                        @endif
                    </h3>
                    <p class="text-sm text-gray-500">Manage customer inquiries</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('manage_coupons') || Auth::user()->isAdmin()) && $show_coupons_card)
        <a href="{{ route('admin.coupons.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Discounts</h3>
                    <p class="text-sm text-gray-500">Manage coupons & promotions</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('manage_settings') || Auth::user()->isAdmin()) && $show_settings_card)
        <a href="{{ route('admin.settings.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-violet-100 rounded-full">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Settings</h3>
                    <p class="text-sm text-gray-500">Configure store settings</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('view_reports') || Auth::user()->isAdmin()) && $show_reports_card)
        <a href="{{ route('admin.reports.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-amber-100 rounded-full">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Reports</h3>
                    <p class="text-sm text-gray-500">View detailed store analytics</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('manage_shipping') || Auth::user()->isAdmin()) && $show_shipping_card)
        <a href="{{ route('admin.shipping.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Shipping</h3>
                    <p class="text-sm text-gray-500">Manage shipping methods</p>
                </div>
            </div>
        </a>
        @endif

        @if((Auth::user()->hasPermission('view_activity_logs') || Auth::user()->isAdmin()) && $show_activities_card)
        <a href="{{ route('admin.activities.index') }}" class="bg-white rounded-lg shadow-sm p-6 hover:bg-gray-50 transition duration-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-gray-100 rounded-full">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Activity Logs</h3>
                    <p class="text-sm text-gray-500">View system activity history</p>
                </div>
            </div>
        </a>
        @endif
    </div>

    <!-- Charts -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if($show_sales_chart)
        <div class="bg-white p-6 rounded-lg shadow-sm overflow-hidden chart-container transition-all duration-300 hover:shadow-md">
            <div class="flex flex-wrap items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Sales Overview</h3>
                <div class="flex space-x-2">
                    <button id="revenueBtn" class="px-3 py-1 text-xs font-medium rounded-full bg-primary text-white transition-all duration-200 hover:bg-primary-dark">Revenue</button>
                    <button id="ordersBtn" class="px-3 py-1 text-xs font-medium rounded-full bg-gray-200 text-gray-700 transition-all duration-200 hover:bg-gray-300">Orders</button>
                </div>
            </div>
            <div class="h-80 relative">
                <canvas id="salesChart"></canvas>
                <div class="absolute inset-0 bg-gradient-to-b from-white/0 to-white/5 pointer-events-none"></div>
            </div>
        </div>
        @endif

        @if($show_products_chart)
        <div class="bg-white p-6 rounded-lg shadow-sm overflow-hidden chart-container transition-all duration-300 hover:shadow-md">
            <div class="flex flex-wrap items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Top Products</h3>
                <div>
                    <select id="productMetric" class="text-xs rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/50 transition-all duration-200">
                        <option value="revenue">By Revenue</option>
                        <option value="count">By Quantity</option>
                    </select>
                </div>
            </div>
            <div class="h-80 relative">
                <canvas id="productsChart"></canvas>
                <div class="absolute inset-0 bg-gradient-to-b from-white/0 to-white/5 pointer-events-none"></div>
            </div>
        </div>
        @endif

        @if($show_category_chart)
        <div class="bg-white p-6 rounded-lg shadow-sm overflow-hidden chart-container transition-all duration-300 hover:shadow-md">
            <div class="flex flex-wrap items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Category Distribution</h3>
                <div>
                    <select id="categoryMetric" class="text-xs rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/50 transition-all duration-200">
                        <option value="products">Products</option>
                        <option value="revenue">Revenue</option>
                    </select>
                </div>
            </div>
            <div class="h-80 relative">
                <canvas id="categoryChart"></canvas>
                <div class="absolute inset-0 bg-gradient-to-b from-white/0 to-white/5 pointer-events-none"></div>
            </div>
        </div>
        @endif

        @if($show_time_chart)
        <div class="bg-white p-6 rounded-lg shadow-sm overflow-hidden chart-container transition-all duration-300 hover:shadow-md">
            <div class="flex flex-wrap items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Sales by Time</h3>
                <div>
                    <select id="timeMetric" class="text-xs rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/50 transition-all duration-200">
                        <option value="day">Day of Week</option>
                        <option value="hour">Hour of Day</option>
                    </select>
                </div>
            </div>
            <div class="h-80 relative">
                <canvas id="timeChart"></canvas>
                <div class="absolute inset-0 bg-gradient-to-b from-white/0 to-white/5 pointer-events-none"></div>
            </div>
        </div>
        @endif
    </div>

    <!-- Tables Section -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        @if($show_orders_table && (Auth::user()->hasPermission('manage_orders') || Auth::user()->hasPermission('view_orders') || Auth::user()->isAdmin()))
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="recentOrdersTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentOrders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $order->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($order->user && $order->user->profile_image)
                                            <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $order->user->profile_image) }}" alt="{{ $order->user->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                                {{ $order->user ? substr($order->user->name, 0, 1) : 'G' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $order->user ? $order->user->name : 'Guest' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \App\Helpers\SettingsHelper::formatPrice($order->total_amount) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-primary hover:text-primary-dark">View</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No recent orders found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark">
                    View All Orders <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
        @endif

        @if($show_activities_table && (Auth::user()->hasPermission('view_activity_logs') || Auth::user()->isAdmin()))
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="recentActivitiesTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentActivities as $activity)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        @if($activity->causer && method_exists($activity->causer, 'getProfileImageAttribute') && $activity->causer->profile_image)
                                            <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $activity->causer->profile_image) }}" alt="{{ $activity->causer->name }}">
                                        @else
                                            <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                                {{ $activity->causer && method_exists($activity->causer, 'getNameAttribute') ? substr($activity->causer->name, 0, 1) : 'S' }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $activity->causer && method_exists($activity->causer, 'getNameAttribute') ? $activity->causer->name : 'System' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $activity->type === 'create' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $activity->type === 'update' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $activity->type === 'delete' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $activity->type === 'login' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $activity->type === 'logout' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($activity->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ Str::limit($activity->description, 30) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $activity->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No recent activities found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 text-right">
                <a href="{{ route('admin.activities.index') }}" class="text-sm font-medium text-primary hover:text-primary-dark">
                    View All Activities <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<!-- Import Chart.js via CDN as backup in case Vite fails -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Pass data to JavaScript using blade -->
<script>
    // Initialize global variables for charts data
    let salesData = {!! json_encode($salesData) !!};
    let productsData = {!! json_encode($topProducts) !!};
    let categoryData = {!! json_encode($categoryData ?? []) !!};
    let timeData = {!! json_encode($timeData ?? ['day' => [], 'hour' => []]) !!};
</script>

<!-- Import dashboard specific JavaScript -->
@vite(['resources/js/admin/dashboard.js'])
@endpush

<style>
.chart-container {
    border: 1px solid rgba(229, 231, 235, 0.8);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.chart-container:hover {
    border-color: rgba(6, 148, 162, 0.3);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

.chart-container canvas {
    animation: fadeIn 0.6s ease-out;
    width: 100% !important;
    height: 100% !important;
}

/* For doughnut charts specifically */
#productsChart {
    max-width: 100%;
    margin: 0 auto;
            }
            
/* For the legend styling */
.chart-container .chart-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 1rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endsection 