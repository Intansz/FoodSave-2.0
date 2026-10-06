@props(['stock'])

@php
    $tone = match (true) {
        $stock <= 2 => 'bg-danger-bg text-danger',
        $stock <= 5 => 'bg-warning-bg text-warning',
        default => 'bg-success-bg text-success',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-caption font-semibold', $tone]) }}>
    <span class="size-1.5 rounded-full bg-current" aria-hidden="true"></span>
    Sisa {{ $stock }}
</span>
