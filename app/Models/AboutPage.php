<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutPage extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'about_page';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'title_ar',
        'subtitle',
        'subtitle_ar',
        'content',
        'content_ar',
        'image',
        'seo_title',
        'seo_title_ar',
        'seo_description',
        'seo_description_ar',
        'seo_keywords',
        'seo_keywords_ar',
    ];

    /**
     * Get corporate values associated with the about page.
     */
    public function corporateValues()
    {
        return $this->hasMany(CorporateValue::class);
    }

    /**
     * Get team members associated with the about page.
     */
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }
} 