<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'fee',
        'is_active',
        'sort_order',
        'shipping_config_id',
        'estimated_days',
        'image',
        'code',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fee' => 'float',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    /**
     * For compatibility with both 'price' and 'fee' naming conventions
     */
    public function getPriceAttribute()
    {
        return $this->fee;
    }
    
    /**
     * For compatibility with both 'delivery_time' and 'estimated_days' naming conventions
     */
    public function getDeliveryTimeAttribute()
    {
        return $this->estimated_days;
    }
    
    /**
     * Get shipping config relation
     */
    public function shippingConfig()
    {
        return $this->belongsTo(ShippingConfig::class);
    }
    
    /**
     * Get country fees relation
     */
    public function countryFees()
    {
        return $this->hasMany(CountryShippingFee::class);
    }
} 