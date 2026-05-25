<x-layouts.app>
    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="card p-4">
            <div class="px-2 pt-2 text-sm font-extrabold text-slate-900">Dashboard Penjual</div>
            <nav class="mt-3 grid gap-1">
                <a href="{{ route('seller.listings.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Listing Saya</a>
                <a href="{{ route('seller.listings.create') }}" class="rounded-xl bg-indigo-50 px-3 py-2 text-sm font-extrabold text-indigo-700 ring-1 ring-indigo-100">Tambah Baru</a>
                <a href="{{ route('seller.competitor-analysis.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Analisis Kompetitor</a>
                <a href="{{ route('seller.profile.edit') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Pengaturan</a>
            </nav>
        </aside>

        <section class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-sm font-extrabold text-slate-900">Tambah Listing</div>
                    <div class="mt-1 text-sm text-slate-600">Lengkapi data properti sebelum menentukan lokasi spasial.</div>
                </div>
                <div class="flex items-center gap-2 text-xs font-extrabold">
                    <span class="grid size-8 place-items-center rounded-full bg-indigo-600 text-white">1</span>
                    <span class="text-slate-600">Data Properti</span>
                    <span class="h-0.5 w-8 rounded bg-slate-200"></span>
                    <span class="grid size-8 place-items-center rounded-full bg-slate-200 text-slate-600">2</span>
                    <span class="text-slate-500">Lokasi Spasial</span>
                </div>
            </div>

            <form method="POST" action="{{ route('seller.listings.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-4">
                @csrf
                <div>
                    <label class="text-xs font-semibold text-slate-600">Judul Listing</label>
                    <input name="title" type="text" class="input mt-1" value="{{ old('title') }}" placeholder="Contoh: Rumah Minimalis Samarinda Ulu" required />
                    @error('title')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tipe Properti</label>
                        <select name="type" class="select mt-1" required>
                            @foreach (['Rumah', 'Tanah', 'Ruko', 'Apartemen'] as $type)
                                <option value="{{ $type }}" @selected(old('type', 'Rumah') === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Harga (IDR)</label>
                        <input id="price_display" type="text" class="input mt-1" placeholder="500.000.000" required />
                        <input id="price_real" name="price" type="hidden" value="{{ old('price') }}" />
                        @error('price')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">LT (m²)</label>
                        <input name="land_area" type="number" class="input mt-1" value="{{ old('land_area') }}" placeholder="120" min="0" required />
                        @error('land_area')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">LB (m²)</label>
                        <input name="building_area" type="number" class="input mt-1" value="{{ old('building_area', 0) }}" placeholder="90" min="0" />
                        @error('building_area')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">KT</label>
                        <input name="bedroom" type="number" class="input mt-1" value="{{ old('bedroom', 0) }}" placeholder="3" min="0" />
                        @error('bedroom')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">KM</label>
                        <input name="bathroom" type="number" class="input mt-1" value="{{ old('bathroom', 0) }}" placeholder="2" min="0" />
                        @error('bathroom')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Deskripsi</label>
                    <textarea name="description" rows="4" class="input mt-1 resize-none" placeholder="Deskripsikan properti Anda: kondisi, keunggulan, akses jalan, dll.">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Foto Properti <span class="font-normal text-slate-400">(maks. 5 foto, JPG/PNG/WebP, maks. 3 MB per foto)</span></label>
                    <div id="imageDropzone" class="mt-1 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center transition hover:border-indigo-400 hover:bg-indigo-50/40">
                        <svg class="size-8 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="text-sm font-semibold text-slate-600">Klik atau seret foto ke sini</div>
                        <input id="imageInput" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" />
                    </div>
                    <div id="imagePreview" class="mt-3 grid grid-cols-3 gap-3 sm:grid-cols-5"></div>
                    @error('images')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                    @error('images.*')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-2 flex items-center justify-end">
                    <button type="submit" class="btn btn-primary">Lanjut Ke Lokasi &rarr;</button>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script>
            const dropzone = document.getElementById('imageDropzone');
            const input = document.getElementById('imageInput');
            const preview = document.getElementById('imagePreview');

            dropzone.addEventListener('click', () => input.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.classList.add('border-indigo-400', 'bg-indigo-50/40');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-indigo-400', 'bg-indigo-50/40');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-indigo-400', 'bg-indigo-50/40');
                const dt = new DataTransfer();
                Array.from(e.dataTransfer.files).forEach((f) => dt.items.add(f));
                input.files = dt.files;
                renderPreviews(input.files);
            });

            input.addEventListener('change', () => renderPreviews(input.files));

            function renderPreviews(files) {
                preview.innerHTML = '';
                Array.from(files).slice(0, 5).forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'relative aspect-square overflow-hidden rounded-xl bg-slate-100';
                        div.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" />`;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Price formatter logic
            const priceReal = document.getElementById('price_real');
            const priceDisplay = document.getElementById('price_display');

            function formatNumber(num) {
                return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function updatePrice() {
                let cleanVal = priceDisplay.value.replace(/\D/g, '');
                if (cleanVal === '') {
                    priceReal.value = '';
                    priceDisplay.value = '';
                    return;
                }
                const num = parseInt(cleanVal, 10);
                priceReal.value = num;
                priceDisplay.value = formatNumber(num);
            }

            priceDisplay.addEventListener('input', updatePrice);

            // Initialize formatting
            if (priceReal.value) {
                const initialVal = parseInt(priceReal.value, 10);
                if (!isNaN(initialVal)) {
                    priceDisplay.value = formatNumber(initialVal);
                }
            }
        </script>
    @endpush
</x-layouts.app>
