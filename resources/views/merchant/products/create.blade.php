<x-fs.layout>
    <section class="mx-auto max-w-4xl px-4 py-8 md:px-6 lg:px-10">
        <div>
            <h1 class="text-h2 font-bold text-ink-900">Tambah Produk</h1>
            <p class="mt-1 text-body text-ink-500">
                Tambahkan produk untuk {{ $merchant->business_name }}
            </p>
        </div>

        <form
            action="{{ route('merchant.products.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="mt-8 rounded-2xl bg-surface p-5 shadow-card sm:p-6">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-ink-800">
                        Nama Produk
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                        placeholder="Contoh: Nasi Ayam Teriyaki">
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-ink-800">
                        Kategori
                    </label>
                    <select
                        id="category_id"
                        name="category_id"
                        required
                        class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                        <option value="">Pilih kategori</option>

                        @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected(old('category_id')==$category->id)
                            >
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-ink-800">
                        Deskripsi
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                        placeholder="Jelaskan produk secara singkat">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="image" class="block text-sm font-semibold text-ink-800">
                        Gambar Produk
                    </label>

                    <input
                        id="image"
                        name="image"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 text-sm outline-none transition file:mr-4 file:rounded-lg file:border-0 file:bg-primary-50 file:px-3 file:py-2 file:font-semibold file:text-primary-700 hover:file:bg-primary-100">

                    <p class="mt-1 text-xs text-ink-500">
                        Format JPG, PNG, atau WebP. Maksimal 2 MB.
                    </p>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="normal_price" class="block text-sm font-semibold text-ink-800">
                            Harga Normal
                        </label>
                        <input
                            id="normal_price"
                            name="normal_price"
                            type="number"
                            min="0"
                            required
                            value="{{ old('normal_price') }}"
                            class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                            placeholder="25000">
                    </div>

                    <div>
                        <label for="foodsave_price" class="block text-sm font-semibold text-ink-800">
                            Harga FoodSave
                        </label>
                        <input
                            id="foodsave_price"
                            name="foodsave_price"
                            type="number"
                            min="0"
                            required
                            value="{{ old('foodsave_price') }}"
                            class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100"
                            placeholder="10000">
                    </div>
                </div>

                <div>
                    <label for="stock" class="block text-sm font-semibold text-ink-800">
                        Stok
                    </label>
                    <input
                        id="stock"
                        name="stock"
                        type="number"
                        min="0"
                        required
                        value="{{ old('stock', 0) }}"
                        class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="pickup_start" class="block text-sm font-semibold text-ink-800">
                            Pickup Mulai
                        </label>
                        <input
                            id="pickup_start"
                            name="pickup_start"
                            type="datetime-local"
                            required
                            value="{{ old('pickup_start') }}"
                            class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                    </div>

                    <div>
                        <label for="pickup_end" class="block text-sm font-semibold text-ink-800">
                            Pickup Selesai
                        </label>
                        <input
                            id="pickup_end"
                            name="pickup_end"
                            type="datetime-local"
                            required
                            value="{{ old('pickup_end') }}"
                            class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                    </div>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-ink-800">
                        Status
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="mt-2 w-full rounded-xl border border-ink-200 px-4 py-3 outline-none transition focus:border-primary-500 focus:ring-2 focus:ring-primary-100">
                        <option
                            value="active"
                            @selected(old('status', 'active' )==='active' )>
                            Aktif
                        </option>

                        <option
                            value="inactive"
                            @selected(old('status')==='inactive' )>
                            Nonaktif
                        </option>
                    </select>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-ink-100 pt-6 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('merchant.products.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-ink-200 px-5 py-3 text-sm font-semibold text-ink-700 transition hover:bg-ink-50">
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-700">
                        Simpan Produk
                    </button>
                </div>
            </div>
        </form>
    </section>
</x-fs.layout>