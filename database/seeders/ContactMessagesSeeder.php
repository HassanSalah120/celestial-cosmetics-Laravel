<?php

namespace Database\Seeders;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactMessagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if contact messages already exist
        if (ContactMessage::count() > 0) {
            $this->command->info('Contact messages already exist. Skipping...');
            return;
        }
        
        $this->command->info('Creating sample contact messages...');
        
        // Get a user ID for some messages (if users exist)
        $userId = User::where('email', 'customer@example.com')->first()?->id;
        
        // Create sample contact messages
        $messages = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'subject' => 'Product Inquiry',
                'message' => 'Hello, I\'m interested in your Celestial Glow Serum but I have sensitive skin. Is this product suitable for sensitive skin types? Thank you!',
                'status' => 'new',
                'user_id' => null,
            ],
            [
                'name' => 'Mohammed Ali',
                'email' => 'mohammed.ali@example.com',
                'subject' => 'Shipping Question',
                'message' => 'Hi there, I\'d like to know if you ship to Saudi Arabia and what the shipping costs would be for an order of around 300 EGP. Thanks!',
                'status' => 'new',
                'user_id' => null,
            ],
            [
                'name' => 'Emily Parker',
                'email' => 'emily.parker@example.com',
                'subject' => 'Order Status',
                'message' => 'I placed an order (#CE12345) three days ago and haven\'t received any shipping confirmation. Could you please check the status of my order?',
                'status' => 'new',
                'user_id' => null,
            ],
            [
                'name' => 'Customer User',
                'email' => 'customer@example.com',
                'subject' => 'Return Policy Question',
                'message' => 'What is your return policy for products that have been opened but caused an allergic reaction? I purchased the Cosmic Bath Elixir and unfortunately had a reaction to it.',
                'status' => 'new',
                'user_id' => $userId,
            ],
            [
                'name' => 'Aisha Rahman',
                'email' => 'aisha.rahman@example.com',
                'subject' => 'Wholesale Inquiry',
                'message' => 'I own a small boutique in Cairo and I\'m interested in stocking your products. Do you offer wholesale pricing and what would be the minimum order quantity?',
                'status' => 'new',
                'user_id' => null,
            ],
        ];
        
        foreach ($messages as $message) {
            ContactMessage::create($message);
        }
        
        // Create a message with a reply
        ContactMessage::create([
            'name' => 'Fatima Hassan',
            'email' => 'fatima.hassan@example.com',
            'subject' => 'Product Availability',
            'message' => 'When will the Starlight Shimmer Highlighter be back in stock? I\'ve been waiting to purchase it for weeks!',
            'status' => 'replied',
            'user_id' => null,
            'reply' => 'Thank you for your interest in our Starlight Shimmer Highlighter! We expect it to be back in stock within the next 7-10 days. We can notify you via email once it\'s available if you\'d like.',
            'replied_at' => now()->subDays(2),
        ]);
        
        $this->command->info('Sample contact messages created successfully!');
    }
}
