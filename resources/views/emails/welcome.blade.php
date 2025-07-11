@extends('layouts.email')

@section('title', 'Welcome to Celestial Cosmetics')

@section('heading', 'Welcome to Celestial Cosmetics')

@section('content')
    <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Hello {{$name}},</p>
    
    <p>Thank you for creating an account with us. We're excited to have you as part of our community of beauty enthusiasts!</p>
    
    <div style="background: linear-gradient(to right, rgba(43, 91, 108, 0.1), rgba(212, 175, 55, 0.1)); padding: 20px; border-radius: 8px; margin: 25px 0;">
        <h3 style="color: #2B5B6C; margin-top: 0;">With your new account, you can:</h3>
        <ul style="padding-left: 20px;">
            <li style="margin-bottom: 10px;">Track your orders and view order history</li>
            <li style="margin-bottom: 10px;">Save your favorite products for quick access</li>
            <li style="margin-bottom: 10px;">Checkout faster with saved shipping information</li>
            <li style="margin-bottom: 10px;">Receive exclusive offers and early access to new products</li>
        </ul>
    </div>
    
    <p>To get started, explore our collection of premium cosmetics crafted with rare ingredients from around the cosmos.</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/products') }}" class="button">Explore Products</a>
    </div>
    
    <table width="100%" cellspacing="0" cellpadding="0" style="margin: 40px 0;">
        <tr>
            <td width="30%" style="text-align: center; padding: 10px;">
                <img src="{{ asset('storage/1 (4).jpg') }}" alt="Skincare" style="width: 100%; border-radius: 100%; max-width: 100px; height: auto;">
                <p style="font-weight: bold; color: #2B5B6C; margin-bottom: 5px;">Skincare</p>
                <p style="font-size: 13px; margin-top: 0;">Transform your routine</p>
            </td>
            <td width="30%" style="text-align: center; padding: 10px;">
                <img src="{{ asset('storage/1 (5).jpg') }}" alt="Makeup" style="width: 100%; border-radius: 100%; max-width: 100px; height: auto;">
                <p style="font-weight: bold; color: #2B5B6C; margin-bottom: 5px;">Makeup</p>
                <p style="font-size: 13px; margin-top: 0;">Express yourself</p>
            </td>
            <td width="30%" style="text-align: center; padding: 10px;">
                <img src="{{ asset('storage/1 (6).jpg') }}" alt="Fragrances" style="width: 100%; border-radius: 100%; max-width: 100px; height: auto;">
                <p style="font-weight: bold; color: #2B5B6C; margin-bottom: 5px;">Fragrances</p>
                <p style="font-size: 13px; margin-top: 0;">Captivate senses</p>
            </td>
        </tr>
    </table>
    
    <p>If you have any questions or need assistance, our customer service team is always ready to help.</p>
    
    <p>We look forward to helping you discover your perfect celestial glow!</p>
@endsection

@section('signature', 'Happy shopping!') 