<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CountryShippingFee extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_code',
        'country_name',
        'fee',
        'shipping_method_id',
        'is_available',
        'delivery_time',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'fee' => 'float',
        'is_available' => 'boolean',
    ];
    
    /**
     * Get shipping method relation
     */
    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }
} 