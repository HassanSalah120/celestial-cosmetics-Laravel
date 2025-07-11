<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class AboutPageController extends Controller
{
    /**
     * Display the about page content settings form
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        try {
            // Get about page main content
            $aboutPage = DB::table('about_page')->first();
            
            // Get corporate values
            $corporateValues = DB::table('corporate_values')
                ->orderBy('sort_order')
                ->get();
                
            // Get team members
            $teamMembers = DB::table('team_members')
                ->orderBy('sort_order')
                ->get();
            
            // Get section visibility settings
            $sectionVisibility = DB::table('about_section_visibility')->first();
            if (!$sectionVisibility) {
                // Create default visibility settings if not exist
                $sectionVisibility = (object)[
                    'show_hero' => true,
                    'show_story' => true,
                    'show_values' => true,
                    'show_team' => true,
                    'show_certifications' => true,
                    'show_cta' => true,
                ];
                
                // Insert default settings
                DB::table('about_section_visibility')->insert([
                    'show_hero' => true,
                    'show_story' => true,
                    'show_values' => true,
                    'show_team' => true,
                    'show_certifications' => true,
                    'show_cta' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return view('admin.about.edit', compact(
                'aboutPage',
                'corporateValues',
                'teamMembers',
                'sectionVisibility'
            ));
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in about page edit: ' . $e->getMessage());
            
            // Return a view with error
            return back()->with('error', 'An error occurred while loading settings: ' . $e->getMessage());
        }
    }
    
    /**
     * Store and handle a team member image upload
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string Image path relative to storage/app/public
     */
    private function handleTeamMemberImage($file)
    {
        try {
            // Generate a unique filename based on timestamp and original name
            $timestamp = time();
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            
            // Normalize extension for JPG/JPEG files
            if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                $extension = 'jpg'; // Standardize to jpg
            }
            
            $sanitizedFilename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $originalName);
            $filename = $sanitizedFilename . '_' . $timestamp . '.' . $extension;
            
            // Make sure the team directory exists in public storage
            if (!Storage::disk('public')->exists('team')) {
                Storage::disk('public')->makeDirectory('team');
            }
            
            // Store the file in public/storage/team
            $path = $file->storeAs('team', $filename, 'public');
            
            // Also copy the file to public/team for direct access
            $sourcePath = storage_path('app/public/team/' . $filename);
            $publicPath = public_path('team');
            
            // Create the public/team directory if it doesn't exist
            if (!file_exists($publicPath)) {
                mkdir($publicPath, 0755, true);
            }
            
            // Copy the file
            if (file_exists($sourcePath)) {
                copy($sourcePath, $publicPath . '/' . $filename);
            }
            
            return 'team/' . $filename;
        } catch (\Exception $e) {
            Log::error("Error handling image upload: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Update the about page content settings
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Basic validation for required fields
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string|max:255',
            'our_story' => 'required|string',
        ]);
        
        // Add validation rules for each team member image if present
        foreach ($request->allFiles() as $key => $file) {
            if (strpos($key, 'member_image_') === 0 || $key === 'new_member_image') {
                $validator->addRules([
                    $key => 'image|mimes:jpeg,jpg,png,gif|max:5120', // 5MB max
                ]);
            }
        }
        
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
        
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
                        // Certification 1
                        'certification_1_title' => $request->input('certification_1_title'),
                        'certification_1_title_ar' => $request->input('certification_1_title_ar'),
                        'certification_1_description' => $request->input('certification_1_description'),
                        'certification_1_description_ar' => $request->input('certification_1_description_ar'),
                        'certification_1_icon' => $request->input('certification_1_icon'),
                        // Certification 2
                        'certification_2_title' => $request->input('certification_2_title'),
                        'certification_2_title_ar' => $request->input('certification_2_title_ar'),
                        'certification_2_description' => $request->input('certification_2_description'),
                        'certification_2_description_ar' => $request->input('certification_2_description_ar'),
                        'certification_2_icon' => $request->input('certification_2_icon'),
                        // Certification 3
                        'certification_3_title' => $request->input('certification_3_title'),
                        'certification_3_title_ar' => $request->input('certification_3_title_ar'),
                        'certification_3_description' => $request->input('certification_3_description'),
                        'certification_3_description_ar' => $request->input('certification_3_description_ar'),
                        'certification_3_icon' => $request->input('certification_3_icon'),
                        // Certification 4
                        'certification_4_title' => $request->input('certification_4_title'),
                        'certification_4_title_ar' => $request->input('certification_4_title_ar'),
                        'certification_4_description' => $request->input('certification_4_description'),
                        'certification_4_description_ar' => $request->input('certification_4_description_ar'),
                        'certification_4_icon' => $request->input('certification_4_icon'),
                        'updated_at' => now()
                    ]
                );
            
            // Update section visibility
            $visibilityData = [
                'show_hero' => $request->has('show_hero'),
                'show_story' => $request->has('show_story'),
                'show_values' => $request->has('show_values'),
                'show_team' => $request->has('show_team'),
                'show_certifications' => $request->has('show_certifications'),
                'show_cta' => $request->has('show_cta'),
                'updated_at' => now()
            ];
            
            DB::table('about_section_visibility')
                ->updateOrInsert(
                    ['id' => 1],
                    $visibilityData
                );
            
            // Handle corporate values
            if ($request->has('values')) {
                foreach ($request->input('values') as $id => $value) {
                    DB::table('corporate_values')
                        ->where('id', $id)
                        ->update([
                            'title' => $value['title'],
                            'description' => $value['description'],
                            'title_ar' => $value['title_ar'] ?? null,
                            'description_ar' => $value['description_ar'] ?? null,
                            'icon' => $value['icon'],
                            'updated_at' => now()
                        ]);
                }
            }
            
            // Handle team members
            if ($request->has('members')) {
                foreach ($request->input('members') as $id => $member) {
                    $data = [
                        'name' => $member['name'],
                        'name_ar' => $member['name_ar'] ?? null,
                        'title' => $member['title'],
                        'title_ar' => $member['title_ar'] ?? null,
                        'position' => $member['title'], // Map title to position for backward compatibility
                        'position_ar' => $member['title_ar'] ?? null, // Map title_ar to position_ar for backward compatibility
                        'bio' => $member['bio'] ?? null,
                        'bio_ar' => $member['bio_ar'] ?? null,
                        'social_linkedin' => $member['social_linkedin'] ?? null,
                        'social_twitter' => $member['social_twitter'] ?? null,
                        'social_instagram' => $member['social_instagram'] ?? null,
                        'is_visible' => isset($member['is_visible']) ? 1 : 0,
                        'updated_at' => now()
                    ];
                    
                    // Using direct file name (not array format)
                    $fileKey = "member_image_" . $id;
                    if ($request->hasFile($fileKey)) {
                        try {
                            $file = $request->file($fileKey);
                            if ($file->isValid()) {
                                $data['image'] = $this->handleTeamMemberImage($file);
                            }
                        } catch (\Exception $e) {
                            Log::error("Failed to process image: " . $e->getMessage());
                        }
                    }
                    
                    DB::table('team_members')
                        ->where('id', $id)
                        ->update($data);
                }
            }
            
            // Handle new value addition
            if ($request->has('new_value')) {
                $newValue = $request->input('new_value');
                if (!empty($newValue['title']) && !empty($newValue['description'])) {
                    // Get the max sort order
                    $maxOrder = DB::table('corporate_values')->max('sort_order');
                    
                    DB::table('corporate_values')->insert([
                        'title' => $newValue['title'],
                        'description' => $newValue['description'],
                        'title_ar' => $newValue['title_ar'] ?? null,
                        'description_ar' => $newValue['description_ar'] ?? null,
                        'icon' => $newValue['icon'] ?? 'star',
                        'sort_order' => $maxOrder + 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            // Handle new team member addition
            if ($request->has('new_member')) {
                $newMember = $request->input('new_member');
                if (!empty($newMember['name']) && !empty($newMember['title'])) {
                    // Get the max sort order
                    $maxOrder = DB::table('team_members')->max('sort_order');
                    
                    $data = [
                        'name' => $newMember['name'],
                        'name_ar' => $newMember['name_ar'] ?? null,
                        'title' => $newMember['title'],
                        'title_ar' => $newMember['title_ar'] ?? null,
                        'position' => $newMember['title'], // Map title to position for backward compatibility
                        'position_ar' => $newMember['title_ar'] ?? null, // Map title_ar to position_ar for backward compatibility
                        'bio' => $newMember['bio'] ?? '',
                        'bio_ar' => $newMember['bio_ar'] ?? null,
                        'social_linkedin' => $newMember['social_linkedin'] ?? null,
                        'social_twitter' => $newMember['social_twitter'] ?? null,
                        'social_instagram' => $newMember['social_instagram'] ?? null,
                        'sort_order' => $maxOrder + 1,
                        'is_visible' => isset($newMember['is_visible']) ? 1 : 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    
                    // Handle image upload if provided
                    if ($request->hasFile('new_member_image')) {
                        try {
                            $file = $request->file('new_member_image');
                            if ($file->isValid()) {
                                $data['image'] = $this->handleTeamMemberImage($file);
                            }
                        } catch (\Exception $e) {
                            Log::error("Failed to process new member image: " . $e->getMessage());
                        }
                    }
                    
                    DB::table('team_members')->insert($data);
                }
            }
            
            DB::commit();
            
            // Clear the cache
            Artisan::call('cache:clear');
            
            return redirect()
                ->route('admin.about.edit')
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
    
    /**
     * Delete a corporate value
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteValue($id)
    {
        try {
            DB::table('corporate_values')->where('id', $id)->delete();
            return redirect()
                ->route('admin.about.edit')
                ->with('success', 'Corporate value has been deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting corporate value: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred while deleting the corporate value.');
        }
    }
    
    /**
     * Delete a team member
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMember($id)
    {
        try {
            // Get the team member to delete their image if needed
            $member = DB::table('team_members')->where('id', $id)->first();
            
            if ($member && $member->image) {
                // Get base filename without path
                $filename = basename($member->image);
                
                // Try to delete from 'public' disk
                Storage::disk('public')->delete('team/' . $filename);
                
                // Also delete from public/team if it exists
                $publicPath = public_path('team/' . $filename);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }
            }
            
            // Delete the team member
            DB::table('team_members')->where('id', $id)->delete();
            
            return redirect()
                ->route('admin.about.edit')
                ->with('success', 'Team member has been deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting team member: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred while deleting the team member.');
        }
    }
    
    /**
     * Update the sorting order of corporate values or team members
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrder(Request $request)
    {
        try {
            $type = $request->input('type');
            $items = $request->input('items');
            
            if ($type === 'values') {
                foreach ($items as $index => $id) {
                    DB::table('corporate_values')
                        ->where('id', $id)
                        ->update(['sort_order' => $index]);
                }
            } elseif ($type === 'members') {
                foreach ($items as $index => $id) {
                    DB::table('team_members')
                        ->where('id', $id)
                        ->update(['sort_order' => $index]);
                }
            }
            
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Clear a certification's data
     *
     * @param int $number Certification number (1-4)
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteCertification($number)
    {
        try {
            // Validate that the number is between 1 and 4
            if (!in_array($number, [1, 2, 3, 4])) {
                return redirect()
                    ->back()
                    ->with('error', 'Invalid certification number.');
            }
            
            // Clear the certification fields in the database
            $updateData = [
                "certification_{$number}_title" => null,
                "certification_{$number}_title_ar" => null,
                "certification_{$number}_description" => null,
                "certification_{$number}_description_ar" => null,
                "certification_{$number}_icon" => null,
                "updated_at" => now()
            ];
            
            DB::table('about_page')->update($updateData);
            
            DB::commit();
            
            return redirect()
                ->route('admin.about.edit')
                ->with('success', 'Certification ' . $number . ' has been cleared successfully!');
        } catch (\Exception $e) {
            Log::error("Error clearing certification #{$number}: " . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'An error occurred while clearing the certification.');
        }
    }
} 