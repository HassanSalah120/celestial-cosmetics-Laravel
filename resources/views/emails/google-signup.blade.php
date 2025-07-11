<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Celestial Cosmetics</title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            line-height: 1.6;
            color: #2C3E50;
            background-color: #F5F8FA;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background: linear-gradient(to right, #2B5B6C, #1A3F4C);
            border-radius: 8px 8px 0 0;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-family: 'Georgia', serif;
        }
        .content {
            padding: 0 30px 30px;
        }
        .highlight {
            color: #D4AF37;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #D4AF37;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            margin-top: 30px;
            border-top: 1px solid #e9ecef;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✨ Celestial Cosmetics ✨</h1>
        </div>
        
        <div class="content">
            <h2>Welcome, {{ $user->name }}!</h2>
            
            <p>Thank you for joining Celestial Cosmetics using your Google account. Your account has been automatically verified, and you're all set to explore our cosmic collection of beauty products!</p>
            
            <p>Your journey into <span class="highlight">stellar beauty</span> begins now:</p>
            
            <ul>
                <li>Browse our collection of celestial-inspired products</li>
                <li>Enjoy personalized recommendations based on your preferences</li>
                <li>Track your orders and deliveries with ease</li>
                <li>Access exclusive offers for our community members</li>
            </ul>
            
            <p style="text-align: center;">
                <a href="{{ config('app.url') }}" class="button">Start Shopping Now</a>
            </p>
            
            <p>If you have any questions or need assistance, our customer support team is always ready to help.</p>
            
            <p>Wishing you a magical experience,<br>The Celestial Cosmetics Team</p>
        </div>
        
        <div class="footer">
            <p>© {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
            <p>This email was sent to you because you registered at Celestial Cosmetics using your Google account.</p>
        </div>
    </div>
</body>
</html> 