<a
    href="{{ route('home') }}"
    aria-label="FoodSave — beranda"
    {{ $attributes->class([
        'inline-flex items-center justify-center',
        'focus:outline-none focus-visible:ring-2',
        'focus-visible:ring-primary-300 rounded-lg',
    ]) }}>
    <img
        src="{{ asset('images/foodsave-logo.png') }}"
        alt="FoodSave"
        class="h-auto w-28 object-contain sm:w-32">
</a>