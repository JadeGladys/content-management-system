<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Login</title>
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
    <main class="relative z-10 flex min-h-screen flex-col items-center justify-center px-4 py-4 md:px-8">
        <div class="grid w-full max-w-lg items-center gap-12 lg:max-w-6xl lg:grid-cols-2">
            <div class="space-y-6">

                <div>
                    <h1 class="text-4xl font-bold leading-tight text-slate-900 lg:text-5xl">
                        Seamless Login for Exclusive Access
                    </h1>
                    <p class="mt-6 max-w-xl text-base leading-relaxed text-slate-600">
                        Sign in to manage jobs, articles, media, and site access across the ISCO content management system.
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white/70 p-5 shadow-sm">
                    <p class="text-sm font-medium text-slate-900">Need access?</p>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Accounts are created by an administrator. Once your account is ready, you will receive a password setup email.
                    </p>
                </div>
            </div>

            <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-xl shadow-slate-200/60 lg:ml-auto">
                <div class="mb-10">
                    <h2 class="mt-3 text-3xl font-bold text-slate-900">
                        Sign in
                    </h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">
                        Enter your account details below to continue to the CMS dashboard.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
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
                            placeholder="john@isco.com"
                            required
                            autofocus
                            class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                        />
                    </div>

                    <div>
                        <label for="password" class="mb-2 inline-block text-sm font-medium text-slate-900">
                            Password
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                        />
                    </div>

                    <div class="flex flex-wrap items-start gap-2">
                        <label class="group flex items-center">
                            <input
                                id="remember"
                                name="remember"
                                type="checkbox"
                                value="1"
                                @checked(old('remember'))
                                class="sr-only"
                            />
                            <span
                                class="flex h-4 w-4 shrink-0 items-center justify-center rounded bg-white outline-1 outline-slate-300 group-has-[input:checked]:bg-blue-600 group-has-[input:checked]:outline-blue-600 group-focus-within:outline-2 group-focus-within:outline-blue-600"
                                aria-hidden="true"
                            >
                                <svg
                                    class="size-3 text-white opacity-0 group-has-[input:checked]:opacity-100"
                                    viewBox="0 0 12 10"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path d="M1 5l3 3 7-7" />
                                </svg>
                            </span>
                            <span class="ml-3 text-sm text-slate-700">
                                Remember me
                            </span>
                        </label>

                        <a
                            href="#"
                            class="ml-auto rounded text-sm font-medium text-blue-700 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                        >
                            Forgot password?
                        </a>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-md border border-blue-600 bg-blue-600 px-3.5 py-2 text-sm font-semibold text-white transition-all hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        Sign in
                    </button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
