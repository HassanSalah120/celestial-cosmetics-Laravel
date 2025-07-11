<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterSection;
use App\Models\FooterLink;
use App\Models\FooterSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FooterController extends Controller
{
    /**
     * Display the footer management page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $sections = FooterSection::with('links')->orderBy('sort_order')->get();
        $settings = FooterSetting::all()->keyBy('key');
        
        return view('admin.footer.index', compact('sections', 'settings'));
    }
    
    /**
     * Store a new footer section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeSection(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'type' => 'required|string|in:links,newsletter,contact,social',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        FooterSection::create($validated);
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer section created successfully.');
    }
    
    /**
     * Update a footer section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FooterSection  $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSection(Request $request, FooterSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'type' => 'required|string|in:links,newsletter,contact,social',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);
        
        $validated['is_active'] = $request->has('is_active');
        
        $section->update($validated);
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer section updated successfully.');
    }
    
    /**
     * Delete a footer section.
     *
     * @param  \App\Models\FooterSection  $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroySection(FooterSection $section)
    {
        // This will also delete related links due to cascade delete
        $section->delete();
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer section deleted successfully.');
    }
    
    /**
     * Store a new footer link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeLink(Request $request)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:footer_sections,id',
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        
        FooterLink::create($validated);
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer link created successfully.');
    }
    
    /**
     * Update a footer link.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FooterLink  $link
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLink(Request $request, FooterLink $link)
    {
        $validated = $request->validate([
            'column_id' => 'required|exists:footer_sections,id',
            'title' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);
        
        $link->update($validated);
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer link updated successfully.');
    }
    
    /**
     * Delete a footer link.
     *
     * @param  \App\Models\FooterLink  $link
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyLink(FooterLink $link)
    {
        $link->delete();
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer link deleted successfully.');
    }
    
    /**
     * Update footer settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request)
    {
        $settings = $request->except('_token', '_method');
        
        foreach ($settings as $key => $value) {
            FooterSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value' => $value,
                    'display_name' => ucwords(str_replace('_', ' ', $key)),
                    'type' => 'text'
                ]
            );
        }
        
        return redirect()->route('admin.footer.index')
            ->with('success', 'Footer settings updated successfully.');
    }
} 