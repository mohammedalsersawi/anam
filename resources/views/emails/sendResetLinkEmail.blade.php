<!DOCTYPE html>
<html lang="en" style="font-family: Arial, sans-serif;">
<head>
    <meta charset="UTF-8">
    <title>{{ $data['subject'] }}</title>
</head>
<body style="background-color: #f9f9f9; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border: 1px solid #ddd; border-radius: 10px;">
        <tr>
            <td style="text-align: center; padding: 30px 20px;">
                {{-- Logo (use a valid direct image link) --}}
                <img src="https://yourdomain.com/logo.png" alt="Anamil Logo" style="max-width: 120px; margin-bottom: 20px;">

                <h2 style="color: #d32f2f;">{{ $data['subject'] }}</h2>

                <p style="color: #555; font-size: 16px;">{{ $data['message'] }}</p>

                <a href="{{ $data['reset_link'] }}" style="display: inline-block; background-color: #4CAF50; color: #fff; padding: 12px 24px; margin: 20px 0; text-decoration: none; border-radius: 5px;">
                    Reset Password
                </a>

                <p style="font-size: 14px; color: #999;">
                    If you didn’t request a password reset, you can safely ignore this email.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f1f1f1; text-align: center; padding: 15px; font-size: 12px; color: #888;">
                &copy; {{ date('Y') }} Anamil Educational Services Co. All rights reserved.
            </td>
        </tr>
    </table>
</body>
</html>
