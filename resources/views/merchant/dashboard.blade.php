<x-fs.layout>
    <section class="mx-auto max-w-7xl px-4 py-8 md:px-6 lg:px-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-h2 font-bold text-ink-900">Dashboard Mitra</h1>
                <p class="mt-1 text-body text-ink-500">
                    {{ $merchant->business_name }}
                </p>
            </div>

            <a
                href="{{ route('merchant.products.index') }}"
                class="inline-flex w-fit items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700">
                Kelola Produk
            </a>
        </div>

        <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Total Produk</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['total_products'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">Semua produk mitra</p>
            </div>

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Produk Aktif</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['active_products'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">Sedang ditampilkan</p>
            </div>

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Menunggu Konfirmasi</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['pending_orders'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">Perlu diproses</p>
            </div>

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <p class="text-body-sm text-ink-500">Pesanan Berjalan</p>
                <p class="mt-2 text-h3 font-bold text-ink-900">
                    {{ $summary['ongoing_orders'] }}
                </p>
                <p class="mt-1 text-caption text-ink-500">Belum selesai</p>
            </div>
        </div>

        <div class="mt-10">
            <h2 class="text-h3 font-semibold text-ink-900">Produk Saya</h2>

            @forelse ($products as $product)
            <div class="mt-3 flex gap-4 rounded-2xl bg-surface p-4 shadow-card">
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded-xl bg-primary-50">
                    @if ($product->image_url)
                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover">
                    @else
                    <div class="flex h-full w-full items-center justify-center text-primary-500">
                        <x-fs.icon name="leaf" class="size-7" />
                    </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <p class="truncate font-semibold text-ink-900">
                                {{ $product->name }}
                            </p>

                            <p class="mt-1 text-body-sm text-ink-500">
                                Rp{{ number_format($product->foodsave_price, 0, ',', '.') }}
                                <span class="mx-1">·</span>
                                Stok {{ $product->stock }}
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold
                    {{ $product->status === 'active'
                        ? 'bg-primary-50 text-primary-700'
                        : 'bg-ink-100 text-ink-500' }}">
                            {{ $product->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <a
                        href="{{ route('merchant.products.edit', $product) }}"
                        class="mt-3 inline-flex text-sm font-semibold text-primary-700 hover:text-primary-800">
                        Edit produk
                    </a>
                </div>
            </div>
            @empty
            <div class="mt-3 rounded-2xl bg-surface p-6 text-center shadow-card">
                <p class="text-body text-ink-500">Belum ada produk.</p>

                <a
                    href="{{ route('merchant.products.create') }}"
                    class="mt-3 inline-flex text-sm font-semibold text-primary-700">
                    Tambah produk
                </a>
            </div>
            @endforelse
        </div>

        <div class="mt-10">
            <h2 class="text-h3 font-semibold text-ink-900">Pesanan</h2>

            @forelse ($orders as $order)
            <div class="mt-3 rounded-2xl bg-surface p-5 shadow-card">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-ink-900">
                            Pesanan #{{ $order->order_number }}
                        </p>

                        <p class="mt-1 text-body-sm text-ink-500">
                            {{ $order->user->name }}
                        </p>
                    </div>

                    <span class="inline-flex w-fit rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary-700">
                        {{ $order->status->label() }}
                    </span>
                </div>

                <div class="mt-4 border-t border-ink-100 pt-4">
                    <p class="text-body-sm text-ink-500">
                        Total pesanan
                    </p>

                    <p class="mt-1 font-semibold text-ink-900">
                        Rp{{ number_format($order->total, 0, ',', '.') }}
                    </p>
                </div>


                @if ($order->status === \App\Enums\OrderStatus::Pending)
                <div class="mt-4 flex flex-wrap gap-3">
                    <form
                        action="{{ route('merchant.orders.status', $order) }}"
                        method="POST">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="status" value="confirmed">

                        <button
                            type="submit"
                            class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 sm:w-auto">
                            Konfirmasi Pesanan
                        </button>
                    </form>

                    <form
                        action="{{ route('merchant.orders.status', $order) }}"
                        method="POST"
                        onsubmit="return confirm('Yakin ingin membatalkan pesanan ini? Stok produk akan dikembalikan.');">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="status" value="cancelled">

                        <button
                            type="submit"
                            class="w-full rounded-xl border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50 sm:w-auto">
                            Batalkan Pesanan
                        </button>
                    </form>
                </div>

                @elseif ($order->status === \App\Enums\OrderStatus::Confirmed)
                <form
                    action="{{ route('merchant.orders.status', $order) }}"
                    method="POST"
                    class="mt-4">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="status" value="ready_for_pickup">

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 sm:w-auto">
                        Tandai Siap Diambil
                    </button>
                </form>

                @elseif ($order->status === \App\Enums\OrderStatus::ReadyForPickup)
                <form
                    action="{{ route('merchant.orders.status', $order) }}"
                    method="POST"
                    class="mt-4">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="status" value="completed">

                    <button
                        type="submit"
                        class="w-full rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 sm:w-auto">
                        Tandai Selesai
                    </button>
                </form>
                @endif
            </div>
            @empty
            <div class="mt-3 rounded-2xl bg-surface p-6 text-center shadow-card">
                <p class="text-body text-ink-500">Belum ada pesanan.</p>
            </div>
            @endforelse
        </div>
    </section>
</x-fs.layout>