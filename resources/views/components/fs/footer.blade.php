<footer class="mt-16 border-t border-line bg-surface">
    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-10 md:flex-row md:items-start md:justify-between md:px-6 lg:px-10">
        <div class="max-w-sm">
            <x-fs.logo />
            <p class="mt-3 text-body-sm text-ink-500">
                Marketplace makanan surplus layak konsumsi dari UMKM kuliner sekitar kampus.
                Pembayaran dilakukan langsung kepada mitra.
            </p>
        </div>

        <nav aria-label="Tautan footer" class="grid grid-cols-2 gap-x-12 gap-y-2 text-body-sm">
            <a href="{{ route('products.index') }}" class="text-ink-700 hover:text-primary-700">Jelajahi Makanan</a>
            <a href="{{ route('register.merchant') }}" class="text-ink-700 hover:text-primary-700">Jadi Mitra</a>
            <a href="{{ route('home') }}#cara-kerja" class="text-ink-700 hover:text-primary-700">Cara Kerja</a>
            <a href="{{ route('login') }}" class="text-ink-700 hover:text-primary-700">Masuk</a>
        </nav>
    </div>

    <div class="border-t border-line">
        <p class="mx-auto max-w-7xl px-4 py-4 text-caption text-ink-500 md:px-6 lg:px-10">
            © {{ date('Y') }} FoodSave. Selamatkan makanan, hemat pengeluaran.
        </p>
    </div>
</footer>
