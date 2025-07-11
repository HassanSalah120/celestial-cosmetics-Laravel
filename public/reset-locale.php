<?php
/**
 * EMERGENCY LOCALE RESET SCRIPT
 * 
 * This script completely resets the application's locale settings.
 * It operates outside the Laravel framework to avoid any cached settings or middleware issues.
 */

// Get the requested locale from URL parameter
$locale = isset($_GET['locale']) ? $_GET['locale'] : 'en';

// Validate locale (only allow en or ar)
if (!in_array($locale, ['en', 'ar'])) {
    $locale = 'en';
}

// Get the redirect URL if provided
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '/';

// Clean up the redirect URL (remove double slashes)
$redirect = ltrim($redirect, '/');
$redirect = '/' . $redirect;

// Fix any double slashes that might have been created
$redirect = preg_replace('#/+#', '/', $redirect);

// Start or resume session
session_start();

// Clear ALL session data
session_unset();
session_destroy();
session_start();

// Set the locale in session
$_SESSION['locale'] = $locale;

// Set cookie (1 year expiration)
setcookie('locale', $locale, time() + 60*60*24*365, '/', '', false, false);

// Optional: Clear other cookies that might cause issues
setcookie('laravel_session', '', time() - 3600, '/', '', false, false);

// Prepare to redirect with RTL/LTR settings
$isRtl = ($locale === 'ar');
$html = '<!DOCTYPE html>
<html lang="' . $locale . '" dir="' . ($isRtl ? 'rtl' : 'ltr') . '">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resetting Locale</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .loader {
            border: 16px solid #f3f3f3;
            border-top: 16px solid #3498db;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <h1>' . ($locale == 'ar' ? 'إعادة تعيين اللغة...' : 'Resetting Language...') . '</h1>
    <div class="loader"></div>
    <p>' . ($locale == 'ar' ? 'جارٍ إعادة تعيين إعدادات اللغة إلى العربية...' : 'Resetting language settings to English...') . '</p>
    <script>
        // Wait a moment to ensure all cookies and session changes take effect
        setTimeout(function() {
            window.location.href = "<?php echo htmlspecialchars($redirect); ?>?_=" + Date.now() + "&reset_locale=<?php echo $locale; ?>";
        }, 2000);
    </script>
</body>
</html>';

// Set headers to prevent caching
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: Sat, 01 Jan 2000 00:00:00 GMT');

// Output the HTML
echo $html;
exit; 