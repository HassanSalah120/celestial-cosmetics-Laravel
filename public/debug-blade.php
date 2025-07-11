<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if (!auth()->check()) {
    die('Not logged in');
}

$user = auth()->user();
$hasPermission = $user->hasPermission('manage_marketing');

echo '<pre>';
echo "User ID: " . $user->id . "\n";
echo "User Name: " . $user->name . "\n";
echo "User Email: " . $user->email . "\n";
echo "User Role: " . $user->role . "\n";
echo "Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
echo "Has manage_marketing permission: " . ($hasPermission ? 'Yes' : 'No') . "\n";
echo '</pre>';

// Now let's directly test the blade condition
echo '<div style="margin-top: 20px; padding: 10px; background-color: #f0f0f0; border: 1px solid #ccc;">';
echo '<h3>Testing Blade Condition</h3>';

if ($hasPermission) {
    echo '<div style="background-color: green; color: white; padding: 10px; margin-top: 10px;">
        Permission check passed - Button should be visible
        <a href="#" style="background-color: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; margin-left: 10px;">
            <i class="fas fa-plus mr-2"></i>Add New Coupon (Test)
        </a>
    </div>';
} else {
    echo '<div style="background-color: red; color: white; padding: 10px; margin-top: 10px;">
        Permission check failed - Button would be hidden
    </div>';
}

echo '</div>'; 