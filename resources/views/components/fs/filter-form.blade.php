@props(['filters'])

{{-- Dipakai dua kali di halaman Explore: sidebar (desktop) dan bottom sheet (mobile). --}}

<form
    action="{{ route('products.index') }}"
    method="GET"
    aria-label="Filter makanan"
    class="space-y-5">
    @foreach (['q', 'category', 'sort'] as $keep)
    @if (filled($filters[$keep] ?? null) && ! ($keep === 'sort' && $filters['sort'] === 'terbaru'))
    <input
        type="hidden"
        name="{{ $keep }}"
        value="{{ $filters[$keep] }}">
    @endif
    @endforeach

    @if ($filters['flash'])
    <input type="hidden" name="flash" value="1">
    @endif

    <div>
        <label
            for="filter-harga"
            class="block text-body-sm font-semibold text-ink-900">
            Rentang harga
        </label>

        <select
            id="filter-harga"
            name="harga"
            class="mt-2 block min-h-11 w-full rounded-xl border border-line bg-surface px-3 text-body-sm text-ink-900 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
            <option value="">Semua harga</option>

            @foreach (\App\Http\Controllers\ProductController::PRICE_RANGES as $key => $range)
            <option
                value="{{ $key }}"
                @selected($filters['harga']===$key)>
                {{ $range['label'] }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label
            for="filter-area"
            class="block text-body-sm font-semibold text-ink-900">
            Lokasi mitra
        </label>

        <select
            id="filter-area"
            name="area"
            class="mt-2 block min-h-11 w-full rounded-xl border border-line bg-surface px-3 text-body-sm text-ink-900 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
            <option value="">Semua wilayah</option>

            @foreach (config('foodsave.areas') as $area)
            <option
                value="{{ $area }}"
                @selected($filters['area']===$area)>
                {{ $area }}
            </option>
            @endforeach
        </select>
    </div>

    <div class="flex flex-col gap-2 pt-1 sm:flex-row">
        <x-fs.button
            type="submit"
            class="flex-1">
            Terapkan
        </x-fs.button>

        <x-fs.button
            variant="secondary"
            :href="route('products.index', array_filter([
                'q' => $filters['q'],
                'category' => $filters['category'],
            ]))"
            class="flex-1">
            Reset
        </x-fs.button>
    </div>
</form>