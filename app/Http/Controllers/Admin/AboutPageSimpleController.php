<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AboutPageSimpleController extends Controller
{
    /**
     * Show the about page edit form
     */
    public function edit()
    {
        // Get the about page content
        $aboutPage = DB::table('about_page')->first();
        
        // Get section visibility settings
        $sectionVisibility = DB::table('about_section_visibility')->first();
        
        // Get corporate values
        $corporateValues = DB::table('corporate_values')
            ->orderBy('sort_order', 'asc')
            ->get();
            
        // Get team members
        $teamMembers = DB::table('team_members')
            ->orderBy('sort_order', 'asc')
            ->get();
        
        return view('admin.about.simple', compact(
            'aboutPage', 
            'sectionVisibility',
            'corporateValues',
            'teamMembers'
        ));
    }
    
    /**
     * Update the about page content
     */
    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Update the main about page content
            DB::table('about_page')
                ->updateOrInsert(
                    ['id' => $request->input('about_page_id', 1)],
                    [
                        'title' => $request->input('title'),
                        'subtitle' => $request->input('subtitle'),
                        'our_story' => $request->input('our_story'),
                        'title_ar' => $request->input('title_ar'),
                        'subtitle_ar' => $request->input('subtitle_ar'),
                        'our_story_ar' => $request->input('our_story_ar'),
                        'updated_at' => now()
                    ]
                );
            
            // Update section visibility
            DB::table('about_section_visibility')
                ->updateOrInsert(
                    ['id' => 1],
                    [
                        'show_hero' => $request->has('show_hero') ? 1 : 0,
                        'show_story' => $request->has('show_story') ? 1 : 0,
                        'show_values' => $request->has('show_values') ? 1 : 0,
                        'show_team' => $request->has('show_team') ? 1 : 0,
                        'show_certifications' => $request->has('show_certifications') ? 1 : 0,
                        'show_cta' => $request->has('show_cta') ? 1 : 0,
                        'updated_at' => now()
                    ]
                );
            
            DB::commit();
            
            return redirect()
                ->route('admin.about.simple')
                ->with('success', 'About page content has been updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating about page: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->with('error', 'An error occurred while updating the about page: ' . $e->getMessage())
                ->withInput();
        }
    }
}
