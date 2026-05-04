<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Your sign-in code') }}</title>
</head>

<body style="font-family: system-ui, -apple-system, sans-serif; line-height: 1.5; color: #2d2f31;">
    <p>{{ __('Your sign-in code is:') }}</p>
    <p style="font-size: 24px; font-weight: 700; letter-spacing: 0.2em;">{{ $otp }}</p>
    <p style="font-size: 14px; color: #6a6f73;">{{ __('This code expires in 5 minutes. If you did not request it, you can ignore this email.') }}
    </p>
</body>

</html>
