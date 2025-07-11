<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Facades\Settings;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    /**
     * Display the about page with data from the appropriate tables.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Check if we're in RTL mode
        $isRtl = is_rtl();
        
        // Get the about page main content
        $aboutPage = DB::table('about_page')->first();
        
        if (!$aboutPage) {
            $aboutPage = (object)[
                'title' => 'About Us',
                'subtitle' => 'Learn about our journey, values, and the team behind Celestial Cosmetics.',
                'our_story' => 'Our story content goes here.',
            ];
        }
        
        // Get section visibility settings
        $sectionVisibility = DB::table('about_section_visibility')->first();
        if (!$sectionVisibility) {
            $sectionVisibility = (object)[
                'show_hero' => true,
                'show_story' => true,
                'show_values' => true,
                'show_team' => true,
                'show_certifications' => true,
                'show_cta' => true,
            ];
        }
        
        // Get our values from the corporate_values table
        $ourValues = DB::table('corporate_values')
            ->orderBy('sort_order')
            ->get()
            ->map(function($value) use ($isRtl) {
                // If in RTL mode and Arabic content exists, use it
                if ($isRtl && !empty($value->title_ar)) {
                    $value->title = $value->title_ar;
                }
                
                if ($isRtl && !empty($value->description_ar)) {
                    $value->description = $value->description_ar;
                }
                
                return [
                    'title' => $value->title,
                    'description' => $value->description,
                    'icon' => $value->icon,
                ];
            })
            ->toArray();
            
        // Get team members from team_members table
        $teamMembers = DB::table('team_members')
            ->orderBy('sort_order')
            ->get()
            ->map(function($member) use ($isRtl) {
                // If in RTL mode and Arabic content exists, use it
                if ($isRtl && !empty($member->name_ar)) {
                    $member->name = $member->name_ar;
                }
                
                if ($isRtl && !empty($member->title_ar)) {
                    $member->title = $member->title_ar;
                }
                
                if ($isRtl && !empty($member->bio_ar)) {
                    $member->bio = $member->bio_ar;
                }
                
                // Properly handle the image path
                if (!empty($member->image)) {
                    // First check: Is it a full URL?
                    if (filter_var($member->image, FILTER_VALIDATE_URL)) {
                        $image = $member->image;
                    } 
                    else {
                        // Make sure the path starts with storage/ for artisan serve
                        // If the path already starts with team/, prefix with storage/
                        if (strpos($member->image, 'team/') === 0) {
                            $image = 'storage/' . $member->image;
                        } else {
                            // For other paths, just use as is
                            $image = $member->image;
                        }
                    }
                } else {
                    $image = 'images/default-profile.jpg';
                }
                
                return [
                    'name' => $member->name,
                    'title' => $member->title,
                    'bio' => $member->bio,
                    'image' => str_starts_with($image, 'http') ? $image : asset($image),
                ];
            })
            ->toArray();
        
        // Set the page title and meta data
        $title = Settings::get($isRtl ? 'about_meta_title_ar' : 'about_meta_title') ?? Settings::get('default_meta_title', config('app.name'));
        $description = Settings::get($isRtl ? 'about_meta_description_ar' : 'about_meta_description') ?? Settings::get('default_meta_description');
        $keywords = Settings::get('about_meta_keywords') ?? Settings::get('default_meta_keywords');
        
        return view('about', compact('aboutPage', 'ourValues', 'teamMembers', 'title', 'description', 'keywords', 'sectionVisibility'));
    }
} 