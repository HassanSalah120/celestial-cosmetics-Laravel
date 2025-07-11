<?php

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store a newly created testimonial for overall brand experience.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
            'customer_role' => 'nullable|string|max:50',
            'avatar' => 'nullable|image|max:1024', // 1MB max
        ]);

        // Verify user has at least one delivered order
        $hasDeliveredOrder = \App\Models\Order::where('user_id', auth()->id())
            ->where('status', 'delivered')
            ->exists();
            
        if (!$hasDeliveredOrder) {
            return redirect()->route('testimonials')
                ->with('error', 'Only customers with delivered orders can submit testimonials.');
        }

        // Handle avatar upload if provided
        $avatarPath = null;
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $avatarPath = $request->file('avatar')->store('testimonials/avatars', 'public');
        }

        // Determine customer role if not provided
        $customerRole = $request->customer_role;
        if (!$customerRole) {
            // Count user's orders to determine loyalty
            $orderCount = \App\Models\Order::where('user_id', auth()->id())->count();
            
            if ($orderCount > 5) {
                $customerRole = 'Loyal Customer';
            } elseif ($orderCount > 2) {
                $customerRole = 'Regular Customer';
            } else {
                $customerRole = 'Customer';
            }
        }

        // Create the testimonial for overall experience (not product-specific)
        $testimonial = new Testimonial([
            'rating' => $request->rating,
            'title' => $request->title,
            'message' => $request->message,
            'customer_name' => auth()->user()->name,
            'email' => auth()->user()->email,
            'avatar' => $avatarPath,
            'customer_role' => $customerRole,
            'user_id' => auth()->id(),
            'is_approved' => true, // Auto-approve for registered users
            'is_featured' => false,
            // No product_id - this is for overall experience
        ]);
        
        $testimonial->save();
        
        return redirect()->route('testimonials')
            ->with('success', 'Thank you for sharing your experience! Your testimonial has been submitted.');
    }
    
    /**
     * Update the specified testimonial.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Testimonial  $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Testimonial $review)
    {
        // Check if the authenticated user is the owner of the review
        if (auth()->id() !== $review->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to update this testimonial.');
        }
        
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'message' => 'required|string|min:10',
        ]);
        
        $review->update([
            'rating' => $request->rating,
            'title' => $request->title,
            'message' => $request->message,
        ]);
        
        return redirect()->route('testimonials')
            ->with('success', 'Your testimonial has been updated successfully!');
    }
    
    /**
     * Remove the specified testimonial.
     *
     * @param  \App\Models\Testimonial  $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Testimonial $review)
    {
        // Check if the authenticated user is the owner of the review or an admin
        if (auth()->id() !== $review->user_id && !auth()->user()->is_admin) {
            return redirect()->back()->with('error', 'You are not authorized to delete this testimonial.');
        }
        
        $review->delete();
        
        return redirect()->route('testimonials')
            ->with('success', 'Testimonial has been deleted successfully!');
    }
} 