<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoDefaults extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seo_defaults';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'default_meta_title',
        'default_meta_description',
        'default_meta_keywords',
        'og_default_image',
        'og_site_name',
        'twitter_site',
        'twitter_creator',
        'default_robots_content',
        'enable_structured_data',
        'enable_robots_txt',
        'enable_sitemap',
        'sitemap_change_frequency',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enable_structured_data' => 'boolean',
        'enable_robots_txt' => 'boolean',
        'enable_sitemap' => 'boolean',
    ];
}
