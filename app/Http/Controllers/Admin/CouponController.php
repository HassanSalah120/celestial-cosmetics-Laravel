<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Product;
use App\Services\MarketingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    protected $marketingService;

    public function __construct(MarketingService $marketingService)
    {
        $this->marketingService = $marketingService;
    }

    /**
     * Display a listing of coupons.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Remove debug logging
        
        $query = Coupon::query();

        // Filter by active status
        if ($request->has('status') && $request->status !== '') {
            $status = $request->status === 'active';
            $query->where('is_active', $status);
        }

        // Filter by discount type
        if ($request->has('type') && $request->type !== '') {
            $query->where('discount_type', $request->type);
        }

        // Filter by period
        if ($request->has('period') && $request->period !== '') {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'last7days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'thismonth':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'lastmonth':
                    $query->whereMonth('created_at', now()->subMonth()->month)
                          ->whereYear('created_at', now()->subMonth()->year);
                    break;
                case 'custom':
                    if ($request->has('start_date') && $request->has('end_date')) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                        $query->whereBetween('created_at', [$startDate, $endDate]);
                    }
                    break;
            }
        }

        // Filter by search term
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Order by created_at desc
        $query->orderBy('created_at', 'desc');

        // Return JSON response for AG Grid
        if ($request->has('format') && $request->format === 'json') {
            try {
                $coupons = $query->get()->map(function ($coupon) {
                    return [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'description' => $coupon->description,
                        'discount_type' => $coupon->discount_type,
                        'discount_value' => $coupon->discount_value,
                        'minimum_order_amount' => $coupon->minimum_order_amount,
                        'maximum_discount_amount' => $coupon->maximum_discount_amount,
                        'usage_limit_per_coupon' => $coupon->usage_limit_per_coupon,
                        'usage_limit_per_user' => $coupon->usage_limit_per_user,
                        'start_date' => $coupon->start_date ? $coupon->start_date->format('Y-m-d') : null,
                        'end_date' => $coupon->end_date ? $coupon->end_date->format('Y-m-d') : null,
                        'is_active' => $coupon->is_active,
                        'created_at' => $coupon->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $coupon->updated_at->format('Y-m-d H:i:s'),
                        'applicable_products' => $coupon->applicable_products,
                        'applicable_categories' => $coupon->applicable_categories,
                        
                        // Include usage statistics
                        'usage_count' => $coupon->usages()->count(),
                    ];
                });
                
                return response()->json($coupons);
            } catch (\Exception $e) {
                Log::error('Error generating coupons JSON: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Failed to load coupon data'
                ], 500);
            }
        }

        $coupons = $query->paginate(10);
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show the form for creating a new coupon.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        
        return view('admin.coupons.create', compact('products', 'categories'));
    }

    /**
     * Store a newly created coupon in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Check if auto_generate_code is set
        if ($request->has('auto_generate_code')) {
            $validated = $request->validate([
                'code_prefix' => 'nullable|string|max:10',
                'description' => 'nullable|string|max:255',
                'discount_type' => 'required|in:percentage,fixed_amount',
                'discount_value' => 'required|numeric|min:0',
                'minimum_order_amount' => 'required|numeric|min:0',
                'maximum_discount_amount' => 'nullable|numeric|min:0',
                'usage_limit_per_coupon' => 'nullable|integer|min:1',
                'usage_limit_per_user' => 'nullable|integer|min:1',
                'applicable_products' => 'nullable|array',
                'applicable_categories' => 'nullable|array',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_active' => 'boolean',
            ]);
            
            // Generate unique coupon code
            $prefix = $request->code_prefix ?? '';
            $validated['code'] = $this->marketingService->generateUniqueCouponCode($prefix);
        } else {
            $validated = $request->validate([
                'code' => 'required|string|unique:coupons,code|max:20',
                'description' => 'nullable|string|max:255',
                'discount_type' => 'required|in:percentage,fixed_amount',
                'discount_value' => 'required|numeric|min:0',
                'minimum_order_amount' => 'required|numeric|min:0',
                'maximum_discount_amount' => 'nullable|numeric|min:0',
                'usage_limit_per_coupon' => 'nullable|integer|min:1',
                'usage_limit_per_user' => 'nullable|integer|min:1',
                'applicable_products' => 'nullable|array',
                'applicable_categories' => 'nullable|array',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'is_active' => 'boolean',
            ]);
        }

        // Set is_active to true by default if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        // Convert dates to Carbon instances
        if (!empty($validated['start_date'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date']);
        }
        if (!empty($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date']);
        }

        // Create the coupon
        $coupon = Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully!');
    }

    /**
     * Display the specified coupon.
     *
     * @param Coupon $coupon
     * @return \Illuminate\View\View
     */
    public function show(Coupon $coupon)
    {
        // Get usage statistics
        $usageStats = CouponUsage::where('coupon_id', $coupon->id)
            ->select(
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(discount_amount) as total_discount'),
                DB::raw('AVG(discount_amount) as avg_discount')
            )
            ->first();

        // Get recent usage
        $recentUsage = CouponUsage::where('coupon_id', $coupon->id)
            ->with('user:id,name,email', 'order:id,total_amount,created_at')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.coupons.show', compact('coupon', 'usageStats', 'recentUsage'));
    }

    /**
     * Show the form for editing the specified coupon.
     *
     * @param Coupon $coupon
     * @return \Illuminate\View\View
     */
    public function edit(Coupon $coupon)
    {
        $products = Product::select('id', 'name')->orderBy('name')->get();
        $categories = Category::select('id', 'name')->orderBy('name')->get();
        
        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    /**
     * Update the specified coupon in storage.
     *
     * @param Request $request
     * @param Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string|max:255',
            'discount_type' => 'required|in:percentage,fixed_amount',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'required|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit_per_coupon' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'applicable_products' => 'nullable|array',
            'applicable_categories' => 'nullable|array',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        // Set is_active to false if not provided
        if (!isset($validated['is_active'])) {
            $validated['is_active'] = false;
        }

        // Convert dates to Carbon instances
        if (!empty($validated['start_date'])) {
            $validated['start_date'] = Carbon::parse($validated['start_date']);
        }
        if (!empty($validated['end_date'])) {
            $validated['end_date'] = Carbon::parse($validated['end_date']);
        }

        // Update the coupon
        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully!');
    }

    /**
     * Remove the specified coupon from storage.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Coupon $coupon)
    {
        try {
            $coupon->delete();
            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting coupon: ' . $e->getMessage());
            return redirect()->route('admin.coupons.index')
                ->with('error', 'Error deleting coupon. It may be in use by orders.');
        }
    }

    /**
     * Display coupon analytics.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function analytics(Request $request)
    {
        $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->subDays(30);
        $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : Carbon::now();

        $analytics = $this->marketingService->generateCouponAnalytics($startDate, $endDate);

        // Get additional statistics
        $totalDiscounts = array_sum(array_column($analytics, 'total_discount'));
        $totalUsage = array_sum(array_column($analytics, 'usage_count'));
        $avgDiscountPerOrder = $totalUsage > 0 ? $totalDiscounts / $totalUsage : 0;

        return view('admin.coupons.analytics', compact(
            'analytics', 
            'totalDiscounts', 
            'totalUsage', 
            'avgDiscountPerOrder',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Duplicate an existing coupon.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function duplicate(Coupon $coupon)
    {
        // Create a new coupon with the same data but a new code
        $newCoupon = $coupon->replicate();
        $newCoupon->code = $this->marketingService->generateUniqueCouponCode('COPY-');
        $newCoupon->is_active = false; // Set to inactive by default
        $newCoupon->save();

        return redirect()->route('admin.coupons.edit', $newCoupon)
            ->with('success', 'Coupon duplicated successfully! Please review and update before activating.');
    }

    /**
     * Toggle the active status of a coupon.
     *
     * @param Coupon $coupon
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        $status = $coupon->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Coupon {$status} successfully!");
    }
} 