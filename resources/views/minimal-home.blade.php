<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minimal Home</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .section { margin-bottom: 30px; border: 1px solid #ccc; padding: 15px; }
    </style>
</head>
<body>
    <h1>Minimal Home Page</h1>
    
    <div class="section">
        <h2>Featured Products</h2>
        <p>Total: {{ count($featuredProducts) }}</p>
    </div>
    
    <div class="section">
        <h2>New Arrivals</h2>
        <p>Total: {{ count($newArrivals) }}</p>
    </div>
    
    <div class="section">
        <h2>Categories</h2>
        <p>Total: {{ count($categories) }}</p>
    </div>
    
    <div class="section">
        <h2>Testimonials</h2>
        <p>Total: {{ count($featuredTestimonials) }}</p>
        
        @foreach($featuredTestimonials as $testimonial)
        <div style="border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
            <p><strong>ID:</strong> {{ $testimonial['id'] }}</p>
            <p><strong>Title:</strong> {{ isset($testimonial['title']) ? (is_string($testimonial['title']) ? $testimonial['title'] : json_encode($testimonial['title'])) : 'N/A' }}</p>
            <p><strong>Customer:</strong> {{ isset($testimonial['customer_name']) ? (is_string($testimonial['customer_name']) ? $testimonial['customer_name'] : json_encode($testimonial['customer_name'])) : 'N/A' }}</p>
            <p><strong>Rating:</strong> {{ $testimonial['rating'] ?? 'N/A' }}</p>
            <p><strong>Message:</strong> {{ isset($testimonial['message']) ? (is_string($testimonial['message']) ? substr($testimonial['message'], 0, 100) . '...' : json_encode($testimonial['message'])) : 'N/A' }}</p>
        </div>
        @endforeach
    </div>
    
    <div class="section">
        <h2>SEO Settings</h2>
        <p>Total: {{ count($homepageSeo) }}</p>
    </div>
</body>
</html> 