<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Helpers\SettingsHelper;
use App\Models\StructuredData;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'category_id',
        'is_featured',
        'stock',
        'image',
        'ingredients',
        'how_to_use',
        'discount_percent',
        'is_visible',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_card_type',
        'canonical_url',
        'noindex',
        'nofollow',
        'low_stock_threshold'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_visible' => 'boolean',
        'discount_percent' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    /**
     * Get the product's featured image.
     * 
     * @return string|null
     */
    public function getFeaturedImageAttribute()
    {
        return $this->image;
    }

    /**
     * Get formatted price with currency symbol.
     * 
     * @return string
     */
    public function getPriceFormattedAttribute()
    {
        return SettingsHelper::formatPrice($this->price);
    }

    /**
     * Get the final price (after discount) formatted with currency.
     * 
     * @return string
     */
    public function getFinalPriceFormattedAttribute()
    {
        return SettingsHelper::formatPrice($this->final_price);
    }

    /**
     * Default active state to true if not explicitly set.
     * 
     * @return bool
     */
    public function getIsActiveAttribute()
    {
        return true; // Default to active unless explicitly set to false
    }

    /**
     * Check if product is new (created within the last configured days).
     * 
     * @return bool
     */
    public function getIsNewAttribute()
    {
        // Get new product days from homepage_settings table
        $homepageSettings = DB::table('homepage_settings')->first();
        $newProductDays = $homepageSettings ? $homepageSettings->new_product_days : 30;
        
        // Consider a product new if it was created within the configured number of days
        return $this->created_at->gt(Carbon::now()->subDays($newProductDays));
    }

    /**
     * Get the final price after discount.
     * 
     * @return float
     */
    public function getFinalPriceAttribute()
    {
        if ($this->discount_percent > 0) {
            return round($this->price * (1 - $this->discount_percent / 100), 2);
        }
        
        return $this->price;
    }

    /**
     * Get the SEO meta title, use product name if not set.
     * 
     * @return string
     */
    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?? $this->name . ' | ' . SettingsHelper::get('site_name', 'Celestial Cosmetics');
    }

    /**
     * Get the SEO meta description, use product description (truncated) if not set.
     * 
     * @return string
     */
    public function getSeoDescriptionAttribute()
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }
        
        // Truncate and clean the description for meta description use
        $description = strip_tags($this->description);
        return strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;
    }

    /**
     * Get the SEO image for Open Graph/Twitter, use product image if not set.
     * 
     * @return string
     */
    public function getSeoImageAttribute()
    {
        return $this->og_image ?? $this->image;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the order items for the product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price');
    }

    /**
     * Get the inventory transactions for this product.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    // Get product's stock status
    public function getStockStatusAttribute()
    {
        return $this->in_stock ? 'In Stock' : 'Out of Stock';
    }

    /**
     * Check if product is in stock.
     * 
     * @return bool
     */
    public function getInStockAttribute()
    {
        return $this->stock > 0;
    }

    /**
     * Check if product is low on stock.
     * 
     * @return bool
     */
    public function getIsLowStockAttribute()
    {
        $threshold = $this->low_stock_threshold ?? SettingsHelper::get('default_low_stock_threshold', 5);
        return $this->stock > 0 && $this->stock <= $threshold;
    }

    /**
     * Get detailed stock status with color coding.
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
     * Get canonical URL for the product
     *
     * @return string
     */
    public function getCanonicalUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        
        return route('products.show', $this->slug);
    }

    /**
     * Get the JSON-LD structured data for this product
     *
     * @return array
     */
    public function getStructuredDataAttribute()
    {
        // Get the default schema template
        $schemaTemplate = json_decode(SettingsHelper::get('default_schema_product', '{}'), true);
        
        // Replace placeholders with actual values
        $schema = $this->replaceSchemaPlaceholders($schemaTemplate);
        
        // Check if there's a custom schema for this product
        $customSchema = StructuredData::where('entity_type', 'product')
            ->where('entity_id', $this->id)
            ->where('is_active', true)
            ->first();
        
        if ($customSchema) {
            $schema = array_merge($schema, json_decode($customSchema->schema_data, true));
        }
        
        return $schema;
    }

    /**
     * Replace placeholders in schema template with real values
     *
     * @param array $schema
     * @return array
     */
    protected function replaceSchemaPlaceholders($schema)
    {
        $replacements = [
            '{{name}}' => $this->name,
            '{{description}}' => strip_tags($this->description),
            '{{image}}' => $this->image ? url('storage/' . $this->image) : null,
            '{{price}}' => $this->final_price,
            '{{availability}}' => $this->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock'
        ];
        
        $schemaJson = json_encode($schema);
        
        foreach ($replacements as $placeholder => $value) {
            $schemaJson = str_replace($placeholder, $value, $schemaJson);
        }
        
        return json_decode($schemaJson, true);
    }

    /**
     * Get the robot meta tags for this product
     *
     * @return string
     */
    public function getRobotMetaTagsAttribute()
    {
        $tags = [];
        
        if ($this->noindex) {
            $tags[] = 'noindex';
        } else {
            $tags[] = 'index';
        }
        
        if ($this->nofollow) {
            $tags[] = 'nofollow';
        } else {
            $tags[] = 'follow';
        }
        
        return implode(', ', $tags);
    }

    /**
     * Get the offers/bundles that include this product
     */
    public function offers()
    {
        return $this->belongsToMany(Offer::class)
            ->withPivot('quantity', 'discount_percentage', 'fixed_price')
            ->withTimestamps();
    }

    /**
     * Get the wishlist items for this product.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get users who have this product in their wishlist.
     */
    public function wishlistUsers()
    {
        return $this->belongsToMany(User::class, 'wishlists')->withTimestamps();
    }
}
