<x-layouts.seller>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <section class="grid gap-6">
            <div class="card p-6">
                <div class="text-sm font-extrabold text-slate-900">Analisis Kompetitor</div>
                <div class="mt-1 text-sm text-slate-600">Lihat properti kompetitor di sekitar listing Anda dan bandingkan harga.</div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Pilih Properti Anda</label>
                        <select id="propertySelect" class="select mt-1">
                            <option value="">-- Pilih Properti --</option>
                            @foreach ($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->title }} ({{ $property->type }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Radius Pencarian</label>
                        <select id="radiusSelect" class="select mt-1">
                            <option value="500">500 meter</option>
                            <option value="1000" selected>1 kilometer</option>
                            <option value="2000">2 kilometer</option>
                            <option value="3000">3 kilometer</option>
                            <option value="5000">5 kilometer</option>
                        </select>
                    </div>
                </div>

                <button id="analyzeBtn" class="btn btn-primary mt-4" disabled>Analisis Kompetitor</button>
            </div>

            <div id="resultsSection" class="hidden grid gap-6">
                <div class="flex items-center justify-between gap-4 print:hidden">
                    <div class="text-xs font-semibold text-slate-500">Hasil Analisis</div>
                    <div class="flex gap-2">
                        <a id="exportCsvBtn" href="#" class="btn btn-outline flex items-center gap-2">
                            <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Ekspor CSV</span>
                        </a>
                        <button onclick="window.print()" class="btn btn-primary flex items-center gap-2">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Cetak PDF</span>
                        </button>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="text-sm font-extrabold text-slate-900">Statistik Kompetitor</div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div class="rounded-2xl bg-brand-primary/5 p-4 ring-1 ring-brand-primary/10">
                            <div class="text-xs font-semibold text-brand-primary">Total Kompetitor</div>
                            <div id="statTotal" class="mt-2 text-2xl font-extrabold text-brand-primary">-</div>
                        </div>
                        <div class="rounded-2xl bg-emerald-50 p-4 ring-1 ring-emerald-100">
                            <div class="text-xs font-semibold text-emerald-600">Harga Rata-rata</div>
                            <div id="statAvgPrice" class="mt-2 text-2xl font-extrabold text-emerald-900">-</div>
                        </div>
                        <div class="rounded-2xl bg-rose-50 p-4 ring-1 ring-rose-100">
                            <div class="text-xs font-semibold text-rose-600">Harga per m²</div>
                            <div id="statPricePerSqm" class="mt-2 text-2xl font-extrabold text-rose-900">-</div>
                        </div>
                        <div class="rounded-2xl bg-amber-50 p-4 ring-1 ring-amber-100">
                            <div class="text-xs font-semibold text-amber-600">Posisi Harga Anda</div>
                            <div id="statPosition" class="mt-2 text-lg font-extrabold text-amber-900">-</div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-extrabold text-slate-900">Properti Anda</div>
                        <div id="myPropertyInfo" class="mt-2 text-sm text-slate-700"></div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Peta Buffer Zone</div>
                            <div class="mt-1 text-sm text-slate-600">Visualisasi properti kompetitor dalam radius yang dipilih.</div>
                        </div>
                        <label class="flex items-center gap-2 print:hidden cursor-pointer select-none">
                            <input id="toggleDarkMode" type="checkbox" class="size-4 accent-brand-primary rounded" />
                            <span class="text-xs font-bold text-slate-700">Mode Gelap Peta</span>
                        </label>
                    </div>
                    <div id="map" class="relative z-0 mt-4 h-[500px] w-full rounded-2xl overflow-hidden ring-1 ring-slate-200"></div>
                </div>

                <div class="card p-6">
                    <div class="text-sm font-extrabold text-slate-900">Daftar Kompetitor</div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="border-b border-slate-200 text-xs font-extrabold text-slate-700">
                                <tr>
                                    <th class="pb-3">Properti</th>
                                    <th class="pb-3">Jarak</th>
                                    <th class="pb-3">Harga</th>
                                    <th class="pb-3">Luas Tanah</th>
                                    <th class="pb-3">Harga/m²</th>
                                    <th class="pb-3">Status</th>
                                    <th class="pb-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="competitorTable" class="divide-y divide-slate-100">
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-slate-500">Belum ada data</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </section>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const propertySelect = document.getElementById('propertySelect');
            const radiusSelect = document.getElementById('radiusSelect');
            const analyzeBtn = document.getElementById('analyzeBtn');
            const resultsSection = document.getElementById('resultsSection');
            const statTotal = document.getElementById('statTotal');
            const statAvgPrice = document.getElementById('statAvgPrice');
            const statPricePerSqm = document.getElementById('statPricePerSqm');
            const statPosition = document.getElementById('statPosition');
            const myPropertyInfo = document.getElementById('myPropertyInfo');
            const competitorTable = document.getElementById('competitorTable');

            let map = null;
            let markerLayer = null;
            let bufferCircle = null;
            let myMarker = null;

            // Auto-select property from URL query parameter
            const urlParams = new URLSearchParams(window.location.search);
            const propertyIdFromUrl = urlParams.get('property');
            if (propertyIdFromUrl && propertySelect.querySelector(`option[value="${propertyIdFromUrl}"]`)) {
                propertySelect.value = propertyIdFromUrl;
                analyzeBtn.disabled = false;
            }

            propertySelect.addEventListener('change', () => {
                analyzeBtn.disabled = !propertySelect.value;
            });

            analyzeBtn.addEventListener('click', async () => {
                const propertyId = propertySelect.value;
                const radius = radiusSelect.value;

                if (!propertyId) return;

                analyzeBtn.disabled = true;
                analyzeBtn.textContent = 'Menganalisis...';

                try {
                    const response = await fetch(`/seller/competitor-analysis/${propertyId}?radius=${radius}`, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal menganalisis kompetitor');
                    }

                    const data = await response.json();
                    renderResults(data);
                } catch (error) {
                    alert(error.message);
                } finally {
                    analyzeBtn.disabled = false;
                    analyzeBtn.textContent = 'Analisis Kompetitor';
                }
            });

            function formatCurrency(value) {
                if (!value) return 'Rp 0';
                const millions = value / 1000000;
                if (millions >= 1000) {
                    return `Rp ${(millions / 1000).toFixed(1)} M`;
                }
                return `Rp ${millions.toFixed(0)} jt`;
            }

            function formatDistance(meters) {
                if (meters < 1000) {
                    return `${Math.round(meters)} m`;
                }
                return `${(meters / 1000).toFixed(1)} km`;
            }

            function renderResults(data) {
                resultsSection.classList.remove('hidden');

                // Update CSV link
                const prop = data.property;
                document.getElementById('exportCsvBtn').href = `/seller/competitor-analysis/export/${prop.id}?radius=${data.statistics.radius_m}`;

                // Statistics
                statTotal.textContent = data.statistics.total_competitors;
                statAvgPrice.textContent = formatCurrency(data.statistics.avg_price);
                statPricePerSqm.textContent = data.statistics.avg_price_per_sqm 
                    ? `Rp ${Math.round(data.statistics.avg_price_per_sqm).toLocaleString('id-ID')}/m²`
                    : '-';
                statPosition.textContent = data.statistics.price_position;

                // My property info
                myPropertyInfo.innerHTML = `
                    <div class="grid gap-2">
                        <div><span class="font-semibold">Judul:</span> ${prop.title}</div>
                        <div><span class="font-semibold">Harga:</span> ${formatCurrency(prop.price)}</div>
                        <div><span class="font-semibold">Luas Tanah:</span> ${prop.land_area} m²</div>
                        <div><span class="font-semibold">Harga per m²:</span> Rp ${Math.round(prop.price_per_sqm).toLocaleString('id-ID')}/m²</div>
                    </div>
                `;

                // Map
                initMap(data);

                // Table
                renderTable(data.competitors);

                // Scroll to results
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function initMap(data) {
                if (map) {
                    map.remove();
                }

                map = L.map('map', { zoomControl: false }).setView([data.property.lat, data.property.lng], 14);
                L.control.zoom({ position: 'bottomright' }).addTo(map);

                const osmTiles = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                });

                const darkTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd',
                    maxZoom: 20
                });

                const toggleDark = document.getElementById('toggleDarkMode');
                if (toggleDark && toggleDark.checked) {
                    darkTiles.addTo(map);
                } else {
                    osmTiles.addTo(map);
                }

                if (toggleDark) {
                    const newToggle = toggleDark.cloneNode(true);
                    toggleDark.parentNode.replaceChild(newToggle, toggleDark);
                    newToggle.addEventListener('change', (e) => {
                        if (e.target.checked) {
                            map.removeLayer(osmTiles);
                            darkTiles.addTo(map);
                        } else {
                            map.removeLayer(darkTiles);
                            osmTiles.addTo(map);
                        }
                    });
                }

                markerLayer = L.layerGroup().addTo(map);

                // My property marker (red)
                const myIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:20px;height:20px;border-radius:9999px;background:#ef4444;border:3px solid #ffffff;box-shadow:0 8px 20px rgba(15,23,42,.3)"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                });

                myMarker = L.marker([data.property.lat, data.property.lng], { icon: myIcon }).addTo(markerLayer);
                myMarker.bindPopup(`
                    <div style="padding:8px">
                        <div style="font-weight:800;color:#ef4444">Properti Anda</div>
                        <div style="margin-top:4px;font-weight:700;color:#0f172a">${data.property.title}</div>
                        <div style="margin-top:4px;font-weight:600;color:#4338ca">${formatCurrency(data.property.price)}</div>
                    </div>
                `);

                // Buffer circle
                bufferCircle = L.circle([data.property.lat, data.property.lng], {
                    radius: data.statistics.radius_m,
                    color: '#4f46e5',
                    fillColor: '#4f46e5',
                    fillOpacity: 0.08,
                    weight: 2,
                    dashArray: '5, 5'
                }).addTo(map);

                // Competitor markers (blue)
                const competitorIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:14px;height:14px;border-radius:9999px;background:#3b82f6;border:2px solid #ffffff;box-shadow:0 4px 12px rgba(15,23,42,.2)"></div>',
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                data.competitors.forEach((comp) => {
                    const marker = L.marker([comp.lat, comp.lng], { icon: competitorIcon }).addTo(markerLayer);
                    marker.bindPopup(`
                        <div style="padding:8px;min-width:200px">
                            <div style="font-weight:800;color:#3b82f6">${formatDistance(comp.distance_m)}</div>
                            <div style="margin-top:4px;font-weight:700;color:#0f172a">${comp.title}</div>
                            <div style="margin-top:4px;font-weight:600;color:#4338ca">${formatCurrency(comp.price)}</div>
                            <div style="margin-top:4px;font-size:12px;color:#64748b">${comp.land_area} m² • ${comp.status}</div>
                            <a href="/properties/${comp.id}" target="_blank" style="margin-top:8px;display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:8px;background:#4f46e5;color:#fff;font-weight:600;text-decoration:none;font-size:12px;width:100%">Lihat Detail</a>
                        </div>
                    `);
                });

                // Fit bounds to show all markers
                const bounds = L.latLngBounds([
                    [data.property.lat, data.property.lng],
                    ...data.competitors.map(c => [c.lat, c.lng])
                ]);
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            function renderTable(competitors) {
                if (competitors.length === 0) {
                    competitorTable.innerHTML = '<tr><td colspan="7" class="py-4 text-center text-slate-500">Tidak ada kompetitor dalam radius ini</td></tr>';
                    return;
                }

                competitorTable.innerHTML = competitors.map((comp) => {
                    const statusClass = comp.status === 'Terjual' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-100 text-emerald-700';
                    return `
                        <tr class="hover:bg-slate-50">
                            <td class="py-3">
                                <div class="font-semibold text-slate-900">${comp.title}</div>
                                <div class="text-xs text-slate-500">${comp.district_name || 'Samarinda'}</div>
                            </td>
                            <td class="py-3 text-slate-700">${formatDistance(comp.distance_m)}</td>
                            <td class="py-3 font-semibold text-brand-accent">${formatCurrency(comp.price)}</td>
                            <td class="py-3 text-slate-700">${comp.land_area} m²</td>
                            <td class="py-3 text-slate-700">Rp ${Math.round(comp.price_per_sqm).toLocaleString('id-ID')}</td>
                            <td class="py-3">
                                <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold ${statusClass}">${comp.status}</span>
                            </td>
                            <td class="py-3">
                                <a href="/properties/${comp.id}" target="_blank" class="text-sm font-semibold text-brand-primary hover:text-brand-primary-hover">Lihat</a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        </script>
    @endpush
</x-layouts.seller>
