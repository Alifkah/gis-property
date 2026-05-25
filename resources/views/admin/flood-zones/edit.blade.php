<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    @endpush

    <div class="card p-6">
        <div class="mb-6">
            <div class="text-sm font-extrabold text-slate-900">Edit Zona Banjir: {{ $floodZone->area_name }}</div>
            <div class="mt-1 text-sm text-slate-500">
                Polygon lama ditampilkan di peta. Hapus dan gambar ulang jika batas kawasan berubah.
            </div>
        </div>

        <form method="POST" action="{{ route('admin.flood-zones.update', $floodZone->id) }}" id="floodZoneForm" class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
            @csrf
            @method('PUT')

            {{-- Form fields --}}
            <div class="grid gap-4 self-start">
                <div>
                    <label class="text-xs font-semibold text-slate-600" for="area_name">Nama Area <span class="text-rose-500">*</span></label>
                    <input
                        id="area_name" name="area_name" type="text" value="{{ old('area_name', $floodZone->area_name) }}"
                        class="input mt-1 @error('area_name') ring-rose-400 @enderror"
                    />
                    @error('area_name')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600" for="risk_level">Tingkat Risiko <span class="text-rose-500">*</span></label>
                    <select id="risk_level" name="risk_level" class="select mt-1">
                        <option value="Rendah" @selected(old('risk_level', $floodZone->risk_level) === 'Rendah')>Rendah</option>
                        <option value="Sedang" @selected(old('risk_level', $floodZone->risk_level) === 'Sedang')>Sedang</option>
                        <option value="Tinggi" @selected(old('risk_level', $floodZone->risk_level) === 'Tinggi')>Tinggi</option>
                    </select>
                </div>

                <div id="polygonStatus" class="rounded-2xl bg-emerald-50 p-3 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200/70">
                    Polygon lama dimuat. Edit atau gambar ulang bila perlu.
                </div>

                <input type="hidden" id="geojsonInput" name="geojson" value="{{ old('geojson') }}" />
                @error('geojson')
                    <div class="text-xs font-semibold text-rose-600">{{ $message }}</div>
                @enderror

                <div class="flex gap-3">
                    <a href="{{ route('admin.flood-zones.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1">Perbarui Zona</button>
                </div>
            </div>

            {{-- Peta --}}
            <div class="overflow-hidden rounded-2xl ring-1 ring-slate-200/70">
                <div id="map" style="height:520px" class="relative z-0 w-full"></div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
        <script>
            const existingGeojson = @json($existingGeojson);

            const map = L.map('map').setView([-0.5022, 117.1536], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            const drawnItems = new L.FeatureGroup().addTo(map);

            const drawControl = new L.Control.Draw({
                edit: { featureGroup: drawnItems },
                draw: {
                    polygon: { shapeOptions: { color: '#f43f5e', fillOpacity: 0.2 } },
                    polyline: false, rectangle: false, circle: false,
                    marker: false, circlemarker: false,
                }
            });
            map.addControl(drawControl);

            const geojsonInput = document.getElementById('geojsonInput');
            const statusEl = document.getElementById('polygonStatus');
            const submitBtn = document.getElementById('submitBtn');

            // Load existing polygon (only available on pgsql)
            if (existingGeojson) {
                try {
                    const parsed = JSON.parse(existingGeojson);
                    const layer = L.geoJSON({ type: 'Feature', geometry: parsed }, {
                        style: { color: '#f43f5e', fillOpacity: 0.2 }
                    });
                    layer.eachLayer(l => drawnItems.addLayer(l));
                    map.fitBounds(layer.getBounds());
                    geojsonInput.value = existingGeojson;
                } catch (e) {}
            } else if (geojsonInput.value) {
                // Restored from old() after validation failure
                try {
                    const parsed = JSON.parse(geojsonInput.value);
                    const layer = L.geoJSON({ type: 'Feature', geometry: parsed });
                    layer.eachLayer(l => drawnItems.addLayer(l));
                    map.fitBounds(layer.getBounds());
                } catch (e) {}
            }

            map.on(L.Draw.Event.CREATED, function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.layer);
                const geojson = e.layer.toGeoJSON().geometry;
                geojsonInput.value = JSON.stringify(geojson);
                statusEl.className = 'rounded-2xl bg-emerald-50 p-3 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200/70';
                statusEl.textContent = 'Polygon baru digambar. Klik "Perbarui Zona" untuk menyimpan.';
            });

            map.on(L.Draw.Event.EDITED, function () {
                drawnItems.eachLayer(function (layer) {
                    geojsonInput.value = JSON.stringify(layer.toGeoJSON().geometry);
                });
            });

            map.on(L.Draw.Event.DELETED, function () {
                geojsonInput.value = '';
                statusEl.className = 'rounded-2xl bg-amber-50 p-3 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/70';
                statusEl.textContent = 'Polygon dihapus. Gambar ulang polygon baru sebelum menyimpan.';
            });
        </script>
    @endpush
</x-layouts.admin>
