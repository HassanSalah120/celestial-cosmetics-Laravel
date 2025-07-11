<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'general_settings';
    
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
        'site_name',
        'site_name_arabic',
        'site_logo',
        'site_favicon',
        'enable_language_switcher',
        'available_languages',
        'default_language',
        'enable_social_login',
        'enable_registration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enable_language_switcher' => 'boolean',
        'available_languages' => 'array',
        'enable_social_login' => 'boolean',
        'enable_registration' => 'boolean',
    ];
} 