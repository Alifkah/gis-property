<x-layouts.seller>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .pulse-marker-core-competitor {
                width: 14px;
                height: 14px;
                border-radius: 50%;
                background: #3b82f6;
                border: 2.5px solid #ffffff;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }
        </style>
    @endpush

    <div class="space-y-6">
        {{-- Config Section --}}
        <section class="card p-6 bg-white border border-slate-200/50 shadow-sm">
            <div>
                <h1 class="text-lg font-extrabold text-slate-900 font-display flex items-center gap-2">
                    <i class="ti ti-chart-bar text-brand-primary"></i>
                    <span>Analisis Kompetitor Spasial</span>
                </h1>
                <p class="mt-1 text-xs font-semibold text-slate-500">Bandingkan harga penawaran properti Anda terhadap listing kompetitor lain dalam radius tertentu.</p>
            </div>

            <div class="mt-6 flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="w-full sm:flex-1">
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Pilih Properti Anda</label>
                    <select id="propertySelect" class="select bg-slate-50 font-semibold">
                        <option value="">-- Pilih Properti --</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->title }} ({{ $property->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-60">
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Radius Pencarian</label>
                    <select id="radiusSelect" class="select bg-slate-50 font-semibold">
                        <option value="500">500 meter</option>
                        <option value="1000" selected>1 kilometer</option>
                        <option value="2000">2 kilometer</option>
                        <option value="3000">3 kilometer</option>
                        <option value="5000">5 kilometer</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto shrink-0">
                    <button id="analyzeBtn" class="btn btn-accent w-full py-2.5 px-6 font-bold shadow-xs cursor-pointer border-0 flex items-center justify-center gap-1.5" disabled>
                        <i class="ti ti-activity text-base"></i>
                        <span>Analisis Kompetitor</span>
                    </button>
                </div>
            </div>
        </section>

        {{-- Results Area --}}
        <div id="resultsSection" class="hidden space-y-6">
            <div class="flex items-center justify-between gap-4 print:hidden">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hasil Analisis Geospasial</div>
                <a id="exportCsvBtn" href="#" class="btn btn-outline text-xs font-bold py-2.5 px-4 flex items-center gap-1.5">
                    <i class="ti ti-download text-emerald-600 text-sm"></i>
                    <span>Ekspor CSV</span>
                </a>
            </div>

            {{-- Stats Row (4-col) --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Total Kompetitor --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kompetitor</div>
                        <div id="statTotal" class="mt-2 text-2xl font-extrabold text-brand-primary leading-none">-</div>
                        <p class="text-[9px] font-bold text-slate-400 mt-1.5">Unit dalam radius</p>
                    </div>
                    <div class="grid size-9 place-items-center rounded-xl bg-brand-primary/5 text-brand-primary">
                        <i class="ti ti-users-group text-lg"></i>
                    </div>
                </div>

                {{-- Harga Rata-rata --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Harga Rata-rata</div>
                        <div id="statAvgPrice" class="mt-2 text-2xl font-extrabold text-emerald-600 leading-none">-</div>
                        <p class="text-[9px] font-bold text-slate-400 mt-1.5">Harga rata-rata unit</p>
                    </div>
                    <div class="grid size-9 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="ti ti-cash text-lg"></i>
                    </div>
                </div>

                {{-- Harga per m² --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rata-rata Harga/m²</div>
                        <div id="statPricePerSqm" class="mt-2 text-2xl font-extrabold text-rose-600 leading-none">-</div>
                        <p class="text-[9px] font-bold text-slate-400 mt-1.5">Berdasarkan luas tanah</p>
                    </div>
                    <div class="grid size-9 place-items-center rounded-xl bg-rose-50 text-rose-600">
                        <i class="ti ti-maximize text-lg"></i>
                    </div>
                </div>

                {{-- Posisi Harga --}}
                <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm flex items-center justify-between transition-all duration-300" id="pricePositionCard">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-500" id="pricePositionLabel">Posisi Harga Anda</div>
                        <div id="statPosition" class="mt-2 text-xl font-extrabold text-blue-600 leading-none">-</div>
                        <p class="text-[9px] font-bold text-slate-400 mt-1.5">Posisi relatif pasar</p>
                    </div>
                    <div class="grid size-9 place-items-center rounded-xl bg-blue-50 text-blue-600" id="pricePositionIcon">
                        <i class="ti ti-scale text-lg"></i>
                    </div>
                </div>
            </div>

            {{-- Price Position Visual Meter Card --}}
            <div class="card p-6 bg-white border border-slate-200/50 shadow-sm space-y-4">
                <div class="text-xs font-bold text-slate-900 flex items-center gap-1.5 leading-none">
                    <i class="ti ti-chart-bar text-brand-accent text-base"></i>
                    <span>Visualisasi Tolok Ukur Harga Properti</span>
                </div>
                
                {{-- Bounds labels above --}}
                <div class="flex items-center justify-between text-[9px] font-bold text-slate-400 uppercase tracking-wider">
                    <span>Terendah: <span id="meterMin" class="text-slate-800 font-extrabold">-</span></span>
                    <span>Rata-rata: <span id="meterAvg" class="text-brand-primary font-extrabold">-</span></span>
                    <span>Tertinggi: <span id="meterMax" class="text-slate-800 font-extrabold">-</span></span>
                </div>

                {{-- Meter Gradient Bar --}}
                <div class="relative w-full h-2.5 bg-gradient-to-r from-emerald-400 via-yellow-400 to-rose-500 rounded-full border border-slate-100 overflow-visible">
                    {{-- Average vertical line --}}
                    <div id="meterAvgLine" class="absolute top-0 bottom-0 w-0.5 bg-slate-900/60 z-10" style="left: 50%;"></div>
                    {{-- User price marker --}}
                    <div id="meterUserMarker" class="absolute -top-1.5 w-[22px] h-[22px] rounded-full border-4 border-white shadow-lg z-20 transition-all duration-500" style="left: 0%; background-color: #3b82f6;"></div>
                </div>

                {{-- Instruction label below --}}
                <div class="text-center text-xs font-semibold text-slate-600 leading-relaxed pt-1" id="meterLabel"></div>

                <div class="rounded-2xl bg-slate-50 border border-slate-200/50 p-4">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Spesifikasi Properti Anda</div>
                    <div id="myPropertyInfo" class="mt-2.5 text-xs font-semibold text-slate-700"></div>
                </div>
            </div>

            {{-- Split Map & Scatter Plot Layout (2-col) --}}
            <div class="grid gap-6 lg:grid-cols-2">
                {{-- Leaflet buffer map --}}
                <div class="card p-6 bg-white border border-slate-200/50 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5 leading-none">
                                <i class="ti ti-map-2 text-brand-primary text-base"></i>
                                <span>Peta Zona Analisis</span>
                            </h3>
                        </div>
                        <label class="flex items-center gap-2 print:hidden cursor-pointer select-none">
                            <input id="toggleDarkMode" type="checkbox" class="size-4 accent-brand-primary rounded" />
                            <span class="text-xs font-bold text-slate-500">Mode Gelap</span>
                        </label>
                    </div>
                    <div id="map" class="relative z-0 mt-4 h-[380px] w-full rounded-2xl overflow-hidden border border-slate-200/60"></div>
                </div>

                {{-- Scatter plot chart --}}
                <div class="card p-6 bg-white border border-slate-200/50 shadow-sm flex flex-col justify-between">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5 leading-none">
                            <i class="ti ti-chart-dots text-brand-primary text-base"></i>
                            <span>Sebaran Harga & Jarak</span>
                        </h3>
                    </div>
                    <div id="scatterChart" class="mt-4 w-full" style="min-height: 380px;"></div>
                </div>
            </div>

            {{-- Competitor table list --}}
            <div class="card p-6 bg-white border border-slate-200/50 shadow-sm">
                <div class="border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5 leading-none">
                        <i class="ti ti-list text-brand-primary text-base"></i>
                        <span>Daftar Properti Pesaing</span>
                    </h3>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                <th class="px-5 py-3.5">Properti</th>
                                <th class="px-5 py-3.5">Jarak Peta</th>
                                <th class="px-5 py-3.5">Luas Tanah</th>
                                <th class="px-5 py-3.5">Harga per m²</th>
                                <th class="px-5 py-3.5">Status</th>
                                <th class="px-5 py-3.5 text-right">Harga</th>
                                <th class="px-5 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="competitorTable" class="divide-y divide-slate-100">
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 font-semibold bg-white">
                                    <i class="ti ti-info-circle text-2xl text-slate-300 mb-2 block"></i>
                                    Belum ada data analisis. Silakan pilih properti Anda di atas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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
            let scatterChart = null;

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
                statTotal.textContent = data.statistics.total_competitors + ' Unit';
                statAvgPrice.textContent = formatCurrency(data.statistics.avg_price);
                statPricePerSqm.textContent = data.statistics.avg_price_per_sqm 
                    ? `Rp ${Math.round(data.statistics.avg_price_per_sqm).toLocaleString('id-ID')}/m²`
                    : '-';

                // Price position gauge and card styling
                const pricePositionCard = document.getElementById('pricePositionCard');
                const pricePositionLabel = document.getElementById('pricePositionLabel');
                const pricePositionIcon = document.getElementById('pricePositionIcon');
                const meterMin = document.getElementById('meterMin');
                const meterAvg = document.getElementById('meterAvg');
                const meterMax = document.getElementById('meterMax');
                const meterAvgLine = document.getElementById('meterAvgLine');
                const meterUserMarker = document.getElementById('meterUserMarker');
                const meterLabel = document.getElementById('meterLabel');

                const pos = data.statistics.price_position;
                let cardBg = 'bg-white border border-slate-200/50 shadow-sm';
                let labelColor = 'text-slate-500';
                let valueColor = 'text-blue-600';
                let iconColor = 'bg-blue-50 text-blue-600 border border-blue-100';
                let markerColor = '#3b82f6';
                let labelText = 'Harga properti Anda kompetitif dan berada dalam kisaran rata-rata pasar.';

                if (pos === 'di bawah rata-rata') {
                    valueColor = 'text-emerald-600';
                    iconColor = 'bg-emerald-50 text-emerald-600 border border-emerald-100';
                    markerColor = '#10b981';
                    labelText = 'Harga properti Anda di bawah rata-rata pasar. (Potensi laku lebih cepat)';
                } else if (pos === 'di atas rata-rata') {
                    valueColor = 'text-rose-600';
                    iconColor = 'bg-rose-50 text-rose-600 border border-rose-100';
                    markerColor = '#f43f5e';
                    labelText = 'Harga properti Anda di atas rata-rata pasar. (Disarankan optimasi harga kembali)';
                }

                if (pricePositionCard) {
                    pricePositionCard.className = `bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm flex items-center justify-between transition-all duration-300`;
                }
                if (pricePositionIcon) {
                    pricePositionIcon.className = `grid size-9 place-items-center rounded-xl shrink-0 ${iconColor}`;
                    pricePositionIcon.innerHTML = `<i class="ti ti-scale text-lg"></i>`;
                }
                if (statPosition) {
                    statPosition.className = `mt-2 text-2xl font-extrabold leading-none ${valueColor}`;
                    statPosition.textContent = pos.toUpperCase();
                }

                const myPrice = prop.price;
                const minPrice = data.statistics.min_price || 0;
                const avgPrice = data.statistics.avg_price || 0;
                const maxPrice = data.statistics.max_price || 0;

                const minVal = Math.min(minPrice, myPrice);
                const maxVal = Math.max(maxPrice, myPrice);
                const range = maxVal - minVal;

                let userPercent = 50;
                let avgPercent = 50;

                if (range > 0) {
                    userPercent = ((myPrice - minVal) / range) * 100;
                    avgPercent = ((avgPrice - minVal) / range) * 100;
                }

                if (meterMin) meterMin.textContent = formatCurrency(minVal);
                if (meterMax) meterMax.textContent = formatCurrency(maxVal);
                if (meterAvg) meterAvg.textContent = formatCurrency(avgPrice);

                if (meterAvgLine) meterAvgLine.style.left = `${avgPercent}%`;
                if (meterUserMarker) {
                    meterUserMarker.style.left = `${userPercent}%`;
                    meterUserMarker.style.backgroundColor = markerColor;
                }
                if (meterLabel) meterLabel.textContent = labelText;

                // My property info
                myPropertyInfo.innerHTML = `
                    <div class="grid gap-2 sm:grid-cols-4 font-semibold text-slate-600">
                        <div>Harga: <span class="text-slate-900 font-extrabold">${formatCurrency(prop.price)}</span></div>
                        <div>Tipe: <span class="text-slate-900 font-extrabold">${prop.type}</span></div>
                        <div>Luas Tanah: <span class="text-slate-900 font-extrabold">${prop.land_area} m²</span></div>
                        <div>Harga/m²: <span class="text-slate-900 font-extrabold">Rp ${Math.round(prop.price_per_sqm).toLocaleString('id-ID')}/m²</span></div>
                    </div>
                `;

                // Map
                initMap(data);

                // Scatter Plot
                renderScatterPlot(prop, data.competitors);

                // Table
                renderTable(data.competitors);

                // Scroll to results
                resultsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function renderScatterPlot(myProperty, competitors) {
                if (scatterChart) {
                    scatterChart.destroy();
                }

                const competitorPoints = competitors.map(c => [
                    parseFloat((c.distance_m / 1000).toFixed(2)),
                    c.price
                ]);

                const myPoint = [0, myProperty.price];

                const options = {
                    series: [
                        {
                            name: 'Kompetitor',
                            data: competitorPoints
                        },
                        {
                            name: 'Properti Anda',
                            data: [myPoint]
                        }
                    ],
                    chart: {
                        type: 'scatter',
                        height: 380,
                        toolbar: { show: false },
                        zoom: { enabled: true, type: 'xy' }
                    },
                    colors: ['#3b82f6', '#ef4444'],
                    xaxis: {
                        tickAmount: 5,
                        labels: {
                            formatter: function (val) {
                                return parseFloat(val).toFixed(2) + ' km';
                            },
                            style: { colors: '#64748b', fontSize: '11px', fontFamily: "'Geist', 'Inter', sans-serif", fontWeight: 500 }
                        },
                        title: {
                            text: 'Jarak (km)',
                            style: { color: '#0f172a', fontSize: '12px', fontFamily: "'Geist', 'Inter', sans-serif", fontWeight: 700 }
                        }
                    },
                    yaxis: {
                        tickAmount: 5,
                        labels: {
                            formatter: function (val) {
                                return formatCurrency(val);
                            },
                            style: { colors: '#64748b', fontSize: '11px', fontFamily: "'Geist', 'Inter', sans-serif", fontWeight: 500 }
                        },
                        title: {
                            text: 'Harga',
                            style: { color: '#0f172a', fontSize: '12px', fontFamily: "'Geist', 'Inter', sans-serif", fontWeight: 700 }
                        }
                    },
                    grid: {
                        borderColor: '#e2e8f0',
                        strokeDashArray: 4,
                        xaxis: { lines: { show: true } },
                        yaxis: { lines: { show: true } }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontFamily: "'Geist', 'Inter', sans-serif",
                        fontWeight: 600,
                        labels: { colors: '#334155' },
                        markers: { radius: 12 }
                    },
                    markers: {
                        size: [6, 10],
                        strokeWidth: 2,
                        strokeColors: '#ffffff',
                        hover: { sizeOffset: 3 }
                    },
                    tooltip: {
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const data = w.config.series[seriesIndex].data[dataPointIndex];
                            const distance = data[0];
                            const price = data[1];
                            
                            if (seriesIndex === 1) {
                                return `
                                    <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-md font-sans">
                                        <div class="font-extrabold text-rose-600 mb-1">Properti Anda</div>
                                        <div class="text-xs font-semibold text-slate-800">Harga: ${formatCurrency(price)}</div>
                                        <div class="text-xs font-semibold text-slate-500">Jarak: ${distance} km</div>
                                    </div>
                                `;
                            } else {
                                const comp = competitors[dataPointIndex];
                                return `
                                    <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-md font-sans">
                                        <div class="font-extrabold text-blue-600 mb-1">${comp.title}</div>
                                        <div class="text-xs font-semibold text-slate-800">Harga: ${formatCurrency(price)}</div>
                                        <div class="text-xs font-semibold text-slate-500">Jarak: ${distance} km</div>
                                        <div class="text-xs text-slate-500 mt-1">Luas Tanah: ${comp.land_area} m²</div>
                                    </div>
                                `;
                            }
                        }
                    }
                };

                scatterChart = new ApexCharts(document.querySelector("#scatterChart"), options);
                scatterChart.render();
            }

            function initMap(data) {
                if (map) {
                    map.remove();
                }

                map = L.map('map', { zoomControl: false, attributionControl: false }).setView([data.property.lat, data.property.lng], 14);
                L.control.zoom({ position: 'bottomright' }).addTo(map);

                const voyagerTiles = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd'
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
                    voyagerTiles.addTo(map);
                }

                if (toggleDark) {
                    const newToggle = toggleDark.cloneNode(true);
                    toggleDark.parentNode.replaceChild(newToggle, toggleDark);
                    newToggle.addEventListener('change', (e) => {
                        if (e.target.checked) {
                            map.removeLayer(voyagerTiles);
                            darkTiles.addTo(map);
                        } else {
                            map.removeLayer(darkTiles);
                            voyagerTiles.addTo(map);
                        }
                    });
                }

                markerLayer = L.layerGroup().addTo(map);

                // My property marker (red pulse)
                const myIcon = L.divIcon({
                    className: '',
                    html: `<div class="pulse-marker-icon">
                             <div class="pulse-marker-core" style="background:#ef4444"></div>
                             <div class="pulse-marker-ring" style="background:#ef4444"></div>
                           </div>`,
                    iconSize: [24, 24],
                    iconAnchor: [12, 12]
                });

                myMarker = L.marker([data.property.lat, data.property.lng], { icon: myIcon }).addTo(markerLayer);
                myMarker.bindPopup(`
                    <div style="padding:6px; font-family:'Geist','Inter',sans-serif">
                        <div style="font-weight:900;color:#ef4444;font-size:11px">Properti Anda</div>
                        <div style="margin-top:4px;font-weight:800;color:#0f172a;font-size:12px;line-height:1.4">${data.property.title}</div>
                        <div style="margin-top:4px;font-weight:900;color:#0F4C5C;font-size:12px">${formatCurrency(data.property.price)}</div>
                    </div>
                `);

                // Buffer circle
                bufferCircle = L.circle([data.property.lat, data.property.lng], {
                    radius: data.statistics.radius_m,
                    color: '#0F4C5C',
                    fillColor: '#0F4C5C',
                    fillOpacity: 0.05,
                    weight: 2,
                    dashArray: '5, 5'
                }).addTo(map);

                // Competitor markers (blue)
                const competitorIcon = L.divIcon({
                    className: '',
                    html: '<div class="pulse-marker-core-competitor"></div>',
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                data.competitors.forEach((comp) => {
                    const marker = L.marker([comp.lat, comp.lng], { icon: competitorIcon }).addTo(markerLayer);
                    marker.bindPopup(`
                        <div style="padding:6px;min-width:200px;font-family:'Geist','Inter',sans-serif">
                            <div style="font-weight:900;color:#3b82f6;font-size:11px">Jarak: ${formatDistance(comp.distance_m)}</div>
                            <div style="margin-top:4px;font-weight:800;color:#0f172a;font-size:12px;line-height:1.4">${comp.title}</div>
                            <div style="margin-top:4px;font-weight:900;color:#0F4C5C;font-size:12px">${formatCurrency(comp.price)}</div>
                            <div style="margin-top:4px;font-size:10px;color:#64748b;font-weight:700">${comp.land_area} m² · ${comp.status}</div>
                            <a href="/properties/${comp.slug}" target="_blank" style="margin-top:8px;display:inline-flex;align-items:center;justify-content:center;padding:7px 10px;border-radius:8px;background:#E36414;color:#fff;font-weight:800;text-decoration:none;font-size:10px;width:100%;box-shadow:0 4px 10px rgba(227,100,20,0.15)">Lihat Detail Properti</a>
                        </div>
                    `);
                });

                const bounds = L.latLngBounds([
                    [data.property.lat, data.property.lng],
                    ...data.competitors.map(c => [c.lat, c.lng])
                ]);
                map.fitBounds(bounds, { padding: [50, 50] });
            }

            function renderTable(competitors) {
                if (competitors.length === 0) {
                    competitorTable.innerHTML = '<tr><td colspan="7" class="px-5 py-8 text-center text-slate-400 font-semibold bg-white"><i class="ti ti-info-circle text-xl mb-1.5 block"></i>Tidak ada kompetitor dalam radius ini</td></tr>';
                    return;
                }

                competitorTable.innerHTML = competitors.map((comp) => {
                    const statusClass = comp.status === 'Terjual' ? 'bg-slate-100 text-slate-600 border border-slate-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-100';
                    return `
                        <tr class="group hover:bg-slate-50/50 transition duration-150">
                            <td class="px-5 py-3.5">
                                <div class="font-bold text-slate-900">${comp.title}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Kecamatan ${comp.district_name || 'Samarinda'}</div>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600 font-semibold">${formatDistance(comp.distance_m)}</td>
                            <td class="px-5 py-3.5 text-slate-600 font-semibold">${comp.land_area} m²</td>
                            <td class="px-5 py-3.5 text-slate-600 font-semibold whitespace-nowrap">Rp ${Math.round(comp.price_per_sqm).toLocaleString('id-ID')}/m²</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[9px] font-bold uppercase tracking-wider ${statusClass}">${comp.status}</span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-extrabold text-slate-900">${formatCurrency(comp.price)}</td>
                            <td class="px-5 py-3.5 text-right">
                                <a href="/properties/${comp.slug}" target="_blank" class="btn btn-outline py-1.5 px-3 text-[10px] inline-flex items-center gap-1">
                                    <span>Detail</span>
                                    <i class="ti ti-external-link"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        </script>
    @endpush
</x-layouts.seller>
