<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'team_members';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'name_ar',
        'position',
        'position_ar',
        'bio',
        'bio_ar',
        'image',
        'sort_order',
        'social_linkedin',
        'social_twitter',
        'social_instagram',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Get the about page this team member belongs to.
     */
    public function aboutPage()
    {
        return $this->belongsTo(AboutPage::class);
    }
} 