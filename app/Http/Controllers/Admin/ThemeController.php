<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Theme;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThemeController extends Controller
{
    public function index()
    {
        // Get all themes and group them
        $themes = Theme::all();
        $groupedThemes = $themes->groupBy('group');
        $activeTheme = Theme::where('is_active', true)->first();
        return view('admin.theme.index', compact('themes', 'groupedThemes', 'activeTheme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'theme_id' => 'required|exists:themes,id',
        ]);

        DB::transaction(function () use ($request) {
            Theme::where('is_active', true)->update(['is_active' => false]);
            Theme::find($request->theme_id)->update(['is_active' => true]);
        });
        
        // Clear the theme cache
        cache()->forget('active_theme_colors');
        
        // Manually clear other caches that might be affecting theme
        cache()->flush();
        
        // Force reload of theme data
        $theme = Theme::find($request->theme_id);
        
        // If this is a direct apply request, redirect to homepage
        if ($request->has('direct_apply')) {
            return redirect('/');
        }
        
        // Check if we should redirect back to showcase
        $redirectRoute = $request->has('from_showcase') ? 'admin.theme.showcase' : 'admin.theme.index';
        
        // Add a timestamp to force a refresh
        return redirect()->route($redirectRoute, ['refresh' => time()])
            ->with('success', "Theme '{$theme->name}' applied successfully. Please refresh the page to see all changes.");
    }
    
    public function create()
    {
        return view('admin.theme.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:themes,name',
            'group' => 'nullable|string|max:255',
            'primary' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'primary-light' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'primary-dark' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary-light' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary-dark' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent-light' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent-dark' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        ]);
        
        $colors = [
            'primary' => $request->input('primary'),
            'primary-light' => $request->input('primary-light'),
            'primary-dark' => $request->input('primary-dark'),
            'secondary' => $request->input('secondary'),
            'secondary-light' => $request->input('secondary-light'),
            'secondary-dark' => $request->input('secondary-dark'),
            'accent' => $request->input('accent'),
            'accent-light' => $request->input('accent-light'),
            'accent-dark' => $request->input('accent-dark'),
        ];
        
        Theme::create([
            'name' => $request->input('name'),
            'group' => $request->input('group'),
            'colors' => $colors,
            'is_active' => false,
        ]);
        
        return redirect()->route('admin.theme.index')->with('success', 'Theme created successfully.');
    }
    
    public function destroy(Theme $theme)
    {
        // Don't allow deleting the active theme
        if ($theme->is_active) {
            return redirect()->route('admin.theme.index')->with('error', 'Cannot delete the active theme.');
        }
        
        // Don't allow deleting if it's the only theme
        if (Theme::count() <= 1) {
            return redirect()->route('admin.theme.index')->with('error', 'Cannot delete the only theme.');
        }
        
        $theme->delete();
        
        return redirect()->route('admin.theme.index')->with('success', 'Theme deleted successfully.');
    }
    
    public function duplicate(Theme $theme)
    {
        $newTheme = $theme->replicate();
        $newTheme->name = $theme->name . ' (Copy)';
        $newTheme->group = $theme->group;
        $newTheme->is_active = false;
        $newTheme->save();
        
        return redirect()->route('admin.theme.index')->with('success', 'Theme duplicated successfully.');
    }
    
    public function edit(Theme $theme)
    {
        return view('admin.theme.edit', compact('theme'));
    }
    
    public function updateTheme(Request $request, Theme $theme)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:themes,name,' . $theme->id,
            'group' => 'nullable|string|max:255',
            'primary' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'primary-light' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'primary-dark' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary-light' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'secondary-dark' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent-light' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
            'accent-dark' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        ]);
        
        $colors = [
            'primary' => $request->input('primary'),
            'primary-light' => $request->input('primary-light'),
            'primary-dark' => $request->input('primary-dark'),
            'secondary' => $request->input('secondary'),
            'secondary-light' => $request->input('secondary-light'),
            'secondary-dark' => $request->input('secondary-dark'),
            'accent' => $request->input('accent'),
            'accent-light' => $request->input('accent-light'),
            'accent-dark' => $request->input('accent-dark'),
        ];
        
        $theme->update([
            'name' => $request->input('name'),
            'group' => $request->input('group'),
            'colors' => $colors,
        ]);
        
        // Clear the theme cache if this is the active theme
        if ($theme->is_active) {
            cache()->forget('active_theme_colors');
            cache()->flush();
        }
        
        return redirect()->route('admin.theme.index')->with('success', 'Theme updated successfully.');
    }
    
    public function showcase()
    {
        $themes = Theme::all();
        $groupedThemes = $themes->groupBy('group');
        $activeTheme = Theme::where('is_active', true)->first();
        return view('admin.theme.showcase', compact('themes', 'groupedThemes', 'activeTheme'));
    }
    
    public function directApply($id)
    {
        // Validate the theme ID
        $theme = Theme::findOrFail($id);
        
        // Update the active theme
        DB::transaction(function () use ($theme) {
            Theme::where('is_active', true)->update(['is_active' => false]);
            $theme->update(['is_active' => true]);
        });
        
        // Clear all caches
        cache()->flush();
        
        // Redirect to the homepage to see the theme in action
        return redirect('/');
    }
    
    public function createStarlightTheme()
    {
        // Check if the theme already exists
        if (Theme::where('name', 'Starlight Galaxy')->exists()) {
            return redirect()->route('admin.theme.index')->with('error', 'Starlight Galaxy theme already exists.');
        }
        
        // Create the new theme
        Theme::create([
            'name' => 'Starlight Galaxy',
            'colors' => [
                'primary' => '#0b0b1a',
                'primary-light' => '#191932',
                'primary-dark' => '#050510',
                'secondary' => '#e83e8c',
                'secondary-light' => '#f16ba6',
                'secondary-dark' => '#c21f6b',
                'accent' => '#7df9ff',
                'accent-light' => '#a5fbff',
                'accent-dark' => '#00e5f7',
            ],
            'is_active' => false,
        ]);
        
        return redirect()->route('admin.theme.index')->with('success', 'Starlight Galaxy theme created successfully.');
    }
}
