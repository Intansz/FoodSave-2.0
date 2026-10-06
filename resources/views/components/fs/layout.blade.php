@props(['title' => null, 'description' => 'Marketplace makanan surplus layak konsumsi dari UMKM kuliner sekitar kampus. Hemat, bayar langsung ke mitra.'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <title>{{ $title ? $title.' · FoodSave' : 'FoodSave — Selamatkan Makanan, Hemat Pengeluaran' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    <a href="#konten" class="sr-only rounded-lg bg-primary-700 px-4 py-2 text-white focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50">
        Lewati ke konten
    </a>

    <x-fs.navbar />
    <x-fs.flash />

    <main id="konten" class="flex-1">
        {{ $slot }}
    </main>

    <x-fs.footer />
</body>
</html>
