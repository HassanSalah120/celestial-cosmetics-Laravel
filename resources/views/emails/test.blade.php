@extends('layouts.email')

@section('title', 'Test Email - Celestial Cosmetics')

@section('heading', 'Test Email')

@section('content')
    <h2 style="color: #2B5B6C; margin-top: 0; margin-bottom: 20px; font-family: 'Playfair Display', Georgia, serif;">{{ $details['title'] }}</h2>
    
    <p>{{ $details['body'] }}</p>
    
    <div style="background-color: #f7f9fc; padding: 20px; border-radius: 6px; margin: 25px 0;">
        <p style="margin-top: 0;">This is a test email to confirm that the SMTP configuration is working correctly.</p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/') }}" class="button">Visit Our Website</a>
    </div>
    
    <p>If you received this email, it means your email service is configured properly.</p>
@endsection

@section('signature', 'Thank you for testing our email system!') 