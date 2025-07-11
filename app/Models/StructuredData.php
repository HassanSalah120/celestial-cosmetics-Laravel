<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StructuredData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'schema_type',
        'schema_data',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'schema_data' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope a query to only include active structured data.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the related entity (polymorphic).
     *
     * @return mixed
     */
    public function entity()
    {
        if ($this->entity_type === 'product') {
            return $this->belongsTo(Product::class, 'entity_id');
        } elseif ($this->entity_type === 'category') {
            return $this->belongsTo(Category::class, 'entity_id');
        }
        
        return null;
    }

    /**
     * Get the structured data as a JSON string.
     *
     * @return string
     */
    public function getJsonAttribute()
    {
        return json_encode($this->schema_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
} 