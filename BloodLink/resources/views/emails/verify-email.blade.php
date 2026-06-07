<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #e53e3e, #c53030); padding: 32px 40px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; letter-spacing: 1px;">BloodLink</h1>
                            <p style="color: #feb2b2; margin: 8px 0 0 0; font-size: 14px;">Connecting Donors, Saving Lives</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 40px;">
                            <h2 style="color: #2d3748; margin: 0 0 16px 0; font-size: 22px;">Verify Your Email</h2>
                            <p style="color: #718096; line-height: 1.6; margin: 0 0 24px 0;">
                                Hello <strong style="color: #2d3748;">{{ $user->name }}</strong>,<br><br>
                                Thank you for registering on BloodLink. Please click the button below to verify your email address and activate your account.
                            </p>
                            <table cellpadding="0" cellspacing="0" style="margin: 0 auto 32px auto;">
                                <tr>
                                    <td style="background-color: #e53e3e; border-radius: 6px; text-align: center;">
                                        <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 14px 36px; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; letter-spacing: 0.5px;">Verify Email</a>
                                    </td>
                                </tr>
                            </table>
                            <p style="color: #718096; line-height: 1.6; margin: 0 0 16px 0; font-size: 14px;">
                                If the button doesn't work, copy and paste this link into your browser:
                            </p>
                            <p style="color: #e53e3e; line-height: 1.4; margin: 0 0 24px 0; font-size: 13px; word-break: break-all; background-color: #fff5f5; padding: 12px; border-radius: 4px;">
                                {{ $verificationUrl }}
                            </p>
                            <hr style="border: none; border-top: 1px solid #e2e8f0; margin: 32px 0;">
                            <p style="color: #a0aec0; font-size: 12px; line-height: 1.5; margin: 0;">
                                If you did not create an account on BloodLink, please ignore this email.<br>
                                &copy; {{ date('Y') }} BloodLink. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
