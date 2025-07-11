<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\VerifyEmailNotification;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'permissions' => 'array',
        'phone_number' => 'encrypted',
        'address' => 'encrypted',
        'date_of_birth' => 'encrypted',
    ];

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has a specific permission or is an admin.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        // Admin users have all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Staff users have specific permissions
        if ($this->permissions) {
            // Ensure permissions is treated as an array
            $permissions = is_string($this->permissions) ? json_decode($this->permissions, true) : $this->permissions;
            
            // Check if permission exists in the array
            return is_array($permissions) && in_array($permission, $permissions);
        }

        return false;
    }

    /**
     * Check if the user has any of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAnyPermission(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the user has all of the given permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function hasAllPermissions(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if the user has any of the given roles.
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get all permissions for the user.
     *
     * @return array
     */
    public function getAllPermissions()
    {
        if ($this->isAdmin()) {
            return [
                'view_dashboard',
                'view_reports',
                'manage_products',
                'view_products',
                'manage_categories',
                'manage_orders',
                'view_orders',
                'manage_users',
                'view_customers',
                'view_activity_logs',
                'manage_marketing',
                'manage_settings',
                'create coupons'
            ];
        }

        return is_string($this->permissions) ? json_decode($this->permissions, true) : ($this->permissions ?? []);
    }

    /**
     * Get the addresses for the user.
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
    
    /**
     * Get the default address for the user.
     */
    public function defaultAddress()
    {
        return $this->hasOne(Address::class)->where('is_default', true);
    }
    
    /**
     * Get the wishlist items for the user.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }
    
    /**
     * Get the products in the user's wishlist.
     */
    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withTimestamps();
    }
}
