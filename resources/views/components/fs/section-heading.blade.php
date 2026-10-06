@props(['title', 'subtitle' => null])

<div {{ $attributes->class(['flex items-end justify-between gap-4']) }}>
    <div>
        <h2 class="text-h3 font-semibold text-ink-900">{{ $title }}</h2>
        @if ($subtitle)
            <p class="mt-1 text-body-sm text-ink-500">{{ $subtitle }}</p>
        @endif
    </div>
    {{ $slot }}
</div>
