<x-layouts.seller>
    <div class="max-w-2xl mx-auto">
        {{-- Step Indicator --}}
        <div class="flex items-center justify-center gap-3 sm:gap-4 mb-8">
            <div class="flex items-center gap-2 shrink-0">
                <span class="grid size-8 place-items-center rounded-full bg-brand-primary text-white text-xs font-bold ring-4 ring-brand-primary/10">1</span>
                <span class="text-xs font-bold text-slate-900">Data Properti</span>
            </div>
            <div class="h-0.5 w-12 sm:w-16 bg-slate-200 shrink-0"></div>
            <div class="flex items-center gap-2 shrink-0">
                <span class="grid size-8 place-items-center rounded-full bg-slate-200 text-slate-500 text-xs font-bold">2</span>
                <span class="text-xs font-bold text-slate-400">Lokasi Spasial</span>
            </div>
        </div>

        {{-- Form Card --}}
        <section class="card p-6 bg-white border border-slate-200/50 shadow-sm overflow-hidden mb-12">
            <div class="mb-6">
                <h1 class="text-lg font-extrabold text-slate-950 font-display">Tambah Properti Baru</h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">Lengkapi data properti Anda sebelum menentukan koordinat lokasi di peta.</p>
            </div>

            <form method="POST" action="{{ route('seller.listings.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Section 1: Informasi Dasar --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-info-circle text-brand-primary"></i>
                        <span>Informasi Dasar</span>
                    </h3>
                    
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Judul Listing <span class="text-rose-500">*</span></label>
                        <input name="title" type="text" class="input" value="{{ old('title') }}" placeholder="Contoh: Rumah Minimalis Modern Bukit Mediterania" required />
                        @error('title')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Tipe Properti <span class="text-rose-500">*</span></label>
                            <select name="type" class="select" required>
                                @foreach (['Rumah', 'Tanah', 'Ruko', 'Apartemen'] as $type)
                                    <option value="{{ $type }}" @selected(old('type', 'Rumah') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Harga Penawaran (IDR) <span class="text-rose-500">*</span></label>
                            <input id="price_display" type="text" class="input" placeholder="Contoh: 750.000.000" required />
                            <input id="price_real" name="price" type="hidden" value="{{ old('price') }}" />
                            @error('price')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 2: Detail Properti --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-home text-brand-primary"></i>
                        <span>Detail Spesifikasi</span>
                    </h3>

                    <div class="grid gap-4 grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Luas Tanah (m²) <span class="text-rose-500">*</span></label>
                            <input name="land_area" type="number" class="input" value="{{ old('land_area') }}" placeholder="120" min="1" required />
                            @error('land_area')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Luas Bangunan (m²) <span class="text-slate-400">(Opsional)</span></label>
                            <input name="building_area" type="number" class="input" value="{{ old('building_area', 0) }}" placeholder="90" min="0" />
                            @error('building_area')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Kamar Tidur <span class="text-slate-400">(Opsional)</span></label>
                            <input name="bedroom" type="number" class="input" value="{{ old('bedroom', 0) }}" placeholder="3" min="0" />
                            @error('bedroom')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Kamar Mandi <span class="text-slate-400">(Opsional)</span></label>
                            <input name="bathroom" type="number" class="input" value="{{ old('bathroom', 0) }}" placeholder="2" min="0" />
                            @error('bathroom')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Section 3: Deskripsi --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-file-text text-brand-primary"></i>
                        <span>Deskripsi Properti</span>
                    </h3>
                    <div>
                        <textarea name="description" rows="4" class="input resize-none" placeholder="Tuliskan spesifikasi lengkap, kelebihan lokasi, ketersediaan sertifikat (SHM/HGB), serta akses jalan masuk mobil...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 4: Foto Properti --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-photo text-brand-primary"></i>
                        <span>Foto Properti</span>
                    </h3>
                    
                    <div>
                        <div id="imageDropzone" class="flex cursor-pointer flex-col items-center justify-center gap-2.5 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center transition hover:border-brand-primary/45 hover:bg-brand-primary/5">
                            <i class="ti ti-cloud-upload text-3xl text-slate-400 group-hover:text-brand-primary transition"></i>
                            <div class="text-xs font-bold text-slate-700">Klik atau seret foto properti Anda ke sini</div>
                            <p class="text-[10px] text-slate-400 font-semibold leading-none">Format: JPG, PNG, WebP (Maksimal 15 foto, 3 MB per foto)</p>
                            <input id="imageInput" name="images[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="hidden" />
                        </div>
                        <div id="imagePreview" class="mt-4 grid grid-cols-3 gap-3 sm:grid-cols-5"></div>
                        @error('images')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Sticky Bottom Actions Panel --}}
                <div class="sticky bottom-0 bg-white/95 backdrop-blur-md border-t border-slate-100 -mx-6 px-6 py-4 mt-8 flex justify-end gap-3 z-10 shadow-lg -mb-6">
                    <button type="submit" class="btn btn-primary text-xs font-bold px-6 py-3 shadow-sm flex items-center gap-1 border-0 cursor-pointer">
                        <span>Lanjut ke Lokasi</span>
                        <i class="ti ti-arrow-narrow-right text-base"></i>
                    </button>
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
                dropzone.classList.add('border-brand-primary/40', 'bg-brand-primary/5');
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.classList.remove('border-brand-primary/40', 'bg-brand-primary/5');
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-brand-primary/40', 'bg-brand-primary/5');
                const dt = new DataTransfer();
                Array.from(e.dataTransfer.files).forEach((f) => dt.items.add(f));
                input.files = dt.files;
                renderPreviews(input.files);
            });

            input.addEventListener('change', () => renderPreviews(input.files));

            function renderPreviews(files) {
                preview.innerHTML = '';
                Array.from(files).slice(0, 15).forEach((file) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const div = document.createElement('div');
                        div.className = 'relative aspect-square overflow-hidden rounded-xl bg-slate-50 border border-slate-200/50';
                        div.innerHTML = `<img src="${e.target.result}" class="h-full w-full object-cover" />`;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Price formatter
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

            // Initialize price
            if (priceReal.value) {
                const initialVal = parseInt(priceReal.value, 10);
                if (!isNaN(initialVal)) {
                    priceDisplay.value = formatNumber(initialVal);
                }
            }
        </script>
    @endpush
</x-layouts.seller>
