<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * The settings service instance.
     *
     * @var \App\Services\SettingsService
     */
    protected $settingsService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\SettingsService $settingsService
     * @return void
     */
    public function __construct(SettingsService $settingsService)
    {
        $this->settingsService = $settingsService;
    }

    /**
     * Update the menu structure settings.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateMenuStructure(Request $request)
    {
        $this->settingsService->updateMenuStructure($request->header_menu_structure);
        
        return back()->with('toast', 'Header menu structure updated successfully.');
    }

    /**
     * Seed the database with initial settings.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function seed()
    {
        $result = $this->settingsService->seedInitialSettings();
        
        return back()->with('toast', $result['message']);
    }
} 