@props(['icon' => 'search', 'title', 'description' => null, 'actionLabel' => null, 'actionHref' => null])

<div {{ $attributes->class(['flex flex-col items-center rounded-2xl bg-surface px-6 py-14 text-center shadow-card']) }}>
    <span class="flex size-14 items-center justify-center rounded-full bg-primary-50 text-primary-600">
        <x-fs.icon :name="$icon" class="size-7" />
    </span>
    <h3 class="mt-4 text-h4 font-semibold text-ink-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 max-w-sm text-body text-ink-500">{{ $description }}</p>
    @endif
    @if ($actionLabel && $actionHref)
        <x-fs.button :href="$actionHref" class="mt-6">{{ $actionLabel }}</x-fs.button>
    @endif
</div>
