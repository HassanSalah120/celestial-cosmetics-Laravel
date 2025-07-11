<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingConfig extends Model
{
    use HasFactory;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'shipping_config';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'enable_shipping',
        'free_shipping_min',
        'shipping_flat_rate',
        'enable_local_pickup',
        'local_pickup_cost',
        'pickup_address',
        'pickup_instructions',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enable_shipping' => 'boolean',
        'free_shipping_min' => 'float',
        'shipping_flat_rate' => 'float',
        'enable_local_pickup' => 'boolean',
        'local_pickup_cost' => 'float',
    ];
    
    /**
     * Get shipping methods relation
     */
    public function shippingMethods()
    {
        return $this->hasMany(ShippingMethod::class);
    }
} 