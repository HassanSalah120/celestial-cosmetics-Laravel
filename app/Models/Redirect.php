<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'source_url',
        'target_url',
        'type',
        'is_active',
        'notes'
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
     * Scope a query to only include active redirects.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Format the source URL to ensure it's properly formatted
     *
     * @param  string  $value
     * @return void
     */
    public function setSourceUrlAttribute($value)
    {
        // Remove leading slash if present
        $this->attributes['source_url'] = ltrim($value, '/');
    }

    /**
     * Format the target URL to ensure it's properly formatted
     *
     * @param  string  $value
     * @return void
     */
    public function setTargetUrlAttribute($value)
    {
        // Keep the leading slash if it's a relative URL
        if (!preg_match('~^(?:f|ht)tps?://~i', $value)) {
            $value = '/' . ltrim($value, '/');
        }
        
        $this->attributes['target_url'] = $value;
    }
} 