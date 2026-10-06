<x-fs.layout>
    <section class="mx-auto max-w-7xl px-4 py-8 md:px-6 lg:px-10">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-h2 font-bold text-ink-900">Settlement</h1>
                <p class="mt-1 text-body text-ink-500">
                    Kelola service fee mitra yang belum disettle
                </p>
            </div>

            <p class="text-sm text-ink-500">
                {{ $fees->count() }} fee belum disettle
            </p>
        </div>

        @if (session('status'))
        <div class="mt-5 rounded-2xl border border-primary-100 bg-primary-50 px-4 py-3 text-sm text-primary-700">
            {{ session('status') }}
        </div>
        @endif

        <div class="mt-6 space-y-4">
            @forelse ($fees->groupBy('merchant_id') as $merchantId => $merchantFees)
            @php
            $merchant = $merchantFees->first()->merchant;
            $totalFee = $merchantFees->sum('fee_amount');
            @endphp

            <div class="rounded-2xl bg-surface p-5 shadow-card">
                <div class="flex flex-col gap-5">
                    <div class="min-w-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <h2 class="font-semibold text-ink-900">
                                {{ $merchant->business_name }}
                            </h2>

                            <span class="inline-flex w-fit rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">
                                Belum Disettle
                            </span>
                        </div>

                        <p class="mt-2 text-sm text-ink-500">
                            {{ $merchantFees->count() }} service fee menunggu settlement
                        </p>


                        <div class="mt-4 flex justify-center">
                            @foreach ($merchantFees as $fee)
                            <div class="w-full max-w-2xl rounded-xl border border-gray-200 p-4">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="font-semibold text-ink-900">
                                            Pesanan #{{ $fee->order->id }}
                                        </p>

                                        <p class="mt-1 text-sm text-ink-500">
                                            Selesai:
                                            {{ $fee->order->completed_at?->format('d/m/Y H:i') ?? '-' }}
                                        </p>
                                    </div>

                                    <span class="w-fit rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">
                                        Belum Disettle
                                    </span>
                                </div>

                                <div class="mt-3 space-y-2">
                                    @foreach ($fee->order->items as $item)
                                    <div class="flex justify-between gap-4 text-sm">
                                        <div>
                                            <p class="font-medium text-ink-900">
                                                {{ $item->product_name_snapshot }}
                                            </p>
                                            <p class="text-ink-500">
                                                {{ $item->quantity }} ×
                                                Rp{{ number_format($item->unit_price, 0, ',', '.') }}
                                            </p>
                                        </div>

                                        <p class="shrink-0 font-medium text-ink-900">
                                            Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>
                                    @endforeach
                                </div>

                                <div class="mt-3 border-t border-gray-200 pt-3 text-sm">
                                    <div class="flex justify-between gap-4">
                                        <span class="text-ink-500">Nilai transaksi</span>
                                        <span class="font-medium">
                                            Rp{{ number_format($fee->transaction_amount, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="mt-1 flex justify-between gap-4">
                                        <span class="text-ink-500">
                                            Service fee ({{ number_format($fee->fee_percentage, 2, ',', '.') }}%)
                                        </span>
                                        <span class="font-bold text-ink-900">
                                            Rp{{ number_format($fee->fee_amount, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <form
                        action="{{ route('admin.settlements.settle', $merchant) }}"
                        method="POST"
                        class="self-end">
                        @csrf

                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-200 sm:w-auto">
                            Tandai Sudah Disettle
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="rounded-2xl bg-surface p-8 text-center shadow-card">
                <p class="text-sm text-ink-500">
                    Tidak ada service fee yang belum disettle.
                </p>
            </div>
            @endforelse
        </div>
    </section>
</x-fs.layout>