<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - GAS</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background: linear-gradient(135deg, #FFE4F3 0%, #FFEEF2 50%, #FFF0F5 100%); min-height: 100vh; padding: 40px 20px;">
    
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background: rgba(255, 255, 255, 0.95); border-radius: 20px; box-shadow: 0 20px 50px rgba(255, 146, 194, 0.15); overflow: hidden;">
        
        <!-- Header with Logo -->
        <tr>
            <td style="padding: 40px 40px 20px; text-align: center; background: linear-gradient(135deg, #FF92C2 0%, #FFC8FB 100%);">
                <div style="margin: 0 auto 20px;">
                    <img src="{{ $message->embed(public_path('assets/app_logo.PNG')) }}" alt="GAS Logo" style="width: 80px; height: 80px; border-radius: 16px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); background: white; padding: 8px;">
                </div>
                <h1 style="margin: 0; color: white; font-size: 28px; font-weight: bold; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    🔐 Password Reset Request
                </h1>
            </td>
        </tr>

        <!-- Content -->
        <tr>
            <td style="padding: 40px;">
                <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #374151;">
                    Hello <strong>{{ $name }}</strong>! 👋
                </p>
                
                <p style="margin: 0 0 20px; font-size: 16px; line-height: 1.6; color: #374151;">
                    We received a request to reset your password for your <strong style="color: #FF92C2;">GAS Account</strong>.
                </p>

                <p style="margin: 0 0 30px; font-size: 16px; line-height: 1.6; color: #374151;">
                    Click the button below to create a new password:
                </p>

                <!-- Button -->
                <div style="text-align: center; margin: 30px 0;">
                    <a href="{{ $resetUrl }}" 
                       style="display: inline-block; padding: 16px 40px; background: linear-gradient(135deg, #FF92C2 0%, #FFC8FB 100%); color: white; text-decoration: none; font-size: 16px; font-weight: 600; border-radius: 12px; box-shadow: 0 4px 15px rgba(255, 146, 194, 0.3); transition: all 0.3s;">
                        Reset Password
                    </a>
                </div>

                <!-- Alternative Link -->
                <p style="margin: 30px 0 20px; font-size: 14px; line-height: 1.6; color: #6B7280; text-align: center;">
                    Or copy and paste this link into your browser:
                </p>
                <p style="margin: 0 0 30px; font-size: 13px; line-height: 1.6; color: #9CA3AF; word-break: break-all; background: #F9FAFB; padding: 12px; border-radius: 8px; border-left: 4px solid #FF92C2;">
                    {{ $resetUrl }}
                </p>

                <!-- Warning Box -->
                <div style="background: linear-gradient(135deg, #FFF0F5 0%, #FFE4F3 100%); border-left: 4px solid #FF92C2; padding: 16px; border-radius: 8px; margin: 30px 0;">
                    <p style="margin: 0 0 10px; font-size: 14px; line-height: 1.6; color: #374151; font-weight: 600;">
                        ⏰ Important:
                    </p>
                    <p style="margin: 0; font-size: 14px; line-height: 1.6; color: #6B7280;">
                        This password reset link will expire in <strong>60 minutes</strong>.
                    </p>
                </div>

                <p style="margin: 0 0 20px; font-size: 14px; line-height: 1.6; color: #6B7280;">
                    If you didn't request a password reset, please ignore this email. Your password will remain unchanged.
                </p>

                <!-- Security Tip -->
                <div style="background: #F9FAFB; border-radius: 8px; padding: 16px; margin: 20px 0;">
                    <p style="margin: 0; font-size: 13px; line-height: 1.6; color: #6B7280;">
                        🔒 <strong>Security Tip:</strong> Never share your password with anyone. GAS will never ask for your password via email.
                    </p>
                </div>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 30px 40px; background: #F9FAFB; border-top: 1px solid #E5E7EB;">
                <p style="margin: 0 0 10px; font-size: 14px; line-height: 1.6; color: #374151;">
                    Stay secure,<br>
                    <strong style="color: #FF92C2;">The GAS Team</strong> 💖
                </p>
                
                <p style="margin: 20px 0 0; font-size: 12px; line-height: 1.6; color: #9CA3AF; text-align: center;">
                    © {{ date('Y') }} GAS. All rights reserved.
                </p>
            </td>
        </tr>

    </table>

</body>
</html>