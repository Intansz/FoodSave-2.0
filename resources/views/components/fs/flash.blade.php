{{-- Pesan sukses (role=status) dan error (role=alert). Tidak hilang otomatis, jadi selalu terbaca. --}}
@if (session('status') || $errors->has('order'))
    <div class="mx-auto w-full max-w-7xl px-4 pt-4 md:px-6 lg:px-10">
        @if (session('status'))
            <div role="status" class="flex items-start gap-2 rounded-xl bg-success-bg p-4 text-body-sm text-success">
                <x-fs.icon name="check" class="mt-0.5 size-5 shrink-0" />
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->has('order'))
            <div role="alert" class="flex items-start gap-2 rounded-xl bg-danger-bg p-4 text-body-sm text-danger">
                <x-fs.icon name="alert" class="mt-0.5 size-5 shrink-0" />
                <p>{{ $errors->first('order') }}</p>
            </div>
        @endif
    </div>
@endif
