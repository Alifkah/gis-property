<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="card p-6">
        <div class="mb-6">
            <div class="text-sm font-extrabold text-slate-900">Edit Fasilitas: {{ $amenity->name }}</div>
            <div class="mt-1 text-sm text-slate-500">Perbarui detail dan geser pin di peta jika lokasi berubah.</div>
        </div>

        <form method="POST" action="{{ route('admin.amenities.update', $amenity->id) }}" class="grid gap-6 lg:grid-cols-[380px_minmax(0,1fr)]">
            @csrf
            @method('PUT')

            {{-- Form fields --}}
            <div class="grid gap-4 self-start">
                <div>
                    <label class="text-xs font-semibold text-slate-600" for="name">Nama Fasilitas <span class="text-rose-500">*</span></label>
                    <input
                        id="name" name="name" type="text" value="{{ old('name', $amenity->name) }}"
                        class="input mt-1 @error('name') ring-rose-400 @enderror"
                    />
                    @error('name')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-semibold text-slate-600" for="type">Tipe Fasilitas <span class="text-rose-500">*</span></label>
                    <input
                        id="type" name="type" type="text" value="{{ old('type', $amenity->type) }}"
                        class="input mt-1 @error('type') ring-rose-400 @enderror"
                    />
                    @error('type')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                    <div class="text-xs font-extrabold text-slate-700">Koordinat</div>
                    <div class="mt-3 grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Latitude</label>
                            <input id="latDisplay" type="text" class="input mt-1 bg-white font-mono text-xs" readonly />
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-500">Longitude</label>
                            <input id="lngDisplay" type="text" class="input mt-1 bg-white font-mono text-xs" readonly />
                        </div>
                    </div>
                    <input type="hidden" id="latInput" name="lat" value="{{ old('lat', $amenity->lat) }}" />
                    <input type="hidden" id="lngInput" name="lng" value="{{ old('lng', $amenity->lng) }}" />
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" class="btn btn-primary flex-1">Perbarui</button>
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
            const existingLat = {{ (float) $amenity->lat }};
            const existingLng = {{ (float) $amenity->lng }};

            const map = L.map('map').setView([existingLat, existingLng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

            const latInput = document.getElementById('latInput');
            const lngInput = document.getElementById('lngInput');
            const latDisplay = document.getElementById('latDisplay');
            const lngDisplay = document.getElementById('lngDisplay');

            latDisplay.value = existingLat.toFixed(6);
            lngDisplay.value = existingLng.toFixed(6);

            let marker = L.marker([existingLat, existingLng], { draggable: true }).addTo(map);

            function updateCoords(lat, lng) {
                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
                latDisplay.value = lat.toFixed(6);
                lngDisplay.value = lng.toFixed(6);
            }

            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                updateCoords(pos.lat, pos.lng);
            });

            map.on('click', function (e) {
                const { lat, lng } = e.latlng;
                marker.setLatLng([lat, lng]);
                updateCoords(lat, lng);
            });
        </script>
    @endpush
</x-layouts.admin>
