<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Latihan Livewire' }} · Pertemuan 12</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100 text-gray-800 antialiased">
    @php
        $menu = [
            'home' => 'Beranda',
            'soal1' => '1 · Cari Mahasiswa',
            'soal2' => '2 · Anti Double Click',
            'soal3' => '3 · Penghitung Karakter',
            'soal4' => '4 · Chat + Database',
            'soal5' => '5 · Split-Panel Editor',
        ];
    @endphp
    <nav class="bg-[#1b2a4a] text-white">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-1 px-4 py-3">
            <span class="mr-4 font-semibold">PBKK · Pertemuan 12</span>
            @foreach ($menu as $route => $label)
                <a href="{{ route($route) }}"
                   class="rounded px-3 py-1.5 text-sm {{ request()->routeIs($route) ? 'bg-teal-600' : 'hover:bg-white/10' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </nav>

    <main class="mx-auto max-w-7xl px-4 py-6">
        @isset($heading)
            <header class="mb-5">
                <h1 class="text-2xl font-bold text-[#1b2a4a]">{{ $heading }}</h1>
                @isset($subheading)
                    <p class="mt-1 text-sm text-teal-700">{{ $subheading }}</p>
                @endisset
            </header>
        @endisset

        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
