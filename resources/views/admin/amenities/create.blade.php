<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="card p-6">
        <div class="mb-6">
            <h1 class="text-sm font-bold text-slate-900">Tambah Fasilitas (POI) Baru</h1>
            <p class="mt-1 text-xs text-slate-500">Lengkapi detail fasilitas publik di bawah, lalu klik langsung pada peta untuk memposisikan titik koordinat.</p>
        </div>

        <form method="POST" action="{{ route('admin.amenities.store') }}" class="grid gap-6 lg:grid-cols-[380px_minmax(0,1fr)]">
            @csrf

            {{-- Form fields --}}
            <div class="grid gap-4.5 self-start">
                <div>
                    <label class="text-xs font-bold text-slate-700" for="name">Nama Fasilitas <span class="text-rose-500">*</span></label>
                    <input
                        id="name" name="name" type="text" value="{{ old('name') }}"
                        class="input mt-1.5 @error('name') ring-2 ring-rose-200 border-rose-300 @enderror"
                        placeholder="cth. RSUD Abdul Wahab Sjahranie"
                    />
                    @error('name')
                        <div class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700" for="type">Tipe / Kategori Fasilitas <span class="text-rose-500">*</span></label>
                    <input
                        id="type" name="type" type="text" value="{{ old('type') }}"
                        class="input mt-1.5 @error('type') ring-2 ring-rose-200 border-rose-300 @enderror"
                        placeholder="cth. Rumah Sakit, Kampus, Mall, Sekolah"
                    />
                    @error('type')
                        <div class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                    <div class="text-xs font-bold text-slate-700">Koordinat Terpilih</div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Tentukan lokasi dengan mengklik area di peta.</p>
                    <div class="mt-3.5 grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Latitude</label>
                            <input id="latDisplay" type="text" class="input mt-1 bg-white font-mono text-xs border-slate-200" readonly placeholder="Klik peta..." />
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Longitude</label>
                            <input id="lngDisplay" type="text" class="input mt-1 bg-white font-mono text-xs border-slate-200" readonly placeholder="Klik peta..." />
                        </div>
                    </div>
                    <input type="hidden" id="latInput" name="lat" value="{{ old('lat') }}" />
                    <input type="hidden" id="lngInput" name="lng" value="{{ old('lng') }}" />
                    @error('lat')
                        <div class="mt-2.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Silakan tentukan titik lokasi pada peta.</span>
                        </div>
                    @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1 cursor-pointer">Simpan</button>
                </div>
            </div>

            {{-- Peta --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200/50 shadow-xs relative z-0">
                <div id="map" style="height:480px" class="w-full"></div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const map = L.map('map').setView([-0.5022, 117.1536], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            const latInput = document.getElementById('latInput');
            const lngInput = document.getElementById('lngInput');
            const latDisplay = document.getElementById('latDisplay');
            const lngDisplay = document.getElementById('lngDisplay');

            let marker = null;

            // Restore existing value if validation failed
            const savedLat = latInput.value;
            const savedLng = lngInput.value;
            if (savedLat && savedLng) {
                const lat = parseFloat(savedLat);
                const lng = parseFloat(savedLng);
                marker = L.marker([lat, lng]).addTo(map);
                latDisplay.value = lat.toFixed(6);
                lngDisplay.value = lng.toFixed(6);
                map.setView([lat, lng], 15);
            }

            map.on('click', function (e) {
                const { lat, lng } = e.latlng;

                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng]).addTo(map);
                }

                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
                latDisplay.value = lat.toFixed(6);
                lngDisplay.value = lng.toFixed(6);
            });
        </script>
    @endpush
</x-layouts.admin>
