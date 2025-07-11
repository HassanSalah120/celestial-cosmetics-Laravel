<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\SettingTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SettingTranslationController extends Controller
{
    /**
     * Display a listing of the settings with their translations.
     */
    public function index()
    {
        $settings = Setting::where('is_public', true)
            ->orderBy('group')
            ->orderBy('display_name')
            ->get();
            
        $availableLocales = Config::get('app.available_locales', ['en']);
        
        return view('admin.settings.translations.index', compact('settings', 'availableLocales'));
    }
    
    /**
     * Show the form for editing translations for a specific setting.
     */
    public function edit(Setting $setting)
    {
        $availableLocales = Config::get('app.available_locales', ['en']);
        
        // Get existing translations
        $translations = $setting->translations->keyBy('locale');
        
        return view('admin.settings.translations.edit', compact('setting', 'availableLocales', 'translations'));
    }
    
    /**
     * Update the translations for a specific setting.
     */
    public function update(Request $request, Setting $setting)
    {
        $validatedData = $request->validate([
            'translations' => 'required|array',
            'translations.*' => 'nullable|string'
        ]);
        
        foreach ($validatedData['translations'] as $locale => $value) {
            if (!empty($value)) {
                $setting->setTranslatedValue($locale, $value);
            }
        }
        
        return redirect()
            ->route('admin.settings.translations.edit', $setting)
            ->with('success', 'Setting translations updated successfully');
    }
    
    /**
     * Delete a specific translation.
     */
    public function destroy(Setting $setting, $locale)
    {
        SettingTranslation::where('setting_id', $setting->id)
            ->where('locale', $locale)
            ->delete();
            
        return redirect()
            ->route('admin.settings.translations.edit', $setting)
            ->with('success', 'Translation removed successfully');
    }
}
