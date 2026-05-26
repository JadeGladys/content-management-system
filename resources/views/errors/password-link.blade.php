<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Link Error' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <main class="grid min-h-full place-items-center bg-gray-900 px-6 py-24 sm:py-32 lg:px-8">
        <div class="text-center">
            <p class="text-base font-semibold text-indigo-400">{{ $code ?? '404' }}</p>
            <h1 class="mt-4 text-5xl font-semibold tracking-tight text-balance text-white sm:text-7xl">
                {{ $title ?? 'Page not found' }}
            </h1>
            <p class="mt-6 text-lg font-medium text-pretty text-gray-400 sm:text-xl/8">
                {{ $message ?? 'Sorry, we couldn’t find the page you’re looking for.' }}
            </p>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a
                    href="{{ $primaryActionUrl }}"
                    class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500"
                >
                    {{ $primaryActionLabel }}
                </a>

                @if (! empty($secondaryActionUrl) && ! empty($secondaryActionLabel))
                    <a href="{{ $secondaryActionUrl }}" class="text-sm font-semibold text-white">
                        {{ $secondaryActionLabel }} <span aria-hidden="true">&rarr;</span>
                    </a>
                @endif
            </div>
        </div>
    </main>
</body>
</html>