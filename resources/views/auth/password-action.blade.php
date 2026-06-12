<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('admin-assets/favicon.ico') }}">
    <title>{{ $title ?? 'Set Password' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-hidden bg-slate-100 text-slate-900">
    <div class="absolute inset-0">
        <img src="{{ asset('admin-assets/images/login-bg.jpg') }}" alt="" class="h-full w-full object-cover opacity-40">
    </div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">{{ $title ?? 'Set Password' }}</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    {{ $description ?? 'Create a strong password for your CMS account.' }}
                </p>
                <p class="mt-2 text-xs text-slate-500">
                    Setting password for {{ $email }}
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ $formAction ?? route('password.setup.update') }}" class="space-y-6">
                @csrf

                <input type="hidden" name="token" value="{{ old('token', $token) }}">
                <input type="hidden" name="email" value="{{ old('email', $email) }}">

                <div>
                    <label for="password" class="mb-2 inline-block text-sm font-medium text-slate-900">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                    />
                    <ul id="password-requirements" class="mt-3 space-y-2 text-xs text-slate-500">
                        <li id="rule-length">At least 12 characters</li>
                        <li id="rule-lower">At least one lowercase letter</li>
                        <li id="rule-upper">At least one uppercase letter</li>
                        <li id="rule-number">At least one number</li>
                        <li id="rule-symbol">At least one symbol</li>
                        <li id="rule-match">Passwords match</li>
                    </ul>
                </div>

                <div>
                    <label for="password_confirmation" class="mb-2 inline-block text-sm font-medium text-slate-900">
                        Confirm Password
                    </label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                        class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full rounded-md border border-blue-600 bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white transition-all hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    {{ $submitLabel ?? 'Save Password' }}
                </button>
            </form>
        </div>
    </main>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordConfirmationInput = document.getElementById('password_confirmation');

        const rules = {
            length: document.getElementById('rule-length'),
            lower: document.getElementById('rule-lower'),
            upper: document.getElementById('rule-upper'),
            number: document.getElementById('rule-number'),
            symbol: document.getElementById('rule-symbol'),
            match: document.getElementById('rule-match'),
        };

        const setRuleState = (element, passed) => {
            element.classList.toggle('text-green-600', passed);
            element.classList.toggle('text-slate-500', ! passed);
        };

        const validatePasswordRules = () => {
            const password = passwordInput.value;
            const confirmation = passwordConfirmationInput.value;

            setRuleState(rules.length, password.length >= 12);
            setRuleState(rules.lower, /[a-z]/.test(password));
            setRuleState(rules.upper, /[A-Z]/.test(password));
            setRuleState(rules.number, /[0-9]/.test(password));
            setRuleState(rules.symbol, /[^A-Za-z0-9]/.test(password));
            setRuleState(rules.match, password.length > 0 && password === confirmation);
        };

        passwordInput.addEventListener('input', validatePasswordRules);
        passwordConfirmationInput.addEventListener('input', validatePasswordRules);

        validatePasswordRules();
    </script>

</body>
</html>