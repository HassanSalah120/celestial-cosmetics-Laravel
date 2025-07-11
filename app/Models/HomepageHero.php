<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageHero extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'homepage_hero';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'button_text',
        'button_text_ar',
        'button_url',
        'secondary_button_text',
        'secondary_button_text_ar',
        'secondary_button_url',
        'image',
        'scroll_indicator_text',
        'scroll_indicator_text_ar',
    ];
} 