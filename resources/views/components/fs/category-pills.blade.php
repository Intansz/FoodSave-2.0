@props(['categories', 'active' => '', 'flash' => false, 'query' => []])

@php
$base = collect($query)->except(['category', 'flash', 'page'])->filter()->all();

$pill = 'inline-flex min-h-11 shrink-0 items-center justify-center gap-1.5 rounded-full px-4 text-body-sm font-semibold whitespace-nowrap transition-colors focus:outline-none focus:ring-2 focus:ring-primary-200';

$on = 'bg-primary-600 text-white shadow-sm';

$off = 'bg-surface text-ink-700 ring-1 ring-line hover:bg-primary-50 hover:text-primary-700';
@endphp

<nav aria-label="Kategori makanan">
    <div class="rounded-2xl bg-surface/70 p-2 shadow-sm ring-1 ring-line/60">
        <ul class="no-scrollbar flex gap-2 overflow-x-auto px-1 py-1 md:flex-wrap md:justify-center md:overflow-visible">
            <li>
                <a
                    href="{{ route('products.index', $base) }}"
                    @class([
                    $pill,
                    $on=> $active === '' && ! $flash,
                    $off => $active !== '' || $flash,
                    ])
                    @if ($active === '' && ! $flash) aria-current="page" @endif
                    >
                    Semua
                </a>
            </li>

            <li>
                <a
                    href="{{ route('products.index', [...$base, 'flash' => 1]) }}"
                    @class([
                    $pill,
                    $on=> $flash,
                    $off => ! $flash,
                    ])
                    @if ($flash) aria-current="page" @endif
                    >
                    <x-fs.icon name="bolt" class="size-4" />
                    Flash Deal
                </a>
            </li>

            @foreach ($categories as $category)
            <li>
                <a
                    href="{{ route('products.index', [...$base, 'category' => $category->slug]) }}"
                    @class([
                    $pill,
                    $on=> $active === $category->slug,
                    $off => $active !== $category->slug,
                    ])
                    @if ($active === $category->slug) aria-current="page" @endif
                    >
                    {{ $category->name }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</nav>