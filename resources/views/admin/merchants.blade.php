<x-fs.layout>
    <section class="mx-auto max-w-7xl px-4 py-8 md:px-6 lg:px-10">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-h2 font-bold text-ink-900">Daftar Mitra</h1>
                <p class="mt-1 text-body text-ink-500">
                    Kelola status verifikasi mitra FoodSave
                </p>
            </div>

            <p class="text-sm text-ink-500">
                {{ $merchants->total() }} mitra
            </p>
        </div>

        <div class="mt-6 space-y-4">
            @forelse ($merchants as $merchant)
            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <h2 class="font-semibold text-ink-900">
                                {{ $merchant->business_name }}
                            </h2>

                            <span class="inline-flex w-fit rounded-full px-3 py-1 text-xs font-semibold
                                    {{ $merchant->verification_status->value === 'approved'
                                        ? 'bg-primary-50 text-primary-700'
                                        : ($merchant->verification_status->value === 'pending'
                                            ? 'bg-yellow-50 text-yellow-700'
                                            : 'bg-ink-100 text-ink-600') }}">
                                {{ $merchant->verification_status->label() }}
                            </span>
                        </div>

                        <p class="mt-1 text-sm text-ink-500">
                            {{ $merchant->user->email }}
                        </p>

                        <p class="mt-2 text-sm text-ink-500">
                            {{ $merchant->area ?: 'Area belum diisi' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2 lg:shrink-0">
                        @if ($merchant->verification_status->value === 'pending')
                        <form
                            action="{{ route('admin.merchants.status', $merchant) }}"
                            method="POST">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="verification_status" value="approved">

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-200">
                                Setujui
                            </button>
                        </form>
                        @endif

                        @if ($merchant->verification_status->value === 'approved')
                        <form
                            action="{{ route('admin.merchants.status', $merchant) }}"
                            method="POST">
                            @csrf
                            @method('PATCH')

                            <input type="hidden" name="verification_status" value="suspended">

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-xl border border-ink-200 px-4 py-2.5 text-sm font-semibold text-ink-700 transition hover:bg-ink-50 focus:outline-none focus:ring-2 focus:ring-primary-200">
                                Tangguhkan Mitra
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-2xl bg-surface p-8 text-center shadow-card">
                <p class="text-sm text-ink-500">
                    Belum ada mitra.
                </p>
            </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $merchants->links() }}
        </div>
    </section>
</x-fs.layout>