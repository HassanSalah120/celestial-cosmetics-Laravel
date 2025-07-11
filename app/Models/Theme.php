<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;

    protected $casts = [
        'colors' => 'array',
    ];

    protected $fillable = [
        'name',
        'group',
        'colors',
        'is_active',
    ];
}
