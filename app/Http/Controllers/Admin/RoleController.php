<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Available permissions in the system
     */
    private $availablePermissions = [
        'view_dashboard' => 'Access the admin dashboard',
        'view_reports' => 'View sales and performance reports',
        'manage_products' => 'Create, edit and delete products',
        'view_products' => 'View product listings',
        'manage_categories' => 'Create, edit and delete product categories',
        'manage_orders' => 'Process and manage customer orders',
        'view_orders' => 'View order details',
        'manage_users' => 'Create, edit and delete user accounts',
        'view_customers' => 'View customer information',
        'view_activity_logs' => 'View system activity logs',
        'manage_marketing' => 'Manage promotions and discounts',
        'manage_settings' => 'Configure system settings',
        'manage_contact_messages' => 'Manage customer inquiries and messages',
        'manage_roles' => 'Create and manage user roles and permissions',
        'manage_inventory' => 'Manage product inventory and stock',
        'manage_shipping' => 'Configure shipping options and rates',
        'manage_payments' => 'Configure payment methods and settings',
        'manage_seo' => 'Manage SEO settings and metadata',
        'export_data' => 'Export reports and system data'
    ];
    
    /**
     * Available roles in the system
     */
    private $availableRoles = [
        'admin' => 'Full system access with all permissions',
        'manager' => 'Advanced access with limited administrative capabilities',
        'staff' => 'Limited access based on assigned permissions',
        'user' => 'Standard customer account'
    ];

    /**
     * Display a listing of the roles and permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
        // Get counts of users by role
        $userCounts = [
            'admin' => User::where('role', 'admin')->count(),
            'staff' => User::where('role', 'staff')->count(),
            'user' => User::where('role', 'user')->count(),
        ];
        
            // Start query for staff and admin users
            $query = User::whereIn('role', ['admin', 'staff']);
            
            // Apply filters if provided
            if ($request->has('filters')) {
                $filters = $request->filters;
                
                // Role filter
                if (isset($filters['role']) && $filters['role'] !== 'all') {
                    $query->where('role', $filters['role']);
                }
                
                // Date filter
                if (isset($filters['date']) && $filters['date'] !== 'all') {
                    switch ($filters['date']) {
                        case 'today':
                            $query->whereDate('updated_at', now()->toDateString());
                            break;
                        case 'yesterday':
                            $query->whereDate('updated_at', now()->subDay()->toDateString());
                            break;
                        case 'last7days':
                            $query->where('updated_at', '>=', now()->subDays(7));
                            break;
                        case 'last30days':
                            $query->where('updated_at', '>=', now()->subDays(30));
                            break;
                    }
                }
                
                // Search filter
                if (isset($filters['search']) && !empty($filters['search'])) {
                    $search = $filters['search'];
                    $query->where(function($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
                }
            }
            
            // Get list of staff and admin users with their permissions
            $staffUsers = $query->select('id', 'name', 'email', 'role', 'permissions', 'profile_image', 'updated_at', 'created_at')
                ->latest()
            ->get();
                
            // Force this to be an array, even if empty
            if ($staffUsers->isEmpty()) {
                Log::info('No staff users found in the database.');
            } else {
                // Ensure permissions are properly formatted
                $staffUsers->transform(function ($user) {
                    // Make sure permissions is properly JSON decoded
                    if (is_string($user->permissions)) {
                        try {
                            $user->permissions = json_decode($user->permissions, true) ?: [];
                        } catch (\Exception $e) {
                            $user->permissions = [];
                        }
                    } elseif (!is_array($user->permissions)) {
                        $user->permissions = [];
                    }
                    
                    return $user;
                });
                
                Log::info('Found ' . $staffUsers->count() . ' staff/admin users.');
            }
            
            // If JSON response is requested, return the data
            if ($request->wantsJson()) {
                return response()->json($staffUsers);
            }
        
        return view('admin.roles.index', [
            'availablePermissions' => $this->availablePermissions,
            'availableRoles' => $this->availableRoles,
            'userCounts' => $userCounts,
                'staffUsers' => $staffUsers,
                'activeFilters' => $request->filters ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading roles: ' . $e->getMessage());
            
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to load roles data'], 500);
            }
            
            return redirect()->back()->with('error', 'An error occurred while loading roles data.');
        }
    }

    /**
     * Show the form for creating a new role assignment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Get users who aren't staff or admin yet
        $eligibleUsers = User::where('role', 'user')->get();
        
        return view('admin.roles.create', [
            'eligibleUsers' => $eligibleUsers,
            'availablePermissions' => $this->availablePermissions
        ]);
    }

    /**
     * Store a newly created role assignment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:admin,manager,staff,user',
            'permissions' => 'array|required_if:role,staff,manager',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->availablePermissions)),
        ]);
        
        $user = User::findOrFail($validated['user_id']);
        $oldRole = $user->role;
        
        // Update user role
        $user->role = $validated['role'];
        
        // Update permissions if role is staff or manager
        if ($validated['role'] === 'staff' || $validated['role'] === 'manager') {
            $user->permissions = json_encode($validated['permissions'] ?? []);
        } else {
            $user->permissions = null; // Clear permissions for non-staff/manager roles
        }
        
        $user->save();
        
        // Log activity
        Activity::create([
            'description' => "Updated user role for {$user->name} from {$oldRole} to {$user->role}",
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'status' => 'completed',
            'properties' => json_encode([
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'permissions' => $user->permissions
            ])
        ]);
        
        return redirect()->route('admin.roles.index')
            ->with('success', "User role for {$user->name} has been updated successfully.");
    }

    /**
     * Show the form for editing a role assignment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // For staff/manager users, decode permissions
        $currentPermissions = [];
        if (($user->role === 'staff' || $user->role === 'manager') && $user->permissions) {
            $currentPermissions = is_string($user->permissions) 
                ? json_decode($user->permissions, true) 
                : $user->permissions;
        }
        
        return view('admin.roles.edit', [
            'user' => $user,
            'availablePermissions' => $this->availablePermissions,
            'currentPermissions' => $currentPermissions
        ]);
    }

    /**
     * Update the role assignment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,manager,staff,user',
            'permissions' => 'array|required_if:role,staff,manager',
            'permissions.*' => 'string|in:' . implode(',', array_keys($this->availablePermissions)),
        ]);
        
        $user = User::findOrFail($id);
        $oldRole = $user->role;
        
        // Update user role
        $user->role = $validated['role'];
        
        // Update permissions if role is staff or manager
        if ($validated['role'] === 'staff' || $validated['role'] === 'manager') {
            $user->permissions = json_encode($validated['permissions'] ?? []);
        } else {
            $user->permissions = null; // Clear permissions for non-staff/manager roles
        }
        
        $user->save();
        
        // Log activity
        Activity::create([
            'description' => "Updated user role for {$user->name} from {$oldRole} to {$user->role}",
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'status' => 'completed',
            'properties' => json_encode([
                'old_role' => $oldRole,
                'new_role' => $user->role,
                'permissions' => $user->permissions
            ])
        ]);
        
        return redirect()->route('admin.roles.index')
            ->with('success', "User role for {$user->name} has been updated successfully.");
    }

    /**
     * Remove the role assignment (reset to regular user).
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $oldRole = $user->role;
        
        // Don't allow removing your own admin role
        if ($user->id === auth()->id() && $user->role === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', "You cannot remove your own admin privileges.");
        }
        
        // Reset to standard user role
        $user->role = 'user';
        $user->permissions = null;
        $user->save();
        
        // Log activity
        Activity::create([
            'description' => "Reset user role for {$user->name} from {$oldRole} to user",
            'causer_type' => get_class(auth()->user()),
            'causer_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'status' => 'completed',
            'properties' => json_encode([
                'old_role' => $oldRole,
                'new_role' => 'user'
            ])
        ]);
        
        return redirect()->route('admin.roles.index')
            ->with('success', "User role for {$user->name} has been reset to standard user.");
    }
} 