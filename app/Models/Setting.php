<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'description',
        'options',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    /**
     * Get all translations for this setting.
     */
    public function translations(): HasMany
    {
        return $this->hasMany(SettingTranslation::class);
    }

    /**
     * Get the translated value for a specific locale.
     *
     * @param string|null $locale
     * @return mixed
     */
    public function getTranslatedValue($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        // Find translation for the given locale
        $translation = $this->translations()->where('locale', $locale)->first();
        
        // Return translated value if exists, otherwise use the default value
        return $translation ? $translation->value : $this->value;
    }

    /**
     * Set a translated value for a specific locale.
     *
     * @param string $locale
     * @param mixed $value
     * @return void
     */
    public function setTranslatedValue($locale, $value)
    {
        $translation = $this->translations()->where('locale', $locale)->first();
        
        if ($translation) {
            $translation->update(['value' => $value]);
        } else {
            $this->translations()->create([
                'locale' => $locale,
                'value' => $value
            ]);
        }
        
        // Clear any cached version of this setting
        Cache::forget('setting_' . $this->key . '_' . $locale);
    }

    /**
     * Get a setting by key with optional default value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        // Check if settings table exists
        if (!Schema::hasTable('settings')) {
            return $default;
        }
        
        return Cache::remember('setting_'.$key, 600, function() use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            // Process the value based on type
            return self::processSettingValue($setting);
        });
    }

    /**
     * Set a setting value
     * 
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    public static function set($key, $value, $group = 'general')
    {
        // Check if settings table exists
        if (!Schema::hasTable('settings')) {
            return false;
        }
        
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            // Create new setting if it doesn't exist
            static::create([
                'key' => $key,
                'value' => $value,
                'group' => $group,
                'type' => 'text', // Default type
            ]);
        } else {
            // Update existing setting
            $setting->value = $value;
            $setting->save();
        }
        
        // Clear the cache for this setting
        Cache::forget('setting_' . $key);
        
        return true;
    }

    /**
     * Get all settings in a specific group.
     *
     * @param  string  $group
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getGroup($group)
    {
        return static::where('group', $group)->get();
    }

    /**
     * Get all settings as a key => value array
     * 
     * @return array
     */
    public static function getAll()
    {
        // Check if settings table exists
        if (!Schema::hasTable('settings')) {
            return [];
        }
        
        $settings = self::all();
        $result = [];
        
        foreach ($settings as $setting) {
            $result[$setting->key] = self::processSettingValue($setting);
        }
        
        return $result;
    }

    /**
     * Handle file uploads in the value.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setValueAttribute($value)
    {
        // For file inputs with actual file upload
        if (isset($this->attributes['type']) && $this->attributes['type'] === 'file' && request()->hasFile($this->key)) {
            // Delete old file if it exists
            if (!empty($this->attributes['value']) && Storage::disk('public')->exists($this->attributes['value'])) {
                Storage::disk('public')->delete($this->attributes['value']);
            }
            
            // Upload new file
            $file = request()->file($this->key);
            $path = $file->store('settings', 'public');
            $this->attributes['value'] = $path;
        } 
        // Handle edit modal file uploads which use "value" as the field name
        else if (isset($this->attributes['type']) && $this->attributes['type'] === 'file' && request()->hasFile('value') && request()->route()->getName() === 'admin.settings.update') {
            // Delete old file if it exists
            if (!empty($this->attributes['value']) && Storage::disk('public')->exists($this->attributes['value'])) {
                Storage::disk('public')->delete($this->attributes['value']);
            }
            
            // Upload new file
            $file = request()->file('value');
            $path = $file->store('settings', 'public');
            $this->attributes['value'] = $path;
        }
        // For other types
        else {
            $this->attributes['value'] = $value;
            
            // Auto-detect type if type is text but value suggests otherwise
            if (isset($this->attributes['type']) && $this->attributes['type'] === 'text') {
                $detectedType = $this->detectTypeFromValue($value);
                if ($detectedType) {
                    $this->attributes['type'] = $detectedType;
                }
            }
        }
    }

    /**
     * Format the value based on type.
     *
     * @return mixed
     */
    public function getFormattedValueAttribute()
    {
        switch ($this->type) {
            case 'boolean':
                return (bool) $this->value;
            case 'number':
                return (float) $this->value;
            case 'array':
            case 'json':
                return json_decode($this->value, true);
            default:
                return $this->value;
        }
    }
    
    /**
     * Detect the type of a setting based on its value
     *
     * @param mixed $value
     * @return string|null
     */
    private function detectTypeFromValue($value)
    {
        // Skip if empty value
        if (empty($value)) {
            return null;
        }
        
        // Explicit boolean values (0/1)
        if ($value === '0' || $value === '1') {
            return 'boolean';
        }
        
        // Check for file paths
        $fileExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'pdf', 'doc', 'docx', 'ico'];
        $valueExtension = pathinfo($value, PATHINFO_EXTENSION);
        $isLikelyFile = in_array(strtolower($valueExtension), $fileExtensions) && 
                         (strpos($value, '/') !== false || strpos($value, '\\') !== false);
        if ($isLikelyFile) {
            return 'file';
        }
        
        // Check for valid JSON 
        if ($this->isValidJson($value)) {
            return 'json';
        }
        
        // Multi-line content suggests textarea
        if (strpos($value, "\n") !== false || strlen($value) > 255) {
            return 'textarea';
        }
        
        // Check for color values (#hex or rgb/rgba)
        if (preg_match('/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i', $value) || 
            preg_match('/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*[\d\.]+)?\)$/i', $value)) {
            return 'color';
        }
        
        // No specific type detected
        return null;
    }
    
    /**
     * Check if a string is valid JSON
     *
     * @param string $string
     * @return bool
     */
    private function isValidJson($string) {
        if (!is_string($string)) {
            return false;
        }
        
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Process the value based on type
     *
     * @param Setting $setting
     * @return mixed
     */
    private static function processSettingValue($setting)
    {
        // Process the value based on type
        switch ($setting->type) {
            case 'boolean':
                return (bool) $setting->value;
            case 'number':
                return (float) $setting->value;
            case 'array':
            case 'json':
                return json_decode($setting->value, true);
            default:
                return $setting->value;
        }
    }
}
