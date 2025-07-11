<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\OrderItem;
use App\Models\Category;
use App\Models\CurrencyConfig;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Format price consistently using SettingsHelper
     *
     * @param float $price
     * @param bool $includeSymbol
     * @return string
     */
    protected function formatPrice($price, $includeSymbol = true)
    {
        return SettingsHelper::formatPrice($price, $includeSymbol);
    }

    /**
     * Get currency symbol from normalized tables or default
     *
     * @return string
     */
    protected function getCurrencySymbol()
    {
        if (Schema::hasTable('currency_config')) {
            $config = CurrencyConfig::first();
            return $config ? $config->currency_symbol : 'ج.م';
        }
        
        return SettingsHelper::get('currency_symbol', 'ج.م');
    }

    /**
     * Display the reports dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Date ranges
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Current month stats
        $currentMonthOrders = Order::whereMonth('created_at', $currentMonth->month)
                             ->whereYear('created_at', $currentMonth->year)
                             ->count();
        
        $currentMonthRevenue = Order::whereMonth('created_at', $currentMonth->month)
                              ->whereYear('created_at', $currentMonth->year)
                              ->sum('total_amount');
                              
        $currentMonthUsers = User::whereMonth('created_at', $currentMonth->month)
                            ->whereYear('created_at', $currentMonth->year)
                            ->count();
        
        // Last month stats
        $lastMonthOrders = Order::whereMonth('created_at', $lastMonth->month)
                           ->whereYear('created_at', $lastMonth->year)
                           ->count();
        
        $lastMonthRevenue = Order::whereMonth('created_at', $lastMonth->month)
                            ->whereYear('created_at', $lastMonth->year)
                            ->sum('total_amount');
                            
        $lastMonthUsers = User::whereMonth('created_at', $lastMonth->month)
                          ->whereYear('created_at', $lastMonth->year)
                          ->count();
        
        // Calculate percentage changes
        $orderChange = $lastMonthOrders > 0 
            ? (($currentMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100 
            : 100;
            
        $revenueChange = $lastMonthRevenue > 0 
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 100;
            
        $userChange = $lastMonthUsers > 0 
            ? (($currentMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100 
            : 100;
        
        // Daily sales for current month
        $dailySales = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Category data
        $categoryData = Category::select('categories.name')
            ->selectRaw('SUM(order_items.subtotal) as revenue')
            ->join('products', 'categories.id', '=', 'products.category_id')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereMonth('orders.created_at', $currentMonth->month)
            ->whereYear('orders.created_at', $currentMonth->year)
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();
        
        // Top selling products
        $topProducts = Product::select(
                'products.id',
                'products.name',
                'products.image',
                'products.stock',
                'products.category_id',
                DB::raw('COUNT(order_items.id) as sales_count'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereNotNull('order_items.id')
            ->whereMonth('orders.created_at', $currentMonth->month)
            ->whereYear('orders.created_at', $currentMonth->year)
            ->groupBy('products.id', 'products.name', 'products.image', 'products.stock', 'products.category_id')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->with('category')
            ->get();
            
        // Top selling offers
        $topOffers = \App\Models\Offer::select(
                'offers.id',
                'offers.title',
                'offers.image',
                DB::raw('COUNT(order_items.id) as sales_count'),
                DB::raw('SUM(order_items.subtotal) as revenue')
            )
            ->join('order_items', 'offers.id', '=', 'order_items.offer_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('order_items.offer_id')
            ->whereMonth('orders.created_at', $currentMonth->month)
            ->whereYear('orders.created_at', $currentMonth->year)
            ->groupBy('offers.id', 'offers.title', 'offers.image')
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.reports.index', compact(
            'currentMonthOrders', 'lastMonthOrders', 'orderChange',
            'currentMonthRevenue', 'lastMonthRevenue', 'revenueChange',
            'currentMonthUsers', 'lastMonthUsers', 'userChange',
            'dailySales', 'categoryData', 'topProducts', 'topOffers'
        ));
    }
    
    /**
     * Display detailed sales reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sales(Request $request)
    {
        // Date range
        $endDate = $request->input('end_date') 
            ? Carbon::parse($request->input('end_date')) 
            : Carbon::now();
            
        $startDate = $request->input('start_date') 
            ? Carbon::parse($request->input('start_date')) 
            : $endDate->copy()->subDays(30);
        
        // Status filter
        $status = $request->input('status');
        
        // Orders query
        $ordersQuery = Order::with(['items.product', 'items.offer', 'user'])
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            
        if ($status) {
            $ordersQuery->where('status', $status);
        }
        
        // Get order statuses for filter
        $orderStatuses = Order::distinct()->pluck('status')->toArray();
        
        // Get orders for table
        $orders = $ordersQuery->clone()->latest()->paginate(15);
        
        // Calculate summary stats
        $orderCount = $ordersQuery->clone()->count();
        $totalRevenue = $ordersQuery->clone()->sum('total_amount');
        $totalTax = $orderCount > 0 ? $totalRevenue * 0.1 : 0; // Approximation, should use actual tax data
        $averageOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;
        $averageTaxRate = $totalRevenue > 0 ? ($totalTax / $totalRevenue) * 100 : 0;
        
        // Calculate items sold
        $itemsData = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select(
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('COUNT(DISTINCT CASE WHEN order_items.product_id IS NOT NULL THEN order_items.product_id END) as unique_products'),
                DB::raw('COUNT(DISTINCT CASE WHEN order_items.offer_id IS NOT NULL THEN order_items.offer_id END) as unique_offers')
            );
            
        if ($status) {
            $itemsData->where('orders.status', $status);
        }
        
        $itemsResult = $itemsData->first();
        $totalItemsSold = $itemsResult->total_quantity ?? 0;
        $uniqueProductsSold = $itemsResult->unique_products ?? 0;
        $uniqueOffersSold = $itemsResult->unique_offers ?? 0;
        
        // Revenue trend data
        $revenueTrend = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Order status distribution
        $orderStatusData = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        // Top products
        $topProducts = Product::select(
                'products.id',
                'products.name',
                'products.image',
                'products.category_id',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('(SUM(order_items.subtotal) / ' . ($totalRevenue ?: 1) . ') * 100 as percentage')
            )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($status, function($query) use ($status) {
                return $query->where('orders.status', $status);
            })
            ->with('category')
            ->groupBy('products.id', 'products.name', 'products.image', 'products.category_id')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();
            
        // Top offers
        $topOffers = \App\Models\Offer::select(
                'offers.id',
                'offers.title',
                'offers.image',
                DB::raw('SUM(order_items.quantity) as quantity_sold'),
                DB::raw('SUM(order_items.subtotal) as revenue'),
                DB::raw('(SUM(order_items.subtotal) / ' . ($totalRevenue ?: 1) . ') * 100 as percentage')
            )
            ->join('order_items', 'offers.id', '=', 'order_items.offer_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereNotNull('order_items.offer_id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($status, function($query) use ($status) {
                return $query->where('orders.status', $status);
            })
            ->groupBy('offers.id', 'offers.title', 'offers.image')
            ->orderBy('revenue', 'desc')
            ->limit(5)
            ->get();
        
        // Payment methods
        $paymentMethods = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->select('payment_method as method', DB::raw('count(*) as count'))
            ->groupBy('payment_method')
            ->orderBy('count', 'desc')
            ->get();
        
        // Location data (based on shipping address state/country)
        $locationData = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($status, function($query) use ($status) {
                return $query->where('status', $status);
            })
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(shipping_address, '$.state')) as location"),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->groupBy('location')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.reports.sales', compact(
            'startDate', 'endDate', 'orderStatuses',
            'orders', 'orderCount', 'totalRevenue', 'totalTax',
            'averageOrderValue', 'averageTaxRate',
            'totalItemsSold', 'uniqueProductsSold', 'uniqueOffersSold',
            'revenueTrend', 'orderStatusData', 'topProducts', 'topOffers',
            'paymentMethods', 'locationData'
        ));
    }
    
    /**
     * Display product performance reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function products(Request $request)
    {
        // Date range
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();
            
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : $endDate->copy()->subDays(30);
        
        // Category filter
        $categoryId = $request->input('category_id');
        
        // Get all categories for filter
        $categories = Category::orderBy('name')->get();
        
        // Products with sales data
        $productsQuery = Product::select(
                'products.*',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as units_sold'),
                DB::raw('COALESCE(SUM(order_items.subtotal), 0) as revenue'),
                DB::raw('COALESCE(SUM(order_items.subtotal) / NULLIF(SUM(order_items.quantity), 0), 0) as avg_price'),
                DB::raw('35 as profit_margin') // Placeholder, would need cost data for actual margin
            )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->with('category')
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('products.category_id', $categoryId);
            })
            ->groupBy('products.id')
            ->orderBy('units_sold', 'desc');
        
        $products = $productsQuery->paginate(15);
        
        // Summary stats
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_visible', true)->count();
        $totalUnitsSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->join('products', 'order_items.product_id', '=', 'products.id')
                             ->where('products.category_id', $categoryId);
            })
            ->sum('order_items.quantity');
            
        $totalRevenue = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->join('products', 'order_items.product_id', '=', 'products.id')
                             ->where('products.category_id', $categoryId);
            })
            ->sum('order_items.subtotal');
            
        $daysInPeriod = $startDate->diffInDays($endDate) + 1;
        $salesPerDay = $daysInPeriod > 0 ? round($totalUnitsSold / $daysInPeriod, 1) : 0;
        $averageProductRevenue = $totalProducts > 0 ? $totalRevenue / $totalProducts : 0;
        
        // Inventory status
        $lowStockCount = Product::where('stock', '<=', 5)
            ->where('stock', '>', 0)
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->count();
            
        $outOfStockCount = Product::where('stock', 0)
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->count();
        
        // Charts data
        // Top Products Chart
        $topProductsChart = Product::select('products.name', DB::raw('COALESCE(SUM(order_items.quantity), 0) as units_sold'))
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('products.category_id', $categoryId);
            })
            ->groupBy('products.id', 'products.name')
            ->orderBy('units_sold', 'desc')
            ->limit(10)
            ->get();
            
        // Category Sales Chart
        $categorySalesChart = Category::select('categories.name', DB::raw('COALESCE(SUM(order_items.subtotal), 0) as revenue'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('categories.id', $categoryId);
            })
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('revenue', 'desc')
            ->get();
            
        // Inventory Status Chart
        $inventoryStatusChart = Product::select(
                'products.name',
                'products.stock',
                DB::raw('COALESCE(SUM(order_items.quantity), 0) as units_sold')
            )
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) use ($startDate, $endDate) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()]);
            })
            ->when($categoryId, function($query) use ($categoryId) {
                return $query->where('products.category_id', $categoryId);
            })
            ->groupBy('products.id', 'products.name', 'products.stock')
            ->orderBy('units_sold', 'desc')
            ->limit(15)
            ->get();

        return view('admin.reports.products', compact(
            'startDate', 'endDate', 'categories',
            'products', 'totalProducts', 'activeProducts',
            'totalUnitsSold', 'salesPerDay', 'totalRevenue',
            'averageProductRevenue', 'lowStockCount', 'outOfStockCount',
            'topProductsChart', 'categorySalesChart', 'inventoryStatusChart'
        ));
    }
    
    /**
     * Display customer reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function customers(Request $request)
    {
        // Date range
        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))
            : Carbon::now();
            
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))
            : $endDate->copy()->subDays(30);
        
        // Get top customers by revenue
        $topCustomers = User::select(
                'users.id',
                'users.name',
                'users.email',
                'users.created_at',
                'users.profile_image',
                DB::raw('COUNT(DISTINCT orders.id) as orders_count'),
                DB::raw('SUM(orders.total_amount) as total_spent'),
                DB::raw('AVG(orders.total_amount) as average_order'),
                DB::raw('MAX(orders.created_at) as last_order_date')
            )
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->whereBetween('orders.created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.created_at', 'users.profile_image')
            ->orderBy('total_spent', 'desc')
            ->paginate(15);
            
        // Cast dates to Carbon objects
        $topCustomers->getCollection()->transform(function ($customer) {
            // Cast created_at to Carbon if it's a string
            if (is_string($customer->created_at)) {
                $customer->created_at = \Carbon\Carbon::parse($customer->created_at);
            }
            
            // Cast last_order_date to Carbon if it's a string and not null
            if (is_string($customer->last_order_date) && $customer->last_order_date) {
                $customer->last_order_date = \Carbon\Carbon::parse($customer->last_order_date);
            }
            
            return $customer;
        });
        
        // Customer stats
        $totalCustomers = User::count();
        $newCustomers = User::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])->count();
        
        // Order stats
        $orderStats = Order::whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COUNT(DISTINCT user_id) as customers_with_orders'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as avg_order_value')
            )
            ->first();
            
        $totalOrders = $orderStats->total_orders ?? 0;
        $avgOrderValue = $orderStats->avg_order_value ?? 0;
        
        // Repeat purchase rate
        $repeatPurchaseData = DB::table('users')
            ->select('users.id', DB::raw('COUNT(orders.id) as order_count'))
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.created_at', '>=', $startDate->startOfDay())
            ->where('orders.created_at', '<=', $endDate->endOfDay())
            ->groupBy('users.id')
            ->get();
            
        $customersWithOrders = $orderStats->customers_with_orders ?? 0;
        $repeatedPurchaseCustomers = $repeatPurchaseData->where('order_count', '>=', 2)->count();
        $returningCustomerRate = $customersWithOrders > 0 
            ? ($repeatedPurchaseCustomers / $customersWithOrders) * 100 
            : 0;
        
        // Customer segments
        $vipThreshold = 500; // Customers who spent over $500
        $vipCustomers = $topCustomers->where('total_spent', '>=', $vipThreshold)->count();
        $vipRevenue = $topCustomers->where('total_spent', '>=', $vipThreshold)->sum('total_spent');
        $vipPercentage = $totalCustomers > 0 ? ($vipCustomers / $totalCustomers) * 100 : 0;
        
        $regularCustomers = $repeatPurchaseData->where('order_count', '>=', 2)
            ->where('order_count', '<=', 3)
            ->count();
        $regularRevenue = $totalOrders > 0 ? $orderStats->total_revenue * 0.4 : 0; // Approximation
        $regularPercentage = $totalCustomers > 0 ? ($regularCustomers / $totalCustomers) * 100 : 0;
        
        $newCustomersWithOneOrder = $repeatPurchaseData->where('order_count', 1)->count();
        $newCustomerRevenue = $totalOrders > 0 ? $orderStats->total_revenue * 0.3 : 0; // Approximation
        $newCustomerPercentage = $totalCustomers > 0 ? ($newCustomersWithOneOrder / $totalCustomers) * 100 : 0;
        
        // Customer growth data (monthly)
        $customerGrowthRaw = User::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as new_customers')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
            
        $customerGrowthData = collect();
        $totalSoFar = 0;
        
        foreach ($customerGrowthRaw as $month) {
            $newCustomers = $month->new_customers;
            $totalSoFar += $newCustomers;
            
            $customerGrowthData->push([
                'date' => Carbon::createFromFormat('Y-m', $month->month)->format('M Y'),
                'new_customers' => $newCustomers,
                'total_customers' => $totalSoFar
            ]);
        }
        
        // Order frequency distribution
        $orderFrequencyData = collect();
        
        // Get all users who have placed orders
        $usersWithOrders = DB::table('users')
            ->select('users.id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->where('orders.created_at', '>=', $startDate)
            ->where('orders.created_at', '<=', $endDate)
            ->groupBy('users.id')
            ->get();
            
        // Count orders for each user
        $orderCounts = [];
        foreach ($usersWithOrders as $user) {
            $count = DB::table('orders')
                ->where('user_id', $user->id)
                ->where('created_at', '>=', $startDate)
                ->where('created_at', '<=', $endDate)
                ->count();
                
            if (!isset($orderCounts[$count])) {
                $orderCounts[$count] = 0;
            }
            $orderCounts[$count]++;
        }
        
        // Format data for the chart
        ksort($orderCounts); // Sort by frequency (key)
        foreach ($orderCounts as $frequency => $count) {
            $orderFrequencyData->push([
                'frequency' => $frequency,
                'count' => $count
            ]);
        }
        
        return view('admin.reports.customers', compact(
            'startDate', 'endDate', 'topCustomers',
            'totalCustomers', 'newCustomers',
            'totalOrders', 'avgOrderValue',
            'returningCustomerRate', 'repeatedPurchaseCustomers',
            'vipCustomers', 'vipRevenue', 'vipPercentage', 'vipThreshold',
            'regularCustomers', 'regularRevenue', 'regularPercentage',
            'newCustomersWithOneOrder', 'newCustomerRevenue', 'newCustomerPercentage',
            'customerGrowthData', 'orderFrequencyData'
        ));
    }
} 