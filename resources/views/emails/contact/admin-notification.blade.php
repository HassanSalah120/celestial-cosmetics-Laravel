<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Contact Form Submission</title>
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
            background-color: #2B5B6C;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .field {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
            color: #2B5B6C;
        }
        .message {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
            border-left: 3px solid #2B5B6C;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Contact Form Submission</h1>
    </div>
    
    <div class="content">
        <p>You have received a new message from your website's contact form.</p>
        
        <div class="field">
            <div class="label">Date/Time:</div>
            <div>{{ $createdAt }}</div>
        </div>
        
        <div class="field">
            <div class="label">Name:</div>
            <div>{{ $name }}</div>
        </div>
        
        <div class="field">
            <div class="label">Email:</div>
            <div>{{ $email }}</div>
        </div>
        
        @if($subject)
        <div class="field">
            <div class="label">Subject:</div>
            <div>{{ $subject }}</div>
        </div>
        @endif
        
        <div class="message">
            <div class="label">Message:</div>
            <div>{!! nl2br(e($messageContent)) !!}</div>
        </div>
        
        <p>Please respond to this inquiry at your earliest convenience.</p>
    </div>
    
    <div class="footer">
        <p>This is an automated notification from your Celestial Cosmetics website.</p>
    </div>
</body>
</html> 