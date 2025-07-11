<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegalPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'title_ar',
        'content',
        'content_ar',
        'last_updated',
        'is_active'
    ];

    protected $casts = [
        'last_updated' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the localized title based on the current app direction.
     *
     * @return string
     */
    public function getTitle(): string
    {
        $isRtl = is_rtl();
        
        if ($isRtl && !empty($this->title_ar)) {
            return $this->title_ar;
        }
        
        return $this->title ?? '';
    }

    /**
     * Get the localized content based on the current app direction.
     *
     * @return string
     */
    public function getContent(): string
    {
        $isRtl = is_rtl();
        
        if ($isRtl && !empty($this->content_ar)) {
            return $this->content_ar;
        }
        
        return $this->content ?? '';
    }
} 