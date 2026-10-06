<x-fs.layout title="Notifikasi">
    <section class="mx-auto max-w-4xl px-4 py-8 md:px-6">
        <div>
            <h1 class="text-h2 font-bold text-ink-900">Notifikasi</h1>
            <p class="mt-1 text-body text-ink-500">
                Informasi terbaru tentang pesanan kamu.
            </p>
        </div>

        <div class="mt-6 space-y-3">
            @forelse ($notifications as $notification)
            <div class="rounded-2xl bg-surface p-5 shadow-card ring-1 ring-line/60 transition-shadow hover:shadow-card-hover">
                <div class="flex items-start gap-4">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                        <x-fs.icon name="bell" class="size-5" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-body font-semibold leading-6 text-ink-900">
                            {{ $notification->data['message'] ?? 'Ada pembaruan pada pesanan kamu.' }}
                        </p>

                        <p class="mt-1.5 text-caption text-ink-500">
                            {{ $notification->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
            </div>
            @empty
            <div class="rounded-2xl bg-surface p-8 text-center shadow-card ring-1 ring-line/60">
                <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                    <x-fs.icon name="bell" class="size-6" />
                </div>

                <p class="mt-4 text-body font-semibold text-ink-900">
                    Belum ada notifikasi
                </p>

                <p class="mt-1 text-sm text-ink-500">
                    Pembaruan status pesanan kamu akan muncul di sini.
                </p>
            </div>
            @endforelse
        </div>

        @if ($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
        @endif
    </section>
</x-fs.layout>