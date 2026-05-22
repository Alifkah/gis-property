<x-layouts.blank>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="flex h-dvh w-full">
        <aside class="flex w-[380px] shrink-0 flex-col border-r border-slate-200 bg-white">
            <div class="border-b border-slate-200 px-6 py-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900">Lokasi Spasial</div>
                        <div class="mt-1 text-sm text-slate-600">Klik pada peta untuk menentukan titik lokasi listing.</div>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-extrabold">
                        <span class="grid size-8 place-items-center rounded-full bg-emerald-500 text-white">1</span>
                        <span class="text-slate-600">Data</span>
                        <span class="h-0.5 w-6 rounded bg-slate-200"></span>
                        <span class="grid size-8 place-items-center rounded-full bg-indigo-600 text-white">2</span>
                        <span class="text-slate-600">Lokasi</span>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                    <div class="text-xs font-semibold text-slate-500">Listing</div>
                    <div class="mt-1 text-sm font-extrabold text-slate-900">{{ $property->title }}</div>
                    <div class="mt-1 text-xs font-semibold text-slate-500">{{ $property->type }} • Rp {{ number_format((float) $property->price, 0, ',', '.') }}</div>
                </div>

                <form method="POST" action="{{ route('seller.listings.location.update', ['property' => $property->id]) }}" class="mt-5 grid gap-4">
                    @csrf
                    @method('PUT')
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Latitude</label>
                            <input id="latInput" name="lat" type="number" class="input mt-1" value="{{ old('lat', $point['lat']) }}" step="0.000001" required />
                            @error('lat')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Longitude</label>
                            <input id="lngInput" name="lng" type="number" class="input mt-1" value="{{ old('lng', $point['lng']) }}" step="0.000001" required />
                            @error('lng')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-full">Simpan Lokasi</button>
                    <a href="{{ route('seller.listings.edit', ['property' => $property->id]) }}" class="btn btn-outline w-full text-center">Kembali ke Data</a>
                    <a href="{{ route('seller.listings.index') }}" class="btn btn-outline w-full text-center">Kembali ke Dashboard</a>
                </form>
            </div>
        </aside>

        <div class="relative flex-1">
            <div id="map" class="h-full w-full"></div>
            <div class="absolute left-4 top-4 w-[320px] card p-4">
                <div class="text-xs font-extrabold text-slate-900">Tips</div>
                <div class="mt-2 text-sm font-semibold text-slate-600">
                    Gunakan zoom untuk lebih presisi. Titik akan otomatis mengisi field latitude/longitude.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const latInput = document.getElementById('latInput');
            const lngInput = document.getElementById('lngInput');

            const start = @json($point);
            const map = L.map('map', { zoomControl: false }).setView([start.lat, start.lng], 13);
            L.control.zoom({ position: 'bottomleft' }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([start.lat, start.lng], { draggable: true }).addTo(map);

            function setPoint(lat, lng) {
                marker.setLatLng([lat, lng]);
                latInput.value = Number(lat).toFixed(6);
                lngInput.value = Number(lng).toFixed(6);
            }

            map.on('click', (e) => setPoint(e.latlng.lat, e.latlng.lng));
            marker.on('dragend', () => {
                const p = marker.getLatLng();
                setPoint(p.lat, p.lng);
            });
        </script>
    @endpush
</x-layouts.blank>
