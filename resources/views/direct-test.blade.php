<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Direct Test Page</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        .debug-box {
            background-color: #eef2ff;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
        }
        .card {
            background-color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <h1>Direct Test Page - No Layout</h1>
    
    <div class="debug-box">
        <h2>Debug Information</h2>
        <p>Time: {{ now() }}</p>
        <p>Route: {{ request()->route()->getName() }}</p>
        <p>URL: {{ url()->current() }}</p>
        <p>Content section exists in app.blade.php: {{ \Illuminate\Support\Facades\View::exists('layouts.app') ? 'Yes' : 'No' }}</p>
        <p>Content section exists in home.blade.php: {{ \Illuminate\Support\Facades\View::exists('home') ? 'Yes' : 'No' }}</p>
        
        <div style="margin-top: 1rem; padding: 1rem; background-color: #f7f7f7; border-radius: 0.5rem;">
            <h3>View Paths</h3>
            <ul>
                @foreach(config('view.paths') as $path)
                    <li>{{ $path }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    
    <div class="container">
        <div class="card">
            <h2>Test Card 1</h2>
            <p>This page doesn't use any layout or extends directive.</p>
        </div>
        <div class="card">
            <h2>Test Card 2</h2>
            <p>If you can see this, Blade is rendering correctly.</p>
        </div>
    </div>
</body>
</html> 