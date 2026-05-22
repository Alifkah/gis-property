<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="card p-6">
        <div class="mb-6">
            <div class="text-sm font-extrabold text-slate-900">Tambah Fasilitas (POI)</div>
            <div class="mt-1 text-sm text-slate-500">Isi detail fasilitas dan klik peta untuk menentukan lokasi titik.</div>
        </div>

        <form method="POST" action="{{ route('admin.amenities.store') }}" class="grid gap-6 lg:grid-cols-[380px_minmax(0,1fr)]">
            @csrf

            {{-- Form fields --}}
            <div class="grid gap-4 self-start">
                <div>
                    <label class="text-xs font-semibold text-slate-600" for="name">Nama Fasilitas <span class="text-rose-500">*</span></label>
                    <input
                        id="name" name="name" type="text" value="{{ old('name') }}"
                        class="input mt-1 @error('name') ring-rose-400 @enderror"
                        placeholder="cth. RSUD Abdul Wahab Sjahranie"
                    />
                    @error('name')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600" for="type">Tipe Fasilitas <span class="text-rose-500">*</span></label>
                    <input
                        id="type" name="type" type="text" value="{{ old('type') }}"
                        class="input mt-1 @error('type') ring-rose-400 @enderror"
                        placeholder="cth. Rumah Sakit, Kampus, Mall, Sekolah"
                    />
                    @error('type')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                    <div class="text-xs font-extrabold text-slate-700">Koordinat Terpilih</div>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Latitude</label>
                            <input id="latDisplay" type="text" class="input mt-1 bg-white font-mono text-xs" readonly placeholder="Klik peta..." />
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Longitude</label>
                            <input id="lngDisplay" type="text" class="input mt-1 bg-white font-mono text-xs" readonly placeholder="Klik peta..." />
                        </div>
                    </div>
                    <input type="hidden" id="latInput" name="lat" value="{{ old('lat') }}" />
                    <input type="hidden" id="lngInput" name="lng" value="{{ old('lng') }}" />
                    @error('lat')
                        <div class="mt-2 text-xs font-semibold text-rose-600">Lokasi belum dipilih. Klik peta untuk menentukan titik.</div>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1">Simpan Fasilitas</button>
                </div>
            </div>

            {{-- Peta --}}
            <div class="overflow-hidden rounded-2xl ring-1 ring-slate-200/70">
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
