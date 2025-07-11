<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'footer_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'title_ar',
        'type',
        'sort_order',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the links for this section
     */
    public function links()
    {
        return $this->hasMany(FooterLink::class, 'column_id')
            ->orderBy('sort_order');
    }

    /**
     * Get only active footer sections
     */
    public static function getActive()
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->with('links')
            ->get();
    }

    /**
     * Get the title in the current locale
     */
    public function getLocalizedTitleAttribute()
    {
        $isArabic = is_rtl();
        
        if ($isArabic && !empty($this->title_ar)) {
            return $this->title_ar;
        }
        
        return $this->title;
    }
}
