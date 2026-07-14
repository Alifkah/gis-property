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
                    </div>
                </div>

                <div class="card p-6">
                    <div class="text-sm font-extrabold text-slate-900">Statistik Kompetitor</div>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        {{-- Total Kompetitor --}}
                        <div class="rounded-2xl bg-brand-primary/5 p-5 ring-1 ring-brand-primary/10 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Kompetitor</div>
                                <div id="statTotal" class="mt-2 text-2xl font-extrabold text-brand-primary">-</div>
                            </div>
                            <div class="grid size-10 place-items-center rounded-xl bg-brand-primary/10 text-brand-primary shrink-0">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Harga Rata-rata --}}
                        <div class="rounded-2xl bg-emerald-50 p-5 ring-1 ring-emerald-100/50 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Harga Rata-rata</div>
                                <div id="statAvgPrice" class="mt-2 text-2xl font-extrabold text-emerald-950">-</div>
                            </div>
                            <div class="grid size-10 place-items-center rounded-xl bg-emerald-500/10 text-emerald-700 shrink-0">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Harga per m² --}}
                        <div class="rounded-2xl bg-rose-50 p-5 ring-1 ring-rose-100/50 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500">Harga per m²</div>
                                <div id="statPricePerSqm" class="mt-2 text-[17px] sm:text-xl lg:text-[15px] xl:text-xl font-extrabold text-rose-950">-</div>
                            </div>
                            <div class="grid size-10 place-items-center rounded-xl bg-rose-500/10 text-rose-700 shrink-0">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Posisi Harga --}}
                        <div class="rounded-2xl bg-blue-50 p-5 ring-1 ring-blue-100/50 flex items-center justify-between transition-all duration-300" id="pricePositionCard">
                            <div>
                                <div class="text-xs font-bold uppercase tracking-wider text-slate-500" id="pricePositionLabel">Posisi Harga Anda</div>
                                <div id="statPosition" class="mt-2 text-xl font-black text-blue-950">-</div>
                            </div>
                            <div class="grid size-10 place-items-center rounded-xl bg-blue-500/10 text-blue-700 shrink-0" id="pricePositionIcon">
                                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Price Position Visual Meter -->
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <div class="text-xs font-extrabold text-slate-900 mb-3 flex items-center gap-1.5">
                            <svg class="size-4 text-brand-accent" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                            </svg>
                            <span>Garis Ukur Perbandingan Harga</span>
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-bold text-slate-500 mb-2">
                            <span>Terendah: <span id="meterMin" class="text-slate-800 font-extrabold">-</span></span>
                            <span>Rata-rata: <span id="meterAvg" class="text-slate-800 font-extrabold">-</span></span>
                            <span>Tertinggi: <span id="meterMax" class="text-slate-800 font-extrabold">-</span></span>
                        </div>
                        <div class="relative w-full h-3 bg-gradient-to-r from-emerald-400 via-yellow-400 to-rose-500 rounded-full ring-1 ring-slate-300/30 overflow-visible">
                            <!-- Average Line Marker -->
                            <div id="meterAvgLine" class="absolute top-0 bottom-0 w-0.5 bg-slate-900/60 z-10" style="left: 50%;"></div>
                            <!-- User Price Marker -->
                            <div id="meterUserMarker" class="absolute -top-1 w-5 h-5 rounded-full border-4 border-white shadow-lg z-20 transition-all duration-500" style="left: 0%; background-color: #3b82f6;"></div>
                        </div>
                        <div class="mt-3 text-center text-xs font-semibold text-slate-600 leading-relaxed" id="meterLabel"></div>
                    </div>

                    <div class="mt-6 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-extrabold text-slate-900">Properti Anda</div>
                        <div id="myPropertyInfo" class="mt-2 text-sm text-slate-700"></div>
                    </div>
                </div>

                <!-- Split Map & Scatter Plot Layout -->
                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Map Card --}}
                    <div class="card p-6 flex flex-col justify-between">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <div class="text-sm font-extrabold text-slate-900">Peta Buffer Zone</div>
                                <div class="mt-1 text-sm text-slate-600">Visualisasi properti kompetitor dalam radius yang dipilih.</div>
                            </div>
                            <label class="flex items-center gap-2 print:hidden cursor-pointer select-none">
                                <input id="toggleDarkMode" type="checkbox" class="size-4 accent-brand-primary rounded" />
                                <span class="text-xs font-bold text-slate-700">Mode Gelap</span>
                            </label>
                        </div>
                        <div id="map" class="relative z-0 mt-4 h-[380px] w-full rounded-2xl overflow-hidden ring-1 ring-slate-200"></div>
                    </div>

                    {{-- Scatter Plot Card --}}
                    <div class="card p-6 flex flex-col justify-between">
                        <div>
                            <div class="text-sm font-extrabold text-slate-900">Grafik Harga vs Jarak</div>
                            <div class="mt-1 text-sm text-slate-600">Perbandingan harga properti kompetitor terhadap jaraknya dari properti Anda.</div>
                        </div>
                        <div id="scatterChart" class="mt-4" style="min-height: 380px; width: 100%;"></div>
                    </div>
                </div>

                <div class="card p-6">
                    <div class="text-sm font-extrabold text-slate-900">Daftar Kompetitor</div>
                    <div class="mt-4 overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-4 py-3">Properti</th>
                                    <th class="px-4 py-3">Jarak</th>
                                    <th class="px-4 py-3">Harga</th>
                                    <th class="px-4 py-3">Luas Tanah</th>
                                    <th class="px-4 py-3">Harga/m²</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="competitorTable" class="divide-y divide-slate-100">
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400 font-semibold">Belum ada data analisis. Silakan pilih properti Anda di atas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
        </section>

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
                statTotal.textContent = data.statistics.total_competitors;
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
                let cardBg = 'bg-blue-50 ring-blue-100/50';
                let labelColor = 'text-blue-600';
                let valueColor = 'text-blue-950';
                let iconColor = 'bg-blue-500/10 text-blue-700';
                let markerColor = '#3b82f6';
                let labelText = 'Harga Anda kompetitif dan berada dalam kisaran rata-rata pasar.';

                if (pos === 'di bawah rata-rata') {
                    cardBg = 'bg-emerald-50 ring-emerald-100/50';
                    labelColor = 'text-emerald-600';
                    valueColor = 'text-emerald-950';
                    iconColor = 'bg-emerald-500/10 text-emerald-700';
                    markerColor = '#10b981';
                    labelText = 'Harga Anda di bawah rata-rata pasar (potensi laku lebih cepat).';
                } else if (pos === 'di atas rata-rata') {
                    cardBg = 'bg-rose-50 ring-rose-100/50';
                    labelColor = 'text-rose-600';
                    valueColor = 'text-rose-950';
                    iconColor = 'bg-rose-500/10 text-rose-700';
                    markerColor = '#f43f5e';
                    labelText = 'Harga Anda di atas rata-rata pasar (disarankan optimasi harga).';
                }

                if (pricePositionCard) {
                    pricePositionCard.className = `rounded-2xl p-5 ring-1 flex items-center justify-between transition-all duration-300 ${cardBg}`;
                }
                if (pricePositionLabel) {
                    pricePositionLabel.className = `text-xs font-semibold ${labelColor}`;
                }
                if (pricePositionIcon) {
                    pricePositionIcon.className = `grid size-10 place-items-center rounded-xl shrink-0 ${iconColor}`;
                }
                if (statPosition) {
                    statPosition.className = `mt-2 text-[17px] sm:text-xl xl:text-2xl font-black ${valueColor}`;
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
                    <div class="grid gap-2">
                        <div><span class="font-semibold">Judul:</span> ${prop.title}</div>
                        <div><span class="font-semibold">Harga:</span> ${formatCurrency(prop.price)}</div>
                        <div><span class="font-semibold">Luas Tanah:</span> ${prop.land_area} m²</div>
                        <div><span class="font-semibold">Harga per m²:</span> Rp ${Math.round(prop.price_per_sqm).toLocaleString('id-ID')}/m²</div>
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

                // Format competitor data points: [distance_km, price]
                const competitorPoints = competitors.map(c => [
                    parseFloat((c.distance_m / 1000).toFixed(2)),
                    c.price
                ]);

                // Format user property point: [0, price]
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
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: true,
                            type: 'xy'
                        }
                    },
                    colors: ['#3b82f6', '#ef4444'], // Blue for competitors, Red for user's property
                    xaxis: {
                        tickAmount: 5,
                        labels: {
                            formatter: function (val) {
                                return parseFloat(val).toFixed(2) + ' km';
                            },
                            style: {
                                colors: '#64748b',
                                fontSize: '11px',
                                fontFamily: "'Instrument Sans', 'Inter', sans-serif",
                                fontWeight: 500
                            }
                        },
                        title: {
                            text: 'Jarak (km)',
                            style: {
                                color: '#0f172a',
                                fontSize: '12px',
                                fontFamily: "'Instrument Sans', 'Inter', sans-serif",
                                fontWeight: 700
                            }
                        }
                    },
                    yaxis: {
                        tickAmount: 5,
                        labels: {
                            formatter: function (val) {
                                return formatCurrency(val);
                            },
                            style: {
                                colors: '#64748b',
                                fontSize: '11px',
                                fontFamily: "'Instrument Sans', 'Inter', sans-serif",
                                fontWeight: 500
                            }
                        },
                        title: {
                            text: 'Harga',
                            style: {
                                color: '#0f172a',
                                fontSize: '12px',
                                fontFamily: "'Instrument Sans', 'Inter', sans-serif",
                                fontWeight: 700
                            }
                        }
                    },
                    grid: {
                        borderColor: '#e2e8f0',
                        strokeDashArray: 4,
                        xaxis: {
                            lines: {
                                show: true
                            }
                        },
                        yaxis: {
                            lines: {
                                show: true
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '12px',
                        fontFamily: "'Instrument Sans', 'Inter', sans-serif",
                        fontWeight: 600,
                        labels: {
                            colors: '#334155'
                        },
                        markers: {
                            radius: 12
                        }
                    },
                    markers: {
                        size: [6, 10], // size for competitor vs my property
                        strokeWidth: 2,
                        strokeColors: '#ffffff',
                        hover: {
                            sizeOffset: 3
                        }
                    },
                    tooltip: {
                        custom: function({ series, seriesIndex, dataPointIndex, w }) {
                            const data = w.config.series[seriesIndex].data[dataPointIndex];
                            const distance = data[0];
                            const price = data[1];
                            
                            if (seriesIndex === 1) { // Properti Anda
                                return `
                                    <div class="p-3 bg-white border border-slate-200 rounded-xl shadow-md font-sans">
                                        <div class="font-extrabold text-rose-600 mb-1">Properti Anda</div>
                                        <div class="text-xs font-semibold text-slate-800">Harga: ${formatCurrency(price)}</div>
                                        <div class="text-xs font-semibold text-slate-500">Jarak: ${distance} km</div>
                                    </div>
                                `;
                            } else { // Kompetitor
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
                    competitorTable.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-slate-500 font-semibold">Tidak ada kompetitor dalam radius ini</td></tr>';
                    return;
                }

                competitorTable.innerHTML = competitors.map((comp) => {
                    const statusClass = comp.status === 'Terjual' ? 'bg-slate-100 text-slate-700' : 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100';
                    return `
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900">${comp.title}</div>
                                <div class="text-xs text-slate-400 font-medium mt-0.5">Kecamatan ${comp.district_name || 'Samarinda'}</div>
                            </td>
                            <td class="px-4 py-3.5 text-slate-600 font-medium">${formatDistance(comp.distance_m)}</td>
                            <td class="px-4 py-3.5 font-bold text-brand-accent">${formatCurrency(comp.price)}</td>
                            <td class="px-4 py-3.5 text-slate-600 font-medium">${comp.land_area} m²</td>
                            <td class="px-4 py-3.5 text-slate-600 font-medium whitespace-nowrap">Rp ${Math.round(comp.price_per_sqm).toLocaleString('id-ID')}/m²</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold ${statusClass}">${comp.status}</span>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <a href="/properties/${comp.id}" target="_blank" class="btn btn-outline min-h-0 h-8 py-1 px-3 text-xs inline-flex items-center">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    `;
                }).join('');
            }
        </script>
    @endpush
</x-layouts.seller>
