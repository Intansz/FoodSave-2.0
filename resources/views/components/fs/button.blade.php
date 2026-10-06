@props(['variant' => 'primary', 'href' => null, 'type' => 'button'])

@php
    $base = 'inline-flex min-h-11 items-center justify-center gap-2 rounded-lg px-5 text-body font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-50';

    $variants = [
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700',
        'secondary' => 'border border-line bg-surface text-primary-700 hover:bg-primary-50',
        'ghost' => 'text-ink-700 hover:bg-primary-50',
        'danger' => 'bg-danger text-white hover:opacity-90',
    ];

    $classes = $base.' '.($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</button>
@endif
