<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Testimonials</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .testimonial { margin-bottom: 30px; border: 1px solid #ccc; padding: 15px; }
    </style>
</head>
<body>
    <h1>Test Testimonials</h1>
    
    @foreach($testimonials as $testimonial)
    <div class="testimonial">
        <h3>ID: {{ $testimonial['id'] }}</h3>
        <p><strong>Title:</strong> "{{ $testimonial['title'] }}"</p>
        <p><strong>Message:</strong> "{{ $testimonial['message'] }}"</p>
        <p><strong>Customer:</strong> {{ $testimonial['customer_name'] }}</p>
        <p><strong>Rating:</strong> {{ $testimonial['rating'] }}</p>
    </div>
    @endforeach
</body>
</html> 