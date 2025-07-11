<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HomepageSection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'homepage_sections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'section_key',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'button_text',
        'button_text_ar',
        'button_url',
        'tag',
        'tag_ar',
        'image',
    ];
} 