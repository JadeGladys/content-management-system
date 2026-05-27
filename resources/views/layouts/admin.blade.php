<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CMS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <div class="flex min-h-screen">
        <x-sidebar />

        <main class="min-w-0 flex-1 overflow-x-hidden p-8 lg:ml-[264px]">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
