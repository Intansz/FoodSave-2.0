@php
$subtotal = $product->foodsave_price * $quantity;
@endphp

<x-fs.layout title="Konfirmasi Pesanan">
    <div class="mx-auto max-w-3xl px-4 py-8 md:px-6">
        <a
            href="{{ route('products.show', $product) }}"
            class="inline-flex min-h-11 items-center gap-1.5 text-body-sm font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
            <x-fs.icon name="arrow-right" class="size-4 rotate-180" />
            Kembali ke produk
        </a>

        <div class="mt-5">
            <p class="text-body-sm font-semibold text-primary-700">Langkah terakhir</p>
            <h1 class="mt-1 text-h2 font-bold text-ink-900">
                Konfirmasi Pesanan
            </h1>
            <p class="mt-1 text-body-sm text-ink-500">
                Pastikan detail pesanan kamu sudah benar sebelum membuat pesanan.
            </p>
        </div>

        <form
            action="{{ route('orders.store') }}"
            method="POST"
            class="mt-7 space-y-4">
            @csrf

            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="{{ $quantity }}">

            {{-- Product --}}
            <section
                class="rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60"
                aria-labelledby="t-produk">
                <h2 id="t-produk" class="text-h4 font-semibold text-ink-900">
                    Produk
                </h2>

                <div class="mt-4 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-body font-semibold text-ink-900">
                            {{ $product->name }}
                        </p>

                        <p class="mt-1 text-body-sm text-ink-500">
                            {{ $product->merchant->business_name }}
                        </p>
                    </div>

                    <p class="shrink-0 text-right text-body-sm font-semibold text-ink-900">
                        {{ $quantity }} × {{ \App\Support\Rupiah::format($product->foodsave_price) }}
                    </p>
                </div>
            </section>

            {{-- Pickup --}}
            <section
                class="rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60"
                aria-labelledby="t-pickup">
                <h2 id="t-pickup" class="text-h4 font-semibold text-ink-900">
                    Pengambilan
                </h2>

                <div class="mt-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                            <x-fs.icon name="clock" class="size-4" />
                        </div>

                        <div>
                            <p class="text-caption text-ink-500">Waktu pengambilan</p>
                            <p class="mt-0.5 text-body-sm font-semibold text-ink-900">
                                {{ $product->pickup_label }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-600">
                            <x-fs.icon name="pin" class="size-4" />
                        </div>

                        <div>
                            <p class="text-caption text-ink-500">Lokasi</p>
                            <p class="mt-0.5 text-body-sm leading-6 text-ink-700">
                                {{ $product->merchant->address }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Payment --}}
            <section
                class="rounded-2xl border border-primary-100 bg-primary-50 p-5"
                aria-labelledby="t-bayar">
                <div class="flex items-start gap-3">
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-surface text-primary-600 shadow-sm">
                        <x-fs.icon name="wallet" class="size-4" />
                    </div>

                    <div>
                        <h2 id="t-bayar" class="text-h4 font-semibold text-ink-900">
                            Pembayaran
                        </h2>

                        <p class="mt-2 text-body-sm leading-6 text-ink-700">
                            Bayar <strong>langsung kepada mitra</strong> saat mengambil makanan.
                            Metode pembayaran mengikuti yang diterima mitra.
                            FoodSave tidak menerima atau menampung uang makanan Anda.
                        </p>
                    </div>
                </div>
            </section>

            {{-- Price summary --}}
            <section
                class="rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60"
                aria-labelledby="t-ringkasan">
                <h2 id="t-ringkasan" class="text-h4 font-semibold text-ink-900">
                    Ringkasan harga
                </h2>

                <dl class="mt-4 space-y-3">
                    <div class="flex items-center justify-between gap-4 text-body-sm">
                        <dt class="text-ink-500">Jumlah</dt>
                        <dd class="font-medium text-ink-900">
                            {{ $quantity }} porsi
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 text-body-sm">
                        <dt class="text-ink-500">Harga per porsi</dt>
                        <dd class="font-medium text-ink-900">
                            {{ \App\Support\Rupiah::format($product->foodsave_price) }}
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 border-t border-line pt-3">
                        <dt class="text-body font-semibold text-ink-900">
                            Total dibayar ke mitra
                        </dt>

                        <dd class="text-lg font-bold text-primary-700">
                            {{ \App\Support\Rupiah::format($subtotal) }}
                        </dd>
                    </div>
                </dl>
            </section>

            {{-- Confirmation --}}
            <div class="rounded-2xl bg-surface p-4 shadow-card ring-1 ring-line/60">
                <label class="flex min-h-11 cursor-pointer items-start gap-3 text-body-sm text-ink-700">
                    <input
                        type="checkbox"
                        name="terms"
                        value="1"
                        required
                        class="mt-0.5 size-5 shrink-0 rounded border-line accent-primary-600 focus:ring-2 focus:ring-primary-200">

                    <span class="leading-6">
                        Saya akan mengambil pesanan pada waktu yang tertera dan membayar langsung kepada mitra.
                    </span>
                </label>

                @error('terms')
                <p class="mt-2 flex items-center gap-1 text-body-sm text-danger">
                    <x-fs.icon name="alert" class="size-4 shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            <x-fs.button type="submit" class="w-full">
                Buat Pesanan
            </x-fs.button>
        </form>
    </div>
</x-fs.layout>