<x-layouts.seller>

        <section class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-sm font-extrabold text-slate-900">Edit Listing</div>
                    <div class="mt-1 text-sm text-slate-600">Perbarui detail properti, atau ubah lokasi pada halaman lokasi.</div>
                </div>
                <a href="{{ route('seller.listings.location.edit', ['property' => $property->id]) }}" class="btn btn-outline">Ubah Lokasi</a>
            </div>

            <form method="POST" action="{{ route('seller.listings.update', ['property' => $property->id]) }}" enctype="multipart/form-data" class="mt-6 grid gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs font-semibold text-slate-600">Judul Listing</label>
                    <input name="title" type="text" class="input mt-1" value="{{ old('title', $property->title) }}" required />
                    @error('title')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tipe Properti</label>
                        <select name="type" class="select mt-1" required>
                            @foreach (['Rumah', 'Tanah', 'Ruko', 'Apartemen'] as $type)
                                <option value="{{ $type }}" @selected(old('type', $property->type) === $type)>{{ $type }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Harga (IDR)</label>
                        <input id="price_display" type="text" class="input mt-1" placeholder="500.000.000" required />
                        <input id="price_real" name="price" type="hidden" value="{{ old('price', (float) $property->price) }}" />
                        @error('price')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-4">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">LT (m²)</label>
                        <input name="land_area" type="number" class="input mt-1" value="{{ old('land_area', (int) $property->land_area) }}" min="0" required />
                        @error('land_area')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">LB (m²)</label>
                        <input name="building_area" type="number" class="input mt-1" value="{{ old('building_area', (int) $property->building_area) }}" min="0" />
                        @error('building_area')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">KT</label>
                        <input name="bedroom" type="number" class="input mt-1" value="{{ old('bedroom', (int) $property->bedroom) }}" min="0" />
                        @error('bedroom')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">KM</label>
                        <input name="bathroom" type="number" class="input mt-1" value="{{ old('bathroom', (int) $property->bathroom) }}" min="0" />
                        @error('bathroom')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Status</label>
                    <select name="status" class="select mt-1">
                        @foreach (['Tersedia', 'Terjual'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $property->status) === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600">Deskripsi</label>
                    <textarea name="description" rows="4" class="input mt-1 resize-none" placeholder="Deskripsikan properti Anda: kondisi, keunggulan, akses jalan, dll.">{{ old('description', $property->description) }}</textarea>
                    @error('description')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Foto yang sudah ada --}}
                @if ($property->images->isNotEmpty())
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Foto Saat Ini <span class="font-normal text-slate-400">(centang untuk hapus)</span></label>
                        <div class="mt-2 grid grid-cols-3 gap-3 sm:grid-cols-5">
                            @foreach ($property->images as $image)
                                <label class="group relative cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" class="peer hidden" />
                                    <div class="relative aspect-square overflow-hidden rounded-xl bg-slate-100 ring-2 ring-transparent transition peer-checked:ring-rose-500">
                                        @php
                                            $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
                                        @endphp
                                        @if (!$isLocalDisk || Storage::disk('public')->exists($image->path))
                                            <img src="{{ Storage::url($image->path) }}" alt="Foto properti" class="h-full w-full object-cover" />
                                        @else
                                            <div class="h-full w-full flex flex-col items-center justify-center bg-slate-50 text-slate-400 p-2">
                                                <svg class="size-6 mb-1 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                                </svg>
                                                <span class="text-[9px] font-bold text-center">Berkas hilang</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 flex items-center justify-center bg-rose-500/0 transition peer-checked:bg-rose-500/40 group-hover:bg-black/10">
                                            <svg class="size-6 text-white opacity-0 drop-shadow transition peer-checked:opacity-100 group-hover:opacity-60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M18 6 6 18M6 6l12 12" stroke-linecap="round" />
                                            </svg>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Upload foto baru --}}
                <div>
                    <label class="text-xs font-semibold text-slate-600">Tambah Foto Baru <span class="font-normal text-slate-400">(maks. 5 foto, JPG/PNG/WebP, maks. 3 MB per foto)</span></label>
                    <div id="imageDropzone" class="mt-1 flex cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-4 py-8 text-center transition hover:border-brand-primary/40 hover:bg-brand-primary/5">
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

                <div class="mt-2 flex items-center justify-end gap-2">
                    <a href="{{ route('seller.listings.index') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </section>

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

            // Highlight foto yang akan dihapus
            document.querySelectorAll('input[name="delete_images[]"]').forEach((cb) => {
                cb.addEventListener('change', (e) => {
                    const wrapper = e.target.closest('label').querySelector('.relative.aspect-square');
                    if (e.target.checked) {
                        wrapper.classList.add('ring-rose-500');
                    } else {
                        wrapper.classList.remove('ring-rose-500');
                    }
                });
            });

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
</x-layouts.seller>
