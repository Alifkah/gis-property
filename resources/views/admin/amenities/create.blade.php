<x-layouts.admin>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 font-display flex items-center gap-2">
                <i class="ti ti-map-pin text-brand-primary"></i>
                <span>Tambah Fasilitas (POI) Baru</span>
            </h1>
            <p class="mt-1.5 text-xs font-semibold text-slate-500">Lengkapi detail fasilitas publik di bawah, lalu klik langsung pada peta untuk memposisikan titik koordinat secara spasial.</p>
        </div>

        {{-- Form Card --}}
        <section class="card p-6 bg-white border border-slate-200/50 shadow-sm overflow-hidden mb-12">
            <form method="POST" action="{{ route('admin.amenities.store') }}" class="space-y-6">
                @csrf

                {{-- Fields --}}
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5" for="name">Nama Fasilitas <span class="text-rose-500">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" class="input @error('name') ring-2 ring-rose-200 border-rose-300 @enderror" placeholder="cth. RSUD Abdul Wahab Sjahranie" required />
                        @error('name')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5" for="type">Tipe / Kategori Fasilitas <span class="text-rose-500">*</span></label>
                        <input id="typeInput" name="type" type="text" value="{{ old('type') }}" class="input @error('type') ring-2 ring-rose-200 border-rose-300 @enderror" placeholder="cth. Sekolah, Rumah Sakit, Mall, Masjid" required />
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1.5">Penanda peta akan menyesuaikan ikon secara otomatis.</p>
                        @error('type')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Coordinates --}}
                <div class="rounded-2xl bg-slate-50 border border-slate-200/50 p-4 space-y-3">
                    <div>
                        <div class="text-xs font-bold text-slate-900">Koordinat Terpilih</div>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Tentukan lokasi dengan mengklik area di peta atau menyeret marker.</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Latitude</label>
                            <input id="latDisplay" type="text" class="input bg-white font-mono text-xs text-slate-700" readonly placeholder="Klik peta..." />
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Longitude</label>
                            <input id="lngDisplay" type="text" class="input bg-white font-mono text-xs text-slate-700" readonly placeholder="Klik peta..." />
                        </div>
                    </div>
                    <input type="hidden" id="latInput" name="lat" value="{{ old('lat') }}" />
                    <input type="hidden" id="lngInput" name="lng" value="{{ old('lng') }}" />
                    @error('lat')
                        <div class="text-xs font-semibold text-rose-600 flex items-center gap-1 mt-1">
                            <i class="ti ti-alert-triangle text-sm shrink-0"></i>
                            <span>Silakan tentukan titik lokasi pada peta.</span>
                        </div>
                    @enderror
                </div>

                {{-- Map Container --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200/60 shadow-sm relative z-0" style="height:480px">
                    <div id="map" class="h-full w-full"></div>
                </div>

                {{-- Form Actions --}}
                <div class="flex gap-3 pt-2">
                    <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline flex-1">Batal</a>
                    <button type="submit" id="submitBtn" class="btn btn-primary flex-1 border-0 font-bold cursor-pointer">Simpan Fasilitas</button>
                </div>
            </form>
        </section>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const map = L.map('map', { zoomControl: false }).setView([-0.5022, 117.1536], 12);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd'
            }).addTo(map);

            const latInput = document.getElementById('latInput');
            const lngInput = document.getElementById('lngInput');
            const latDisplay = document.getElementById('latDisplay');
            const lngDisplay = document.getElementById('lngDisplay');
            const typeInput = document.getElementById('typeInput');

            let marker = null;

            function getIconHTML(type) {
                type = type.toLowerCase();
                let iconClass = 'ti-map-pin';
                let bgClass = 'bg-brand-primary';
                
                if (type.includes('sekolah') || type.includes('kampus') || type.includes('universitas') || type.includes('pendidikan') || type.includes('sd') || type.includes('smp') || type.includes('sma')) {
                    iconClass = 'ti-school';
                    bgClass = 'bg-blue-600';
                } else if (type.includes('sakit') || type.includes('klinik') || type.includes('dokter') || type.includes('puskesmas') || type.includes('medis')) {
                    iconClass = 'ti-heart-handshake';
                    bgClass = 'bg-rose-600';
                } else if (type.includes('mall') || type.includes('supermarket') || type.includes('pasar') || type.includes('belanja') || type.includes('mart') || type.includes('plaza')) {
                    iconClass = 'ti-shopping-cart';
                    bgClass = 'bg-purple-600';
                } else if (type.includes('masjid') || type.includes('gereja') || type.includes('ibadah') || type.includes('vihara')) {
                    iconClass = 'ti-building-church';
                    bgClass = 'bg-emerald-600';
                }
                
                return `<div class="grid size-9 place-items-center rounded-xl ${bgClass} text-white shadow-md border-2 border-white"><i class="ti ${iconClass} text-base"></i></div>`;
            }

            function getCustomIcon(type) {
                return L.divIcon({
                    className: '',
                    html: getIconHTML(type),
                    iconSize: [36, 36],
                    iconAnchor: [18, 18]
                });
            }

            function setPoint(lat, lng) {
                const currentType = typeInput.value || '';
                const customIcon = getCustomIcon(currentType);

                if (marker) {
                    marker.setLatLng([lat, lng]);
                    marker.setIcon(customIcon);
                } else {
                    marker = L.marker([lat, lng], { icon: customIcon, draggable: true }).addTo(map);
                    marker.on('dragend', () => {
                        const p = marker.getLatLng();
                        setPoint(p.lat, p.lng);
                    });
                }

                latInput.value = lat.toFixed(6);
                lngInput.value = lng.toFixed(6);
                latDisplay.value = lat.toFixed(6);
                lngDisplay.value = lng.toFixed(6);
            }

            map.on('click', (e) => setPoint(e.latlng.lat, e.latlng.lng));

            typeInput.addEventListener('input', () => {
                if (marker) {
                    const p = marker.getLatLng();
                    setPoint(p.lat, p.lng);
                }
            });

            // Restore if validation failed
            const savedLat = latInput.value;
            const savedLng = lngInput.value;
            if (savedLat && savedLng) {
                const lat = parseFloat(savedLat);
                const lng = parseFloat(savedLng);
                setPoint(lat, lng);
                map.setView([lat, lng], 15);
            }

            // Invalidate size immediately
            map.invalidateSize();
            setTimeout(() => map.invalidateSize(), 500);
        </script>
    @endpush
</x-layouts.admin>
