<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MarketingService
{
    /**
     * Get a valid coupon by code.
     *
     * @param string $code
     * @return Coupon|null
     */
    public function getCouponByCode(string $code)
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->isValid()) {
            return null;
        }

        return $coupon;
    }

    /**
     * Apply a coupon to an order.
     *
     * @param Order $order
     * @param string $couponCode
     * @return array
     */
    public function applyCouponToOrder(Order $order, string $couponCode)
    {
        // Get coupon
        $coupon = $this->getCouponByCode($couponCode);

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid or expired coupon code.'
            ];
        }

        // Try to apply the coupon
        $applied = $order->applyCoupon($coupon);

        if (!$applied) {
            return [
                'success' => false,
                'message' => 'This coupon cannot be applied to your order.'
            ];
        }

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount_amount' => $order->discount_amount,
            'total_amount' => $order->total_amount
        ];
    }

    /**
     * Generate coupon usage analytics.
     *
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @return array
     */
    public function generateCouponAnalytics(?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = CouponUsage::query()
            ->select(
                'coupon_id',
                DB::raw('COUNT(*) as usage_count'),
                DB::raw('SUM(discount_amount) as total_discount'),
                DB::raw('AVG(discount_amount) as avg_discount')
            )
            ->with('coupon:id,code,discount_type,discount_value')
            ->groupBy('coupon_id');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $results = $query->get();

        // Format the results for easy use
        $analytics = $results->map(function ($item) {
            return [
                'coupon_id' => $item->coupon_id,
                'coupon_code' => $item->coupon->code ?? 'Unknown',
                'discount_type' => $item->coupon->discount_type ?? 'Unknown',
                'discount_value' => $item->coupon->discount_value ?? 0,
                'usage_count' => $item->usage_count,
                'total_discount' => round($item->total_discount, 2),
                'avg_discount' => round($item->avg_discount, 2)
            ];
        });

        return $analytics->toArray();
    }

    /**
     * Get expiring soon coupons for a notification service.
     *
     * @param int $daysThreshold
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpiringSoonCoupons(int $daysThreshold = 7)
    {
        $thresholdDate = Carbon::now()->addDays($daysThreshold);
        
        return Coupon::where('is_active', true)
            ->whereNotNull('end_date')
            ->where('end_date', '<=', $thresholdDate)
            ->where('end_date', '>', Carbon::now())
            ->get();
    }

    /**
     * Create a promotional coupon for a specific user segment.
     *
     * @param array $data
     * @return Coupon
     */
    public function createPromotionalCoupon(array $data)
    {
        return Coupon::create($data);
    }

    /**
     * Generate a unique coupon code.
     *
     * @param string $prefix
     * @param int $length
     * @return string
     */
    public function generateUniqueCouponCode(string $prefix = '', int $length = 8)
    {
        do {
            $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $code = $prefix;
            
            for ($i = 0; $i < $length; $i++) {
                $code .= $characters[rand(0, strlen($characters) - 1)];
            }
            
            // Check if code already exists
            $exists = Coupon::where('code', $code)->exists();
        } while ($exists);

        return $code;
    }
} 