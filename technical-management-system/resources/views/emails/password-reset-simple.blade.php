<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body style="margin:0;padding:0;background:#ececec;font-family:Arial,sans-serif;color:#1f2937;">

    @php
        $logoUrl = 'https://gemarcph.com/images/gemarclogo.png';
    @endphp

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#ececec;padding:28px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;background:#ffffff;border-radius:14px;overflow:hidden;border:1px solid #d8dfda;">
                    
                    <!-- HEADER -->
                    <tr>
                        <td align="center" style="padding:24px 24px 10px 24px;">
                            <img 
                                src="{{ $logoUrl }}" 
                                alt="Gemarc Logo" 
                                width="140"
                                style="display:block;height:auto;"
                            >
                        </td>
                    </tr>

                    <!-- BODY -->
                    <tr>
                        <td style="padding:10px 34px 28px 34px;">
                            <h2 style="margin:0 0 14px 0;font-size:28px;line-height:1.25;color:#1f2937;">
                                Password Reset
                            </h2>

                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;color:#374151;">
                                Seems like you requested to reset your password for Gemarc TMS.
                            </p>

                            <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#374151;">
                                Click the button below to reset your password.
                            </p>

                            <!-- BUTTON -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin:0 0 18px 0;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $resetUrl }}"
                                           style="display:inline-block;background:#1d66d6;border-radius:999px;padding:12px 24px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;color:#374151;">
                                This link will expire in <strong>{{ $expireMinutes }} minutes</strong>.
                            </p>

                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;color:#374151;">
                                If you did not request a password reset, you can safely ignore this email.
                            </p>

                            <p style="margin:20px 0 0;font-size:15px;line-height:1.7;color:#374151;">
                                Regards,<br>IT Department
                            </p>

                            <p style="margin:18px 0 0;padding-top:14px;border-top:1px solid #d8dfda;font-size:12px;line-height:1.5;color:#6b7280;">
                                All rights reserved Gemarc Enterprises Inc {{ date('Y') }}.
                            </p>
                        </td>
                    </tr>

                </table>

                <!-- FOOTER -->
                <table role="presentation" width="620" cellspacing="0" cellpadding="0" style="max-width:620px;margin-top:16px;">
                    <tr>
                        <td align="center" style="color:#6b7280;font-size:13px;">
                            Gemarc Enterprises Inc • Technical Management System
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>