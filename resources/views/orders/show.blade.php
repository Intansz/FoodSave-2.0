@use('App\Enums\OrderStatus')

@php
$steps = OrderStatus::timeline();
$currentIndex = array_search($order->status, $steps, true);
@endphp

<x-fs.layout :title="'Pesanan '.$order->order_number">
    <div class="mx-auto max-w-3xl px-4 py-8 md:px-6">
        <a
            href="{{ route('orders.index') }}"
            class="inline-flex min-h-11 items-center gap-1.5 text-body-sm font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
            <x-fs.icon name="arrow-right" class="size-4 rotate-180" />
            Semua pesanan
        </a>

        {{-- Header --}}
        <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-body-sm text-ink-500">Nomor pesanan</p>
                <h1 class="mt-1 break-all text-h2 font-bold text-ink-900">
                    {{ $order->order_number }}
                </h1>
            </div>

            <div class="shrink-0">
                <x-fs.status-badge :status="$order->status" />
            </div>
        </div>

        {{-- Order timeline --}}
        @if ($order->status === OrderStatus::Cancelled)
        <div
            role="status"
            class="mt-6 flex items-start gap-3 rounded-2xl border border-danger/20 bg-danger-bg p-4 text-body-sm text-danger">
            <x-fs.icon name="x" class="mt-0.5 size-5 shrink-0" />

            <div>
                <p class="font-semibold">Pesanan dibatalkan</p>
                <p class="mt-0.5">
                    Pesanan ini tidak dapat dilanjutkan.
                </p>
            </div>
        </div>
        @else
        <section
            class="mt-6 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6"
            aria-label="Progres pesanan">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-body font-semibold text-ink-900">
                    Status pesanan
                </h2>

                @if ($currentIndex !== false)
                <span class="text-caption text-ink-500">
                    {{ $currentIndex + 1 }} dari {{ count($steps) }}
                </span>
                @endif
            </div>

            <ol class="mt-5 grid grid-cols-4 gap-2">
                @foreach ($steps as $i => $step)
                @php
                $done = $currentIndex !== false && $i <= $currentIndex;
                    $now=$i===$currentIndex;
                    @endphp

                    <li
                    @if ($now) aria-current="step" @endif
                    class="min-w-0 text-center">
                    <div
                        @class([ 'h-1.5 rounded-full transition-colors' , 'bg-primary-600'=> $done,
                        'bg-line' => ! $done,
                        ])
                        ></div>

                    <p
                        @class([ 'mt-2 text-caption leading-4' , 'font-bold text-primary-700'=> $now,
                        'font-medium text-ink-700' => $done && ! $now,
                        'text-ink-400' => ! $done,
                        ])
                        >
                        {{ $step->label() }}
                    </p>
                    </li>
                    @endforeach
            </ol>
        </section>
        @endif

        {{-- Order items --}}
        <section
            class="mt-4 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6"
            aria-labelledby="order-items">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-caption text-ink-500">Mitra</p>
                    <h2 id="order-items" class="mt-0.5 text-h4 font-semibold text-ink-900">
                        {{ $order->merchant->business_name }}
                    </h2>
                </div>

                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <x-fs.icon name="store" class="size-5" />
                </div>
            </div>

            <ul class="mt-5 divide-y divide-line">
                @foreach ($order->items as $item)
                <li class="flex items-start justify-between gap-4 py-3 first:pt-0 last:pb-0">
                    <div class="min-w-0">
                        <p class="text-body-sm font-medium text-ink-900">
                            {{ $item->quantity }} × {{ $item->product_name_snapshot }}
                        </p>
                    </div>

                    <p class="shrink-0 text-body-sm font-semibold text-ink-900">
                        {{ \App\Support\Rupiah::format($item->subtotal) }}
                    </p>
                </li>
                @endforeach
            </ul>

            <div class="mt-4 flex items-center justify-between gap-4 border-t border-line pt-4">
                <span class="text-body font-semibold text-ink-900">
                    Total
                </span>

                <span class="text-lg font-bold text-primary-700">
                    {{ \App\Support\Rupiah::format($order->total) }}
                </span>
            </div>
        </section>

        {{-- Pickup location --}}
        <section
            class="mt-4 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6"
            aria-labelledby="pickup-location">
            <div class="flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                    <x-fs.icon name="pin" class="size-5" />
                </div>

                <div class="min-w-0">
                    <h2 id="pickup-location" class="text-h4 font-semibold text-ink-900">
                        Lokasi pengambilan
                    </h2>

                    <p class="mt-2 text-body-sm leading-6 text-ink-700">
                        {{ $order->merchant->address }}
                    </p>

                    @if ($order->merchant->hasValidMapsUrl())
                    <a
                        href="{{ $order->merchant->maps_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="mt-3 inline-flex min-h-11 items-center gap-1.5 text-body-sm font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                        Buka di Google Maps
                        <x-fs.icon name="arrow-right" class="size-4" />
                    </a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Payment note --}}
        <div class="mt-4 flex items-start gap-3 rounded-2xl border border-primary-100 bg-primary-50 p-4 text-body-sm text-ink-700">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-surface text-primary-600 shadow-sm">
                <x-fs.icon name="wallet" class="size-4" />
            </div>

            <p class="leading-6">
                Bayar <strong>langsung kepada mitra</strong> saat pengambilan.
                Status pesanan diperbarui oleh mitra.
            </p>
        </div>
    </div>
</x-fs.layout>