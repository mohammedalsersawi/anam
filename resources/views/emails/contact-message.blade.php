<!DOCTYPE html>
<html lang="ar" style="font-family: Arial, sans-serif;">
<head>
    <meta charset="UTF-8">
    <title>رسالة جديدة من نموذج التواصل</title>
</head>
<body style="background-color: #f9f9f9; padding: 20px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border: 1px solid #ddd; border-radius: 10px;">
        <tr>
            <td style="text-align: center; padding: 30px 20px;">
                {{-- Logo (عدلي الرابط حسب موقعك الفعلي) --}}
                <img src="https://images.pexels.com/photos/32480606/pexels-photo-32480606.jpeg" alt="شعار أنامل" style="max-width: 120px; margin-bottom: 20px;">

                <h2 style="color: #d32f2f;">رسالة جديدة من نموذج تواصل معنا</h2>

                <table width="100%" cellpadding="10" style="text-align: right; direction: rtl;">
                    <tr>
                        <td style="color: #333;"><strong>الاسم:</strong></td>
                        <td style="color: #555;">{{ $data['name'] }}</td>
                    </tr>
                    <tr>
                        <td style="color: #333;"><strong>البريد الإلكتروني:</strong></td>
                        <td style="color: #555;">{{ $data['email'] }}</td>
                    </tr>
                    <tr>
                        <td style="color: #333;"><strong>الموضوع:</strong></td>
                        <td style="color: #555;">{{ $data['subject'] }}</td>
                    </tr>
                    <tr>
                        <td style="color: #333;"><strong>نص الرسالة:</strong></td>
                        <td style="color: #555;">{{ $data['message'] }}</td>
                    </tr>
                </table>

                <p style="font-size: 14px; color: #999; margin-top: 30px;">
                    تم إرسال هذه الرسالة عبر نموذج "تواصل معنا" على موقع أنامل.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f1f1f1; text-align: center; padding: 15px; font-size: 12px; color: #888;">
                &copy; {{ date('Y') }} شركة أنامل للخدمات التعليمية. جميع الحقوق محفوظة.
            </td>
        </tr>
    </table>
</body>
</html>
