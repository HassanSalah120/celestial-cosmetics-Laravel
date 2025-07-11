<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AboutPageEmergencyController extends Controller
{
    /**
     * Emergency update method with minimal code
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Log detailed request information
        Log::channel('about_debug')->info('EMERGENCY ABOUT UPDATE - REQUEST RECEIVED', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'all_headers' => $request->headers->all(),
            'request_data' => $request->all()
        ]);

        try {
            DB::beginTransaction();
            
            // Log the transaction start
            Log::channel('about_debug')->info('EMERGENCY ABOUT UPDATE - DB TRANSACTION STARTED');
            
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
            
            Log::channel('about_debug')->info('EMERGENCY ABOUT UPDATE - MAIN CONTENT UPDATED');
            
            // Update section visibility
            DB::table('about_section_visibility')
                ->updateOrInsert(
                    ['id' => 1],
                    [
                        'show_hero' => $request->has('show_hero'),
                        'show_story' => $request->has('show_story'),
                        'show_values' => $request->has('show_values'),
                        'show_team' => $request->has('show_team'),
                        'show_certifications' => $request->has('show_certifications'),
                        'show_cta' => $request->has('show_cta'),
                        'updated_at' => now()
                    ]
                );
            
            Log::channel('about_debug')->info('EMERGENCY ABOUT UPDATE - VISIBILITY UPDATED');
            
            DB::commit();
            Log::channel('about_debug')->info('EMERGENCY ABOUT UPDATE - DB TRANSACTION COMMITTED');
            
            return redirect()
                ->back()
                ->with('success', 'About page content has been updated successfully using the emergency method!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('about_debug')->error('EMERGENCY ABOUT UPDATE - ERROR', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
} 