<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        // Check if this is an AJAX request or JSON request for AG Grid
        if ($request->ajax() || $request->wantsJson() || $request->has('format')) {
            try {
                $query = Category::withCount('products');
                
                // Handle search
                if ($search = $request->input('search')) {
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('slug', 'like', "%{$search}%");
                    });
                }
                
                // Handle status filtering
                if ($status = $request->input('status')) {
                    // Status is not directly stored in the model, so we'll ignore this for now
                    // In a real implementation, you might add a custom filter based on your business logic
                }
                
                // Get all categories with product count
                $categories = $query->get();
                
                // Format data for AG Grid
                $data = $categories->map(function($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'description' => $category->description,
                        'image' => $category->image ? asset('storage/' . $category->image) : null,
                        'product_count' => $category->products_count,
                        'status' => 'Active', // Default all categories to active since there's no is_active field
                        'created_at' => $category->created_at->format('Y-m-d H:i:s')
                    ];
                });
                
                return response()->json($data);
            } catch (\Exception $e) {
                Log::error('Categories JSON error: ' . $e->getMessage());
                Log::error('Stack trace: ' . $e->getTraceAsString());
                
                return response()->json([
                    'error' => 'Failed to load categories: ' . $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        }
        
        // Regular view request
        $categories = Category::withCount('products')->latest()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Generate unique slug from name
        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;

        // Keep checking until we find a unique slug
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $validated['slug'] = $slug;

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Create the category
        $category = Category::create($validated);

        // Log activity
        Activity::create([
            'description' => 'Created new category: ' . $category->name,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'created'])
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load('products');
        $products = $category->products()->paginate(10);
        
        return view('admin.categories.show', [
            'category' => $category,
            'products' => $products
        ]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Generate slug only if name has changed
        if ($category->name !== $validated['name']) {
            $baseSlug = Str::slug($validated['name']);
            $slug = $baseSlug;
            $counter = 1;

            // Keep checking until we find a unique slug, excluding the current category
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $baseSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = $request->file('image')->store('categories', 'public');
        }

        // Update the category
        $category->update($validated);

        // Log activity
        Activity::create([
            'description' => 'Updated category: ' . $category->name,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'updated'])
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Cannot delete category that has products. Please remove or reassign all products first.');
        }

        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        // Delete the category
        $category->delete();

        // Log activity
        Activity::create([
            'description' => 'Deleted category: ' . $category->name,
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => Category::class,
            'subject_id' => $category->id,
            'status' => 'completed',
            'properties' => json_encode(['action' => 'deleted'])
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }
} 