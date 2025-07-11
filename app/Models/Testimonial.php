<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_name_ar',
        'email',
        'avatar',
        'customer_role',
        'customer_role_ar',
        'title',
        'title_ar',
        'message',
        'message_ar',
        'rating',
        'is_approved',
        'is_featured',
        'user_id'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * Scope a query to only include approved testimonials.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Scope a query to only include featured testimonials.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Get the user that wrote the testimonial.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the customer role based on the current locale.
     * 
     * @return string
     */
    public function getCustomerRoleAttribute($value)
    {
        if (is_rtl() && $this->customer_role_ar) {
            return $this->customer_role_ar;
        }
        
        return $value ?: 'Customer';
    }
}
