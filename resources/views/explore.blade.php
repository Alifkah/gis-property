<x-layouts.blank>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="flex h-dvh w-full">

        {{-- Kiri: Hasil Pencarian --}}
        <div class="flex shrink-0 flex-col border-r border-slate-200 bg-white" style="width:300px;min-width:300px">

            {{-- Header: branding + tombol kembali --}}
            <div style="border-bottom:1px solid #e2e8f0" class="px-4 py-3">
                <a
                    href="{{ route('home') }}"
                    class="group inline-flex w-full items-center gap-2 rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-600 ring-1 ring-slate-200/70 transition hover:bg-indigo-50 hover:text-indigo-700 hover:ring-indigo-200"
                >
                    <svg class="size-4 shrink-0 transition group-hover:-translate-x-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>

            {{-- Results count --}}
            <div style="border-bottom:1px solid #e2e8f0" class="px-4 py-2.5">
                <div class="text-xs font-extrabold text-slate-900">Hasil Pencarian</div>
                <div id="resultCount" class="mt-0.5 text-xs font-semibold text-slate-400">Memuat data...</div>
            </div>

            <div class="flex-1 overflow-y-auto px-3 py-3">
                <div id="propertyList" class="grid gap-2"></div>
            </div>
        </div>


        {{-- Tengah: Peta --}}
        <div class="relative flex-1">
            <div id="map" class="h-full w-full"></div>
        </div>

        {{-- Layer Control Panel (injected as Leaflet control via JS) --}}
        <div id="layerControlPanel" style="display:none">
            <div style="width:200px;background:#fff;border-radius:1rem;padding:12px;box-shadow:0 4px 24px rgba(15,23,42,.15);border:1px solid #e2e8f0">
                <div class="text-xs font-extrabold text-slate-900">Lapisan Peta</div>
                <div class="mt-2 grid gap-1.5">
                    <label class="flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-700">Zona Banjir</span>
                        <input id="toggleFlood" type="checkbox" class="size-4 accent-indigo-600" checked />
                    </label>
                    <label class="flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-700">Batas Admin</span>
                        <input id="toggleDistricts" type="checkbox" class="size-4 accent-indigo-600" checked />
                    </label>
                    <label class="flex items-center justify-between gap-3">
                        <span class="text-xs font-semibold text-slate-700">Mode Gelap Peta</span>
                        <input id="toggleDarkMode" type="checkbox" class="size-4 accent-indigo-600" />
                    </label>
                </div>
                <div style="margin-top:8px;padding-top:8px;border-top:1px solid #f1f5f9">
                    <div class="text-xs font-extrabold text-slate-900">Legenda</div>
                    <div class="mt-1.5 grid gap-1">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                            <span style="width:10px;height:10px;border-radius:2px;background:#10b981;display:inline-block"></span> Bebas banjir
                        </div>
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                            <span style="width:10px;height:10px;border-radius:2px;background:#f43f5e;display:inline-block"></span> Zona banjir
                        </div>
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-600">
                            <span style="width:10px;height:10px;border-radius:2px;background:#f97316;display:inline-block"></span> Properti
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Filter Pencarian --}}
        <div class="flex shrink-0 flex-col bg-white" style="width:300px;min-width:300px;border-left:1px solid #e2e8f0">
            <div class="px-4 py-3" style="border-bottom:1px solid #e2e8f0">
                <div class="text-xs font-extrabold text-slate-900">Filter Pencarian</div>
            </div>
            <div class="flex-1 overflow-y-auto px-4 py-4">
                <div class="grid gap-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Tipe</label>
                        <select id="filterType" class="select mt-1">
                            <option value="">Semua</option>
                            @foreach ($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Kecamatan</label>
                        <select id="filterDistrict" class="select mt-1">
                            <option value="">Semua</option>
                            @foreach ($districtFeatures['features'] as $feature)
                                <option value="{{ $feature['properties']['name'] }}">{{ $feature['properties']['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Rentang Harga</label>
                        <select id="filterPrice" class="select mt-1">
                            <option value="">Semua</option>
                            <option value="0-250000000">0 – 250 jt</option>
                            <option value="250000000-750000000">250 jt – 750 jt</option>
                            <option value="750000000-2000000000">750 jt – 2 M</option>
                            <option value="2000000000-999999999999">2 M+</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Dekat Fasilitas</label>
                        <select id="filterAmenityType" class="select mt-1">
                            <option value="">Semua</option>
                            @foreach ($amenityTypes as $amenityType)
                                <option value="{{ $amenityType }}">{{ $amenityType }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="amenityIdGroup" class="opacity-40 transition-opacity">
                        <label class="text-xs font-semibold text-slate-600">Pilih Fasilitas <span class="text-[10px] font-normal text-slate-400">(pilih tipe dulu)</span></label>
                        <select id="filterAmenityId" class="select mt-1" disabled>
                            <option value="">Semua</option>
                        </select>
                    </div>
                    <div id="amenityRadiusGroup" class="opacity-40 transition-opacity">
                        <label class="text-xs font-semibold text-slate-600">Radius Fasilitas <span class="text-[10px] font-normal text-slate-400">(pilih tipe dulu)</span></label>
                        <select id="filterAmenityRadius" class="select mt-1" disabled>
                            <option value="500">500 m</option>
                            <option value="1000" selected>1 km</option>
                            <option value="2000">2 km</option>
                            <option value="5000">5 km</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Status</label>
                        <select id="filterStatus" class="select mt-1">
                            <option value="">Semua</option>
                            <option value="Tersedia">Tersedia</option>
                            <option value="Terjual">Terjual</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Urutkan Harga</label>
                        <select id="filterSort" class="select mt-1">
                            <option value="desc">Tertinggi</option>
                            <option value="asc">Terendah</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="button" class="pill" data-pill="Rumah">Rumah</button>
                        <button type="button" class="pill" data-pill="Tanah">Tanah</button>
                        <button type="button" class="pill" data-pill="BebasBanjir">Bebas Banjir</button>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-3 ring-1 ring-slate-200/70">
                        <div class="text-xs font-extrabold text-slate-900">Cari di Sekitar Titik</div>
                        <div class="mt-1 text-xs font-semibold text-slate-500">Klik peta untuk menentukan titik pusat.</div>
                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-[11px] font-semibold text-slate-600">Lat</label>
                                <input id="centerLat" type="text" class="input mt-1" readonly />
                            </div>
                            <div>
                                <label class="text-[11px] font-semibold text-slate-600">Lng</label>
                                <input id="centerLng" type="text" class="input mt-1" readonly />
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="text-[11px] font-semibold text-slate-600">Radius</label>
                            <select id="radiusM" class="select mt-1">
                                <option value="">Tanpa radius</option>
                                <option value="500">500 m</option>
                                <option value="1000" selected>1 km</option>
                                <option value="2000">2 km</option>
                                <option value="5000">5 km</option>
                            </select>
                        </div>
                        <button id="clearCenter" type="button" class="btn btn-outline mt-2 w-full">Reset Titik</button>
                    </div>
                </div>
            </div>
            <div class="p-4" style="border-top:1px solid #e2e8f0">
                <button id="applyFilters" class="btn btn-primary w-full">Terapkan Filter</button>
            </div>
        </div>

    </div>


    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const districts = @json($districtFeatures);
            const floodZones = @json($floodZoneFeatures);

            const map = L.map('map', { zoomControl: false }).setView([-0.5, 117.15], 12);
            L.control.zoom({ position: 'bottomleft' }).addTo(map);

            // Register Lapisan Peta as a proper Leaflet control so it's always on top
            var LayerPanel = L.Control.extend({
                options: { position: 'topleft' },
                onAdd: function () {
                    var panel = document.getElementById('layerControlPanel');
                    panel.style.display = 'block';
                    L.DomEvent.disableClickPropagation(panel);
                    L.DomEvent.disableScrollPropagation(panel);
                    return panel;
                }
            });
            new LayerPanel().addTo(map);

            const osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap contributors'
            });

            const darkTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
            });

            osmTiles.addTo(map);

            document.getElementById('toggleDarkMode').addEventListener('change', (e) => {
                if (e.target.checked) {
                    map.removeLayer(osmTiles);
                    darkTiles.addTo(map);
                } else {
                    map.removeLayer(darkTiles);
                    osmTiles.addTo(map);
                }
            });

            const markerLayer = L.layerGroup().addTo(map);

            const districtLayer = L.geoJSON(districts, {
                style: {
                    color: '#4338ca',
                    weight: 2,
                    fillOpacity: 0.06
                }
            }).addTo(map);

            const floodLayer = L.geoJSON(floodZones, {
                style: {
                    color: '#f43f5e',
                    weight: 2,
                    fillOpacity: 0.14
                }
            }).addTo(map);

            const markerIcon = L.divIcon({
                className: '',
                html: '<div style="width:14px;height:14px;border-radius:9999px;background:#f97316;border:2px solid #ffffff;box-shadow:0 8px 20px rgba(15,23,42,.2)"></div>',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });

            const propertyList = document.getElementById('propertyList');
            const applyFilters = document.getElementById('applyFilters');
            const filterType = document.getElementById('filterType');
            const filterDistrict = document.getElementById('filterDistrict');
            const filterPrice = document.getElementById('filterPrice');
            const filterAmenityType = document.getElementById('filterAmenityType');
            const filterAmenityId = document.getElementById('filterAmenityId');
            const filterAmenityRadius = document.getElementById('filterAmenityRadius');
            const filterStatus = document.getElementById('filterStatus');
            const filterSort = document.getElementById('filterSort');
            const pills = Array.from(document.querySelectorAll('.pill'));
            const centerLat = document.getElementById('centerLat');
            const centerLng = document.getElementById('centerLng');
            const radiusM = document.getElementById('radiusM');
            const clearCenter = document.getElementById('clearCenter');

            // Baca query string dari URL (dikirim dari form search di halaman home)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('type')) { filterType.value = urlParams.get('type'); }
            if (urlParams.get('district')) { filterDistrict.value = urlParams.get('district'); }
            if (urlParams.get('price')) { filterPrice.value = urlParams.get('price'); }
            if (urlParams.get('status')) { filterStatus.value = urlParams.get('status'); }

            let selectedPills = new Set();
            let properties = [];
            let meta = { page: 1, per_page: 50, total: 0 };
            let center = null;
            let centerMarker = null;
            let centerCircle = null;
            let markerMap = new Map();

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID').format(value);
            }

            function formatDistance(meters) {
                if (!Number.isFinite(Number(meters))) return null;
                const km = Number(meters) / 1000;
                return `${km.toFixed(1)} km`;
            }

            function imageUrl(type, imageUrl) {
                if (imageUrl) return imageUrl;
                const typeUpper = (type || 'PROPERTI').toUpperCase();
                const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" fill="none"><rect width="400" height="250" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4f46e5" stop-opacity="0.12"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0.04"/></linearGradient></defs><path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/><text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">${typeUpper}</text></svg>`;
                return 'data:image/svg+xml;base64,' + btoa(svg);
            }

            function renderList(items) {
                const resultCount = document.getElementById('resultCount');
                if (resultCount) {
                    resultCount.textContent = items.length > 0
                        ? `${items.length} properti ditemukan`
                        : 'Tidak ada properti';
                }

                propertyList.innerHTML = items.length === 0
                    ? '<div class="py-6 text-center text-xs font-semibold text-slate-400">Tidak ada properti sesuai filter.</div>'
                    : items.map((p) => {
                    const isSold = p.status === 'Terjual';
                    const badges = isSold
                        ? '<span class="inline-flex items-center rounded-full bg-slate-700 px-2 py-1 text-xs font-semibold text-white">Terjual</span>'
                        : [
                            p.is_new ? '<span class="inline-flex items-center rounded-full bg-indigo-600 px-2 py-1 text-xs font-semibold text-white">Rumah Baru</span>' : '',
                            p.is_flood_safe ? '<span class="inline-flex items-center rounded-full bg-emerald-500 px-2 py-1 text-xs font-semibold text-white">Bebas Banjir</span>' : ''
                          ].join('');

                    return `
                        <button type="button" class="w-full text-left card overflow-hidden hover:shadow-md transition" data-id="${p.id}">
                            <div class="relative overflow-hidden bg-slate-100" style="height:120px">
                                <img src="${imageUrl(p.type, p.image_url)}" alt="${p.title}" class="h-full w-full object-cover ${isSold ? 'opacity-60' : ''}" loading="lazy" />
                                <div class="absolute left-2 top-2 flex flex-wrap gap-1">${badges}</div>
                            </div>
                            <div class="p-3">
                                <div class="text-xs font-extrabold text-slate-900 truncate">${p.title}</div>
                                <div class="mt-0.5 text-[11px] font-semibold text-slate-500 truncate">${p.district_name ?? 'Kota Samarinda'}</div>
                                <div class="mt-2 flex items-center justify-between gap-2">
                                    <div class="text-xs font-extrabold ${isSold ? 'text-slate-400' : 'text-indigo-700'}">Rp ${formatCurrency(p.price)}</div>
                                    <span class="text-[11px] font-semibold text-slate-500">${p.amenity_distance_m !== null && p.amenity_distance_m !== undefined ? formatDistance(p.amenity_distance_m) : `${p.land_area} m²`}</span>
                                </div>
                            </div>
                        </button>
                    `;
                }).join('');

                Array.from(propertyList.querySelectorAll('button[data-id]')).forEach((el) => {
                    el.addEventListener('click', () => {
                        const id = parseInt(el.dataset.id, 10);
                        const target = items.find((p) => p.id === id);
                        if (!target) return;
                        map.setView([target.lat, target.lng], 15, { animate: true });
                        const marker = markerMap.get(id);
                        if (marker) {
                            marker.openPopup();
                        }
                    });
                });
            }

            async function fetchProperties(params) {
                const url = new URL('/api/explore/properties', window.location.origin);
                Object.entries(params).forEach(([key, value]) => {
                    if (value === '' || value === null || value === undefined) return;
                    url.searchParams.set(key, String(value));
                });

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Gagal memuat data properti.');
                }

                return await response.json();
            }

            async function fetchAmenities(type) {
                const url = new URL('/api/explore/amenities', window.location.origin);
                if (type) {
                    url.searchParams.set('type', type);
                }

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Gagal memuat fasilitas.');
                }

                const payload = await response.json();
                return payload.data ?? [];
            }

            async function fetchGeojson(params) {
                const url = new URL('/api/explore/properties.geojson', window.location.origin);
                Object.entries(params).forEach(([key, value]) => {
                    if (value === '' || value === null || value === undefined) return;
                    url.searchParams.set(key, String(value));
                });

                const response = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                if (!response.ok) {
                    throw new Error('Gagal memuat GeoJSON.');
                }

                return await response.json();
            }

            function renderMarkers(geojson) {
                markerLayer.clearLayers();
                markerMap = new Map();

                const features = geojson?.features ?? [];

                features.forEach((feature) => {
                    const p = feature?.properties ?? {};
                    const geometry = feature?.geometry ?? {};
                    const coords = geometry?.coordinates ?? null;
                    if (!coords || coords.length < 2) return;

                    const lng = coords[0];
                    const lat = coords[1];

                    // Try to find image_url from the already-fetched properties list
                    const listItem = properties.find((item) => item.id === Number(p.id));
                    const imgSrc = imageUrl(p.type, listItem?.image_url ?? null);

                    const popupHtml = `
                        <div style="width:240px">
                            <img src="${imgSrc}" alt="${p.title}" style="width:100%;height:120px;object-fit:cover;border-radius:12px" />
                            <div style="padding-top:10px">
                                <div style="font-weight:800;color:#4338ca">Rp ${formatCurrency(p.price)}</div>
                                <div style="margin-top:4px;font-weight:700;color:#0f172a">${p.title}</div>
                                <a href="/properties/${p.id}" style="margin-top:10px;display:inline-flex;align-items:center;justify-content:center;padding:10px 12px;border-radius:12px;background:#4f46e5;color:#fff;font-weight:700;text-decoration:none;width:100%">Lihat Detail</a>
                            </div>
                        </div>
                    `;

                    const marker = L.marker([lat, lng], { icon: markerIcon }).addTo(markerLayer);
                    marker.bindPopup(popupHtml, { closeButton: true, className: 'rounded-xl' });

                    if (Number.isFinite(Number(p.id))) {
                        markerMap.set(Number(p.id), marker);
                    }
                });
            }

            function renderPagination() {
                const current = meta.page ?? 1;
                const perPage = meta.per_page ?? 50;
                const total = meta.total ?? 0;
                const totalPages = Math.max(1, Math.ceil(total / perPage));
                const isPrevDisabled = current <= 1;
                const isNextDisabled = current >= totalPages;

                const html = `
                    <div class="mt-3 flex items-center justify-between gap-2 rounded-2xl bg-white p-3 ring-1 ring-slate-200/60">
                        <button type="button" id="prevPage" class="btn btn-outline ${isPrevDisabled ? 'pointer-events-none opacity-50' : ''}">Prev</button>
                        <div class="text-xs font-extrabold text-slate-700">Halaman ${current} / ${totalPages}</div>
                        <button type="button" id="nextPage" class="btn btn-outline ${isNextDisabled ? 'pointer-events-none opacity-50' : ''}">Next</button>
                    </div>
                `;

                propertyList.insertAdjacentHTML('beforeend', html);

                const prevBtn = document.getElementById('prevPage');
                const nextBtn = document.getElementById('nextPage');
                if (prevBtn && !isPrevDisabled) {
                    prevBtn.addEventListener('click', () => {
                        meta.page = Math.max(1, current - 1);
                        apply();
                    });
                }
                if (nextBtn && !isNextDisabled) {
                    nextBtn.addEventListener('click', () => {
                        meta.page = Math.min(totalPages, current + 1);
                        apply();
                    });
                }
            }

            async function apply() {
                const type = filterType.value;
                const district = filterDistrict.value;
                const price = filterPrice.value;
                const sort = filterSort.value;
                const status = filterStatus.value;
                const pillsLocal = new Set(selectedPills);

                const floodSafe = pillsLocal.has('BebasBanjir') ? 1 : null;
                const remoteType = pillsLocal.has('Rumah') ? 'Rumah' : pillsLocal.has('Tanah') ? 'Tanah' : type;
                const radius = radiusM.value || '';
                const centerLatValue = center ? center.lat : '';
                const centerLngValue = center ? center.lng : '';
                const amenityType = filterAmenityType.value || '';
                const amenityId = filterAmenityId.value || '';
                const amenityRadius = filterAmenityRadius.disabled ? '' : (filterAmenityRadius.value || '');

                const skeletonHtml = Array.from({ length: 4 }).map(() => `
                    <div class="w-full card overflow-hidden animate-pulse border border-slate-100 bg-white rounded-2xl">
                        <div class="bg-slate-200" style="height:120px"></div>
                        <div class="p-3 space-y-2.5">
                            <div class="h-3.5 bg-slate-200 rounded w-3/4"></div>
                            <div class="h-3 bg-slate-200 rounded w-1/2"></div>
                            <div class="pt-2 flex items-center justify-between gap-2">
                                <div class="h-4 bg-slate-200 rounded w-1/3"></div>
                                <div class="h-3 bg-slate-200 rounded w-1/4"></div>
                            </div>
                        </div>
                    </div>
                `).join('');
                propertyList.innerHTML = skeletonHtml;

                try {
                    const [priceMin, priceMax] = price ? price.split('-') : ['', ''];
                    const payload = await fetchProperties({
                        type: remoteType || '',
                        district: district || '',
                        status: status || '',
                        sort,
                        flood_safe: floodSafe,
                        price_min: priceMin,
                        price_max: priceMax,
                        page: meta.page ?? 1,
                        per_page: meta.per_page ?? 50,
                        center_lat: centerLatValue,
                        center_lng: centerLngValue,
                        radius_m: radius,
                        amenity_type: amenityType,
                        amenity_id: amenityId,
                        amenity_radius_m: amenityRadius,
                    });

                    properties = payload.data ?? [];
                    meta = payload.meta ?? meta;

                    renderList(properties);
                    renderPagination();

                    try {
                        const geojson = await fetchGeojson({
                            type: remoteType || '',
                            district: district || '',
                            status: status || '',
                            sort,
                            flood_safe: floodSafe,
                            price_min: priceMin,
                            price_max: priceMax,
                            page: meta.page ?? 1,
                            per_page: meta.per_page ?? 50,
                            center_lat: centerLatValue,
                            center_lng: centerLngValue,
                            radius_m: radius,
                            amenity_type: amenityType,
                            amenity_id: amenityId,
                            amenity_radius_m: amenityRadius,
                        });

                        renderMarkers(geojson);
                    } catch (e) {
                        markerLayer.clearLayers();
                    }
                } catch (e) {
                    propertyList.innerHTML = '<div class="text-sm font-semibold text-rose-600">Gagal memuat data. Coba lagi.</div>';
                    markerLayer.clearLayers();
                }
            }

            pills.forEach((pill) => {
                pill.addEventListener('click', () => {
                    const key = pill.dataset.pill;
                    if (selectedPills.has(key)) {
                        selectedPills.delete(key);
                        pill.classList.remove('pill-active');
                    } else {
                        selectedPills.add(key);
                        pill.classList.add('pill-active');
                    }
                });
            });

            applyFilters.addEventListener('click', () => {
                meta.page = 1;
                apply();
            });
            apply();

            async function syncAmenitySelects() {
                const type = filterAmenityType.value;
                const amenityIdGroup = document.getElementById('amenityIdGroup');
                const amenityRadiusGroup = document.getElementById('amenityRadiusGroup');
                filterAmenityId.innerHTML = '<option value="">Semua</option>';

                if (!type) {
                    filterAmenityId.disabled = true;
                    filterAmenityRadius.disabled = true;
                    filterAmenityRadius.value = '1000';
                    if (amenityIdGroup) { amenityIdGroup.style.opacity = '0.4'; }
                    if (amenityRadiusGroup) { amenityRadiusGroup.style.opacity = '0.4'; }
                    return;
                }

                filterAmenityId.disabled = false;
                filterAmenityRadius.disabled = false;
                if (amenityIdGroup) { amenityIdGroup.style.opacity = '1'; }
                if (amenityRadiusGroup) { amenityRadiusGroup.style.opacity = '1'; }

                try {
                    const amenities = await fetchAmenities(type);
                    filterAmenityId.innerHTML = [
                        '<option value="">Semua</option>',
                        ...amenities.map((a) => `<option value="${a.id}">${a.name}</option>`)
                    ].join('');
                } catch (e) {
                    filterAmenityId.innerHTML = '<option value="">Gagal memuat</option>';
                }
            }

            filterAmenityType.addEventListener('change', () => {
                meta.page = 1;
                syncAmenitySelects().then(apply);
            });

            filterAmenityId.addEventListener('change', () => {
                meta.page = 1;
                apply();
            });

            filterAmenityRadius.addEventListener('change', () => {
                meta.page = 1;
                apply();
            });

            function setCenterPoint(lat, lng) {
                center = { lat, lng };
                centerLat.value = Number(lat).toFixed(6);
                centerLng.value = Number(lng).toFixed(6);

                if (centerMarker) {
                    map.removeLayer(centerMarker);
                }
                if (centerCircle) {
                    map.removeLayer(centerCircle);
                }

                centerMarker = L.marker([lat, lng]).addTo(map);

                const radiusValue = Number(radiusM.value || 0);
                if (radiusValue > 0) {
                    centerCircle = L.circle([lat, lng], { radius: radiusValue, color: '#4f46e5', fillOpacity: 0.08 }).addTo(map);
                }
            }

            map.on('click', (e) => {
                setCenterPoint(e.latlng.lat, e.latlng.lng);
                meta.page = 1;
                apply();
            });

            radiusM.addEventListener('change', () => {
                if (!center) return;
                setCenterPoint(center.lat, center.lng);
                meta.page = 1;
                apply();
            });

            clearCenter.addEventListener('click', () => {
                center = null;
                centerLat.value = '';
                centerLng.value = '';

                if (centerMarker) {
                    map.removeLayer(centerMarker);
                    centerMarker = null;
                }
                if (centerCircle) {
                    map.removeLayer(centerCircle);
                    centerCircle = null;
                }

                meta.page = 1;
                apply();
            });

            document.getElementById('toggleFlood').addEventListener('change', (e) => {
                if (e.target.checked) {
                    floodLayer.addTo(map);
                } else {
                    map.removeLayer(floodLayer);
                }
            });

            document.getElementById('toggleDistricts').addEventListener('change', (e) => {
                if (e.target.checked) {
                    districtLayer.addTo(map);
                } else {
                    map.removeLayer(districtLayer);
                }
            });
        </script>
        <style>
            .pill { display:inline-flex; align-items:center; justify-content:center; border-radius:9999px; padding:8px 12px; font-size:12px; font-weight:700; background:#f8fafc; color:#334155; border:1px solid rgba(148,163,184,.5); transition:all .2s }
            .pill:hover { background:#f1f5f9 }
            .pill-active { background:#4f46e5; color:#fff; border-color:#4f46e5 }

            .card {
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .card:hover {
                transform: translateY(-2px);
            }
        </style>
    @endpush
</x-layouts.blank>
