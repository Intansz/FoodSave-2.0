<x-fs.layout title="Masuk">
    <div class="mx-auto max-w-md px-4 py-10 sm:py-14">
        <div class="text-center">
            <h1 class="text-h2 font-bold text-ink-900">Masuk ke FoodSave</h1>

            <p class="mt-2 text-body-sm text-ink-500">
                Belum punya akun?
                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-primary-700 transition hover:text-primary-800 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-200">
                    Daftar
                </a>
            </p>
        </div>

        <form
            action="{{ url('/masuk') }}"
            method="POST"
            class="mt-7 space-y-5 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6">
            @csrf

            <x-fs.input
                label="Email"
                name="email"
                type="email"
                autocomplete="email"
                required
                autofocus />

            <x-fs.input
                label="Kata sandi"
                name="password"
                type="password"
                autocomplete="current-password"
                required />

            <label class="flex min-h-11 cursor-pointer items-center gap-3 text-body-sm text-ink-700">
                <input
                    type="checkbox"
                    name="remember"
                    class="size-5 rounded border-line accent-primary-600 focus:ring-2 focus:ring-primary-200">
                <span>Ingat saya</span>
            </label>

            <x-fs.button type="submit" class="w-full">
                Masuk
            </x-fs.button>
        </form>
    </div>
</x-fs.layout>