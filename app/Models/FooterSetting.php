<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterSetting extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'footer_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'display_name',
        'type',
        'description',
    ];

    /**
     * Get a setting by key with optional default value
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        
        if (!$setting) {
            return $default;
        }
        
        // Process the value based on type
        return self::processSettingValue($setting);
    }

    /**
     * Process setting value based on its type
     * 
     * @param FooterSetting $setting
     * @return mixed
     */
    protected static function processSettingValue($setting)
    {
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

    /**
     * Set a setting value
     * 
     * @param string $key
     * @param mixed $value
     * @return bool
     */
    public static function set($key, $value)
    {
        $setting = static::where('key', $key)->first();
        
        if (!$setting) {
            // Create new setting if it doesn't exist
            static::create([
                'key' => $key,
                'value' => $value,
                'type' => 'text',
            ]);
        } else {
            // Update existing setting
            $setting->value = $value;
            $setting->save();
        }
        
        return true;
    }
}
