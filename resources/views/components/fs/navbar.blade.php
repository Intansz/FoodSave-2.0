{{--
    Navbar minimalis.
    Desktop : Logo · 2 tautan · [Masuk] [Daftar]      (login: menu akun)
    Mobile  : Logo · ikon cari · ikon menu
    Dihapus dari versi Stitch: chip lokasi, "Tentang Kami", tombol "Jadi Mitra" (pindah ke hero & footer),
    avatar yang tampil bersamaan dengan Masuk/Daftar, dan bayangan tebal (diganti border tipis).
--}}
@php
$links = [
['label' => 'Jelajahi', 'href' => route('products.index'), 'active' => request()->routeIs('products.*')],
['label' => 'Cara Kerja', 'href' => route('home').'#cara-kerja', 'active' => false],
];
$user = auth()->user();
@endphp

<header x-data="{ open: false }" @keydown.escape.window="open = false"
    class="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 md:px-6 lg:px-10">
        <x-fs.logo />

        {{-- Desktop --}}
        <nav aria-label="Navigasi utama" class="hidden items-center gap-1 md:flex">
            @foreach ($links as $link)
            <a href="{{ $link['href'] }}" @if ($link['active']) aria-current="page" @endif
                @class([ 'rounded-lg px-3 py-2 text-body font-semibold transition-colors' , 'text-primary-700'=> $link['active'],
                'text-ink-700 hover:text-primary-700' => ! $link['active'],
                ])>{{ $link['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden items-center gap-2 md:flex">
            @guest
            <x-fs.button variant="ghost" :href="route('login')">Masuk</x-fs.button>
            <x-fs.button :href="route('register')">Daftar</x-fs.button>
            @else
            <div x-data="{ menu: false }" @click.outside="menu = false" @keydown.escape="menu = false" class="relative">
                <button type="button" @click="menu = !menu" :aria-expanded="menu" aria-haspopup="true"
                    class="inline-flex min-h-11 items-center gap-2 rounded-lg px-3 text-body font-semibold text-ink-900 hover:bg-primary-50">
                    <span class="flex size-8 items-center justify-center rounded-full bg-primary-100 text-primary-700">
                        {{ \Illuminate\Support\Str::of($user->name)->substr(0, 1)->upper() }}
                    </span>
                    <span class="max-w-32 truncate">{{ \Illuminate\Support\Str::before($user->name, ' ') }}</span>
                    <x-fs.icon name="chevron-down" class="size-4 text-ink-500" />
                </button>

                <div x-show="menu" x-cloak x-transition.opacity
                    class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-line bg-surface py-1 shadow-card-hover">
                    <p class="truncate px-4 py-2 text-caption text-ink-500">{{ $user->email }}</p>
                    @if ($user->isConsumer())
                    <a href="{{ route('orders.index') }}" class="block px-4 py-2.5 text-body text-ink-700 hover:bg-primary-50">Pesanan Saya</a>
                    @endif
                    @if ($user->isConsumer())
                    <a href="{{ url('/notifikasi') }}"
                        class="block px-4 py-2.5 text-body text-ink-700 transition-colors hover:bg-primary-50 focus:outline-none focus:bg-primary-50">
                        Notifikasi
                    </a>
                    @endif
                    @if ($user->isMerchant() && Route::has('merchant.dashboard'))
                    <a href="{{ route('merchant.dashboard') }}" class="block px-4 py-2.5 text-body text-ink-700 hover:bg-primary-50">Dashboard Mitra</a>
                    @endif
                    @if ($user->isAdmin() && Route::has('admin.dashboard'))
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2.5 text-body text-ink-700 hover:bg-primary-50">Dashboard Admin</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-line">
                        @csrf
                        <button type="submit" class="block w-full px-4 py-2.5 text-left text-body text-danger hover:bg-danger-bg">Keluar</button>
                    </form>
                </div>
            </div>
            @endguest
        </div>

        {{-- Mobile --}}
        <div class="flex items-center md:hidden">
            <a href="{{ route('products.index') }}" aria-label="Cari makanan"
                class="flex size-11 items-center justify-center rounded-lg text-ink-700 hover:bg-primary-50">
                <x-fs.icon name="search" />
            </a>
            <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile" aria-label="Menu"
                class="flex size-11 items-center justify-center rounded-lg text-ink-700 hover:bg-primary-50">
                <x-fs.icon name="menu" x-show="!open" />
                <x-fs.icon name="x" x-show="open" x-cloak />
            </button>
        </div>
    </div>

    <div id="menu-mobile" x-show="open" x-cloak x-transition.opacity class="border-t border-line bg-surface md:hidden">
        <nav aria-label="Navigasi mobile" class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-3">
            @foreach ($links as $link)
            <a href="{{ $link['href'] }}" @click="open = false"
                class="flex min-h-11 items-center rounded-lg px-3 text-body font-semibold {{ $link['active'] ? 'bg-primary-50 text-primary-700' : 'text-ink-700' }}">{{ $link['label'] }}</a>
            @endforeach

            @guest
            <div class="mt-2 grid grid-cols-2 gap-2 border-t border-line pt-3">
                <x-fs.button variant="secondary" :href="route('login')">Masuk</x-fs.button>
                <x-fs.button :href="route('register')">Daftar</x-fs.button>
            </div>
            @else
            @if ($user->isConsumer())
            <a href="{{ route('orders.index') }}" class="flex min-h-11 items-center rounded-lg px-3 text-body font-semibold text-ink-700">Pesanan Saya</a>
            @endif
            <a href="{{ url('/notifikasi') }}"
                class="flex min-h-11 items-center rounded-lg px-3 text-body font-semibold text-ink-700 transition-colors hover:bg-primary-50 focus:outline-none focus:bg-primary-50">
                Notifikasi
            </a>
            @if ($user->isMerchant() && Route::has('merchant.dashboard'))
            <a href="{{ route('merchant.dashboard') }}" class="flex min-h-11 items-center rounded-lg px-3 text-body font-semibold text-ink-700">Dashboard Mitra</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="mt-2 border-t border-line pt-3">
                @csrf
                <button type="submit" class="flex min-h-11 w-full items-center rounded-lg px-3 text-body font-semibold text-danger">Keluar ({{ $user->name }})</button>
            </form>
            @endguest
        </nav>
    </div>
</header>