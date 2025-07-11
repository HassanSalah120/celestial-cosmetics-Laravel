<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\CouponUsage;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'coupon_id',
        'coupon_code',
        'subtotal',
        'discount_amount',
        'shipping_fee',
        'shipping_method',
        'payment_fee',
        'total_amount',
        'status',
        'shipping_address',
        'billing_address',
        'payment_status',
        'payment_method',
        'tracking_number',
        'currency',
        'notes',
        'ip_address',
        'user_agent',
        'cod_fee'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'payment_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'cod_fee' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the coupon associated with this order.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the coupon usage record for this order.
     */
    public function couponUsage()
    {
        return $this->hasOne(CouponUsage::class);
    }

    /**
     * Calculate and return the subtotal for this order.
     *
     * @return float
     */
    public function calculateSubtotal()
    {
        return $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Apply a coupon to this order.
     *
     * @param Coupon $coupon
     * @return bool Whether the coupon was successfully applied
     */
    public function applyCoupon(Coupon $coupon)
    {
        // Check if coupon is valid
        if (!$coupon->isValid()) {
            Log::warning('Attempted to apply invalid coupon', [
                'order_id' => $this->id,
                'coupon_code' => $coupon->code
            ]);
            return false;
        }

        // Check if user is eligible to use this coupon
        if ($this->user_id && !$coupon->isValidForUser(User::find($this->user_id))) {
            Log::warning('User not eligible for coupon', [
                'order_id' => $this->id,
                'coupon_code' => $coupon->code,
                'user_id' => $this->user_id
            ]);
            return false;
        }

        // Check if coupon is applicable to the items in this order
        $items = $this->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity
            ];
        })->toArray();

        if (!$coupon->isApplicableToCart($items, $this->subtotal)) {
            Log::warning('Coupon not applicable to cart items', [
                'order_id' => $this->id,
                'coupon_code' => $coupon->code,
                'subtotal' => $this->subtotal
            ]);
            return false;
        }

        // Calculate discount
        $discount = $coupon->calculateDiscount($this->subtotal);

        try {
            // Use a transaction with serializable isolation to prevent race conditions
            return DB::transaction(function() use ($coupon, $discount) {
                // Update order values
                $this->coupon_id = $coupon->id;
                $this->coupon_code = $coupon->code;
                $this->discount_amount = $discount;
                
                // Calculate total with shipping fee and cod fee
                $this->total_amount = $this->subtotal - $discount + ($this->shipping_fee ?? 0) + ($this->cod_fee ?? 0) + ($this->payment_fee ?? 0);
                $this->save();

                // Check if a coupon usage record already exists for this order
                $existingUsage = CouponUsage::where('order_id', $this->id)
                    ->where('coupon_id', $coupon->id)
                    ->first();

                // Only create a new coupon usage record if one doesn't already exist
                if (!$existingUsage) {
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $this->user_id,
                        'order_id' => $this->id,
                        'discount_amount' => $discount
                    ]);
                }

                return true;
            }, 5); // 5 retries if deadlock occurs
        } catch (\Exception $e) {
            Log::error('Failed to apply coupon', [
                'order_id' => $this->id,
                'coupon_code' => $coupon->code,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Remove the applied coupon from this order.
     *
     * @return bool
     */
    public function removeCoupon()
    {
        if (!$this->coupon_id) {
            return false;
        }

        try {
            return DB::transaction(function() {
                // Delete all coupon usage records for this order
                CouponUsage::where('order_id', $this->id)->delete();

                // Update order values
                $this->coupon_id = null;
                $this->coupon_code = null;
                $this->discount_amount = 0;
                
                // Calculate total with shipping fee and cod fee
                $this->total_amount = $this->subtotal + ($this->shipping_fee ?? 0) + ($this->cod_fee ?? 0) + ($this->payment_fee ?? 0);
                $this->save();

                return true;
            }, 5); // 5 retries if deadlock occurs
        } catch (\Exception $e) {
            Log::error('Failed to remove coupon', [
                'order_id' => $this->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get customer's name from shipping address
     * 
     * @return string
     */
    public function getCustomerNameAttribute()
    {
        if ($this->user) {
            return $this->user->name;
        }
        
        if (is_array($this->shipping_address)) {
            $firstName = $this->shipping_address['first_name'] ?? '';
            $lastName = $this->shipping_address['last_name'] ?? '';
            
            return trim($firstName . ' ' . $lastName);
        }
        
        return 'Guest';
    }
    
    /**
     * Get customer's email from shipping address
     * 
     * @return string|null
     */
    public function getCustomerEmailAttribute()
    {
        if ($this->user) {
            return $this->user->email;
        }
        
        if (is_array($this->shipping_address)) {
            return $this->shipping_address['email'] ?? null;
        }
        
        return null;
    }
} 