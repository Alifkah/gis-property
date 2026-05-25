<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    @endpush

    <div class="card p-6">
        <div class="mb-6">
            <h1 class="text-sm font-bold text-slate-900">Tambah Zona Rawan Banjir</h1>
            <p class="mt-1 text-xs text-slate-500">
                Isi detail kawasan di bawah, lalu gunakan bilah alat (toolbar) menggambar di peta untuk membuat polygon batas wilayah.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.flood-zones.store') }}" id="floodZoneForm" class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
            @csrf

            {{-- Form fields --}}
            <div class="grid gap-4.5 self-start">
                <div>
                    <label class="text-xs font-bold text-slate-700" for="area_name">Nama Area / Kawasan <span class="text-rose-500">*</span></label>
                    <input
                        id="area_name" name="area_name" type="text" value="{{ old('area_name') }}"
                        class="input mt-1.5 @error('area_name') ring-2 ring-rose-200 border-rose-300 @enderror"
                        placeholder="cth. Kelurahan Karang Asam Ulu"
                    />
                    @error('area_name')
                        <div class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700" for="risk_level">Tingkat Risiko <span class="text-rose-500">*</span></label>
                    <select id="risk_level" name="risk_level" class="select mt-1.5 @error('risk_level') ring-2 ring-rose-200 border-rose-300 @enderror">
                        <option value="">Pilih tingkat risiko</option>
                        <option value="Rendah" @selected(old('risk_level') === 'Rendah')>Rendah</option>
                        <option value="Sedang" @selected(old('risk_level') === 'Sedang')>Sedang</option>
                        <option value="Tinggi" @selected(old('risk_level') === 'Tinggi')>Tinggi</option>
                    </select>
                    @error('risk_level')
                        <div class="mt-1 text-xs font-bold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Panduan --}}
                <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100">
                    <div class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <svg class="size-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Panduan Menggambar Polygon</span>
                    </div>
                    <ol class="mt-2.5 space-y-1.5 text-xs text-slate-500 font-medium">
                        <li class="flex gap-1.5">
                            <span class="text-slate-400">1.</span>
                            <span>Klik tombol polygon <span class="rounded bg-white px-1.5 py-0.5 border border-slate-200 font-mono font-bold text-slate-700 shadow-xs">▲</span> di bilah peta sebelah kiri.</span>
                        </li>
                        <li class="flex gap-1.5">
                            <span class="text-slate-400">2.</span>
                            <span>Klik berulang di peta untuk membentuk sudut area.</span>
                        </li>
                        <li class="flex gap-1.5">
                            <span class="text-slate-400">3.</span>
                            <span>Klik ganda / klik titik awal kembali untuk menutup bidang.</span>
                        </li>
                        <li class="flex gap-1.5">
                            <span class="text-slate-400">4.</span>
                            <span>Gunakan ikon hapus / edit di menu bilah untuk menyesuaikan.</span>
                        </li>
                    </ol>
                </div>

                {{-- Status --}}
                <div id="polygonStatus" class="rounded-2xl bg-rose-50/50 p-3 text-xs font-bold text-rose-600 border border-rose-100/50 flex items-center gap-2">
                    <div class="size-2 rounded-full bg-rose-500 shrink-0"></div>
                    <span>Polygon batas wilayah belum digambar di peta.</span>
                </div>

                <input type="hidden" id="geojsonInput" name="geojson" value="{{ old('geojson') }}" />
                @error('geojson')
                    <div class="text-xs font-bold text-rose-600">{{ $message }}</div>
                @enderror

                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.flood-zones.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1 cursor-pointer" disabled>Simpan Zona</button>
                </div>
            </div>

            {{-- Peta --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200/50 shadow-xs relative z-0">
                <div id="map" style="height:520px" class="w-full"></div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
        <script>
            const map = L.map('map').setView([-0.5022, 117.1536], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            const drawnItems = new L.FeatureGroup().addTo(map);

            const drawControl = new L.Control.Draw({
                edit: { featureGroup: drawnItems },
                draw: {
                    polygon: { shapeOptions: { color: '#f43f5e', fillOpacity: 0.2 } },
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false,
                }
            });
            map.addControl(drawControl);

            const geojsonInput = document.getElementById('geojsonInput');
            const statusEl = document.getElementById('polygonStatus');
            const submitBtn = document.getElementById('submitBtn');

            map.on(L.Draw.Event.CREATED, function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.layer);

                const geojson = e.layer.toGeoJSON().geometry;
                geojsonInput.value = JSON.stringify(geojson);

                statusEl.className = 'rounded-2xl bg-emerald-50/50 p-3 text-xs font-bold text-emerald-700 border border-emerald-100/50 flex items-center gap-2';
                statusEl.innerHTML = '<div class="size-2 rounded-full bg-emerald-500 shrink-0"></div><span>Polygon berhasil digambar! Klik "Simpan Zona" untuk menyelesaikan.</span>';
                submitBtn.disabled = false;
            });

            map.on(L.Draw.Event.EDITED, function () {
                drawnItems.eachLayer(function (layer) {
                    const geojson = layer.toGeoJSON().geometry;
                    geojsonInput.value = JSON.stringify(geojson);
                });
            });

            map.on(L.Draw.Event.DELETED, function () {
                geojsonInput.value = '';
                statusEl.className = 'rounded-2xl bg-rose-50/50 p-3 text-xs font-bold text-rose-600 border border-rose-100/50 flex items-center gap-2';
                statusEl.innerHTML = '<div class="size-2 rounded-full bg-rose-500 shrink-0"></div><span>Polygon dihapus. Silakan gambar ulang di peta.</span>';
                submitBtn.disabled = true;
            });

            // Restore if validation failed and geojson already set
            const savedGeojson = geojsonInput.value;
            if (savedGeojson) {
                try {
                    const geom = JSON.parse(savedGeojson);
                    const layer = L.geoJSON({ type: 'Feature', geometry: geom });
                    layer.eachLayer(l => drawnItems.addLayer(l));
                    map.fitBounds(layer.getBounds());
                    statusEl.className = 'rounded-2xl bg-emerald-50/50 p-3 text-xs font-bold text-emerald-700 border border-emerald-100/50 flex items-center gap-2';
                    statusEl.innerHTML = '<div class="size-2 rounded-full bg-emerald-500 shrink-0"></div><span>Polygon terdeteksi. Klik "Simpan Zona" untuk memproses.</span>';
                    submitBtn.disabled = false;
                } catch (e) {}
            }
        </script>
    @endpush
</x-layouts.admin>
