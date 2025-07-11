<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'causer_type',
        'causer_id',
        'subject_type',
        'subject_id',
        'status',
        'properties'
    ];

    protected $casts = [
        'properties' => 'array'
    ];

    public function causer()
    {
        return $this->morphTo();
    }

    public function subject()
    {
        return $this->morphTo();
    }
} 