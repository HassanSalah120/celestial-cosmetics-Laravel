<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::select([
            'id', 
            'name', 
            'email', 
            'role', 
            'profile_image', 
            'email_verified_at', 
            'created_at'
        ]);
        
        // Apply filters if they exist
        if ($request->has('filters')) {
            $filters = $request->filters;
            
            // Role filters
            if (isset($filters['role']) && !empty($filters['role'])) {
                $roleFilters = explode(',', $filters['role']);
                
                if (!in_array('all', $roleFilters)) {
                    $query->where(function($q) use ($roleFilters) {
                        foreach ($roleFilters as $role) {
                            switch ($role) {
                                case 'admin':
                                    $q->orWhere('role', 'admin');
                                    break;
                                case 'staff':
                                    $q->orWhere('role', 'staff');
                                    break;
                                case 'user':
                                    $q->orWhere('role', 'customer');
                                    break;
                            }
                        }
                    });
                }
            }
            
            // Status filters
            if (isset($filters['status']) && !empty($filters['status'])) {
                $statusFilters = explode(',', $filters['status']);
                
                $query->where(function($q) use ($statusFilters) {
                    foreach ($statusFilters as $status) {
                        switch ($status) {
                            case 'verified':
                                $q->orWhereNotNull('email_verified_at');
                                break;
                            case 'unverified':
                                $q->orWhereNull('email_verified_at');
                                break;
                        }
                    }
                });
            }
            
            // Period filters
            if (isset($filters['period']) && !empty($filters['period'])) {
                $periodFilters = explode(',', $filters['period']);
                
                $query->where(function($q) use ($periodFilters) {
                    foreach ($periodFilters as $period) {
                        switch ($period) {
                            case 'recent':
                                $q->orWhere('created_at', '>=', now()->subDays(30));
                                break;
                            case 'yesterday':
                                $q->orWhereDate('created_at', now()->subDay()->toDateString());
                                break;
                            case 'last7days':
                                $q->orWhere('created_at', '>=', now()->subDays(7));
                                break;
                        }
                    }
                });
            }
        } else if ($request->has('filter')) {
            // Keep backward compatibility with old filter
            $filter = $request->filter;
            
            switch ($filter) {
                case 'admin':
                    $query->where('role', 'admin');
                    break;
                case 'staff':
                    $query->where('role', 'staff');
                    break;
                case 'user':
                    $query->where('role', 'customer');
                    break;
                case 'verified':
                    $query->whereNotNull('email_verified_at');
                    break;
                case 'unverified':
                    $query->whereNull('email_verified_at');
                    break;
                case 'recent':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', now()->subDay()->toDateString());
                    break;
                case 'last7days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                default:
                    // No filter or all users
                    break;
            }
        }
        
        // Apply search if present
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sortField = $request->input('sort', 'id');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        if ($request->ajax() || $request->wantsJson()) {
            $users = $query->get()
                ->map(function($user) {
                    // Format the profile image URL
                    if ($user->profile_image) {
                        $user->profile_image = asset('storage/' . $user->profile_image);
                    } else {
                        $user->profile_image = 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';
                    }
                    
                    // Format dates
                    if ($user->created_at) {
                        $user->created_at_formatted = $user->created_at->format('M d, Y');
                    }
                    
                    return $user;
                });
            
            return response()->json($users);
        }
        
        return view('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,staff,user'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(), // Auto verify admin-created users
        ];
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $userData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }

        $user = User::create($userData);

        // Log activity
        Activity::create([
            'description' => "Created user {$user->name} with role {$user->role}",
            'causer_type' => User::class,
            'causer_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'status' => 'completed',
            'properties' => [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role' => $user->role,
                'has_profile_image' => (bool)$user->profile_image
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,staff,user'],
            'profile_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        $oldRole = $user->role;
        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => $validated['role'],
        ];
        
        // Clear permissions if changing from staff to another role
        if ($oldRole === 'staff' && $validated['role'] !== 'staff') {
            $updateData['permissions'] = null;
        }
        
        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete the old image if it exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            
            // Store the new image
            $updateData['profile_image'] = $request->file('profile_image')->store('profile-images', 'public');
        }
        
        $user->update($updateData);

        // Log activity
        $roleChanged = $oldRole !== $user->role;
        Activity::create([
            'description' => "Updated user {$user->name}" . 
                ($roleChanged ? " - Role changed from {$oldRole} to {$user->role}" : ''),
            'causer_type' => User::class,
            'causer_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'status' => 'completed',
            'properties' => [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'role' => $user->role,
                'old_role' => $oldRole,
                'profile_image_updated' => $request->hasFile('profile_image'),
                'changes' => [
                    'name' => $validated['name'] !== $user->getOriginal('name'),
                    'email' => $validated['email'] !== $user->getOriginal('email'),
                    'role' => $roleChanged,
                    'profile_image' => $request->hasFile('profile_image')
                ]
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $userRole = $user->role;
        $user->delete();

        Activity::create([
            'description' => "Deleted user {$userName} with role {$userRole}",
            'causer_type' => User::class,
            'causer_id' => auth()->id(),
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'status' => 'completed',
            'properties' => [
                'user_name' => $userName,
                'user_email' => $user->email,
                'user_role' => $userRole,
                'deleted_by' => auth()->user()->name
            ]
        ]);

        return redirect()->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }
} 