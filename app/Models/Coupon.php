<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit_per_coupon',
        'usage_limit_per_user',
        'is_active',
        'applicable_products',
        'applicable_categories',
        'start_date',
        'end_date'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_order_amount' => 'decimal:2',
        'maximum_discount_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get all orders that used this coupon.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all usage records for this coupon.
     */
    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if the coupon is valid for use.
     *
     * @return bool
     */
    public function isValid()
    {
        // Check if coupon is active
        if (!$this->is_active) {
            return false;
        }

        // Check date validity
        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Check if coupon has reached its total usage limit
        if ($this->usage_limit_per_coupon && $this->usages()->count() >= $this->usage_limit_per_coupon) {
            return false;
        }

        return true;
    }

    /**
     * Check if the coupon is valid for a specific user.
     *
     * @param User $user
     * @return bool
     */
    public function isValidForUser(User $user)
    {
        // First check general validity
        if (!$this->isValid()) {
            return false;
        }

        // Check if user has reached their usage limit
        if ($this->usage_limit_per_user) {
            $usageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($usageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the coupon is applicable to a cart.
     *
     * @param array $items Array of product IDs and quantities
     * @param float $totalAmount
     * @return bool
     */
    public function isApplicableToCart(array $items, float $totalAmount)
    {
        // Check minimum order amount
        if ($totalAmount < $this->minimum_order_amount) {
            return false;
        }

        // If no specific products or categories are set, coupon applies to all
        if (empty($this->applicable_products) && empty($this->applicable_categories)) {
            return true;
        }

        // Check if any products in the cart are eligible
        if (!empty($this->applicable_products)) {
            foreach ($items as $item) {
                // Handle both regular products and offers
                if (isset($item['product_id']) && $item['product_id'] && in_array($item['product_id'], $this->applicable_products)) {
                    return true;
                }
                
                // Check if this is an offer item
                if (isset($item['offer_id']) && $item['offer_id']) {
                    // If offers are directly eligible
                    if (in_array('offer_' . $item['offer_id'], $this->applicable_products)) {
                        return true;
                    }
                    
                    // Check if offer contains eligible products
                    $offer = \App\Models\Offer::find($item['offer_id']);
                    if ($offer && $offer->products) {
                        foreach ($offer->products as $offerProduct) {
                            if (in_array($offerProduct->id, $this->applicable_products)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        // Check if any products in applicable categories
        if (!empty($this->applicable_categories)) {
            foreach ($items as $item) {
                // Handle regular products
                if (isset($item['product_id']) && $item['product_id']) {
                    $product = Product::find($item['product_id']);
                    if ($product && in_array($product->category_id, $this->applicable_categories)) {
                        return true;
                    }
                }
                
                // Handle offers
                if (isset($item['offer_id']) && $item['offer_id']) {
                    $offer = \App\Models\Offer::find($item['offer_id']);
                    if ($offer && $offer->products) {
                        foreach ($offer->products as $offerProduct) {
                            if (in_array($offerProduct->category_id, $this->applicable_categories)) {
                                return true;
                            }
                        }
                    }
                }
            }
        }

        return false;
    }

    /**
     * Calculate the discount amount for an order.
     *
     * @param float $subtotal
     * @param array|null $items Cart items with product details
     * @return float
     */
    public function calculateDiscount(float $subtotal, ?array $items = null)
    {
        $discount = 0;

        // If no items provided or no product/category restrictions, apply to entire subtotal
        if (!$items || (empty($this->applicable_products) && empty($this->applicable_categories))) {
            if ($this->discount_type === 'percentage') {
                $discount = $subtotal * ($this->discount_value / 100);
            } else { // fixed_amount
                $discount = $this->discount_value;
            }
        } else {
            // Calculate discount only for eligible products
            $eligibleSubtotal = 0;
            
            foreach ($items as $item) {
                $isEligible = false;
                
                // Handle regular products
                if (isset($item['product_id']) && $item['product_id']) {
                    $productId = $item['product_id'] ?? ($item['product']->id ?? null);
                    $price = $item['price'] ?? ($item['product']->price ?? 0);
                    $quantity = $item['quantity'] ?? 1;
                    $categoryId = $item['category_id'] ?? ($item['product']->category_id ?? null);
                    
                    // Check if product is directly eligible
                    if (!empty($this->applicable_products) && $productId && in_array($productId, $this->applicable_products)) {
                        $isEligible = true;
                    }
                    
                    // Check if product's category is eligible
                    if (!$isEligible && !empty($this->applicable_categories) && $categoryId && in_array($categoryId, $this->applicable_categories)) {
                        $isEligible = true;
                    }
                    
                    if ($isEligible) {
                        $eligibleSubtotal += $price * $quantity;
                    }
                }
                
                // Handle offers
                if (isset($item['offer_id']) && $item['offer_id']) {
                    $offerId = $item['offer_id'];
                    $price = $item['price'] ?? 0;
                    $quantity = $item['quantity'] ?? 1;
                    
                    // Check if offer is directly eligible
                    if (!empty($this->applicable_products) && in_array('offer_' . $offerId, $this->applicable_products)) {
                        $isEligible = true;
                    }
                    
                    // Check if offer contains eligible products
                    if (!$isEligible && !empty($this->applicable_products)) {
                        $offer = \App\Models\Offer::find($offerId);
                        if ($offer && $offer->products) {
                            foreach ($offer->products as $offerProduct) {
                                if (in_array($offerProduct->id, $this->applicable_products)) {
                                    $isEligible = true;
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Check if offer contains products in eligible categories
                    if (!$isEligible && !empty($this->applicable_categories)) {
                        $offer = \App\Models\Offer::with('products')->find($offerId);
                        if ($offer && $offer->products) {
                            foreach ($offer->products as $offerProduct) {
                                if (in_array($offerProduct->category_id, $this->applicable_categories)) {
                                    $isEligible = true;
                                    break;
                                }
                            }
                        }
                    }
                    
                    if ($isEligible) {
                        $eligibleSubtotal += $price * $quantity;
                    }
                }
            }
            
            // Calculate discount based on eligible subtotal
            if ($this->discount_type === 'percentage') {
                $discount = $eligibleSubtotal * ($this->discount_value / 100);
            } else {
                // For fixed amount, apply full discount if there are eligible items
                // but prorate it if eligible subtotal is less than the discount
                $discount = $eligibleSubtotal > 0 ? min($this->discount_value, $eligibleSubtotal) : 0;
            }
        }

        // Apply maximum discount if set
        if ($this->maximum_discount_amount && $discount > $this->maximum_discount_amount) {
            $discount = $this->maximum_discount_amount;
        }

        // Discount cannot be more than the subtotal
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }

        return round($discount, 2);
    }
} 