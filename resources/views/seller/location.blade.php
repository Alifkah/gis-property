<x-layouts.blank>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
        <style>
            .pulse-marker-icon {
                position: relative;
                width: 24px;
                height: 24px;
            }
            .pulse-marker-core {
                position: absolute;
                width: 24px;
                height: 24px;
                background: #E36414; /* brand-accent */
                border: 4px solid #ffffff;
                border-radius: 50%;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.35);
                z-index: 2;
            }
            .pulse-marker-ring {
                position: absolute;
                width: 44px;
                height: 44px;
                background: #E36414;
                border-radius: 50%;
                opacity: 0.35;
                top: -10px;
                left: -10px;
                animation: pulse-ring 1.8s cubic-bezier(0.24, 0, 0.38, 1) infinite;
                z-index: 1;
            }
            @keyframes pulse-ring {
                0% { transform: scale(0.6); opacity: 0.5; }
                70% { transform: scale(1.4); opacity: 0; }
                100% { transform: scale(0.6); opacity: 0; }
            }
        </style>
    @endpush

    <div class="flex flex-col md:flex-row h-screen w-full bg-slate-50">
        {{-- Left Sidebar (380px) --}}
        <aside class="flex w-full md:w-[380px] shrink-0 flex-col border-b md:border-b-0 md:border-r border-slate-200/60 bg-white z-20 overflow-y-auto max-h-[50dvh] md:max-h-none">
            {{-- Step Indicator --}}
            <div class="border-b border-slate-100 px-6 py-5">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="text-sm font-extrabold text-slate-900 font-display">Lokasi Spasial</div>
                        <div class="mt-0.5 text-[11px] text-slate-500 font-semibold">Tentukan titik spasial listing properti Anda.</div>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold shrink-0">
                        <span class="grid size-7 place-items-center rounded-full bg-emerald-500 text-white text-[11px]">
                            <i class="ti ti-check"></i>
                        </span>
                        <span class="text-slate-500 hidden sm:inline">Data</span>
                        <span class="h-0.5 w-4 rounded bg-slate-200 hidden sm:block"></span>
                        <span class="grid size-7 place-items-center rounded-full bg-brand-primary text-white text-[11px] ring-4 ring-brand-primary/10">2</span>
                        <span class="text-slate-800 font-bold">Lokasi</span>
                    </div>
                </div>
            </div>

            <div class="flex-1 px-6 py-5 space-y-5">
                {{-- Compact Listing Card --}}
                <div class="rounded-2xl border border-slate-200/60 bg-slate-50 p-4 flex gap-3">
                    @php
                        $firstImage = $property->images->first();
                        $imageUrl = $firstImage ? Storage::disk('public')->url($firstImage->path) : null;
                    @endphp
                    @if($imageUrl)
                        <img src="{{ $imageUrl }}" alt="" class="size-14 object-cover rounded-xl border border-slate-200/50 shrink-0" />
                    @else
                        <div class="size-14 grid place-items-center rounded-xl bg-slate-200/50 border border-slate-200/50 text-slate-400 shrink-0">
                            <i class="ti ti-photo-off text-xl"></i>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <div class="truncate text-xs font-bold text-slate-900">{{ $property->title }}</div>
                        <div class="truncate text-[10px] font-bold text-slate-400 uppercase mt-0.5 tracking-wider">{{ $property->type }}</div>
                        <div class="text-xs font-extrabold text-brand-accent mt-1">Rp {{ number_format((float) $property->price, 0, ',', '.') }}</div>
                    </div>
                </div>

                {{-- Price Estimation Box --}}
                <div id="estimationCard" class="rounded-2xl bg-emerald-50/50 p-4 border border-emerald-500/20 hidden">
                    <div class="text-xs font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5 leading-none">
                        <i class="ti ti-chart-arrows-vertical text-emerald-600 animate-pulse text-base"></i>
                        <span>Estimasi Harga Lokasi</span>
                    </div>
                    <div class="mt-3">
                        <div id="estRange" class="text-lg font-extrabold text-emerald-950 leading-none">-</div>
                        <p class="text-[10px] text-emerald-700 mt-2 font-semibold leading-relaxed">Rekomendasi taksiran harga pasar berdasarkan lokasi koordinat terpilih di kecamatan <span id="estDistrict" class="font-extrabold">Samarinda</span>.</p>
                    </div>
                    <div class="mt-3 pt-3 border-t border-emerald-500/10">
                        <div class="text-[9px] font-bold text-emerald-800 uppercase tracking-wider mb-2">Faktor Geospasial Terdekat:</div>
                        <ul id="estFactors" class="space-y-1.5 text-[10px] font-bold text-emerald-700"></ul>
                    </div>
                </div>

                {{-- Location Coordinate Display --}}
                <form method="POST" action="{{ route('seller.listings.location.update', ['property' => $property->id]) }}" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div class="rounded-2xl bg-slate-50 border border-slate-200/50 p-4">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2.5">Koordinat Terpilih</div>
                        <div class="grid gap-3 grid-cols-2">
                            <div>
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Latitude</label>
                                <input id="latInput" name="lat" type="number" class="input font-mono py-1.5 text-xs text-slate-700" value="{{ old('lat', $point['lat']) }}" step="0.000001" required />
                            </div>
                            <div>
                                <label class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Longitude</label>
                                <input id="lngInput" name="lng" type="number" class="input font-mono py-1.5 text-xs text-slate-700" value="{{ old('lng', $point['lng']) }}" step="0.000001" required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <button type="submit" class="btn btn-primary w-full py-3 text-xs font-bold border-0 cursor-pointer shadow-sm">Simpan Lokasi</button>
                        <a href="{{ route('seller.listings.edit', ['property' => $property->id]) }}" class="btn btn-outline w-full text-center py-2.5 text-xs font-bold block">Kembali ke Form</a>
                        <a href="{{ route('seller.listings.index') }}" class="btn btn-outline w-full text-center py-2.5 text-xs font-bold block border-slate-200 text-slate-500">Batal</a>
                    </div>
                </form>
            </div>
        </aside>

        {{-- Right Map Container --}}
        <div class="flex-1 p-3 md:p-0 relative h-full">
            <div class="w-full h-full rounded-2xl md:rounded-none overflow-hidden relative border border-slate-200/60 md:border-none shadow-sm md:shadow-none">
                <div id="map" class="h-full w-full relative z-0"></div>

                {{-- Map Overlay Instruction Card --}}
                <div class="absolute left-4 top-4 w-[280px] bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/50 p-4 z-[999] shadow-lg pointer-events-none">
                    <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5">
                        <i class="ti ti-help text-brand-primary text-base"></i>
                        <span>Instruksi Penentuan Lokasi</span>
                    </div>
                    <p class="mt-2 text-[10px] font-semibold text-slate-500 leading-relaxed">
                        Klik pada area peta untuk menaruh penanda titik properti, atau seret (*drag*) penanda pin untuk pengaturan posisi presisi.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const latInput = document.getElementById('latInput');
            const lngInput = document.getElementById('lngInput');

            const propertyType = @json($property->type);
            const propertyLandArea = @json($property->land_area);

            const start = @json($point);
            const map = L.map('map', { zoomControl: false }).setView([start.lat, start.lng], 13);
            L.control.zoom({ position: 'bottomright' }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                maxZoom: 20,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd'
            }).addTo(map);

            // Custom draggable pulse marker icon
            const pulseIcon = L.divIcon({
                className: '',
                html: `<div class="pulse-marker-icon">
                         <div class="pulse-marker-core"></div>
                         <div class="pulse-marker-ring"></div>
                       </div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            });

            const marker = L.marker([start.lat, start.lng], { icon: pulseIcon, draggable: true }).addTo(map);

            function fetchEstimation(lat, lng) {
                const card = document.getElementById('estimationCard');
                const estRange = document.getElementById('estRange');
                const estFactors = document.getElementById('estFactors');
                const estDistrict = document.getElementById('estDistrict');

                if (propertyType !== 'Rumah' && propertyType !== 'Tanah') {
                    card.classList.add('hidden');
                    return;
                }

                fetch('{{ route('seller.estimate-price') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        lat: parseFloat(lat),
                        lng: parseFloat(lng),
                        type: propertyType,
                        land_area: parseInt(propertyLandArea)
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;
                    card.classList.remove('hidden');

                    const minStr = new Intl.NumberFormat('id-ID').format(data.min_price);
                    const maxStr = new Intl.NumberFormat('id-ID').format(data.max_price);
                    estRange.textContent = `Rp ${minStr} - Rp ${maxStr}`;
                    estDistrict.textContent = data.district_name;

                    estFactors.innerHTML = '';
                    if (data.factors && data.factors.length > 0) {
                        data.factors.forEach(f => {
                            const li = document.createElement('li');
                            li.className = 'flex items-center justify-between gap-2';
                            const dotColor = f.positive ? 'bg-emerald-500' : 'bg-rose-500';
                            const textColor = f.positive ? 'text-emerald-700' : 'text-rose-700';
                            li.innerHTML = `<span class="flex items-center gap-1.5 min-w-0"><span class="size-1.5 rounded-full ${dotColor} shrink-0"></span><span class="truncate">${f.name}</span></span><span class="${textColor}">${f.impact}</span>`;
                            estFactors.appendChild(li);
                        });
                    } else {
                        estFactors.innerHTML = '<li class="text-slate-400">Tidak ada faktor penyesuaian</li>';
                    }
                })
                .catch(err => console.error(err));
            }

            function setPoint(lat, lng) {
                marker.setLatLng([lat, lng]);
                latInput.value = Number(lat).toFixed(6);
                lngInput.value = Number(lng).toFixed(6);
                fetchEstimation(lat, lng);
            }

            map.on('click', (e) => setPoint(e.latlng.lat, e.latlng.lng));
            marker.on('dragend', () => {
                const p = marker.getLatLng();
                setPoint(p.lat, p.lng);
            });

            // Initial estimation load
            if (start.lat && start.lng) {
                fetchEstimation(start.lat, start.lng);
            }

            // Invalidate size immediately
            map.invalidateSize();
            setTimeout(() => map.invalidateSize(), 500);
        </script>
    @endpush
</x-layouts.blank>