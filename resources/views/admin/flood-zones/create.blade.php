<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    @endpush

    <div class="card p-6">
        <div class="mb-6">
            <div class="text-sm font-extrabold text-slate-900">Tambah Zona Rawan Banjir</div>
            <div class="mt-1 text-sm text-slate-500">
                Isi detail zona, lalu gunakan alat gambar di peta untuk membuat polygon batas kawasan banjir.
            </div>
        </div>

        <form method="POST" action="{{ route('admin.flood-zones.store') }}" id="floodZoneForm" class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
            @csrf

            {{-- Form fields --}}
            <div class="grid gap-4 self-start">
                <div>
                    <label class="text-xs font-semibold text-slate-600" for="area_name">Nama Area <span class="text-rose-500">*</span></label>
                    <input
                        id="area_name" name="area_name" type="text" value="{{ old('area_name') }}"
                        class="input mt-1 @error('area_name') ring-rose-400 @enderror"
                        placeholder="cth. Kelurahan Karang Asam Ulu"
                    />
                    @error('area_name')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600" for="risk_level">Tingkat Risiko <span class="text-rose-500">*</span></label>
                    <select id="risk_level" name="risk_level" class="select mt-1 @error('risk_level') ring-rose-400 @enderror">
                        <option value="">Pilih tingkat risiko</option>
                        <option value="Rendah" @selected(old('risk_level') === 'Rendah')>Rendah</option>
                        <option value="Sedang" @selected(old('risk_level') === 'Sedang')>Sedang</option>
                        <option value="Tinggi" @selected(old('risk_level') === 'Tinggi')>Tinggi</option>
                    </select>
                    @error('risk_level')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                    <div class="text-xs font-extrabold text-slate-700">Panduan Menggambar Polygon</div>
                    <ol class="mt-2 space-y-1 text-xs font-semibold text-slate-500">
                        <li>1. Klik ikon polygon <span class="rounded bg-white px-1 py-0.5 ring-1 ring-slate-200">▲</span> di toolbar kiri peta</li>
                        <li>2. Klik titik-titik batas area di peta</li>
                        <li>3. Klik ganda di titik terakhir untuk menutup polygon</li>
                        <li>4. Klik "Simpan Zona" di bawah</li>
                    </ol>
                </div>

                <div id="polygonStatus" class="rounded-2xl bg-rose-50 p-3 text-xs font-semibold text-rose-600 ring-1 ring-rose-200/70">
                    Polygon belum digambar. Gunakan alat gambar di peta.
                </div>

                <input type="hidden" id="geojsonInput" name="geojson" value="{{ old('geojson') }}" />
                @error('geojson')
                    <div class="text-xs font-semibold text-rose-600">{{ $message }}</div>
                @enderror

                <div class="flex gap-3">
                    <a href="{{ route('admin.flood-zones.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1" disabled>Simpan Zona</button>
                </div>
            </div>

            {{-- Peta --}}
            <div class="overflow-hidden rounded-2xl ring-1 ring-slate-200/70">
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

            function onPolygonDrawn() {
                // Only keep the latest drawn polygon
                drawnItems.clearLayers();
            }

            map.on(L.Draw.Event.CREATED, function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.layer);

                const geojson = e.layer.toGeoJSON().geometry;
                geojsonInput.value = JSON.stringify(geojson);

                statusEl.className = 'rounded-2xl bg-emerald-50 p-3 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200/70';
                statusEl.textContent = 'Polygon berhasil digambar. Klik "Simpan Zona" untuk menyimpan.';
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
                statusEl.className = 'rounded-2xl bg-rose-50 p-3 text-xs font-semibold text-rose-600 ring-1 ring-rose-200/70';
                statusEl.textContent = 'Polygon dihapus. Gambar ulang untuk melanjutkan.';
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
                    statusEl.className = 'rounded-2xl bg-emerald-50 p-3 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200/70';
                    statusEl.textContent = 'Polygon tersimpan. Klik "Simpan Zona" untuk melanjutkan.';
                    submitBtn.disabled = false;
                } catch (e) {}
            }
        </script>
    @endpush
</x-layouts.admin>
