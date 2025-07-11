<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderNavigationItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'header_navigation_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'name_ar',
        'route',
        'url',
        'translation_key',
        'open_in_new_tab',
        'sort_order',
        'is_active',
        'has_dropdown',
        'show_in_header',
        'show_in_footer',
        'show_in_mobile',
        'icon',
        'badge_text',
        'badge_color',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'open_in_new_tab' => 'boolean',
        'is_active' => 'boolean',
        'has_dropdown' => 'boolean',
        'show_in_header' => 'boolean',
        'show_in_footer' => 'boolean',
        'show_in_mobile' => 'boolean',
    ];

    /**
     * Get the parent navigation item
     */
    public function parent()
    {
        return $this->belongsTo(HeaderNavigationItem::class, 'parent_id');
    }

    /**
     * Get the child navigation items
     */
    public function children()
    {
        return $this->hasMany(HeaderNavigationItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /**
     * Get only top-level navigation items
     */
    public static function getTopLevel()
    {
        return self::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the name in the current locale
     */
    public function getLocalizedNameAttribute()
    {
        $isArabic = app()->getLocale() === 'ar';
        
        if ($isArabic && !empty($this->name_ar)) {
            return $this->name_ar;
        }
        
        if (!empty($this->translation_key)) {
            return __($this->translation_key);
        }
        
        return $this->name;
    }
}
