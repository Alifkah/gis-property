<x-layouts.admin>
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Dashboard Admin</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data properti, fasilitas, dan zona rawan banjir di Samarinda.</p>
        </div>
        <div class="flex items-center gap-2 print:hidden">
            <a href="{{ route('admin.listings.export') }}" class="btn btn-outline flex items-center gap-2">
                <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Ekspor Excel (CSV)</span>
            </a>
            <button onclick="window.print()" class="btn btn-primary flex items-center gap-2">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Cetak PDF</span>
            </button>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Listing</div>
                <div class="grid size-9 place-items-center rounded-xl bg-indigo-100">
                    <svg class="size-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($totalProperties) }}</div>
            <div class="mt-1 flex gap-2 text-xs font-semibold text-slate-500">
                <span class="text-emerald-600">{{ $availableProperties }} tersedia</span>
                <span>·</span>
                <span>{{ $soldProperties }} terjual</span>
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Total Penjual</div>
                <div class="grid size-9 place-items-center rounded-xl bg-violet-100">
                    <svg class="size-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($totalSellers) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500">Pengguna terdaftar</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Fasilitas (POI)</div>
                <div class="grid size-9 place-items-center rounded-xl bg-amber-100">
                    <svg class="size-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($totalAmenities) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500">Titik fasilitas publik</div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400">Zona Banjir</div>
                <div class="grid size-9 place-items-center rounded-xl bg-rose-100">
                    <svg class="size-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-3xl font-extrabold text-slate-900">{{ number_format($totalFloodZones) }}</div>
            <div class="mt-1 text-xs font-semibold text-slate-500">Area rawan genangan</div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 grid gap-3 sm:grid-cols-3">
        <a href="{{ route('admin.amenities.create') }}" class="card flex items-center gap-3 p-4 transition hover:shadow-md">
            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-amber-100">
                <svg class="size-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-extrabold text-slate-900">Tambah Fasilitas</div>
                <div class="text-xs font-semibold text-slate-500">Tandai POI baru di peta</div>
            </div>
        </a>
        <a href="{{ route('admin.flood-zones.create') }}" class="card flex items-center gap-3 p-4 transition hover:shadow-md">
            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-rose-100">
                <svg class="size-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-extrabold text-slate-900">Tambah Zona Banjir</div>
                <div class="text-xs font-semibold text-slate-500">Gambar polygon kawasan baru</div>
            </div>
        </a>
        <a href="{{ route('admin.listings.index') }}" class="card flex items-center gap-3 p-4 transition hover:shadow-md">
            <div class="grid size-10 shrink-0 place-items-center rounded-xl bg-indigo-100">
                <svg class="size-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-extrabold text-slate-900">Kelola Listing</div>
                <div class="text-xs font-semibold text-slate-500">Moderasi semua iklan</div>
            </div>
        </a>
    </div>

    {{-- Charts Section --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-5">
            <div class="text-sm font-extrabold text-slate-900 mb-4">Sebaran Properti per Kecamatan</div>
            <div id="districtChart" style="min-height: 280px;"></div>
        </div>
        <div class="card p-5">
            <div class="text-sm font-extrabold text-slate-900 mb-4">Persentase Risiko Genangan Banjir</div>
            <div id="floodChart" style="min-height: 280px;"></div>
        </div>
    </div>

    <div class="mt-6 card p-5">
        <div class="text-sm font-extrabold text-slate-900 mb-4">Tren Rata-rata Harga Properti (per m²)</div>
        <div id="priceTrendChart" style="min-height: 280px;"></div>
    </div>

    {{-- Listing Terbaru --}}
    <div class="mt-6 card p-6">
        <div class="flex items-center justify-between">
            <div class="text-sm font-extrabold text-slate-900">Listing Terbaru</div>
            <a href="{{ route('admin.listings.index') }}" class="text-xs font-semibold text-indigo-600 hover:underline">Lihat semua →</a>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Properti</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Penjual</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Harga</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Status</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($recentProperties as $property)
                        <tr class="group">
                            <td class="py-3">
                                <div class="flex items-center gap-3">
                                    <div class="grid size-8 shrink-0 place-items-center rounded-lg bg-indigo-100 text-xs font-extrabold text-indigo-700">
                                        {{ strtoupper(substr($property->type, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('properties.show', $property->id) }}" target="_blank"
                                           class="font-extrabold text-slate-900 hover:text-indigo-600 hover:underline">
                                            {{ $property->title }}
                                        </a>
                                        <div class="text-xs text-slate-400">{{ $property->district_name ?? 'Samarinda' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-slate-600">{{ $property->user?->name ?? '-' }}</td>
                            <td class="py-3 font-semibold text-indigo-700">Rp {{ number_format((float) $property->price, 0, ',', '.') }}</td>
                            <td class="py-3">
                                @if ($property->status === 'Terjual')
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Terjual</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                            <td class="py-3 text-xs text-slate-400">{{ $property->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // 1. District Bar Chart
            const districtData = @json($propertiesPerDistrict);
            const districtLabels = districtData.map(d => d.name);
            const districtTotals = districtData.map(d => d.total);

            const districtOptions = {
                series: [{
                    name: 'Jumlah Properti',
                    data: districtTotals
                }],
                chart: {
                    type: 'bar',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'Inter, system-ui, sans-serif'
                },
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        horizontal: true,
                        barHeight: '60%'
                    }
                },
                colors: ['#4f46e5'],
                dataLabels: { enabled: false },
                xaxis: {
                    categories: districtLabels,
                    labels: {
                        style: { colors: '#64748b', fontWeight: 600 }
                    }
                },
                yaxis: {
                    labels: {
                        style: { colors: '#64748b', fontWeight: 600 }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                }
            };
            new ApexCharts(document.querySelector("#districtChart"), districtOptions).render();

            // 2. Flood Risk Pie Chart
            const floodSafe = {{ $floodSafeCount }};
            const floodRisk = {{ $floodRiskCount }};

            const floodOptions = {
                series: [floodSafe, floodRisk],
                chart: {
                    type: 'donut',
                    height: 280,
                    fontFamily: 'Inter, system-ui, sans-serif'
                },
                labels: ['Bebas Banjir', 'Rawan Banjir'],
                colors: ['#10b981', '#f43f5e'],
                plotOptions: {
                    pie: {
                        donut: {
                           size: '70%',
                           labels: {
                               show: true,
                               total: {
                                   show: true,
                                   label: 'Total Listing',
                                   color: '#64748b',
                                   fontWeight: 700,
                                   formatter: function (w) {
                                       return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                   }
                               }
                           }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontWeight: 600,
                    labels: { colors: '#475569' }
                },
                dataLabels: { enabled: false }
            };
            new ApexCharts(document.querySelector("#floodChart"), floodOptions).render();

            // 3. Price Trend Line Chart
            const trendData = @json($priceTrend);
            const trendPeriods = trendData.map(t => t.period);
            const trendAverages = trendData.map(t => Math.round(t.avg_price_per_sqm));

            const trendOptions = {
                series: [{
                    name: 'Harga Rata-rata per m²',
                    data: trendAverages
                }],
                chart: {
                    type: 'line',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: 'Inter, system-ui, sans-serif',
                    zoom: { enabled: false }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                colors: ['#6366f1'],
                xaxis: {
                    categories: trendPeriods,
                    labels: {
                        style: { colors: '#64748b', fontWeight: 600 }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        },
                        style: { colors: '#64748b', fontWeight: 600 }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9'
                },
                markers: {
                    size: 4,
                    colors: ['#4f46e5'],
                    strokeColors: '#fff',
                    strokeWidth: 2
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(val) + ' / m²';
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#priceTrendChart"), trendOptions).render();
        });
    </script>
    @endpush
</x-layouts.admin>
