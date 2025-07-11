<?php

// Load Laravel app
require_once __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Debug output
echo '<html><head><title>Language Debug</title>';
echo '<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .container { max-width: 800px; margin: 0 auto; }
    .debug-box { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .value { font-weight: bold; background: #fff; padding: 3px 6px; border-radius: 3px; }
    h1 { color: #721c24; }
    h2 { margin-top: 30px; color: #721c24; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 10px; text-align: left; }
    th { background-color: #f2f2f2; }
    .card { background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 15px; }
    .test-row { display: flex; border-bottom: 1px solid #eee; padding: 8px 0; }
    .test-label { width: 40%; font-weight: bold; }
    .test-value { width: 60%; }
</style>';
echo '</head><body>';
echo '<div class="container">';
echo '<h1>Laravel Language Debug</h1>';

// Basic app settings
echo '<div class="debug-box">';
echo '<h2>Application Settings</h2>';
echo '<p>app()->getLocale(): <span class="value">' . app()->getLocale() . '</span></p>';
echo '<p>is_rtl() function: <span class="value">' . (function_exists('is_rtl') ? (is_rtl() ? 'true' : 'false') : 'function not available') . '</span></p>';
echo '<p>session(\'locale\'): <span class="value">' . (session('locale') ?? 'null') . '</span></p>';
echo '<p>Request::header(\'Accept-Language\'): <span class="value">' . request()->header('Accept-Language') . '</span></p>';
echo '</div>';

// Translation Tests
echo '<div class="debug-box">';
echo '<h2>Translation Tests</h2>';

// Test key strings that appear in the website
$testStrings = [
    'Featured Product',
    'View Details',
    'View details',
    'Explore Category',
    'Add to Cart',
    'Offer ends:',
    'OFF',
    'Est.',
    'Cruelty-Free',
    '100% Natural',
    'Experience the Cosmos',
    'Scroll to explore',
    'Category'
];

echo '<div class="card">';
foreach ($testStrings as $string) {
    echo '<div class="test-row">';
    echo '<div class="test-label">' . $string . '</div>';
    
    // Test __ function
    $translatedString = __($string);
    echo '<div class="test-value">__(): ' . $translatedString . ' ';
    if ($translatedString === $string) {
        echo '<span style="color:red">(not translated)</span>';
    } else {
        echo '<span style="color:green">(translated)</span>';
    }
    echo '</div>';
    
    echo '</div>';
    
    // Test TranslationHelper if available
    if (class_exists('\App\Helpers\TranslationHelper')) {
        echo '<div class="test-row">';
        echo '<div class="test-label">' . $string . ' (TranslationHelper)</div>';
        $translatedWithHelper = \App\Helpers\TranslationHelper::get($string, $string);
        echo '<div class="test-value">TranslationHelper::get(): ' . $translatedWithHelper . ' ';
        if ($translatedWithHelper === $string) {
            echo '<span style="color:red">(not translated)</span>';
        } else {
            echo '<span style="color:green">(translated)</span>';
        }
        echo '</div>';
        echo '</div>';
    }
    
    // Test using app()->getLocale() vs 'ar' directly
    echo '<div class="test-row">';
    echo '<div class="test-label">' . $string . ' (Conditional Check)</div>';
    $conditionalString = app()->getLocale() === 'ar' ? 'Arabic version' : 'English version';
    echo '<div class="test-value">app()->getLocale() === \'ar\': ' . $conditionalString . '</div>';
    echo '</div>';
    
    echo '<hr>';
}
echo '</div>';
echo '</div>';

// Session data
echo '<div class="debug-box">';
echo '<h2>Session Data</h2>';
echo '<table>';
echo '<tr><th>Key</th><th>Value</th></tr>';
foreach (session()->all() as $key => $value) {
    echo '<tr>';
    echo '<td>' . $key . '</td>';
    echo '<td>' . (is_array($value) || is_object($value) ? json_encode($value) : $value) . '</td>';
    echo '</tr>';
}
echo '</table>';
echo '</div>';

// Config settings
echo '<div class="debug-box">';
echo '<h2>Config Settings</h2>';
echo '<p>config(\'app.locale\'): <span class="value">' . config('app.locale') . '</span></p>';
echo '<p>config(\'app.fallback_locale\'): <span class="value">' . config('app.fallback_locale') . '</span></p>';
echo '<p>config(\'app.available_locales\'): <span class="value">' . json_encode(config('app.available_locales', [])) . '</span></p>';
echo '</div>';

// Translation files check
echo '<div class="debug-box">';
echo '<h2>Translation Files Check</h2>';
echo '<table>';
echo '<tr><th>Locale</th><th>Path</th><th>Exists</th></tr>';

$locales = ['en', 'ar'];
foreach ($locales as $locale) {
    $langPath = resource_path("lang/{$locale}");
    $jsonPath = resource_path("lang/{$locale}.json");
    $messagesPath = resource_path("lang/{$locale}/messages.php");
    
    echo '<tr>';
    echo '<td>' . $locale . '</td>';
    echo '<td>' . $langPath . '</td>';
    echo '<td>' . (file_exists($langPath) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>') . '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<td>' . $locale . '</td>';
    echo '<td>' . $jsonPath . '</td>';
    echo '<td>' . (file_exists($jsonPath) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>') . '</td>';
    echo '</tr>';
    
    echo '<tr>';
    echo '<td>' . $locale . '</td>';
    echo '<td>' . $messagesPath . '</td>';
    echo '<td>' . (file_exists($messagesPath) ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>') . '</td>';
    echo '</tr>';
}
echo '</table>';

// Check if JSON translations exist
$arJsonPath = resource_path("lang/ar.json");
if (file_exists($arJsonPath)) {
    $translations = json_decode(file_get_contents($arJsonPath), true);
    echo '<h3>Arabic JSON Translations</h3>';
    echo '<p>Total translations: ' . count($translations) . '</p>';
    
    // Show first 10 translations as a sample
    echo '<table>';
    echo '<tr><th>Key</th><th>Translation</th></tr>';
    $counter = 0;
    foreach ($translations as $key => $value) {
        if ($counter < 10) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($key) . '</td>';
            echo '<td>' . htmlspecialchars($value) . '</td>';
            echo '</tr>';
        }
        $counter++;
    }
    echo '</table>';
    
    // Check for our test strings
    echo '<h3>Test Strings in JSON</h3>';
    echo '<table>';
    echo '<tr><th>String</th><th>Found in JSON</th><th>Translation</th></tr>';
    foreach ($testStrings as $string) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($string) . '</td>';
        $found = isset($translations[$string]);
        echo '<td>' . ($found ? '<span style="color:green">Yes</span>' : '<span style="color:red">No</span>') . '</td>';
        echo '<td>' . ($found ? htmlspecialchars($translations[$string]) : '-') . '</td>';
        echo '</tr>';
    }
    echo '</table>';
}
echo '</div>';

// Request data
echo '<div class="debug-box">';
echo '<h2>Request Information</h2>';
echo '<p>Current URL: <span class="value">' . url()->current() . '</span></p>';
echo '<p>Full URL: <span class="value">' . url()->full() . '</span></p>';
echo '<p>Path: <span class="value">' . request()->path() . '</span></p>';
echo '</div>';

// Cookie data
echo '<div class="debug-box">';
echo '<h2>Cookie Information</h2>';
echo '<table>';
echo '<tr><th>Cookie Name</th><th>Value</th></tr>';
foreach ($_COOKIE as $name => $value) {
    echo '<tr>';
    echo '<td>' . htmlspecialchars($name) . '</td>';
    echo '<td>' . htmlspecialchars($value) . '</td>';
    echo '</tr>';
}
echo '</table>';
echo '</div>';

// Switch language links
echo '<div class="debug-box">';
echo '<h2>Language Switch Links</h2>';
echo '<p><a href="?locale=en" style="padding:10px; background:#4CAF50; color:white; text-decoration:none; margin-right:10px;">Switch to English</a>';
echo '<a href="?locale=ar" style="padding:10px; background:#2196F3; color:white; text-decoration:none;">Switch to Arabic</a></p>';
echo '</div>';

// Server environment
echo '<div class="debug-box">';
echo '<h2>Server Environment</h2>';
echo '<p>PHP Version: <span class="value">' . PHP_VERSION . '</span></p>';
echo '<p>Server Software: <span class="value">' . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . '</span></p>';
echo '<p>User Agent: <span class="value">' . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown') . '</span></p>';
echo '</div>';

echo '</div>';
echo '</body></html>';
exit; 