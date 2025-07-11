<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function dashboard(Request $request)
    {
        if (!auth()->user()->hasPermission('view_dashboard')) {
            return redirect()->route('home')->with('error', 'You do not have permission to access the dashboard.');
        }

        // Get the time range from the request, default to 'month'
        $timeRange = $request->get('timeRange', 'month');

        // Define date range based on selected time range
        $startDate = now();
        $endDate = now();
        
        switch ($timeRange) {
            case 'today':
                $startDate = now()->startOfDay();
                $previousStart = now()->subDay()->startOfDay();
                $previousEnd = now()->subDay()->endOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $previousStart = now()->subWeek()->startOfWeek();
                $previousEnd = now()->subWeek()->endOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                $previousStart = now()->subMonth()->startOfMonth();
                $previousEnd = now()->subMonth()->endOfMonth();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $previousStart = now()->subYear()->startOfYear();
                $previousEnd = now()->subYear()->endOfYear();
                break;
            default:
                $startDate = now()->startOfMonth();
                $previousStart = now()->subMonth()->startOfMonth();
                $previousEnd = now()->subMonth()->endOfMonth();
        }

        // Get statistics for dashboard
        $totalProducts = \App\Models\Product::count();
        $totalOrders = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalUsers = \App\Models\User::count();
        $totalRevenue = \App\Models\Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');
        
        // Get active products count - using is_visible instead of is_active
        $activeProducts = \App\Models\Product::where('is_visible', true)->count();
        
        // Get pending orders count
        $pendingOrders = \App\Models\Order::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        
        // Get new users count
        $newUsers = \App\Models\User::whereBetween('created_at', [$startDate, $endDate])->count();
        
        // Calculate revenue growth
        $previousPeriodRevenue = \App\Models\Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('total_amount');
            
        $revenueGrowth = $previousPeriodRevenue > 0 
            ? round((($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100, 1)
            : 100;

        // Get recent orders for dashboard
        $recentOrders = \App\Models\Order::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->take(5)
            ->get();

        // Get sales data for chart based on time range
        $salesData = $this->getSalesData($timeRange);
        
        // Get top products for the selected time range
        $topProducts = $this->getTopProducts($timeRange);

        // Get recent activities
        $recentActivities = \App\Models\Activity::latest()->take(5)->get();

        // Get dashboard visibility settings
        $dashboardSettings = $this->getDashboardVisibilitySettings();

        return view('admin.dashboard', array_merge(
            compact(
            'totalProducts', 
            'totalOrders', 
            'totalUsers', 
            'totalRevenue', 
            'salesData', 
            'topProducts', 
            'recentActivities',
                'recentOrders',
                'activeProducts',
                'pendingOrders',
                'newUsers',
                'revenueGrowth',
                'timeRange'
            ),
            $dashboardSettings
        ));
    }
    
    /**
     * Refresh dashboard data via AJAX based on time range.
     */
    public function refreshDashboard(Request $request)
    {
        try {
            if (!auth()->user()->hasPermission('view_dashboard')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $timeRange = $request->get('timeRange', 'month');
            
            // Define date range based on selected time range
            $startDate = now();
            $endDate = now();
            
            switch ($timeRange) {
                case 'today':
                    $startDate = now()->startOfDay();
                    $previousStart = now()->subDay()->startOfDay();
                    $previousEnd = now()->subDay()->endOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $previousStart = now()->subWeek()->startOfWeek();
                    $previousEnd = now()->subWeek()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $previousStart = now()->subMonth()->startOfMonth();
                    $previousEnd = now()->subMonth()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $previousStart = now()->subYear()->startOfYear();
                    $previousEnd = now()->subYear()->endOfYear();
                    break;
                default:
                    $startDate = now()->startOfMonth();
                    $previousStart = now()->subMonth()->startOfMonth();
                    $previousEnd = now()->subMonth()->endOfMonth();
            }
            
            // Get statistics for the selected time range
            $totalProducts = \App\Models\Product::count();
            $totalOrders = \App\Models\Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $totalUsers = \App\Models\User::whereBetween('created_at', [$startDate, $endDate])->count();
            $totalRevenue = \App\Models\Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount');
                
            // Get active products count - using is_visible instead of is_active
            $activeProducts = \App\Models\Product::where('is_visible', true)->count();
            
            // Get pending orders count for the selected time range
            $pendingOrders = \App\Models\Order::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            
            // Get new users count for the selected time range
            $newUsers = \App\Models\User::whereBetween('created_at', [$startDate, $endDate])->count();
            
            // Calculate revenue growth
            $previousPeriodRevenue = \App\Models\Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->sum('total_amount');
                
            $revenueGrowth = $previousPeriodRevenue > 0 
                ? round((($totalRevenue - $previousPeriodRevenue) / $previousPeriodRevenue) * 100, 1)
                : 100;
                
            // Get recent orders for the selected time range
            $recentOrders = \App\Models\Order::with('user')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->latest()
                ->take(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'user' => $order->user ? [
                            'name' => $order->user->name,
                            'profile_image' => $order->user->profile_image ? asset('storage/' . $order->user->profile_image) : null
                        ] : null,
                        'created_at' => $order->created_at->format('M d, Y'),
                        'total_amount' => $order->total_amount,
                        'status' => $order->status
                    ];
                });
                
            // Get sales data for chart based on time range
            $salesData = $this->getSalesData($timeRange);
            
            // Get top products for the selected time range
            $topProducts = $this->getTopProducts($timeRange);
            
            return response()->json([
                'stats' => [
                    'totalProducts' => $totalProducts,
                    'totalOrders' => $totalOrders,
                    'totalUsers' => $totalUsers,
                    'totalRevenue' => $totalRevenue,
                    'activeProducts' => $activeProducts,
                    'pendingOrders' => $pendingOrders,
                    'newUsers' => $newUsers,
                    'revenueGrowth' => $revenueGrowth
                ],
                'salesData' => $salesData,
                'topProducts' => $topProducts,
                'recentOrders' => $recentOrders
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while refreshing data'], 500);
        }
    }
    
    /**
     * Get sales data for chart
     */
    private function getSalesData($timeRange = 'month')
    {
        try {
            $query = \App\Models\Order::query();
            
            // Apply date filters based on time range
            switch ($timeRange) {
                case 'today':
                    $query->whereDate('created_at', today());
                    $groupBy = 'hour';
                    $format = 'H:i';
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()]);
                    $groupBy = 'date';
                    $format = 'D';
                    break;
                case 'month':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()]);
                    $groupBy = 'date';
                    $format = 'M d';
                    break;
                case 'year':
                    $query->whereBetween('created_at', [now()->startOfYear(), now()]);
                    $groupBy = 'month';
                    $format = 'M';
                    break;
                default:
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()]);
                    $groupBy = 'date';
                    $format = 'M d';
            }
            
            if ($groupBy === 'hour') {
                $data = $query->selectRaw('HOUR(created_at) as hour, SUM(total_amount) as amount, COUNT(id) as orders')
                    ->groupBy('hour')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'date' => date('H:i', mktime($item->hour, 0, 0)),
                            'amount' => round($item->amount, 2),
                            'orders' => $item->orders
                        ];
                    });
            } elseif ($groupBy === 'date') {
                $data = $query->selectRaw('DATE(created_at) as date, SUM(total_amount) as amount, COUNT(id) as orders')
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function ($item) use ($format) {
                        return [
                            'date' => date($format, strtotime($item->date)),
                            'amount' => round($item->amount, 2),
                            'orders' => $item->orders
                        ];
                    });
            } elseif ($groupBy === 'month') {
                $data = $query->selectRaw('MONTH(created_at) as month, SUM(total_amount) as amount, COUNT(id) as orders')
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'date' => date('M', mktime(0, 0, 0, $item->month, 1)),
                            'amount' => round($item->amount, 2),
                            'orders' => $item->orders
                        ];
                    });
            }
            
            // Ensure we have data (if empty, return a default structure)
            if ($data->isEmpty()) {
                return [
                    ['date' => 'No data', 'amount' => 0, 'orders' => 0]
                ];
            }
            
            return $data;
        } catch (\Exception $e) {
            return [
                ['date' => 'Error', 'amount' => 0, 'orders' => 0]
            ];
        }
    }
    
    /**
     * Get top products by sales
     */
    private function getTopProducts($timeRange = 'month')
    {
        try {
            $query = \App\Models\OrderItem::query()->with('product');
            
            // Apply date filters based on time range
            switch ($timeRange) {
                case 'today':
                    $query->whereHas('order', function ($q) {
                        $q->whereDate('created_at', today());
                    });
                    break;
                case 'week':
                    $query->whereHas('order', function ($q) {
                        $q->whereBetween('created_at', [now()->startOfWeek(), now()]);
                    });
                    break;
                case 'month':
                    $query->whereHas('order', function ($q) {
                        $q->whereBetween('created_at', [now()->startOfMonth(), now()]);
                    });
                    break;
                case 'year':
                    $query->whereHas('order', function ($q) {
                        $q->whereBetween('created_at', [now()->startOfYear(), now()]);
                    });
                    break;
                default:
                    $query->whereHas('order', function ($q) {
                        $q->whereBetween('created_at', [now()->startOfMonth(), now()]);
                    });
            }
            
            $topProducts = $query->selectRaw('product_id, SUM(quantity) as sales_count, SUM(quantity * price) as revenue')
                ->groupBy('product_id')
                ->orderByDesc('revenue')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->product_id,
                        'name' => $item->product ? $item->product->name : 'Unknown Product',
                        'sales_count' => $item->sales_count,
                        'revenue' => round($item->revenue, 2)
                    ];
                });
                
            // Ensure we have data (if empty, return a default structure)
            if ($topProducts->isEmpty()) {
                return [
                    ['id' => 0, 'name' => 'No products', 'sales_count' => 0, 'revenue' => 0]
                ];
            }
                
            return $topProducts;
        } catch (\Exception $e) {
            return [
                ['id' => 0, 'name' => 'No data available', 'sales_count' => 0, 'revenue' => 0]
            ];
        }
    }
    
    /**
     * Display the user's profile form.
     */
    public function profile()
    {
        return view('profile.settings', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);
        
        // Normalize email to lowercase
        $validated['email'] = strtolower($validated['email']);
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete the old image if it exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            // Store the new image
            $validated['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }
        
        // Check if email has changed
        if ($validated['email'] !== $user->email) {
            $validated['email_verified_at'] = null;
            
            $user->update($validated);
            
            $user->sendEmailVerificationNotification();
            
            return redirect()->route('profile')->with('status', 'Profile updated successfully. Please verify your new email address.');
        }
        
        $user->update($validated);
        
        return redirect()->route('profile')->with('status', 'Profile updated successfully.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required', 
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);
        
        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        
        return redirect()->route('profile')->with('status', 'Password updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        
        $user = Auth::user();
        
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        $user->delete();
        
        return redirect()->route('home')->with('status', 'Your account has been deleted.');
    }

    /**
     * Get dashboard visibility settings from either normalized tables or old settings
     */
    private function getDashboardVisibilitySettings()
    {
        // Default settings
        $settings = [
            // Stats Cards
            'show_products_stat' => true,
            'show_orders_stat' => true,
            'show_users_stat' => true,
            'show_revenue_stat' => true,
            
            // Quick Action Cards
            'show_add_product_card' => true,
            'show_categories_card' => true,
            'show_orders_card' => true,
            'show_users_card' => true,
            'show_messages_card' => true,
            'show_coupons_card' => true,
            'show_settings_card' => true,
            'show_reports_card' => true,
            'show_shipping_card' => true,
            'show_activities_card' => true,
            
            // Charts
            'show_sales_chart' => true,
            'show_products_chart' => true,
            'show_category_chart' => true,
            'show_time_chart' => true,
            
            // Tables
            'show_orders_table' => true,
            'show_activities_table' => true,
            
            // Other Settings
            'elements_per_row' => 4,
            'refresh_interval' => 0,
        ];
        
        // Try to get settings from the normalized dashboard_visibility table
        if (Schema::hasTable('dashboard_visibility')) {
            $dashboardVisibility = DB::table('dashboard_visibility')->first();
            
            if ($dashboardVisibility) {
                // Update settings with values from normalized table
                $settings['show_products_stat'] = $dashboardVisibility->show_products_stat;
                $settings['show_orders_stat'] = $dashboardVisibility->show_orders_stat;
                $settings['show_users_stat'] = $dashboardVisibility->show_users_stat;
                $settings['show_revenue_stat'] = $dashboardVisibility->show_revenue_stat;
                $settings['show_sales_chart'] = $dashboardVisibility->show_sales_chart;
                $settings['show_products_chart'] = $dashboardVisibility->show_products_chart;
                $settings['show_category_chart'] = $dashboardVisibility->show_category_chart;
                $settings['show_time_chart'] = $dashboardVisibility->show_time_chart;
                $settings['show_orders_table'] = $dashboardVisibility->show_orders_table;
                $settings['show_activities_table'] = $dashboardVisibility->show_activities_table;
                $settings['elements_per_row'] = $dashboardVisibility->elements_per_row;
            }
            
            return $settings;
        }
        
        // Fall back to the old settings table if it exists
        if (Schema::hasTable('settings')) {
            // Get all dashboard-related settings
            $dashboardSettings = Setting::where('group', 'dashboard')
                ->orWhere('group', 'dashboard_charts')
                ->orWhere('group', 'dashboard_tables')
                ->get();
            
            // Update settings from database
            foreach ($dashboardSettings as $setting) {
                $key = str_replace('dashboard_', '', $setting->key);
                
                if (isset($settings[$key])) {
                    // Convert boolean string values to actual booleans
                    if ($setting->type === 'boolean') {
                        $settings[$key] = (bool) $setting->value;
                    } else {
                        $settings[$key] = $setting->value;
                    }
                }
            }
        }
        
        return $settings;
    }
} 