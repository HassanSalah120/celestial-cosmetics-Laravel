<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OurStoryContent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'our_story_content';

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
        'description',
        'description_ar',
        'image',
        'button_text',
        'button_text_ar',
        'button_url',
        'feature1_icon',
        'feature1_title',
        'feature1_title_ar',
        'feature1_text',
        'feature1_text_ar',
        'feature2_icon',
        'feature2_title',
        'feature2_title_ar',
        'feature2_text',
        'feature2_text_ar',
        'secondary_button_text',
        'secondary_button_text_ar',
        'secondary_button_url',
        'year_founded',
    ];
} 