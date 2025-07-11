<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingTranslation extends Model
{
    protected $fillable = [
        'setting_id',
        'locale',
        'value'
    ];

    /**
     * Get the setting that owns this translation.
     */
    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
