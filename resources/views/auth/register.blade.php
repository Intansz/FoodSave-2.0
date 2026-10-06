<x-fs.layout title="Daftar">
    <div class="mx-auto max-w-md px-4 py-10 sm:py-14">
        <div class="text-center">
            <h1 class="text-h2 font-bold text-ink-900">Buat akun FoodSave</h1>

            <p class="mt-2 text-body-sm text-ink-500">
                Sudah punya akun?
                <a
                    href="{{ route('login') }}"
                    class="font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                    Masuk
                </a>
            </p>
        </div>

        <form
            action="{{ url('/daftar') }}"
            method="POST"
            class="mt-7 space-y-5 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            @csrf

            <x-fs.input
                label="Nama lengkap"
                name="name"
                autocomplete="name"
                required />

            <x-fs.input
                label="Email"
                name="email"
                type="email"
                autocomplete="email"
                required />

            <x-fs.input
                label="Nomor HP (opsional)"
                name="phone"
                type="tel"
                autocomplete="tel" />

            <x-fs.input
                label="Kata sandi"
                name="password"
                type="password"
                autocomplete="new-password"
                hint="Minimal 8 karakter."
                required />

            <x-fs.input
                label="Ulangi kata sandi"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required />

            <x-fs.button type="submit" class="w-full">
                Daftar
            </x-fs.button>
        </form>

        <p class="mt-5 text-center text-body-sm text-ink-500">
            Punya usaha kuliner?
            <a
                href="{{ route('register.merchant') }}"
                class="font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                Daftar sebagai mitra
            </a>
        </p>
    </div>
</x-fs.layout>