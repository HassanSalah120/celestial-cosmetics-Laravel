<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TestimonialsController extends Controller
{
    /**
     * Display a listing of testimonials.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Testimonial::query();
        
        // Filter by approval status
        if ($request->has('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            }
        }
        
        // Filter by featured status
        if ($request->has('featured')) {
            $query->where('is_featured', $request->featured === 'yes');
        }
        
        // Search by customer name or content
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_name_ar', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhere('message_ar', 'like', "%{$search}%")
                  ->orWhere('customer_role', 'like', "%{$search}%")
                  ->orWhere('customer_role_ar', 'like', "%{$search}%");
            });
        }
        
        $testimonials = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Show the form for creating a new testimonial.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.testimonials.create');
    }

    /**
     * Store a newly created testimonial in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'message' => 'required|string',
            'message_ar' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'customer_role' => 'nullable|string|max:50',
            'customer_role_ar' => 'nullable|string|max:50',
            'is_approved' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'avatar' => 'nullable|image|max:1024', // 1MB max
        ]);

        // Handle avatar upload if provided
        $avatarPath = null;
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatarPath = $request->file('avatar')->store('testimonials/avatars', 'public');
        }

        $testimonial = new Testimonial([
            'customer_name' => $request->customer_name,
            'customer_name_ar' => $request->customer_name_ar,
            'email' => $request->email,
            'title' => $request->title,
            'title_ar' => $request->title_ar,
            'message' => $request->message,
            'message_ar' => $request->message_ar,
            'rating' => $request->rating,
            'customer_role' => $request->customer_role,
            'customer_role_ar' => $request->customer_role_ar,
            'avatar' => $avatarPath,
            'is_approved' => $request->boolean('is_approved'),
            'is_featured' => $request->boolean('is_featured'),
        ]);
        
        $testimonial->save();
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial created successfully!');
    }

    /**
     * Show the form for editing the specified testimonial.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\View\View
     */
    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    /**
     * Update the specified testimonial in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_name_ar' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'message' => 'required|string',
            'message_ar' => 'nullable|string',
            'rating' => 'required|integer|min:1|max:5',
            'customer_role' => 'nullable|string|max:50',
            'customer_role_ar' => 'nullable|string|max:50',
            'is_approved' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'avatar' => 'nullable|image|max:1024', // 1MB max
        ]);

        // Handle avatar upload if provided
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Delete old avatar if exists
            if ($testimonial->avatar) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            
            $avatarPath = $request->file('avatar')->store('testimonials/avatars', 'public');
            $testimonial->avatar = $avatarPath;
        }

        $testimonial->customer_name = $request->customer_name;
        $testimonial->customer_name_ar = $request->customer_name_ar;
        $testimonial->email = $request->email;
        $testimonial->title = $request->title;
        $testimonial->title_ar = $request->title_ar;
        $testimonial->message = $request->message;
        $testimonial->message_ar = $request->message_ar;
        $testimonial->rating = $request->rating;
        $testimonial->customer_role = $request->customer_role;
        $testimonial->customer_role_ar = $request->customer_role_ar;
        $testimonial->is_approved = $request->boolean('is_approved');
        $testimonial->is_featured = $request->boolean('is_featured');
        
        $testimonial->save();
        
        return redirect()->route('admin.testimonials.index')
            ->with('success', 'Testimonial updated successfully!');
    }

    /**
     * Remove the specified testimonial from storage.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Testimonial $testimonial)
    {
        try {
            // Delete avatar if exists
            if ($testimonial->avatar) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            
            $testimonial->delete();
            
            return redirect()->route('admin.testimonials.index')
                ->with('success', 'Testimonial deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Error deleting testimonial: ' . $e->getMessage());
            
            return redirect()->route('admin.testimonials.index')
                ->with('error', 'Error deleting testimonial. Please try again.');
        }
    }
    
    /**
     * Toggle the approval status of a testimonial.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleApproval(Testimonial $testimonial)
    {
        $testimonial->is_approved = !$testimonial->is_approved;
        $testimonial->save();
        
        $status = $testimonial->is_approved ? 'approved' : 'unapproved';
        
        return redirect()->back()
            ->with('success', "Testimonial {$status} successfully!");
    }
    
    /**
     * Toggle the featured status of a testimonial.
     *
     * @param  \App\Models\Testimonial  $testimonial
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleFeatured(Testimonial $testimonial)
    {
        $testimonial->is_featured = !$testimonial->is_featured;
        $testimonial->save();
        
        $status = $testimonial->is_featured ? 'featured' : 'unfeatured';
        
        return redirect()->back()
            ->with('success', "Testimonial {$status} successfully!");
    }
} 