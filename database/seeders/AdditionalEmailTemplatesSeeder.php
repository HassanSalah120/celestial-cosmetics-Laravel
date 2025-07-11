<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdditionalEmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if table exists
        if (!Schema::hasTable('email_templates')) {
            $this->command->info('email_templates table does not exist. Skipping AdditionalEmailTemplatesSeeder.');
            return;
        }
        
        $templates = [
            [
                'name' => 'Contact Form Response',
                'code' => 'contact_form_response',
                'description' => 'Sent when someone submits the contact form',
                'subject' => 'Thank you for contacting Celestial Cosmetics',
                'body_html' => $this->getContactFormTemplate(),
                'available_variables' => json_encode(['name', 'email', 'message']),
                'is_active' => true,
                'include_header_footer' => true,
            ],
            [
                'name' => 'Newsletter Subscription',
                'code' => 'newsletter_subscription',
                'description' => 'Sent to new newsletter subscribers',
                'subject' => 'Welcome to the Celestial Cosmetics Newsletter',
                'body_html' => $this->getNewsletterTemplate(),
                'available_variables' => json_encode(['email', 'unsubscribe_link']),
                'is_active' => true,
                'include_header_footer' => true,
            ],
            [
                'name' => 'Account Verification',
                'code' => 'account_verification',
                'description' => 'Sent to verify new user accounts',
                'subject' => 'Verify Your Celestial Cosmetics Account',
                'body_html' => $this->getVerificationTemplate(),
                'available_variables' => json_encode(['name', 'verification_link']),
                'is_active' => true,
                'include_header_footer' => true,
            ],
            [
                'name' => 'Order Delivered',
                'code' => 'order_delivered',
                'description' => 'Sent when an order is delivered',
                'subject' => 'Your Celestial Cosmetics Order Has Been Delivered!',
                'body_html' => $this->getOrderDeliveredTemplate(),
                'available_variables' => json_encode(['customer_name', 'order_number', 'order_date']),
                'is_active' => true,
                'include_header_footer' => true,
            ],
            [
                'name' => 'Refund Confirmation',
                'code' => 'refund_confirmation',
                'description' => 'Sent when a refund is processed',
                'subject' => 'Your Refund Has Been Processed',
                'body_html' => $this->getRefundTemplate(),
                'available_variables' => json_encode(['customer_name', 'order_number', 'refund_amount', 'refund_date']),
                'is_active' => true,
                'include_header_footer' => true,
            ],
        ];
        
        foreach ($templates as $template) {
            // Check if template already exists
            $existingTemplate = DB::table('email_templates')
                ->where('code', $template['code'])
                ->first();
                
            if (!$existingTemplate) {
                // Use DB::table instead of model to avoid potential issues
                DB::table('email_templates')->insert([
                    'name' => $template['name'],
                    'code' => $template['code'],
                    'description' => $template['description'],
                    'subject' => $template['subject'],
                    'body_html' => $template['body_html'],
                    'available_variables' => $template['available_variables'],
                    'is_active' => $template['is_active'],
                    'include_header_footer' => $template['include_header_footer'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $this->command->info("Created email template: {$template['code']}");
            } else {
                $this->command->info("Email template {$template['code']} already exists - skipping");
            }
        }
    }

    private function getContactFormTemplate()
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <div style="padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <!-- Logo Header -->
            <div style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                <img src="https://celestial-cosmetics.com/storage/logo.jpg" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
            </div>
            
            <!-- Title Bar -->
            <div style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">We've Received Your Message</h1>
            </div>
            
            <!-- Main Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Hello {{name}},</p>
                
                <p style="margin: 16px 0; line-height: 1.6;">Thank you for contacting Celestial Cosmetics. We've received your message and will get back to you as soon as possible, typically within 24-48 hours.</p>
                
                <div style="background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #2B5B6C; margin-top: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Your Message</h3>
                    <p style="margin: 16px 0; line-height: 1.6; font-style: italic;">{{message}}</p>
                </div>
                
                <p style="margin: 16px 0; line-height: 1.6;">In the meantime, feel free to explore our collection of premium beauty products crafted with the finest cosmic ingredients.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://celestial-cosmetics.com/products" style="display: inline-block; background-color: #D4AF37; color: white; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Explore Our Products</a>
                </div>
                
                <div style="height: 1px; background-color: #E2E8F0; margin: 30px 0;"></div>
                <p style="font-style: italic; margin: 0 0 8px 0;">We appreciate your interest in Celestial Cosmetics.</p>
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
                
                <p style="margin: 20px 0 0 0;">© 2025 Celestial Cosmetics. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getNewsletterTemplate()
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <div style="padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <!-- Logo Header -->
            <div style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                <img src="https://celestial-cosmetics.com/storage/logo.jpg" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
            </div>
            
            <!-- Title Bar -->
            <div style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">Welcome to Our Newsletter!</h1>
            </div>
            
            <!-- Main Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Thank you for subscribing!</p>
                
                <p style="margin: 16px 0; line-height: 1.6;">You're now subscribed to the Celestial Cosmetics newsletter with email: {{email}}. You'll be the first to know about:</p>
                
                <div style="background: linear-gradient(to right, rgba(43, 91, 108, 0.1), rgba(212, 175, 55, 0.1)); padding: 20px; border-radius: 8px; margin: 25px 0;">
                    <ul style="padding-left: 20px; margin: 0;">
                        <li style="margin-bottom: 10px;">New product launches</li>
                        <li style="margin-bottom: 10px;">Exclusive subscriber offers</li>
                        <li style="margin-bottom: 10px;">Seasonal promotions</li>
                        <li style="margin-bottom: 10px;">Beauty tips and tutorials</li>
                        <li style="margin-bottom: 0;">Special events</li>
                    </ul>
                </div>
                
                <p style="margin: 16px 0; line-height: 1.6;">Keep an eye on your inbox for our next newsletter. We typically send 2-4 emails per month and promise never to overwhelm your inbox.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://celestial-cosmetics.com/products" style="display: inline-block; background-color: #D4AF37; color: white; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Shop Now</a>
                </div>
                
                <div style="height: 1px; background-color: #E2E8F0; margin: 30px 0;"></div>
                <p style="font-style: italic; margin: 0 0 8px 0;">Stay celestial!</p>
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
                
                <p style="margin: 20px 0 0 0;">© 2025 Celestial Cosmetics. All rights reserved.</p>
                <p style="margin: 10px 0 0 0; font-size: 12px;">
                    <a href="{{unsubscribe_link}}" style="color: #2B5B6C; text-decoration: underline;">Unsubscribe</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getVerificationTemplate()
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <div style="padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <!-- Logo Header -->
            <div style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                <img src="https://celestial-cosmetics.com/storage/logo.jpg" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
            </div>
            
            <!-- Title Bar -->
            <div style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">Verify Your Email Address</h1>
            </div>
            
            <!-- Main Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Hello {{name}},</p>
                
                <p style="margin: 16px 0; line-height: 1.6;">Thank you for creating an account with Celestial Cosmetics. To complete your registration and access all features, please verify your email address by clicking the button below:</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{verification_link}}" style="display: inline-block; background-color: #D4AF37; color: white; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Verify Email Address</a>
                </div>
                
                <p style="margin: 16px 0; line-height: 1.6;">This verification link will expire in 24 hours. If you did not create an account, no further action is required.</p>
                
                <div style="background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <p style="margin: 0; line-height: 1.6;">If the button above doesn't work, please copy and paste the following URL into your web browser:</p>
                    <p style="margin: 10px 0 0 0; word-break: break-all; font-family: monospace; font-size: 12px; background-color: #EDF2F7; padding: 10px; border-radius: 4px;">{{verification_link}}</p>
                </div>
                
                <div style="height: 1px; background-color: #E2E8F0; margin: 30px 0;"></div>
                <p style="font-style: italic; margin: 0 0 8px 0;">Looking forward to having you as part of our celestial community!</p>
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
                
                <p style="margin: 20px 0 0 0;">© 2025 Celestial Cosmetics. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getOrderDeliveredTemplate()
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <div style="padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <!-- Logo Header -->
            <div style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                <img src="https://celestial-cosmetics.com/storage/logo.jpg" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
            </div>
            
            <!-- Title Bar -->
            <div style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">Your Order Has Been Delivered!</h1>
            </div>
            
            <!-- Main Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Hello {{customer_name}},</p>
                
                <p style="margin: 16px 0; line-height: 1.6;">Great news! Your Celestial Cosmetics order (#{{order_number}}) from {{order_date}} has been delivered.</p>
                
                <div style="background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #2B5B6C; margin-top: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">How did we do?</h3>
                    <p style="margin: 16px 0; line-height: 1.6;">We'd love to hear about your experience. Share your thoughts and help other customers discover our cosmic beauty products!</p>
                </div>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://celestial-cosmetics.com/account/orders/{{order_number}}/review" style="display: inline-block; background-color: #D4AF37; color: white; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Write a Review</a>
                </div>
                
                <p style="margin: 16px 0; line-height: 1.6;">If you have any issues with your order, please contact our customer service team right away.</p>
                
                <div style="height: 1px; background-color: #E2E8F0; margin: 30px 0;"></div>
                <p style="font-style: italic; margin: 0 0 8px 0;">Enjoy your celestial beauty products!</p>
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
                
                <p style="margin: 20px 0 0 0;">© 2025 Celestial Cosmetics. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    private function getRefundTemplate()
    {
        return <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <div style="padding: 20px;">
        <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
            <!-- Logo Header -->
            <div style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                <img src="https://celestial-cosmetics.com/storage/logo.jpg" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
            </div>
            
            <!-- Title Bar -->
            <div style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">Refund Confirmation</h1>
            </div>
            
            <!-- Main Content -->
            <div style="padding: 40px 30px; background-color: #ffffff;">
                <p style="font-size: 18px; color: #2B5B6C; font-weight: 500;">Hello {{customer_name}},</p>
                
                <p style="margin: 16px 0; line-height: 1.6;">We've processed your refund for order #{{order_number}}. Here's a summary of the refund details:</p>
                
                <div style="background-color: #f8f9fa; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin: 25px 0;">
                    <h3 style="color: #2B5B6C; margin-top: 0; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px;">Refund Details</h3>
                    <p style="margin: 8px 0; line-height: 1.6;"><strong>Refund Amount:</strong> {{refund_amount}}</p>
                    <p style="margin: 8px 0; line-height: 1.6;"><strong>Refund Date:</strong> {{refund_date}}</p>
                    <p style="margin: 8px 0; line-height: 1.6;"><strong>Original Order:</strong> #{{order_number}}</p>
                    <p style="margin: 16px 0 0 0; line-height: 1.6;">The refund has been processed to your original payment method. Depending on your bank or card issuer, it may take 5-10 business days for the refund to appear in your account.</p>
                </div>
                
                <p style="margin: 16px 0; line-height: 1.6;">We're sorry that your recent purchase didn't meet your expectations. We value your feedback and would appreciate hearing about your experience to help us improve.</p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <a href="https://celestial-cosmetics.com/products" style="display: inline-block; background-color: #D4AF37; color: white; text-decoration: none; padding: 14px 40px; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);">Continue Shopping</a>
                </div>
                
                <div style="height: 1px; background-color: #E2E8F0; margin: 30px 0;"></div>
                <p style="font-style: italic; margin: 0 0 8px 0;">Thank you for your understanding.</p>
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
                
                <p style="margin: 20px 0 0 0;">© 2025 Celestial Cosmetics. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }
} 