@props(['value' => '', 'category' => null])

<form action="{{ route('products.index') }}" method="GET" role="search" {{ $attributes->class(['flex items-center gap-2 rounded-xl bg-surface p-2 shadow-card']) }}>
    @if ($category)
        <input type="hidden" name="category" value="{{ $category }}">
    @endif

    <label for="q" class="sr-only">Cari makanan</label>
    <x-fs.icon name="search" class="ml-2 size-5 shrink-0 text-ink-500" />
    <input
        id="q" name="q" type="search" value="{{ $value }}" maxlength="100"
        placeholder="Cari makanan atau nama mitra"
        aria-label="Cari makanan"
        class="min-h-11 min-w-0 flex-1 bg-transparent text-body text-ink-900 placeholder:text-ink-500 focus:outline-none"
    >
    <x-fs.button type="submit">Cari</x-fs.button>
</form>
