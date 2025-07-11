<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Check if this is an AJAX request or JSON request for AG Grid
        if ($request->ajax() || $request->wantsJson() || $request->has('format')) {
            try {
                $query = Product::with(['category', 'images']);
                
                // Handle structured filters (new format)
                if ($request->has('filters')) {
                    $filters = $request->input('filters');
                    
                    // Handle category filters
                    if (!empty($filters['category'])) {
                        $categoryIds = explode(',', $filters['category']);
                        if (!empty($categoryIds)) {
                            $query->whereIn('category_id', $categoryIds);
                        }
                    }
                    
                    // Handle status filters
                    if (!empty($filters['status'])) {
                        $statuses = explode(',', $filters['status']);
                        if (!empty($statuses)) {
                            $query->where(function($q) use ($statuses) {
                                foreach ($statuses as $status) {
                                    if ($status === 'active') {
                                        $q->orWhere('is_visible', true);
                                    } elseif ($status === 'inactive') {
                                        $q->orWhere('is_visible', false);
                                    }
                                }
                            });
                        }
                    }
                    
                    // Handle period filters
                    if (!empty($filters['period'])) {
                        $periods = explode(',', $filters['period']);
                        if (!empty($periods)) {
                            $query->where(function($q) use ($periods) {
                                foreach ($periods as $period) {
                                    switch ($period) {
                                        case 'yesterday':
                                            $q->orWhereDate('created_at', now()->subDay()->toDateString());
                                            break;
                                        case 'last7days':
                                            $q->orWhere('created_at', '>=', now()->subDays(7));
                                            break;
                                        case 'recent':
                                            $q->orWhere('created_at', '>=', now()->subDays(30));
                                            break;
                                    }
                                }
                            });
                        }
                    }
                } else {
                    // Legacy filter handling (for backward compatibility)
                    // Handle category filter
                    if ($categoryId = $request->input('category')) {
                        $query->where('category_id', $categoryId);
                    }
                    
                    // Handle status filtering
                    if ($status = $request->input('status')) {
                        if ($status === 'active') {
                            $query->where('is_visible', true);
                        } elseif ($status === 'inactive') {
                            $query->where('is_visible', false);
                        }
                    }
                }
                
                // Handle search
                if ($search = $request->input('search')) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('slug', 'like', "%{$search}%")
                          ->orWhere('price', 'like', "%{$search}%");
                    });
                }
                
                // Get products
                $products = $query->latest()->get();
                
                // Format data for AG Grid
                $data = $products->map(function($product) {
                    // Ensure image path is properly formatted
                    $imagePath = null;
                    if ($product->image) {
                        // For consistency, check if the image exists
                        if (Storage::disk('public')->exists($product->image)) {
                            $imagePath = asset('storage/' . $product->image);
                        } else {
                            // Log the missing image
                            Log::warning("Product #{$product->id} has a non-existent image path: {$product->image}");
                            
                            // Try to use a default placeholder image
                            $imagePath = 'https://placehold.co/300x300/EFEFEF/AAAAAA&text=No+Image';
                        }
                    }
                    
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'price' => $product->price,
                        'discount_percent' => $product->discount_percent,
                        'stock' => $product->stock,
                        'category' => $product->category ? $product->category->name : 'Uncategorized',
                        'category_id' => $product->category_id,
                        'image' => $imagePath,
                        'status' => $product->is_visible ? 'Active' : 'Inactive',
                        'is_featured' => $product->is_featured,
                        'created_at' => $product->created_at->format('Y-m-d H:i:s')
                    ];
                });
                
                return response()->json($data);
            } catch (\Exception $e) {
                Log::error('Products JSON error: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                return response()->json(['error' => 'An error occurred while loading products data'], 500);
            }
        }
        
        // Regular view request
        $products = Product::with(['category', 'images'])->latest()->get();
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Debug information
        Log::info('Product store method called', [
            'has_files' => $request->hasFile('featured_image'),
            'all_files' => $request->allFiles(),
            'file_keys' => $request->files->keys(),
            'file_count' => count($request->allFiles())
        ]);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0|max:1000',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'featured_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'noindex' => 'nullable|boolean',
            'nofollow' => 'nullable|boolean',
        ]);

        // Generate unique slug from name
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        // Keep checking until we find a unique slug
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $validated['slug'] = $slug;

        // Handle featured image with enhanced security
        if ($request->hasFile('featured_image')) {
            $image = $request->file('featured_image');
            
            // Additional security checks
            try {
                // Verify the file is valid
                if (!$image->isValid()) {
                    return redirect()->back()
                        ->withErrors(['featured_image' => 'The uploaded file is not valid'])
                        ->withInput();
                }
                
                // Check file size again
                if ($image->getSize() > 10240 * 1024) { // 10MB in bytes
                    return redirect()->back()
                        ->withErrors(['featured_image' => 'The file size exceeds the maximum allowed limit'])
                        ->withInput();
                }
                
                // Verify mime type with PHP's own functions as an extra layer
                $finfo = new \finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($image->getPathname());
                $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
                
                if (!in_array($mime, $allowedMimes)) {
                    return redirect()->back()
                        ->withErrors(['featured_image' => 'The file must be a valid image (JPEG, PNG, GIF, or WebP)'])
                        ->withInput();
                }
                
                // Generate a random name + original extension
                $extension = $image->getClientOriginalExtension();
                $fileName = Str::uuid() . '.' . $extension;
                
                // Store image with sanitized name
                $path = $image->storeAs('products', $fileName, 'public');
                $validated['image'] = $path;
                
            } catch (\Exception $e) {
                Log::error('File upload error: ' . $e->getMessage());
                return redirect()->back()
                    ->withErrors(['featured_image' => 'An error occurred while processing the image'])
                    ->withInput();
            }
        }

        // Create product
        Log::info('Creating product with data: ', array_merge(
            array_filter($validated, function($key) {
                return !in_array($key, ['image', 'featured_image']);
            }, ARRAY_FILTER_USE_KEY),
            ['image' => $validated['image'] ?? null]
        ));
        
        $product = Product::create($validated);
        
        Log::info('Product created with ID: ' . $product->id, [
            'has_image' => !empty($product->image),
            'image_path' => $product->image
        ]);

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            foreach ($request->file('gallery_images') as $image) {
                $product->images()->create([
                    'image' => $image->store('products/gallery', 'public'),
                    'is_primary' => false
                ]);
            }
        }

        // Log activity
        Activity::create([
            'description' => 'Created new product',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'created'])
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01|regex:/^\d+(\.\d{1,2})?$/',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0|max:1000',
            'ingredients' => 'nullable|string',
            'how_to_use' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url|max:255',
            'noindex' => 'nullable|boolean',
            'nofollow' => 'nullable|boolean',
        ]);

        // Update slug if name has changed
        if ($product->name !== $validated['name']) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;

            // Keep checking until we find a unique slug
            while (Product::where('slug', $slug)->where('id', '!=', $product->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Start by assuming we keep the existing image
        // IMPORTANT: Access original value BEFORE it might be overwritten in $validated
        $originalImagePath = $product->image;
        $validated['image'] = $originalImagePath;
        Log::debug('Initialized validated[image] with existing DB value: ' . ($validated['image'] ?? 'null'));

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            Log::debug('Processing uploaded featured image.');

            // Delete old image if it exists
            if ($originalImagePath) { // Use the value fetched before validation
                Log::debug('Deleting old image: ' . $originalImagePath);
                try {
                    Storage::disk('public')->delete($originalImagePath);
                } catch (\Exception $e) {
                    Log::error('Error deleting old image: ' . $e->getMessage());
                }
            }

            // Store the new image
            $newImagePath = null;
            try {
                $newImagePath = $request->file('featured_image')->store('products/featured', 'public');
            } catch (\Exception $e) {
                 Log::error('Exception during image store: ' . $e->getMessage());
            }
            
            if ($newImagePath) {
                $validated['image'] = $newImagePath;
                Log::debug('New image stored. Path: ' . $validated['image']);
            } else {
                Log::error('Failed to store uploaded image.');
                // Keep original path ($validated['image'] still holds $originalImagePath)
                Log::debug('Keeping original image path: ' . $validated['image']);
            }
        } else {
            Log::debug('No new featured image uploaded.');
            // Check the hidden current_image field (which is in $validated now)
            $currentImageFromForm = $validated['current_image'] ?? null;
            Log::debug('Current image field from form (validated): ' . ($currentImageFromForm ?: 'empty'));

            // Update validated[image] only if the form value differs from the ORIGINAL DB value
            if ($currentImageFromForm !== $originalImagePath) {
                 Log::debug('Form image differs from original DB value. Using form value.');
                $validated['image'] = $currentImageFromForm;
                Log::debug('Validated image path set to form value: ' . ($validated['image'] ?? 'null'));
            } else {
                 Log::debug('Form image matches original DB value. Keeping path: ' . ($validated['image'] ?? 'null'));
            }
        }

        // Remove the temporary form fields from validated data before updating the model
        unset($validated['current_image']);
        // unset($validated['featured_image']); // Not needed as validator->validated() doesn't include file objects

        // Ensure is_featured is correctly handled (checkboxes send 'on' or nothing)
        // Use $request->has() as $validated['is_featured'] might not exist if checkbox unchecked
        $validated['is_featured'] = $request->has('is_featured');

        // Debugging just before the update call
        Log::debug('Final data being sent to product->update():', $validated);

        // Update product
        try {
        $product->update($validated);
            Log::debug('Product update successful.');
        } catch (\Exception $e) {
            Log::error('Error updating product: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Failed to update product. Please check logs.');
        }
        
        // Debug after update
        Log::debug('After update - Image in DB: ' . $product->refresh()->image);

        // Handle gallery images
        if ($request->hasFile('gallery_images')) {
            // Log gallery images info
            Log::debug('Gallery images found', [
                'count' => count($request->file('gallery_images'))
            ]);
            
            foreach ($request->file('gallery_images') as $image) {
                $product->images()->create([
                    'image' => $image->store('products/gallery', 'public'),
                    'is_primary' => false
                ]);
            }
        }

        // Log activity
        Activity::create([
            'description' => 'Updated product',
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Product::class,
            'subject_id' => $product->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Delete associated images from storage
        foreach ($product->images as $image) {
            Storage::delete('public/' . $image->image);
        }

        // Delete the product and its associated images from database
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Display the specified product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\View\View
     */
    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        // Delete the image file from storage
        Storage::delete('public/' . $image->image);

        // Delete the image record from database
        $image->delete();

        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        }

        return redirect()->back()
            ->with('success', 'Image deleted successfully.');
    }

    public function destroyFeaturedImage(Product $product)
    {
        Log::debug('destroyFeaturedImage called for product ID: ' . $product->id);
        Log::debug('Current image path: ' . $product->image);
        
        if ($product->image) {
            // Delete the image file from storage
            Storage::disk('public')->delete($product->image);
            Log::debug('Image deleted from storage: ' . $product->image);
            
            // Update the product to remove the image reference
            $product->update(['image' => null]);
            Log::debug('Product image reference cleared');
            
            // Return JSON response for AJAX requests
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Featured image has been removed successfully.']);
            }
            
            return redirect()->back()->with('success', 'Featured image has been removed successfully.');
        }
        
        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'No featured image found to remove.']);
        }
        
        return redirect()->back()->with('error', 'No featured image found to remove.');
    }

    /**
     * Remove a gallery image - direct simplified approach
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeGalleryImageDirect(Request $request, $id)
    {
        Log::info('removeGalleryImageDirect called', [
            'product_id' => $id,
            'image_path' => $request->input('image_path'),
            'request_data' => $request->all()
        ]);
        
        try {
            $product = Product::findOrFail($id);
            
            // Check if we're using ProductImage model
            if (method_exists($product, 'images') && $product->images) {
                Log::info('Using ProductImage relationship');
                
                // Find the image by path
                $imagePath = $request->input('image_path');
                $imageToRemove = null;
                
                foreach ($product->images as $image) {
                    Log::info('Checking image', ['stored' => $image->image, 'requested' => $imagePath]);
                    if ($image->image === $imagePath) {
                        $imageToRemove = $image;
                        break;
                    }
                }
                
                if ($imageToRemove) {
                    Log::info('Found image to remove', ['id' => $imageToRemove->id]);
                    // Delete the file if it exists
                    if (Storage::disk('public')->exists($imageToRemove->image)) {
                        Storage::disk('public')->delete($imageToRemove->image);
                    }
                    
                    // Delete the record
                    $imageToRemove->delete();
                    
                    return response()->json(['success' => true, 'message' => 'Image removed successfully']);
                }
            }
            
            // Fallback to gallery_images JSON field
            if (property_exists($product, 'gallery_images') || isset($product->gallery_images)) {
                Log::info('Using gallery_images JSON field');
                
                $galleryImages = [];
                
                // Handle both string and array formats
                if (is_string($product->gallery_images)) {
                    $galleryImages = json_decode($product->gallery_images) ?? [];
                } else {
                    $galleryImages = $product->gallery_images ?? [];
                }
                
                Log::info('Current gallery images', ['images' => $galleryImages]);
                
                $imagePath = $request->input('image_path');
                $key = array_search($imagePath, $galleryImages);
                
                Log::info('Searching for image', ['path' => $imagePath, 'found_key' => $key]);
                
                if ($key !== false) {
                    // Remove from array
                    unset($galleryImages[$key]);
                    
                    // Reset array keys
                    $galleryImages = array_values($galleryImages);
                    
                    // Update product
                    $product->gallery_images = json_encode($galleryImages);
                    $product->save();
                    
                    // Delete the file if it exists
                    if (Storage::disk('public')->exists($imagePath)) {
                        Storage::disk('public')->delete($imagePath);
                    }
                    
                    return response()->json(['success' => true, 'message' => 'Image removed successfully']);
                }
            }
            
            // If we get here, the image wasn't found
            return response()->json(['success' => false, 'message' => 'Image not found in gallery']);
            
        } catch (\Exception $e) {
            Log::error('Error in removeGalleryImageDirect', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove a gallery image from a product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeImage(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $imagePath = $request->input('image_path');
        
        if (!$imagePath) {
            return response()->json(['success' => false, 'message' => 'No image path provided'], 400);
        }
        
        // Get current gallery images
        $galleryImages = json_decode($product->gallery_images) ?? [];
        
        // Find and remove the image from the array
        $key = array_search($imagePath, $galleryImages);
        
        if ($key !== false) {
            // Remove from array
            unset($galleryImages[$key]);
            
            // Reset array keys
            $galleryImages = array_values($galleryImages);
            
            // Update product
            $product->gallery_images = json_encode($galleryImages);
            $product->save();
            
            // Delete the file if it exists
            $fullPath = public_path($imagePath);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false, 'message' => 'Image not found in gallery'], 404);
    }
} 