<x-fs.layout>
    <section class="mx-auto max-w-7xl px-4 py-8 md:px-6 lg:px-10">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-h2 font-bold text-ink-900">Produk Saya</h1>
                <p class="mt-1 text-body text-ink-500">
                    Kelola produk dari {{ $merchant->business_name }}
                </p>
            </div>

            <a
                href="{{ route('merchant.products.create') }}"
                class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white">
                + Tambah Produk
            </a>
        </div>

        <div class="mt-6 space-y-3">
            @forelse ($products as $product)
            <div class="flex flex-col gap-4 rounded-2xl bg-surface p-4 shadow-card sm:flex-row sm:items-center">
                <div class="h-24 w-24 shrink-0 overflow-hidden rounded-xl bg-ink-50">
                    @if ($product->image_url)
                    <img
                        src="{{ $product->image_url }}"
                        alt="{{ $product->name }}"
                        class="h-full w-full object-cover">
                    @else
                    <div class="flex h-full w-full items-center justify-center text-xs text-ink-400">
                        No image
                    </div>
                    @endif
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="min-w-0">
                            <h2 class="truncate font-semibold text-ink-900">
                                {{ $product->name }}
                            </h2>

                            <p class="mt-1 text-sm text-ink-500">
                                {{ $product->category->name }}
                                <span class="mx-1">·</span>
                                Stok {{ $product->stock }}
                            </p>

                            <p class="mt-2 font-semibold text-ink-900">
                                Rp{{ number_format($product->foodsave_price, 0, ',', '.') }}
                            </p>
                        </div>

                        <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold
                        {{ $product->status === 'active'
                            ? 'bg-primary-50 text-primary-700'
                            : 'bg-ink-100 text-ink-600' }}">
                            {{ $product->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    <div class="mt-3">
                        <a
                            href="{{ route('merchant.products.edit', $product) }}"
                            class="inline-flex items-center rounded-xl border border-ink-200 px-4 py-2 text-sm font-semibold text-ink-700 transition hover:bg-ink-50">
                            Edit Produk
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-2xl bg-surface p-8 text-center shadow-card">
                <p class="text-sm text-ink-500">
                    Belum ada produk.
                </p>

                <a
                    href="{{ route('merchant.products.create') }}"
                    class="mt-3 inline-flex text-sm font-semibold text-primary-700">
                    Tambah produk pertama
                </a>
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $products->links() }}
        </div>
    </section>
</x-fs.layout>