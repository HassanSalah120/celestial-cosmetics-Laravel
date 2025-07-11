<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }
            
            .footer {
                width: 100% !important;
            }
        }
        
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f5f8fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333333;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0; padding: 20px 0; background-color: #f5f8fa;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto; max-width: 600px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Logo Header -->
                    <tr>
                        <td style="background-color: #2B5B6C; text-align: center; padding: 20px;">
                            <img src="{{ asset('storage/logo.jpg') }}" alt="Celestial Cosmetics" style="max-height: 80px; width: auto;">
                        </td>
                    </tr>
                    
                    <!-- Main Header with gradient -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2B5B6C 0%, #1A3F4C 100%); padding: 40px 20px; text-align: center; border-bottom: 4px solid #D4AF37;">
                            @if (! empty($greeting))
                                <div style="max-width: 500px; margin: 0 auto;">
                                    <h1 style="color: #D4AF37; font-size: 32px; margin: 0; font-weight: bold; font-family: 'Playfair Display', Georgia, serif; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2); line-height: 1.4;">{{ $greeting }}</h1>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px; background-color: #ffffff;">
                            {{-- Intro Lines --}}
                            @foreach ($introLines as $line)
                                <p style="margin: 0 0 20px 0; color: #2C3E50; line-height: 1.8; font-size: 16px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">{{ $line }}</p>
                            @endforeach

                            {{-- Action Button --}}
                            @isset($actionText)
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                    <tr>
                                        <td align="center" style="padding: 30px 0;">
                                            <a href="{{ $actionUrl }}" 
                                               style="display: inline-block; padding: 14px 40px; background-color: #D4AF37; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15); transition: all 0.3s ease;">
                                                {{ $actionText }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            @endisset

                            {{-- Outro Lines with styled bullets --}}
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                                <tr>
                                    <td>
                                        @foreach ($outroLines as $line)
                                            @if (trim($line) === '')
                                                <div style="height: 16px;"></div>
                                            @else
                                                @if (!str_contains($line, 'If you did not create an account'))
                                                    <div style="margin: 12px 0; padding-left: 24px; color: #2C3E50; line-height: 1.6; font-size: 16px; position: relative;">
                                                        <span style="display: inline-block; width: 6px; height: 6px; background-color: #D4AF37; border-radius: 50%; position: absolute; left: 8px; top: 10px;"></span>
                                                        <span style="margin-left: 8px;">{{ $line }}</span>
                                                    </div>
                                                @else
                                                    <p style="margin: 25px 0 0 0; color: #64748b; font-size: 14px;">{{ $line }}</p>
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            </table>

                            {{-- Salutation --}}
                            @if (! empty($salutation))
                                <div style="margin-top: 40px; padding-top: 25px; border-top: 1px solid #E2E8F0;">
                                    <p style="margin: 0 0 8px 0; color: #2C3E50; font-size: 16px; font-style: italic;">{{ $salutation }}</p>
                                    <p style="margin: 0; color: #2B5B6C; font-weight: 600; font-size: 16px;">The Celestial Cosmetics Team</p>
                                </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 25px 30px; text-align: center; border-top: 1px solid #e2e8f0; border-radius: 0 0 8px 8px;">
                            @isset($actionText)
                                <p style="margin: 0 0 20px 0; font-size: 13px; color: #64748b; line-height: 1.6;">
                                    If you're having trouble clicking the "{{ $actionText }}" button,
                                    copy and paste the URL below into your web browser:
                                    <br><br>
                                    <a href="{{ $actionUrl }}" style="color: #2B5B6C; text-decoration: none; word-break: break-all; font-family: monospace; background-color: #EDF2F7; padding: 8px; border-radius: 4px; display: inline-block;">
                                        {{ $actionUrl }}
                                    </a>
                                </p>
                            @endisset
                            
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 15px;">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 10px 0; color: #64748b; font-size: 13px;">Connect with us</p>
                                        <div>
                                            <a href="#" style="display: inline-block; margin: 0 5px; color: #2B5B6C; text-decoration: none;">
                                                <img src="https://cdn-icons-png.flaticon.com/512/733/733547.png" width="24" height="24" alt="Facebook">
                                            </a>
                                            <a href="#" style="display: inline-block; margin: 0 5px; color: #2B5B6C; text-decoration: none;">
                                                <img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="24" height="24" alt="Instagram">
                                            </a>
                                            <a href="#" style="display: inline-block; margin: 0 5px; color: #2B5B6C; text-decoration: none;">
                                                <img src="https://cdn-icons-png.flaticon.com/512/733/733635.png" width="24" height="24" alt="Twitter">
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            
                            <p style="margin: 20px 0 0 0; color: #64748b; font-size: 13px;">© {{ date('Y') }} Celestial Cosmetics. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html> 