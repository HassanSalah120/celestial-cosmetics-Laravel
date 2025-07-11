<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Response to Your Inquiry - Celestial Cosmetics</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2B5B6C, #1A3F4C);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 15px;
        }
        .content {
            padding: 30px 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .greeting {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .message {
            margin-bottom: 25px;
        }
        .message-box {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 3px solid #2B5B6C;
        }
        .cta {
            background-color: #2B5B6C;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .social {
            margin: 15px 0;
        }
        .social a {
            display: inline-block;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logo-white.png') }}" alt="Celestial Cosmetics" class="logo">
        <h1>Response to Your Inquiry</h1>
    </div>
    
    <div class="content">
        <div class="greeting">Hello {{ $contactMessage->name }},</div>
        
        <div class="message">
            <p>Thank you for reaching out to Celestial Cosmetics. We appreciate your interest in our products.</p>
            
            <p>Your original message:</p>
            <div class="message-box">
                <strong>Subject:</strong> {{ $contactMessage->subject }}<br>
                <strong>Date:</strong> {{ $contactMessage->created_at->format('M d, Y h:i A') }}<br><br>
                {{ $contactMessage->message }}
            </div>
            
            <p><strong>Our Response:</strong></p>
            <p>{!! nl2br(e($reply)) !!}</p>
            
            <p>If you have any other questions or concerns, please don't hesitate to contact us again.</p>
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('products.index') }}" class="cta">Shop Our Collection</a>
        </div>
        
        <div class="message">
            <p>Thank you for your interest in Celestial Cosmetics!</p>
            <p>Best regards,<br>The Celestial Cosmetics Team</p>
        </div>
    </div>
    
    <div class="footer">
        <div class="social">
            <a href="#"><img src="{{ asset('images/icons/facebook.png') }}" alt="Facebook" width="24"></a>
            <a href="#"><img src="{{ asset('images/icons/instagram.png') }}" alt="Instagram" width="24"></a>
            <a href="#"><img src="{{ asset('images/icons/twitter.png') }}" alt="Twitter" width="24"></a>
        </div>
        <p>&copy; {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
    </div>
</body>
</html> 