<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Celestial Cosmetics</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <div style="padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <!-- Logo Header -->
            <div style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                <img src="{{ asset('storage/logo.jpg') }}" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
            </div>
            
            <!-- Title Bar -->
            <div style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">Welcome to Celestial Cosmetics</h1>
            </div>
            
            <!-- Main Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Hello {{name}},</p>
                
                <p style="margin: 16px 0; line-height: 1.6;">Thank you for creating an account with us. We're excited to have you as part of our community!</p>
                
                <div style="background: linear-gradient(to right, rgba(43, 91, 108, 0.1), rgba(212, 175, 55, 0.1)); padding: 20px; border-radius: 8px; margin: 25px 0;">
                    <h3 style="color: #2B5B6C; margin-top: 0;">With your new account, you can:</h3>
                    <ul style="padding-left: 20px;">
                        <li style="margin-bottom: 10px;">Track your orders</li>
                        <li style="margin-bottom: 10px;">Save your favorite products</li>
                        <li style="margin-bottom: 10px;">Checkout faster</li>
                        <li style="margin-bottom: 10px;">Receive exclusive offers</li>
                    </ul>
                </div>
                
                <p style="margin: 16px 0; line-height: 1.6;">If you have any questions, feel free to contact our customer service team.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ url('/products') }}" style="display: inline-block; background-color: #D4AF37; color: white; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Explore Products</a>
                </div>
                
                <div style="height: 1px; background-color: #E2E8F0; margin: 30px 0;"></div>
                <p style="font-style: italic; margin: 0 0 8px 0;">Happy shopping!</p>
                <p style="color: #2B5B6C; font-weight: 600; margin: 0;">The Celestial Cosmetics Team</p>
            </div>
            
            <!-- Footer -->
            <div style="background-color: #f8fafc; padding: 25px 30px; text-align: center; border-top: 1px solid #e2e8f0; font-size: 13px; color: #64748b;">
                <div style="margin: 15px 0;">
                    <p style="margin: 0 0 10px 0;">Connect with us</p>
                    <div>
                        <a href="#" style="display: inline-block; margin: 0 5px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24" height="24" alt="Facebook" style="width: 24px; height: 24px;">
                        </a>
                        <a href="#" style="display: inline-block; margin: 0 5px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="24" height="24" alt="Instagram" style="width: 24px; height: 24px;">
                        </a>
                        <a href="#" style="display: inline-block; margin: 0 5px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/733/733635.png" width="24" height="24" alt="Twitter" style="width: 24px; height: 24px;">
                        </a>
                    </div>
                </div>
                
                <p style="margin: 20px 0 0 0;">© {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html> 