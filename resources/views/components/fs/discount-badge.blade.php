@props(['value'])

{{-- Aksen #F59E0B dengan teks gelap (kontras aman); jangan pakai teks putih di atas accent. --}}
<span {{ $attributes->class(['inline-flex items-center rounded-full bg-accent px-2.5 py-1 text-caption font-bold text-ink-900 shadow-sm']) }}>
    -{{ $value }}%
</span>
