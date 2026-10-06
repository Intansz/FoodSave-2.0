@php
$reason = $product->unavailableReason();
$canOrder = $reason === null;
$maxQty = max(1, min($product->stock, 20));
@endphp

<x-fs.layout :title="$product->name">
    <div
        class="mx-auto max-w-7xl px-4 py-6 pb-28 md:px-6 lg:px-10 lg:pb-10"
        x-data="{
            qty: 1,
            max: {{ $maxQty }},
            price: {{ $product->foodsave_price }},
            total() {
                return 'Rp' + new Intl.NumberFormat('id-ID').format(this.qty * this.price)
            },
            dec() {
                this.qty = Math.max(1, this.qty - 1)
            },
            inc() {
                this.qty = Math.min(this.max, this.qty + 1)
            }
        }">
        <a
            href="{{ route('products.index') }}"
            class="inline-flex min-h-11 items-center gap-1.5 text-body-sm font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
            <x-fs.icon name="arrow-right" class="size-4 rotate-180" />
            Kembali ke makanan
        </a>

        <div class="mt-5 grid gap-8 lg:grid-cols-[45fr_55fr] lg:gap-10">
            {{-- Product image --}}
            <div class="relative aspect-4/3 overflow-hidden rounded-2xl bg-primary-50 shadow-card lg:sticky lg:top-24 lg:self-start">
                @if ($product->image_url)
                <img
                    src="{{ $product->image_url }}"
                    alt="{{ $product->name }} dari {{ $product->merchant->business_name }}"
                    class="size-full object-cover">
                @else
                <div class="flex size-full items-center justify-center text-primary-500">
                    <x-fs.icon name="leaf" class="size-16" />
                </div>
                @endif

                @if ($product->discount_percentage > 0)
                <x-fs.discount-badge
                    :value="$product->discount_percentage"
                    class="absolute left-4 top-4" />
                @endif
            </div>

            {{-- Product information --}}
            <div>
                @if ($product->category)
                <span class="inline-flex rounded-full bg-primary-50 px-3 py-1 text-caption font-semibold text-primary-700">
                    {{ $product->category->name }}
                </span>
                @endif

                <h1 class="mt-3 text-h1 font-bold leading-tight text-ink-900">
                    {{ $product->name }}
                </h1>

                <p class="mt-2 flex items-center gap-1.5 text-body-sm text-ink-500">
                    <x-fs.icon name="store" class="size-4 shrink-0 text-ink-400" />
                    <span class="truncate">
                        {{ $product->merchant->business_name }} · {{ $product->merchant->area }}
                    </span>
                </p>

                <x-fs.price
                    :price="$product->foodsave_price"
                    :normal="$product->normal_price"
                    size="lg"
                    class="mt-5" />

                {{-- Quick information --}}
                <dl class="mt-6 grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-surface p-4 shadow-card">
                        <dt class="text-caption text-ink-500">Stok tersedia</dt>
                        <dd class="mt-2">
                            <x-fs.stock-badge :stock="$product->stock" />
                        </dd>
                    </div>

                    <div class="rounded-2xl bg-surface p-4 shadow-card">
                        <dt class="text-caption text-ink-500">Waktu pengambilan</dt>
                        <dd class="mt-2 flex items-center gap-1.5 text-body-sm font-semibold text-ink-900">
                            <x-fs.icon name="clock" class="size-4 shrink-0 text-primary-600" />
                            {{ $product->pickup_label }}
                        </dd>
                    </div>
                </dl>

                @if ($product->description)
                <div class="mt-6">
                    <h2 class="text-body font-semibold text-ink-900">Tentang makanan</h2>
                    <p class="mt-2 text-body leading-7 text-ink-700">
                        {{ $product->description }}
                    </p>
                </div>
                @endif

                {{-- Pickup location --}}
                <div class="mt-6 rounded-2xl bg-surface p-5 shadow-card">
                    <div class="flex items-start gap-3">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                            <x-fs.icon name="pin" class="size-5" />
                        </div>

                        <div class="min-w-0">
                            <h2 class="text-body font-semibold text-ink-900">
                                Lokasi pengambilan
                            </h2>

                            <p class="mt-1.5 text-body-sm leading-6 text-ink-700">
                                {{ $product->merchant->address }}
                            </p>

                            @if ($product->merchant->hasValidMapsUrl())
                            <a
                                href="{{ $product->merchant->maps_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="mt-3 inline-flex min-h-11 items-center gap-1.5 text-body-sm font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                                Buka di Google Maps
                                <x-fs.icon name="arrow-right" class="size-4" />
                            </a>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Payment information --}}
                <div class="mt-3 flex items-start gap-3 rounded-2xl border border-primary-100 bg-primary-50 p-4 text-body-sm text-ink-700">
                    <x-fs.icon name="wallet" class="mt-0.5 size-5 shrink-0 text-primary-600" />
                    <p class="leading-6">
                        Pembayaran dilakukan <strong>langsung kepada mitra</strong> saat pengambilan.
                        FoodSave tidak memproses pembayaran makanan.
                    </p>
                </div>

                {{-- Desktop order CTA --}}
                <form
                    id="order-form"
                    action="{{ route('orders.create', $product) }}"
                    method="GET"
                    class="mt-6 hidden lg:block">
                    @if ($canOrder)
                    <div class="flex items-center gap-3">
                        <div
                            class="inline-flex items-center rounded-xl border border-line bg-surface shadow-sm"
                            role="group"
                            aria-label="Jumlah porsi">
                            <button
                                type="button"
                                @click="dec()"
                                aria-label="Kurangi jumlah"
                                class="flex size-11 items-center justify-center rounded-l-xl text-ink-700 transition hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                                <x-fs.icon name="minus" class="size-4" />
                            </button>

                            <input
                                type="number"
                                name="qty"
                                x-model.number="qty"
                                min="1"
                                max="{{ $maxQty }}"
                                readonly
                                aria-label="Jumlah"
                                class="w-12 border-0 bg-transparent p-0 text-center text-body font-semibold text-ink-900 focus:outline-none">

                            <button
                                type="button"
                                @click="inc()"
                                aria-label="Tambah jumlah"
                                class="flex size-11 items-center justify-center rounded-r-xl text-ink-700 transition hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                                <x-fs.icon name="plus" class="size-4" />
                            </button>
                        </div>

                        <x-fs.button type="submit" class="flex-1">
                            Pesan Sekarang · <span x-text="total()"></span>
                        </x-fs.button>
                    </div>
                    @else
                    <x-fs.button type="button" disabled class="w-full">
                        Pesanan Tidak Tersedia
                    </x-fs.button>
                    @endif
                </form>

                @unless ($canOrder)
                <p
                    role="alert"
                    class="mt-3 flex items-center gap-1.5 text-body-sm font-medium text-danger">
                    <x-fs.icon name="alert" class="size-4 shrink-0" />
                    {{ $reason }}
                </p>
                @endunless
            </div>
        </div>

        {{-- Sticky CTA mobile --}}
        <div class="fixed inset-x-0 bottom-0 z-30 border-t border-line bg-surface/95 p-3 shadow-[0_-4px_16px_rgba(0,0,0,0.06)] backdrop-blur lg:hidden">
            <div class="mx-auto flex max-w-7xl items-center gap-3">
                @if ($canOrder)
                <div
                    class="inline-flex shrink-0 items-center rounded-xl border border-line bg-surface shadow-sm"
                    role="group"
                    aria-label="Jumlah porsi">
                    <button
                        type="button"
                        @click="dec()"
                        aria-label="Kurangi jumlah"
                        class="flex size-11 items-center justify-center rounded-l-xl text-ink-700 transition hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                        <x-fs.icon name="minus" class="size-4" />
                    </button>

                    <span
                        class="w-8 text-center text-body font-semibold text-ink-900"
                        x-text="qty"></span>

                    <button
                        type="button"
                        @click="inc()"
                        aria-label="Tambah jumlah"
                        class="flex size-11 items-center justify-center rounded-r-xl text-ink-700 transition hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                        <x-fs.icon name="plus" class="size-4" />
                    </button>
                </div>

                <x-fs.button type="submit" form="order-form" class="flex-1">
                    Pesan · <span x-text="total()"></span>
                </x-fs.button>
                @else
                <x-fs.button type="button" disabled class="w-full">
                    Tidak tersedia
                </x-fs.button>
                @endif
            </div>
        </div>
    </div>
</x-fs.layout>