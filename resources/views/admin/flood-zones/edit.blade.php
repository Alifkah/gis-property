<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
    @endpush

    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 tracking-tight font-display flex items-center gap-2">
                <i class="ti ti-alert-triangle text-rose-600"></i>
                <span>Edit Zona Banjir: {{ $floodZone->area_name }}</span>
            </h1>
            <p class="mt-1.5 text-xs font-semibold text-slate-500">Polygon area saat ini ditampilkan di peta. Anda dapat memodifikasi polygon langsung atau menghapus dan menggambar ulang polygon baru.</p>
        </div>

        {{-- Form Side-by-Side Grid --}}
        <form method="POST" action="{{ route('admin.flood-zones.update', $floodZone->id) }}" id="floodZoneForm" class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)] mb-12">
            @csrf
            @method('PUT')

            {{-- Form fields (Left) --}}
            <div class="space-y-5 self-start">
                <div class="card p-5 bg-white border border-slate-200/50 shadow-sm space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5" for="area_name">Nama Area / Kawasan <span class="text-rose-500">*</span></label>
                        <input id="area_name" name="area_name" type="text" value="{{ old('area_name', $floodZone->area_name) }}" class="input @error('area_name') ring-2 ring-rose-200 border-rose-300 @enderror" required />
                        @error('area_name')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5" for="risk_level">Tingkat Risiko <span class="text-rose-500">*</span></label>
                        <select id="risk_level" name="risk_level" class="select" required>
                            <option value="Rendah" @selected(old('risk_level', $floodZone->risk_level) === 'Rendah')>Rendah</option>
                            <option value="Sedang" @selected(old('risk_level', $floodZone->risk_level) === 'Sedang')>Sedang</option>
                            <option value="Tinggi" @selected(old('risk_level', $floodZone->risk_level) === 'Tinggi')>Tinggi</option>
                        </select>
                    </div>
                </div>

                {{-- Panduan Tutorial Box --}}
                <div class="rounded-2xl bg-white border border-slate-200/50 p-5 space-y-3.5 shadow-sm">
                    <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="ti ti-info-circle text-brand-primary text-base"></i>
                        <span>Panduan Menggambar Polygon</span>
                    </div>
                    <ol class="space-y-3 text-[11px] text-slate-500 font-semibold leading-relaxed">
                        <li class="flex gap-2">
                            <span class="grid size-5 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-600 font-bold">1</span>
                            <span>Klik tombol polygon <span class="rounded bg-slate-50 px-1.5 py-0.5 border border-slate-200 font-mono font-bold text-slate-700">▲</span> di bilah peta sebelah kanan.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="grid size-5 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-600 font-bold">2</span>
                            <span>Klik berulang di peta untuk membentuk sudut-sudut area.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="grid size-5 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-600 font-bold">3</span>
                            <span>Klik titik awal kembali untuk menutup dan mengunci bidang.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="grid size-5 shrink-0 place-items-center rounded-full bg-slate-100 text-slate-600 font-bold">4</span>
                            <span>Gunakan ikon tong sampah / kuas untuk menghapus atau mengedit.</span>
                        </li>
                    </ol>
                </div>

                {{-- Status Indicator Box with Glowing Pulse Dot --}}
                <div id="polygonStatus" class="rounded-2xl bg-emerald-50/50 p-4 text-xs font-bold text-emerald-700 border border-emerald-100/50 flex items-center gap-3">
                    <div class="relative flex size-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full size-2 bg-emerald-500"></span>
                    </div>
                    <span>Polygon lama berhasil dimuat. Siap diedit.</span>
                </div>

                <input type="hidden" id="geojsonInput" name="geojson" value="{{ old('geojson') }}" />
                @error('geojson')
                    <div class="text-xs font-semibold text-rose-600 px-1">{{ $message }}</div>
                @enderror

                {{-- Actions --}}
                <div class="flex gap-3">
                    <a href="{{ route('admin.flood-zones.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1 border-0 font-bold cursor-pointer">Perbarui Zona</button>
                </div>
            </div>

            {{-- Drawing Map (Right) --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200/50 shadow-sm relative z-0" style="height:520px">
                <div id="map" class="h-full w-full"></div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
        <script>
            const existingGeojson = @json($existingGeojson);

            const map = L.map('map', { zoomControl: false }).setView([-0.5022, 117.1536], 12);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd'
            }).addTo(map);

            const drawnItems = new L.FeatureGroup().addTo(map);

            const drawControl = new L.Control.Draw({
                position: 'topright',
                edit: { featureGroup: drawnItems },
                draw: {
                    polygon: { shapeOptions: { color: '#9A031E', fillOpacity: 0.25, weight: 3 } },
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

            function setStatusSuccess(message) {
                statusEl.className = 'rounded-2xl bg-emerald-50/50 p-4 text-xs font-bold text-emerald-700 border border-emerald-100/50 flex items-center gap-3';
                statusEl.innerHTML = `
                    <div class="relative flex size-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full size-2 bg-emerald-500"></span>
                    </div>
                    <span>${message}</span>
                `;
                submitBtn.disabled = false;
            }

            function setStatusError(message) {
                statusEl.className = 'rounded-2xl bg-rose-50/50 p-4 text-xs font-bold text-rose-600 border border-rose-100/50 flex items-center gap-3';
                statusEl.innerHTML = `
                    <div class="relative flex size-2 shrink-0">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full size-2 bg-rose-500"></span>
                    </div>
                    <span>${message}</span>
                `;
                submitBtn.disabled = true;
            }

            // Load existing polygon
            if (existingGeojson) {
                try {
                    const parsed = JSON.parse(existingGeojson);
                    const layer = L.geoJSON({ type: 'Feature', geometry: parsed }, {
                        style: { color: '#9A031E', fillOpacity: 0.25, weight: 3 }
                    });
                    layer.eachLayer(l => drawnItems.addLayer(l));
                    map.fitBounds(layer.getBounds());
                    geojsonInput.value = existingGeojson;
                } catch (e) {}
            } else if (geojsonInput.value) {
                // Restored from old() after validation failure
                try {
                    const parsed = JSON.parse(geojsonInput.value);
                    const layer = L.geoJSON({ type: 'Feature', geometry: parsed }, {
                        style: { color: '#9A031E', fillOpacity: 0.25, weight: 3 }
                    });
                    layer.eachLayer(l => drawnItems.addLayer(l));
                    map.fitBounds(layer.getBounds());
                } catch (e) {}
            }

            map.on(L.Draw.Event.CREATED, function (e) {
                drawnItems.clearLayers();
                drawnItems.addLayer(e.layer);
                const geojson = e.layer.toGeoJSON().geometry;
                geojsonInput.value = JSON.stringify(geojson);
                setStatusSuccess('Polygon baru digambar! Klik "Perbarui Zona" untuk merekam.');
            });

            map.on(L.Draw.Event.EDITED, function () {
                drawnItems.eachLayer(function (layer) {
                    geojsonInput.value = JSON.stringify(layer.toGeoJSON().geometry);
                });
                setStatusSuccess('Polygon diperbarui! Klik "Perbarui Zona".');
            });

            map.on(L.Draw.Event.DELETED, function () {
                geojsonInput.value = '';
                setStatusError('Polygon kosong. Gambar ulang sebelum memperbarui.');
            });

            map.invalidateSize();
            setTimeout(() => map.invalidateSize(), 500);
        </script>
    @endpush
</x-layouts.admin>
