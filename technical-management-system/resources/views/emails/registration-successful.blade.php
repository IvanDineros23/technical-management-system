<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful</title>
</head>
<body style="margin:0;padding:0;background:#ececec;font-family:Arial,sans-serif;color:#1f2937;">

    @php
        $loginUrl = route('login');
        $logoUrl = 'https://gemarcph.com/images/gemarclogo.png';
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#ececec;padding:28px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #d8dfda;">
                    
                    <!-- HEADER -->
                    <tr>
                        <td align="center" style="padding:24px 24px 10px 24px;background:#ffffff;">
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto;">
                                <tr>
                                    <td align="center">
                                        <img 
                                            src="{{ $logoUrl }}" 
                                            alt="Gemarc Logo" 
                                            width="140"
                                            style="display:block;height:auto;"
                                        >
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:10px 34px 28px 34px;">
                            <h2 style="margin:0 0 14px 0;font-size:28px;line-height:1.25;color:#1f2937;">
                                Welcome, {{ $user->name }}
                            </h2>

                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;color:#374151;">
                                Thank you for registering in Gemarc TMS.
                            </p>

                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;color:#374151;">
                                Your registration was successful and your account is now pending admin approval.
                            </p>

                            <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#374151;">
                                We will notify you once your account has been activated.
                            </p>

                            <!-- BUTTON -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 18px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $loginUrl }}"
                                           style="display:inline-block;background:#16a34a;border-radius:999px;padding:12px 24px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;">
                                            Go to Login
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 8px;font-size:15px;line-height:1.7;color:#374151;">
                                Regards,<br>IT Department
                            </p>

                            <p style="margin:18px 0 0;padding-top:14px;border-top:1px solid #d8dfda;font-size:12px;line-height:1.5;color:#6b7280;">
                                All rights reserved Gemarc Enterprises Inc 2026.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>