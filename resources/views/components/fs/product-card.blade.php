@props(['product'])

@php
$label = sprintf(
'%s, %s, stok %d, ambil %s',
$product->name,
\App\Support\Rupiah::format($product->foodsave_price),
$product->stock,
$product->pickup_label
);
@endphp

<a
    href="{{ route('products.show', $product) }}"
    aria-label="{{ $label }}"
    {{ $attributes->class([
        'group flex h-full flex-col overflow-hidden rounded-2xl bg-surface shadow-card ring-1 ring-transparent transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover hover:ring-primary-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-300',
    ]) }}>
    <div class="relative aspect-4/3 overflow-hidden bg-primary-50">
        @if ($product->image_url)
        <img
            src="{{ $product->image_url }}"
            alt="{{ $product->name }} dari {{ $product->merchant->business_name }}"
            loading="lazy"
            class="size-full object-cover transition-transform duration-300 group-hover:scale-105">
        @else
        <div class="flex size-full items-center justify-center text-primary-500">
            <x-fs.icon name="leaf" class="size-10" />
        </div>
        @endif

        @if ($product->discount_percentage > 0)
        <x-fs.discount-badge
            :value="$product->discount_percentage"
            class="absolute left-3 top-3" />
        @endif
    </div>

    <div class="flex flex-1 flex-col gap-1 p-4">
        <h3 class="line-clamp-2 min-h-12 text-body font-semibold leading-6 text-ink-900">
            {{ $product->name }}
        </h3>

        <p class="truncate text-caption text-ink-500">
            {{ $product->merchant->business_name }} · {{ $product->merchant->area }}
        </p>

        <x-fs.price
            :price="$product->foodsave_price"
            :normal="$product->normal_price"
            class="mt-2" />

        <div class="mt-auto flex flex-wrap items-center justify-between gap-2 pt-3">
            <x-fs.stock-badge :stock="$product->stock" />

            <span class="inline-flex items-center gap-1 text-caption font-medium text-ink-700">
                <x-fs.icon name="clock" class="size-3.5 text-ink-500" />
                {{ $product->pickup_label }}
            </span>
        </div>
    </div>
</a>