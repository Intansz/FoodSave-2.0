<x-fs.layout title="Jadi Mitra">
    <div class="mx-auto max-w-xl px-4 py-10 sm:py-14">
        <div class="text-center">
            <h1 class="text-h2 font-bold text-ink-900">
                Daftar sebagai Mitra
            </h1>

            <p class="mt-2 text-body-sm leading-6 text-ink-500">
                Pengajuan akan diverifikasi admin sebelum Anda bisa memasang penawaran.
            </p>
        </div>

        <form
            action="{{ url('/jadi-mitra') }}"
            method="POST"
            class="mt-7 space-y-5">
            @csrf

            {{-- Data usaha --}}
            <fieldset class="space-y-5 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6">
                <legend class="sr-only">Data usaha</legend>

                <div>
                    <h2 class="text-h4 font-semibold text-ink-900">
                        Data usaha
                    </h2>
                    <p class="mt-1 text-body-sm text-ink-500">
                        Informasi usaha yang akan ditampilkan kepada konsumen.
                    </p>
                </div>

                <x-fs.input
                    label="Nama usaha"
                    name="business_name"
                    required />

                <div>
                    <label
                        for="area"
                        class="block text-body-sm font-semibold text-ink-900">
                        Wilayah
                    </label>

                    <select
                        id="area"
                        name="area"
                        required
                        class="mt-2 block min-h-11 w-full rounded-xl border border-line bg-surface px-3 text-body-sm text-ink-900 transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">Pilih wilayah</option>

                        @foreach (config('foodsave.areas') as $area)
                        <option
                            value="{{ $area }}"
                            @selected(old('area')===$area)>
                            {{ $area }}
                        </option>
                        @endforeach
                    </select>

                    @error('area')
                    <p class="mt-1.5 flex items-center gap-1 text-body-sm text-danger">
                        <x-fs.icon name="alert" class="size-4 shrink-0" />
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <div>
                    <label
                        for="address"
                        class="block text-body-sm font-semibold text-ink-900">
                        Alamat lengkap
                    </label>

                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                        class="mt-2 block w-full rounded-xl border border-line bg-surface px-3 py-2.5 text-body-sm text-ink-900 transition placeholder:text-ink-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">{{ old('address') }}</textarea>

                    @error('address')
                    <p class="mt-1.5 flex items-center gap-1 text-body-sm text-danger">
                        <x-fs.icon name="alert" class="size-4 shrink-0" />
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <x-fs.input
                    label="Link Google Maps (opsional)"
                    name="maps_url"
                    type="url"
                    hint="Gunakan link yang diawali https://" />

                <div>
                    <label
                        for="description"
                        class="block text-body-sm font-semibold text-ink-900">
                        Deskripsi usaha (opsional)
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        rows="3"
                        class="mt-2 block w-full rounded-xl border border-line bg-surface px-3 py-2.5 text-body-sm text-ink-900 transition placeholder:text-ink-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">{{ old('description') }}</textarea>
                </div>
            </fieldset>

            {{-- Akun pemilik --}}
            <fieldset class="space-y-5 rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 sm:p-6">
                <legend class="sr-only">Akun pemilik</legend>

                <div>
                    <h2 class="text-h4 font-semibold text-ink-900">
                        Akun pemilik
                    </h2>
                    <p class="mt-1 text-body-sm text-ink-500">
                        Data ini digunakan untuk masuk dan mengelola usaha.
                    </p>
                </div>

                <x-fs.input
                    label="Nama pemilik"
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
                    label="Nomor HP / WhatsApp"
                    name="phone"
                    type="tel"
                    autocomplete="tel"
                    required />

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
            </fieldset>

            <x-fs.button type="submit" class="w-full">
                Ajukan Pendaftaran
            </x-fs.button>
        </form>
    </div>
</x-fs.layout>