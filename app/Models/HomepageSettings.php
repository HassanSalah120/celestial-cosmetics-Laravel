<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSettings extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'homepage_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sections_order',
        'featured_products_count',
        'new_arrivals_count',
        'new_product_days',
        'featured_categories_count',
        'testimonials_count',
        'show_our_story',
        'show_testimonials',
        'animation_enabled',
        'featured_product_sort',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sections_order' => 'json',
        'show_our_story' => 'boolean',
        'show_testimonials' => 'boolean',
        'animation_enabled' => 'boolean',
    ];
} 