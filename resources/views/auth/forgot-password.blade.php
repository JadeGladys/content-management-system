<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="relative min-h-screen overflow-hidden bg-slate-100 text-slate-900">
    <div class="absolute inset-0">
        <img
            src="/images/login-bg2.jpg"
            alt=""
            class="h-full w-full object-cover opacity-40"
        >
    </div>

    <main class="relative z-10 flex min-h-screen items-center justify-center px-4 py-8">
        <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-900">Forgot Password</h1>
                <p class="mt-3 text-sm leading-6 text-slate-600">
                    Enter your email address and we’ll send you a link to reset your password.
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="mb-2 inline-block text-sm font-medium text-slate-900">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                    />
                </div>

                <button
                    type="submit"
                    class="w-full rounded-md border border-blue-600 bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white transition-all hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Send Reset Link
                </button>
            </form>
        </div>
    </main>
</body>
</html>