<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GeneralSetting;
use App\Models\SeoDefaults;
use App\Models\HomepageHero;
use App\Models\HomepageSection;
use App\Models\FooterSetting;
use App\Models\NavigationItem;
use App\Models\TeamMember;
use App\Models\CorporateValue;
use App\Models\ShippingMethod;
use App\Models\ProductCardSetting;

class NormalizedSettingsController extends Controller
{
    /**
     * Show an example of working with normalized settings
     */
    public function index()
    {
        // Get general settings - first() is safe here because we always have one record
        $generalSettings = GeneralSetting::first();
        
        // Get SEO defaults
        $seoDefaults = SeoDefaults::first();
        
        // Get homepage hero content
        $hero = HomepageHero::first();
        
        // Get homepage sections in order
        $sections = HomepageSection::all();
        
        // Get footer settings
        $footer = FooterSetting::first();
        
        // Get navigation items (top level)
        $navItems = NavigationItem::whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
            
        // For each navigation item, get its children
        foreach ($navItems as $item) {
            $item->children = NavigationItem::where('parent_id', $item->id)
                ->orderBy('sort_order')
                ->get();
        }
        
        // Get team members
        $teamMembers = TeamMember::orderBy('sort_order')->get();
        
        // Get corporate values
        $values = CorporateValue::orderBy('sort_order')->get();
        
        // Get active shipping methods
        $shippingMethods = ShippingMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
            
        // Get product card styling
        $productCardStyle = ProductCardSetting::first();
        
        // Example of how we can use these models directly
        return view('example.normalized-settings', compact(
            'generalSettings',
            'seoDefaults',
            'hero',
            'sections',
            'footer',
            'navItems',
            'teamMembers',
            'values',
            'shippingMethods',
            'productCardStyle'
        ));
    }
    
    /**
     * Show example of updating a normalized setting
     */
    public function update(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'enable_language_switcher' => 'boolean',
            'default_language' => 'required|string|max:10',
        ]);
        
        // Update general settings
        $generalSettings = GeneralSetting::first();
        $generalSettings->site_name = $validated['site_name'];
        $generalSettings->enable_language_switcher = $validated['enable_language_switcher'] ?? false;
        $generalSettings->default_language = $validated['default_language'];
        $generalSettings->save();
        
        return redirect()->route('example.normalized-settings.index')
            ->with('success', 'Settings updated successfully!');
    }
    
    /**
     * Add a new team member example
     */
    public function addTeamMember(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        // Handle image upload if provided
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('team', 'public');
        }
        
        // Create new team member
        TeamMember::create([
            'name' => $validated['name'],
            'title' => $validated['title'],
            'bio' => $validated['bio'] ?? null,
            'image' => $imagePath,
            'sort_order' => TeamMember::count(), // Add to the end
        ]);
        
        return redirect()->route('example.normalized-settings.index')
            ->with('success', 'Team member added successfully!');
    }
    
    /**
     * Update page SEO example
     */
    public function updatePageSeo(Request $request, $pageKey)
    {
        // Validate the request
        $validated = $request->validate([
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string',
            'meta_keywords' => 'nullable|string',
            'og_image' => 'nullable|image|max:2048',
        ]);
        
        // Find the page SEO record
        $pageSeo = \App\Models\PageSeo::where('page_key', $pageKey)->first();
        
        if (!$pageSeo) {
            abort(404, 'Page SEO not found');
        }
        
        // Handle OG image upload if provided
        if ($request->hasFile('og_image')) {
            // Delete old image if exists
            if ($pageSeo->og_image && \Storage::disk('public')->exists($pageSeo->og_image)) {
                \Storage::disk('public')->delete($pageSeo->og_image);
            }
            
            $pageSeo->og_image = $request->file('og_image')->store('seo', 'public');
        }
        
        // Update page SEO
        $pageSeo->meta_title = $validated['meta_title'];
        $pageSeo->meta_description = $validated['meta_description'];
        $pageSeo->meta_keywords = $validated['meta_keywords'] ?? null;
        $pageSeo->save();
        
        return redirect()->route('example.normalized-settings.index')
            ->with('success', 'Page SEO updated successfully!');
    }
} 