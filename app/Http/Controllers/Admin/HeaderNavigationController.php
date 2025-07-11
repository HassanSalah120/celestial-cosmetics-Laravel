<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeaderNavigationItem;
use App\Models\StoreHour;
use App\Models\GlobalHeaderSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HeaderNavigationController extends Controller
{
    public function index()
    {
        $navigationItems = HeaderNavigationItem::whereNull('parent_id')
            ->orderBy('sort_order')
            ->with('children')
            ->get();

        // Get all available routes for the dropdown, excluding admin routes
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return $route->getName();
        })->filter(function($route) {
            // Filter out admin routes, api routes, and other non-frontend routes
            return $route && 
                   !Str::startsWith($route, 'admin.') && 
                   !Str::startsWith($route, 'api.') &&
                   !Str::startsWith($route, 'debugbar.') &&
                   !Str::startsWith($route, 'ignition.') &&
                   !Str::startsWith($route, 'sanctum.') &&
                   !Str::startsWith($route, 'passport.');
        })->sort()->values();

        // Get header settings
        $headerSettings = GlobalHeaderSetting::getSettings();
        
        // Get store hours for display in header
        $storeHours = StoreHour::orderBy('day_number')->get();
        
        return view('admin.header-navigation.index', compact(
            'navigationItems', 
            'routes', 
            'headerSettings',
            'storeHours'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'translation_key' => 'nullable|string|max:255',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'has_dropdown' => 'boolean',
            'parent_id' => 'nullable|exists:header_navigation_items,id'
        ]);

        // Convert checkbox values to boolean
        $data = $request->all();
        $data['open_in_new_tab'] = filter_var($request->input('open_in_new_tab'), FILTER_VALIDATE_BOOLEAN);
        $data['is_active'] = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        $data['has_dropdown'] = filter_var($request->input('has_dropdown'), FILTER_VALIDATE_BOOLEAN);

        HeaderNavigationItem::create($data);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Navigation item created successfully']);
        }

        return redirect()->route('admin.header-navigation.index')
            ->with('success', 'Navigation item created successfully.');
    }

    public function update(Request $request, HeaderNavigationItem $headerNavigation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'translation_key' => 'nullable|string|max:255',
            'open_in_new_tab' => 'boolean',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'has_dropdown' => 'boolean',
            'parent_id' => 'nullable|exists:header_navigation_items,id'
        ]);

        // Convert checkbox values to boolean
        $data = $request->all();
        $data['open_in_new_tab'] = filter_var($request->input('open_in_new_tab'), FILTER_VALIDATE_BOOLEAN);
        $data['is_active'] = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
        $data['has_dropdown'] = filter_var($request->input('has_dropdown'), FILTER_VALIDATE_BOOLEAN);

        $headerNavigation->update($data);
        
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Navigation item updated successfully']);
        }

        return redirect()->route('admin.header-navigation.index')
            ->with('success', 'Navigation item updated successfully.');
    }

    public function destroy(HeaderNavigationItem $headerNavigation)
    {
        $headerNavigation->delete();

        return redirect()->route('admin.header-navigation.index')
            ->with('success', 'Navigation item deleted successfully.');
    }

    public function edit(HeaderNavigationItem $headerNavigation)
    {
        return response()->json($headerNavigation);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:header_navigation_items,id',
            'items.*.sort_order' => 'required|integer',
            'items.*.parent_id' => 'nullable|exists:header_navigation_items,id'
        ]);

        foreach ($request->items as $item) {
            HeaderNavigationItem::where('id', $item['id'])->update([
                'sort_order' => $item['sort_order'],
                'parent_id' => $item['parent_id'] ?? null
            ]);
        }

        return response()->json(['message' => 'Order updated successfully']);
    }
    
    public function updateSettings(Request $request)
    {
        $request->validate([
            'show_logo' => 'nullable|boolean',
            'show_profile' => 'nullable|boolean',
            'show_store_hours' => 'nullable|boolean',
            'show_search' => 'nullable|boolean',
            'show_cart' => 'nullable|boolean',
            'show_language_switcher' => 'nullable|boolean',
            'sticky_header' => 'nullable|boolean',
            'header_style' => 'string|in:default,centered,minimal,full-width',
        ]);
        
        // Define all possible checkbox fields
        $checkboxFields = [
            'show_logo',
            'show_profile',
            'show_store_hours',
            'show_search',
            'show_cart',
            'show_language_switcher',
            'sticky_header',
        ];
        
        // Set unchecked checkboxes to false
        foreach ($checkboxFields as $field) {
            if (!$request->has($field)) {
                $request->merge([$field => false]);
            }
        }
        
        // Update header settings
        GlobalHeaderSetting::updateSettings($request->except(['_token']));
        
        return redirect()->route('admin.header-navigation.index', ['#settings-tab'])
            ->with('success', 'Header settings updated successfully.');
    }
} 