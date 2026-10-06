<x-fs.layout>
    <section class="mx-auto max-w-7xl px-4 py-8 md:px-6 lg:px-10">
        <div>
            <h1 class="text-h2 font-bold text-ink-900">Dashboard Admin</h1>
            <p class="mt-1 text-body text-ink-500">
                Ringkasan aktivitas FoodSave
            </p>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Total Mitra</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['total_merchants'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">
                    Semua mitra terdaftar
                </p>
            </div>

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Menunggu Verifikasi</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['pending_merchants'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">
                    Perlu ditinjau
                </p>
            </div>

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Total Produk</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['total_products'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">
                    Produk dari semua mitra
                </p>
            </div>

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Total Pesanan</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['total_orders'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">
                    Semua transaksi pesanan
                </p>
            </div>
        </div>

        <div class="mt-6 rounded-2xl bg-surface p-5 shadow-card">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-body-sm text-ink-500">
                        Service Fee Belum Disettle
                    </p>

                    <p class="mt-2 text-h3 font-bold text-ink-900">
                        Rp{{ number_format($summary['unsettled_fees'], 0, ',', '.') }}
                    </p>

                    <p class="mt-1 text-caption text-ink-500">
                        Total fee yang masih menunggu settlement
                    </p>
                </div>

                <a
                    href="{{ route('admin.settlements') }}"
                    class="inline-flex w-fit items-center justify-center rounded-xl border border-ink-200 px-4 py-2.5 text-sm font-semibold text-ink-700 transition hover:bg-ink-50">
                    Kelola Settlement
                </a>
            </div>
        </div>
    </section>
</x-fs.layout>