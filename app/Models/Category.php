<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\SettingsHelper;
use App\Models\StructuredData;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'twitter_card_type',
        'canonical_url',
        'noindex',
        'nofollow'
    ];

    /**
     * Get the SEO meta title, use category name if not set.
     * 
     * @return string
     */
    public function getSeoTitleAttribute()
    {
        return $this->meta_title ?? $this->name . ' | ' . SettingsHelper::get('site_name', 'Celestial Cosmetics');
    }

    /**
     * Get the SEO meta description, use category description (truncated) if not set.
     * 
     * @return string
     */
    public function getSeoDescriptionAttribute()
    {
        if ($this->meta_description) {
            return $this->meta_description;
        }
        
        // If description exists, use it for meta description (truncated)
        if ($this->description) {
            $description = strip_tags($this->description);
            return strlen($description) > 160 ? substr($description, 0, 157) . '...' : $description;
        }
        
        // Otherwise use site default
        return SettingsHelper::get('site_meta_description');
    }

    /**
     * Get the SEO image for Open Graph/Twitter, use category image if not set.
     * 
     * @return string
     */
    public function getSeoImageAttribute()
    {
        return $this->og_image ?? $this->image ?? SettingsHelper::get('og_default_image');
    }

    /**
     * Get the URL for the category
     *
     * @return string
     */
    public function getUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        
        return route('products.category', $this->slug);
    }

    /**
     * Get canonical URL for the category
     *
     * @return string
     */
    public function getCanonicalUrlAttribute($value)
    {
        if (!empty($value)) {
            return $value;
        }
        
        return route('products.category', $this->slug);
    }

    /**
     * Get the JSON-LD structured data for this category
     *
     * @return array
     */
    public function getStructuredDataAttribute()
    {
        // Base schema for category
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'CollectionPage',
            'name' => $this->name,
            'description' => strip_tags($this->description ?? ''),
            'url' => route('products.category', $this->slug)
        ];
        
        if ($this->image) {
            $schema['image'] = url('storage/' . $this->image);
        }
        
        // Check if there's a custom schema for this category
        $customSchema = StructuredData::where('entity_type', 'category')
            ->where('entity_id', $this->id)
            ->where('is_active', true)
            ->first();
        
        if ($customSchema) {
            $schema = array_merge($schema, json_decode($customSchema->schema_data, true));
        }
        
        return $schema;
    }

    /**
     * Get the robot meta tags for this category
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
