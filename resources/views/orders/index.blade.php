@php
$tabs = [
'semua' => 'Semua',
'berlangsung' => 'Berlangsung',
'selesai' => 'Selesai',
'dibatalkan' => 'Dibatalkan',
];
@endphp

<x-fs.layout title="Pesanan Saya">
    <div class="mx-auto max-w-3xl px-4 py-8 md:px-6">
        <div>
            <h1 class="text-h2 font-bold text-ink-900">Pesanan Saya</h1>
            <p class="mt-1 text-body-sm text-ink-500">
                Lihat riwayat dan status pesanan kamu.
            </p>
        </div>

        <nav
            aria-label="Filter status pesanan"
            class="no-scrollbar mt-6 overflow-x-auto">
            <div class="flex w-max gap-2 rounded-2xl bg-surface/70 p-2 shadow-sm ring-1 ring-line/60">
                @foreach ($tabs as $key => $label)
                <a
                    href="{{ route('orders.index', ['tab' => $key]) }}"
                    @if ($tab===$key) aria-current="page" @endif
                    @class([ 'inline-flex min-h-11 shrink-0 items-center justify-center rounded-full px-4 text-body-sm font-semibold whitespace-nowrap transition-colors focus:outline-none focus:ring-2 focus:ring-primary-200' , 'bg-primary-600 text-white shadow-sm'=> $tab === $key,
                    'text-ink-700 hover:bg-primary-50 hover:text-primary-700' => $tab !== $key,
                    ])
                    >
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </nav>

        <div class="mt-5 space-y-4">
            @forelse ($orders as $order)
            <a
                href="{{ route('orders.show', $order) }}"
                class="group block rounded-2xl bg-surface p-5 shadow-card ring-1 ring-transparent transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover hover:ring-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-caption font-medium text-ink-500">
                            {{ $order->order_number }}
                        </p>

                        <p class="mt-1 text-body font-semibold text-ink-900">
                            {{ $order->merchant->business_name }}
                        </p>
                    </div>

                    <div class="shrink-0">
                        <x-fs.status-badge :status="$order->status" />
                    </div>
                </div>

                <div class="mt-4 rounded-xl bg-ink-50/60 px-3.5 py-3">
                    <p class="text-body-sm leading-5 text-ink-700">
                        {{ $order->items->map(fn ($i) => $i->quantity . '× ' . $i->product_name_snapshot)->join(', ') }}
                    </p>
                </div>

                <div class="mt-4 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-caption text-ink-500">Total pesanan</p>
                        <p class="mt-0.5 text-body font-bold text-primary-700">
                            {{ \App\Support\Rupiah::format($order->total) }}
                        </p>
                    </div>

                    <span class="inline-flex items-center gap-1 text-caption font-semibold text-ink-600 transition-colors group-hover:text-primary-700">
                        Lihat detail
                        <x-fs.icon name="arrow-right" class="size-3.5" />
                    </span>
                </div>
            </a>
            @empty
            <x-fs.empty-state
                icon="bag"
                title="Belum ada pesanan"
                description="Pesanan makanan yang kamu buat akan muncul di sini."
                action-label="Jelajahi Makanan"
                :action-href="route('products.index')" />
            @endforelse
        </div>

        @if ($orders->hasPages())
        <div class="mt-6">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</x-fs.layout>