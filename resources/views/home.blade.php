<x-fs.layout>
    {{-- HERO --------------------------------------------------------------}}
    <section class="bg-linear-to-b from-primary-50 to-canvas">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-4 py-12 md:px-6 lg:grid-cols-2 lg:px-10 lg:py-16">
            <div>
                <span class="inline-flex items-center rounded-full bg-primary-100 px-3 py-1 text-xs font-semibold text-primary-700">
                    FoodSave untuk sekitar kampus
                </span>

                <h1 class="mt-4 text-display-sm font-bold tracking-tight text-ink-900 lg:text-display">
                    Selamatkan Makanan,
                    <span class="text-primary-600">Hemat Pengeluaran</span>
                </h1>

                <p class="mt-4 max-w-xl text-body-lg text-ink-500">
                    Makanan surplus layak konsumsi dari kafe, bakery, dan UMKM kuliner sekitar kampus dengan harga lebih hemat.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <x-fs.button :href="route('products.index')">
                        Jelajahi Makanan
                        <x-fs.icon name="arrow-right" class="size-4" />
                    </x-fs.button>

                    <x-fs.button variant="secondary" :href="route('register.merchant')">
                        Jadi Mitra
                    </x-fs.button>
                </div>

                <x-fs.search-bar class="mt-8 max-w-xl" />

                <p class="mt-4 flex items-center gap-2 text-body-sm text-ink-500">
                    <x-fs.icon name="wallet" class="size-4 shrink-0 text-primary-600" />
                    Bayar langsung ke mitra saat mengambil makanan.
                </p>
            </div>

            @if ($featured)
            <div class="mx-auto w-full max-w-sm lg:ml-auto">
                <x-fs.product-card :product="$featured" />
            </div>
            @endif
        </div>
    </section>

    {{-- KATEGORI ----------------------------------------------------------}}
    <section class="mx-auto max-w-7xl px-4 pb-2 md:px-6 lg:px-10">
        <x-fs.category-pills :categories="$categories" />
    </section>

    {{-- FLASH DEAL --------------------------------------------------------}}
    @if ($flashDeals->isNotEmpty())
    <section
        class="mx-auto mt-8 max-w-7xl px-4 md:px-6 lg:px-10"
        aria-label="Flash Deal">
        <div class="rounded-hero bg-warning-bg p-5 md:p-6">
            <x-fs.section-heading
                title="Flash Deal"
                subtitle="Waktu pengambilan segera berakhir. Amankan sebelum habis.">
                <a
                    href="{{ route('products.index', ['flash' => 1]) }}"
                    class="hidden min-h-11 items-center text-body-sm font-semibold text-primary-700 hover:underline focus:outline-none focus:ring-2 focus:ring-primary-200 sm:inline-flex">
                    Lihat semua
                </a>
            </x-fs.section-heading>

            <div class="mt-5 grid grid-cols-2 gap-4 lg:grid-cols-4">
                @foreach ($flashDeals as $product)
                <x-fs.product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- MAKANAN TERSEDIA ---------------------------------------------------}}
    <section class="mx-auto mt-12 max-w-7xl px-4 md:px-6 lg:px-10">
        <x-fs.section-heading
            title="Makanan tersedia"
            subtitle="Stok terbatas, ambil sebelum waktunya habis.">
            <a
                href="{{ route('products.index') }}"
                class="inline-flex min-h-11 items-center text-body-sm font-semibold text-primary-700 hover:underline focus:outline-none focus:ring-2 focus:ring-primary-200">
                Lihat semua
            </a>
        </x-fs.section-heading>

        <div class="mt-5">
            @if ($latest->isEmpty())
            <x-fs.empty-state
                icon="leaf"
                title="Belum ada makanan surplus"
                description="Mitra belum memasang penawaran hari ini. Coba cek lagi nanti." />
            @else
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($latest as $product)
                <x-fs.product-card :product="$product" />
                @endforeach
            </div>
            @endif
        </div>
    </section>

    {{-- CARA KERJA --------------------------------------------------------}}
    <section
        id="cara-kerja"
        class="mx-auto mt-16 max-w-7xl scroll-mt-20 px-4 md:px-6 lg:px-10">
        <x-fs.section-heading title="Cara kerja FoodSave" />

        <ol class="mt-6 grid gap-4 md:grid-cols-3">
            @foreach ([
            ['search', 'Temukan', 'Cari makanan surplus di sekitar kampus. Harga, stok, dan jam ambil terlihat langsung.'],
            ['bag', 'Pesan', 'Buat pesanan untuk mengamankan stok. Tidak ada pembayaran lewat FoodSave.'],
            ['wallet', 'Ambil & bayar ke mitra', 'Datang ke lokasi mitra sebelum waktu berakhir, lalu bayar langsung di tempat.'],
            ] as $i => [$icon, $title, $text])
            <li class="rounded-2xl bg-surface p-5 shadow-card">
                <span class="flex size-11 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
                    <x-fs.icon :name="$icon" />
                </span>

                <h3 class="mt-4 text-h4 font-semibold text-ink-900">
                    {{ $i + 1 }}. {{ $title }}
                </h3>

                <p class="mt-1 text-body text-ink-500">
                    {{ $text }}
                </p>
            </li>
            @endforeach
        </ol>
    </section>

    {{-- CTA MITRA ---------------------------------------------------------}}
    <section class="mx-auto mt-16 max-w-7xl px-4 md:px-6 lg:px-10">
        <div class="flex flex-col items-start justify-between gap-5 rounded-hero bg-primary-700 p-6 text-white md:flex-row md:items-center md:p-10">
            <div>
                <h2 class="text-h3 font-semibold">
                    Punya makanan yang tidak habis hari ini?
                </h2>

                <p class="mt-1 max-w-xl text-body text-primary-100">
                    Jual sebagai surplus, kurangi kerugian, dan jangkau pelanggan baru di sekitar kampus.
                </p>
            </div>

            <a
                href="{{ route('register.merchant') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-xl bg-surface px-5 text-body font-semibold text-primary-700 transition hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-white/70">
                Daftar sebagai Mitra
            </a>
        </div>
    </section>
</x-fs.layout>