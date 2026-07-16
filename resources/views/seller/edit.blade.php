<x-layouts.seller>
    <div class="max-w-2xl mx-auto">
        {{-- Title header --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight leading-snug">Edit Listing</h1>
                <p class="text-xs font-semibold text-slate-500 mt-1">Perbarui detail properti atau ubah koordinat spasial.</p>
            </div>
            <a href="{{ route('seller.listings.location.edit', ['property' => $property->id]) }}" class="btn btn-outline text-xs font-bold flex items-center gap-1.5 self-start sm:self-auto">
                <i class="ti ti-map-pin text-sm text-brand-primary"></i>
                <span>Ubah Lokasi Spasial</span>
            </a>
        </div>

        {{-- Form Card --}}
        <section class="card p-6 bg-white border border-slate-200/50 shadow-sm overflow-hidden mb-12">
            <div class="mb-6 pb-3 border-b border-slate-100 flex items-center gap-2">
                <div class="grid size-9 place-items-center rounded-xl bg-brand-primary/5 text-brand-primary">
                    <i class="ti ti-edit text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-slate-900 leading-none truncate">Edit Properti</h2>
                    <span class="text-[10px] font-bold text-slate-400 leading-none mt-1 block truncate" x-text="'{{ $property->title }}'"></span>
                </div>
            </div>

            <form method="POST" action="{{ route('seller.listings.update', ['property' => $property->id]) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                {{-- Section 1: Informasi Dasar --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-info-circle text-brand-primary"></i>
                        <span>Informasi Dasar</span>
                    </h3>
                    
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Judul Listing <span class="text-rose-500">*</span></label>
                        <input name="title" type="text" class="input" value="{{ old('title', $property->title) }}" placeholder="Contoh: Rumah Minimalis Modern Bukit Mediterania" required />
                        @error('title')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Tipe Properti <span class="text-rose-500">*</span></label>
                            <select name="type" class="select" required>
                                @foreach (['Rumah', 'Tanah', 'Ruko', 'Apartemen'] as $type)
                                    <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Harga Penawaran (IDR) <span class="text-rose-500">*</span></label>
                            <input id="price_display" type="text" class="input" placeholder="Contoh: 750.000.000" required />
                            <input id="price_real" name="price" type="hidden" value="{{ old('price', (float) $property->price) }}" />
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
                            <input name="land_area" type="number" class="input" value="{{ old('land_area', (int) $property->land_area) }}" min="1" required />
                            @error('land_area')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Luas Bangunan (m²) <span class="text-slate-400">(Opsional)</span></label>
                            <input name="building_area" type="number" class="input" value="{{ old('building_area', (int) $property->building_area) }}" min="0" />
                            @error('building_area')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Kamar Tidur <span class="text-slate-400">(Opsional)</span></label>
                            <input name="bedroom" type="number" class="input" value="{{ old('bedroom', (int) $property->bedroom) }}" min="0" />
                            @error('bedroom')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700 block mb-1.5">Kamar Mandi <span class="text-slate-400">(Opsional)</span></label>
                            <input name="bathroom" type="number" class="input" value="{{ old('bathroom', (int) $property->bathroom) }}" min="0" />
                            @error('bathroom')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Status Properti</label>
                        <select name="status" class="select">
                            @foreach (['Tersedia', 'Terjual'] as $status)
                                <option value="{{ $status }}" @selected(old('status', $property->status) === $status)>{{ $status }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 3: Deskripsi --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-file-text text-brand-primary"></i>
                        <span>Deskripsi Properti</span>
                    </h3>
                    <div>
                        <textarea name="description" rows="4" class="input resize-none" placeholder="Tuliskan spesifikasi lengkap, kelebihan lokasi, ketersediaan sertifikat (SHM/HGB), serta akses jalan masuk mobil...">{{ old('description', $property->description) }}</textarea>
                        @error('description')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Section 4: Foto Saat Ini --}}
                @if ($property->images->isNotEmpty())
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                            <i class="ti ti-photo text-brand-primary"></i>
                            <span>Foto Saat Ini</span>
                        </h3>
                        
                        <div class="grid grid-cols-3 gap-3 sm:grid-cols-5" x-data="{ deletedIds: [] }">
                            @foreach ($property->images as $image)
                                <div class="relative aspect-square overflow-hidden rounded-xl bg-slate-50 border border-slate-200/50 group" 
                                     :class="deletedIds.includes({{ $image->id }}) ? 'opacity-40 ring-2 ring-rose-500' : ''">
                                    
                                    @php
                                        $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
                                    @endphp
                                    @if (!$isLocalDisk || Storage::disk('public')->exists($image->path))
                                        <img src="{{ Storage::disk('public')->url($image->path) }}" alt="Foto properti" class="h-full w-full object-cover" />
                                    @else
                                        <div class="h-full w-full flex flex-col items-center justify-center bg-slate-50 text-slate-400 p-2 text-center">
                                            <i class="ti ti-photo-off text-lg text-slate-300"></i>
                                            <span class="text-[8px] font-bold mt-1">Berkas Hilang</span>
                                        </div>
                                    @endif

                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" 
                                           id="del-img-{{ $image->id }}" class="hidden" 
                                           @change="if($el.checked) deletedIds.push({{ $image->id }}); else deletedIds = deletedIds.filter(id => id !== {{ $image->id }})" />
                                    
                                    {{-- Custom delete X button in corner --}}
                                    <button type="button" 
                                            @click="document.getElementById('del-img-{{ $image->id }}').click()"
                                            class="absolute top-1.5 right-1.5 size-6 rounded-full bg-rose-600 hover:bg-rose-700 text-white flex items-center justify-center transition shadow-md border-0 cursor-pointer z-10">
                                        <i class="ti text-[10px] font-bold" :class="deletedIds.includes({{ $image->id }}) ? 'ti-rotate' : 'ti-x'"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Section 5: Tambah Foto Baru --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i class="ti ti-plus text-brand-primary"></i>
                        <span>Tambah Foto Baru</span>
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
                    <a href="{{ route('seller.listings.index') }}" class="btn btn-outline text-xs font-bold px-6 py-3 transition">Batal</a>
                    <button type="submit" class="btn btn-primary text-xs font-bold px-6 py-3 border-0 cursor-pointer">Simpan Perubahan</button>
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
