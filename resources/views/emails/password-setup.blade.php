<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set up your password</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.6;">
    <h2>Hello {{ $user->name }},</h2>

    <p>Your ISCO CMS account has been created.</p>

    <p>Click the link below to set up your password:</p>

    <p>
        <a href="{{ $setupUrl }}">Set up password</a>
    </p>

    <p>This link will expire in 24 hours.</p>

    <p>If you did not expect this email, you can ignore it.</p>
</body>
</html>