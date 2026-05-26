<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <h2>Hello {{ $user->name }},</h2>

    <p>{{ $introText }}</p>

    <p>Click the link below to {{ strtolower($actionText) }}:</p>

    <p>
        <a href="{{ $actionUrl }}">{{ $actionText }}</a>
    </p>

    <p>{{ $expiryText }}</p>

    <p>If you did not expect this email, you can ignore it.</p>
</body>
</html>