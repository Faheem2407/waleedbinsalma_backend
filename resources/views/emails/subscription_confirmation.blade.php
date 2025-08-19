<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <tr>
                        <td style="padding: 40px;">
                            <h1 style="font-size: 24px; color: #333; margin: 0 0 20px;">Subscription Confirmation</h1>
                            <p style="font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                Dear {{ $userName }},
                            </p>
                            <p style="font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                Thank you for {{ $isRenewal ? 'renewing' : 'purchasing' }} your subscription for {{ $storeName }}!
                            </p>
                            <h2 style="font-size: 18px; color: #333; margin: 20px 0 10px;">Subscription Details:</h2>
                            <ul style="font-size: 16px; line-height: 1.5; margin: 0 0 20px; padding-left: 20px;">
                                <li><strong>Start Date:</strong> {{ $startDate }}</li>
                                <li><strong>End Date:</strong> {{ $endDate }}</li>
                                <li><strong>Amount Paid:</strong> ${{ $amount }}</li>
                            </ul>
                            <p style="font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                Your subscription is now active, and you can enjoy all the benefits until the end date.
                            </p>
                            <p style="font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                If you have any questions or need assistance, please contact our support team.
                            </p>
                            <p style="font-size: 16px; line-height: 1.5; margin: 0 0 20px;">
                                Best regards,
                            </p>
                            <a href="{{ config('app.url') }}" style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; border-radius: 4px; font-size: 16px;">
                                Visit Your Account
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>