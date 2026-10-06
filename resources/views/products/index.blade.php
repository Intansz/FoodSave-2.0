<x-fs.layout title="Jelajahi Makanan">
    <div
        class="mx-auto max-w-7xl px-4 py-8 md:px-6 lg:px-10"
        x-data="{ sheet: false }"
        @keydown.escape.window="sheet = false">
        {{-- HEADER --}}
        <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <h1 class="text-h2 font-bold text-ink-900">
                    Jelajahi Makanan
                </h1>

                <p class="mt-1 text-body-sm text-ink-500" aria-live="polite">
                    {{ $products->total() }} makanan tersedia
                </p>
            </div>

            <x-fs.search-bar
                class="w-full md:max-w-md"
                :value="$filters['q']"
                :category="$filters['category']" />
        </div>

        {{-- KATEGORI --}}
        <div class="mt-5">
            <x-fs.category-pills
                :categories="$categories"
                :active="$filters['category']"
                :flash="$filters['flash']"
                :query="[
                    'q' => $filters['q'],
                    'area' => $filters['area'],
                    'harga' => $filters['harga'],
                    'sort' => $filters['sort'] === 'terbaru' ? '' : $filters['sort']
                ]" />
        </div>

        {{-- FILTER & SORT --}}
        <div class="mt-6 flex items-center justify-between gap-3">
            <button
                type="button"
                @click="sheet = true"
                class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-line bg-surface px-4 text-body-sm font-semibold text-ink-700 transition hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-200 lg:hidden">
                <x-fs.icon name="sliders" class="size-4" />
                Filter
            </button>

            <form
                action="{{ route('products.index') }}"
                method="GET"
                class="ml-auto flex items-center gap-2">
                @foreach (['q', 'category', 'area', 'harga'] as $keep)
                @if (filled($filters[$keep]))
                <input
                    type="hidden"
                    name="{{ $keep }}"
                    value="{{ $filters[$keep] }}">
                @endif
                @endforeach

                @if ($filters['flash'])
                <input type="hidden" name="flash" value="1">
                @endif

                <label
                    for="sort"
                    class="hidden text-body-sm text-ink-500 sm:inline">
                    Urutkan
                </label>

                <div class="relative">
                    <select
                        id="sort"
                        name="sort"
                        @change="$el.form.requestSubmit()"
                        class="min-h-11 appearance-none rounded-xl border border-line bg-surface py-2 pl-3 pr-8 text-body-sm text-ink-900 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        @foreach (\App\Http\Controllers\ProductController::SORTS as $key => $label)
                        <option value="{{ $key }}" @selected($filters['sort']===$key)>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-ink-600">
                        <x-fs.icon name="chevron-down" class="size-4" />
                    </span>
                </div>
                <noscript>
                    <x-fs.button type="submit" variant="secondary">
                        Terapkan
                    </x-fs.button>
                </noscript>
            </form>
        </div>

        {{-- CONTENT --}}
        <div class="mt-5 grid gap-8 lg:grid-cols-[240px_1fr]">
            {{-- SIDEBAR FILTER --}}
            <aside class="hidden lg:block">
                <div class="sticky top-24 rounded-2xl bg-surface p-5 shadow-card">
                    <h2 class="mb-4 text-h4 font-semibold text-ink-900">
                        Filter
                    </h2>

                    <x-fs.filter-form :filters="$filters" />
                </div>
            </aside>

            {{-- PRODUCT LIST --}}
            <section aria-label="Daftar makanan">
                @if ($products->isEmpty())
                <x-fs.empty-state
                    title="Makanan tidak ditemukan"
                    description="Coba ubah kata kunci atau reset filter."
                    action-label="Reset filter"
                    :action-href="route('products.index')" />
                @else
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                    @foreach ($products as $product)
                    <x-fs.product-card :product="$product" />
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
                @endif
            </section>
        </div>

        {{-- MOBILE FILTER SHEET --}}
        <div
            x-show="sheet"
            x-cloak
            class="fixed inset-0 z-50 lg:hidden"
            role="dialog"
            aria-modal="true"
            aria-label="Filter makanan">
            <div
                class="absolute inset-0 bg-ink-900/40"
                @click="sheet = false"></div>

            <div
                class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-3xl bg-surface p-5 shadow-xl"
                x-transition:enter="transition duration-200 ease-out"
                x-transition:enter-start="translate-y-full"
                x-transition:enter-end="translate-y-0">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-h4 font-semibold text-ink-900">
                            Filter
                        </h2>
                        <p class="mt-1 text-sm text-ink-500">
                            Sesuaikan makanan yang ingin kamu cari.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="sheet = false"
                        aria-label="Tutup filter"
                        class="flex size-11 items-center justify-center rounded-xl text-ink-600 transition hover:bg-primary-50 hover:text-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-200">
                        <x-fs.icon name="x" />
                    </button>
                </div>

                <x-fs.filter-form :filters="$filters" />
            </div>
        </div>
    </div>
</x-fs.layout>