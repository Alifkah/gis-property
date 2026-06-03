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
                        <span class="grid size-8 place-items-center rounded-full bg-brand-primary text-white">2</span>
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

                {{-- Estimation Panel --}}
                <div id="estimationCard" class="mt-5 rounded-2xl bg-brand-primary/5 p-4 border border-brand-primary/10 hidden">
                    <div class="text-xs font-bold text-brand-primary uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="size-4 text-brand-primary animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Taksiran Harga Rekomendasi</span>
                    </div>
                    <div class="mt-2.5">
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Rekomendasi Kisaran:</div>
                        <div id="estRange" class="text-sm font-black text-brand-accent mt-0.5">-</div>
                    </div>
                    <div class="mt-2.5 pt-2.5 border-t border-brand-primary/10">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Faktor Pendukung Spasial:</div>
                        <ul id="estFactors" class="space-y-1.5 text-[11px] font-semibold text-slate-600"></ul>
                    </div>
                    <div class="text-[10px] text-slate-400 mt-2.5 font-medium leading-relaxed">Berdasarkan data spasial kecamatan <span id="estDistrict" class="font-bold text-slate-700"></span>.</div>
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

                    <button type="submit" class="btn btn-primary w-full cursor-pointer">Simpan Lokasi</button>
                    <a href="{{ route('seller.listings.edit', ['property' => $property->id]) }}" class="btn btn-outline w-full text-center">Kembali ke Data</a>
                    <a href="{{ route('seller.listings.index') }}" class="btn btn-outline w-full text-center">Kembali ke Dashboard</a>
                </form>
            </div>
        </aside>

        <div class="relative flex-1">
            <div id="map" class="h-full w-full relative z-0"></div>
            <div class="absolute left-4 top-4 w-[320px] card p-4 z-[9999] shadow-md">
                <div class="text-xs font-bold text-slate-900">Petunjuk Lokasi Spasial</div>
                <div class="mt-2 text-xs font-semibold text-slate-500 leading-relaxed">
                    Klik di mana saja pada peta untuk menentukan posisi properti, atau seret penanda pin untuk penempatan yang lebih presisi.
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const latInput = document.getElementById('latInput');
            const lngInput = document.getElementById('lngInput');

            const propertyType = @json($property->type);
            const propertyLandArea = @json($property->land_area);

            const start = @json($point);
            const map = L.map('map', { zoomControl: false }).setView([start.lat, start.lng], 13);
            L.control.zoom({ position: 'bottomleft' }).addTo(map);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            const marker = L.marker([start.lat, start.lng], { draggable: true }).addTo(map);

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
        </script>
    @endpush
</x-layouts.blank>
