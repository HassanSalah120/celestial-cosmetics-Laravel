<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Facades\Settings;

class Offer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'title_ar',
        'subtitle',
        'subtitle_ar',
        'description',
        'description_ar',
        'image',
        'tag',
        'tag_ar',
        'original_price',
        'discounted_price',
        'discount_text',
        'discount_text_ar',
        'stock',
        'low_stock_threshold',
        'button_text',
        'button_text_ar',
        'button_url',
        'is_active',
        'starts_at',
        'expires_at',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'original_price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'sort_order' => 'integer',
        'stock' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Get the products included in this offer/bundle
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('quantity', 'discount_percentage', 'fixed_price')
            ->withTimestamps();
    }

    /**
     * Get the order items for this offer
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to get only active offers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')
                  ->orWhere('starts_at', '<=', Carbon::now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>=', Carbon::now());
            });
    }

    /**
     * Check if the offer is active
     */
    public function isActive()
    {
        if (!$this->is_active) {
            return false;
        }
        
        $now = Carbon::now();
        
        if ($this->starts_at && $this->starts_at > $now) {
            return false;
        }
        
        if ($this->expires_at && $this->expires_at < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->original_price || !$this->discounted_price || $this->original_price <= 0) {
            return 0;
        }
        
        return round(($this->original_price - $this->discounted_price) / $this->original_price * 100);
    }

    /**
     * Calculate the total regular price of all products in the bundle
     */
    public function getTotalRegularPriceAttribute()
    {
        $total = 0;
        
        foreach ($this->products as $product) {
            $total += $product->price * $product->pivot->quantity;
        }
        
        return $total;
    }

    /**
     * Calculate the savings amount compared to buying products individually
     */
    public function getSavingsAmountAttribute()
    {
        return $this->total_regular_price - $this->discounted_price;
    }

    /**
     * Calculate the savings percentage compared to buying products individually
     */
    public function getSavingsPercentageAttribute()
    {
        if ($this->total_regular_price <= 0) {
            return 0;
        }
        
        return round(($this->savings_amount / $this->total_regular_price) * 100);
    }

    /**
     * Check if offer is in stock
     * 
     * @return bool
     */
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    /**
     * Get offer's stock status
     * 
     * @return string
     */
    public function getStockStatusAttribute()
    {
        return $this->in_stock ? 'In Stock' : 'Out of Stock';
    }

    /**
     * Check if offer is low on stock
     * 
     * @return bool
     */
    public function getIsLowStockAttribute()
    {
        $threshold = $this->low_stock_threshold ?? Settings::get('default_low_stock_threshold', 5);
        return $this->stock > 0 && $this->stock <= $threshold;
    }

    /**
     * Get detailed stock status with color coding
     * 
     * @return array
     */
    public function getDetailedStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return [
                'status' => 'Out of Stock',
                'color' => 'danger',
                'badge' => 'badge-danger',
                'icon' => 'x-circle'
            ];
        }
        
        if ($this->is_low_stock) {
            return [
                'status' => 'Low Stock',
                'color' => 'warning',
                'badge' => 'badge-warning',
                'icon' => 'exclamation-triangle'
            ];
        }
        
        return [
            'status' => 'In Stock',
            'color' => 'success',
            'badge' => 'badge-success',
            'icon' => 'check-circle'
        ];
    }

    /**
     * Get formatted price with currency symbol
     * 
     * @return string
     */
    public function getPriceFormattedAttribute()
    {
        return Settings::formatPrice($this->discounted_price ?? $this->original_price);
    }

    /**
     * Get the final price
     * 
     * @return float
     */
    public function getFinalPriceAttribute()
    {
        return $this->discounted_price ?? $this->original_price;
    }
}
