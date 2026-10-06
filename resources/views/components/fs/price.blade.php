@props(['price', 'normal' => null, 'size' => 'md'])

{{-- Harga FoodSave = visual anchor; harga normal dicoret & lebih kecil (design.md §11.2). --}}
<div {{ $attributes->class(['flex flex-wrap items-baseline gap-x-2']) }}>
    <span @class([
        'font-bold text-primary-700',
        'text-h4' => $size === 'md',
        'text-h2' => $size === 'lg',
    ])>{{ \App\Support\Rupiah::format($price) }}</span>

    @if ($normal && $normal > $price)
        <span class="text-body-sm text-ink-500 line-through">
            <span class="sr-only">Harga normal</span>{{ \App\Support\Rupiah::format($normal) }}
        </span>
    @endif
</div>
