@props(['status'])

@php
    $tones = [
        'warning' => 'bg-warning-bg text-warning',
        'brand' => 'bg-primary-100 text-primary-700',
        'success' => 'bg-success-bg text-success',
        'danger' => 'bg-danger-bg text-danger',
    ];
@endphp

{{-- Status = ikon + teks, tidak hanya warna (design.md §7.3). --}}
<span {{ $attributes->class(['inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-caption font-semibold', $tones[$status->tone()]]) }}>
    <x-fs.icon :name="$status->icon()" class="size-3.5" />
    {{ $status->label() }}
</span>
