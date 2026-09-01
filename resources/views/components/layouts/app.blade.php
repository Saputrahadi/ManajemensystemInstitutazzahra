@props(['title' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-neutral-50 font-sans antialiased">
    <nav class="border-b border-brand-100 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
            <span class="font-semibold text-brand-700">Az-Zahra Digital Campus</span>
            <div class="flex items-center gap-3">
                <livewire:notification-bell />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-sm text-neutral-500 hover:text-brand-600">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-5xl px-4 py-8">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
