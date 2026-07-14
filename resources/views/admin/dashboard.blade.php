<x-layouts.admin>
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900">Dashboard Admin</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau statistik properti, kelola fasilitas publik, dan mitigasi area genangan Samarinda.</p>
        </div>
        <div class="flex items-center gap-2.5 print:hidden">
            <a href="{{ route('admin.listings.export') }}" class="btn btn-outline hover:border-slate-300">
                <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Card 1: Total Listing --}}
        <div class="card p-5 border-l-4 border-l-[#0F4C5C] hover:shadow-lg hover:shadow-[#0F4C5C]/5 hover:-translate-y-0.5 duration-300 transition bg-white">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Listing</div>
                <div class="grid size-10 place-items-center rounded-xl bg-brand-primary/5 text-brand-primary ring-1 ring-brand-primary/10">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalProperties) }}</div>
            <div class="mt-2.5 flex items-center gap-2 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full ring-1 ring-emerald-100/30">{{ $availableProperties }} Aktif</span>
                <span class="inline-flex items-center gap-1 text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full">{{ $soldProperties }} Terjual</span>
            </div>
        </div>

        {{-- Card 2: Total Penjual --}}
        <div class="card p-5 border-l-4 border-l-[#E36414] hover:shadow-lg hover:shadow-[#E36414]/5 hover:-translate-y-0.5 duration-300 transition bg-white">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Total Penjual</div>
                <div class="grid size-10 place-items-center rounded-xl bg-brand-accent/5 text-brand-accent ring-1 ring-brand-accent/10">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalSellers) }}</div>
            <div class="mt-2.5 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center bg-brand-accent/5 text-brand-accent px-2.5 py-0.5 rounded-full ring-1 ring-brand-accent/10">Pengguna Terdaftar</span>
            </div>
        </div>

        {{-- Card 3: Fasilitas POI --}}
        <div class="card p-5 border-l-4 border-l-amber-500 hover:shadow-lg hover:shadow-amber-500/5 hover:-translate-y-0.5 duration-300 transition bg-white">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Fasilitas (POI)</div>
                <div class="grid size-10 place-items-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100/50">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalAmenities) }}</div>
            <div class="mt-2.5 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center bg-amber-500/5 text-amber-700 px-2.5 py-0.5 rounded-full ring-1 ring-amber-500/10">Titik Fasilitas Publik</span>
            </div>
        </div>

        {{-- Card 4: Zona Banjir --}}
        <div class="card p-5 border-l-4 border-l-brand-warning hover:shadow-lg hover:shadow-brand-warning/5 hover:-translate-y-0.5 duration-300 transition bg-white">
            <div class="flex items-center justify-between">
                <div class="text-xs font-extrabold uppercase tracking-wider text-slate-400">Zona Banjir</div>
                <div class="grid size-10 place-items-center rounded-xl bg-brand-warning/5 text-brand-warning ring-1 ring-brand-warning/10">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4 text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalFloodZones) }}</div>
            <div class="mt-2.5 text-xs font-semibold text-slate-500">
                <span class="inline-flex items-center bg-brand-warning/5 text-brand-warning px-2.5 py-0.5 rounded-full ring-1 ring-brand-warning/10">Kawasan Rawan Genangan</span>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.amenities.create') }}" class="card flex items-center gap-4 p-4.5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-amber-200/60 group">
            <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-amber-50 text-amber-600 ring-1 ring-amber-100/50 group-hover:bg-amber-100 transition-colors">
                <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-900 group-hover:text-brand-primary transition-colors">Tambah Fasilitas</div>
                <div class="text-xs text-slate-500 mt-0.5">Tandai titik POI baru pada peta</div>
            </div>
        </a>
        <a href="{{ route('admin.flood-zones.create') }}" class="card flex items-center gap-4 p-4.5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-brand-warning/20 group">
            <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-warning/5 text-brand-warning ring-1 ring-brand-warning/10 group-hover:bg-brand-warning/10 transition-colors">
                <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-900 group-hover:text-brand-primary transition-colors">Tambah Zona Banjir</div>
                <div class="text-xs text-slate-500 mt-0.5">Gambar batas wilayah genangan baru</div>
            </div>
        </a>
        <a href="{{ route('admin.listings.index') }}" class="card flex items-center gap-4 p-4.5 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:border-brand-primary/20 group">
            <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-primary/5 text-brand-primary ring-1 ring-brand-primary/10 group-hover:bg-brand-primary/10 transition-colors">
                <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-900 group-hover:text-brand-primary transition-colors">Kelola Listing</div>
                <div class="text-xs text-slate-500 mt-0.5">Moderasi & filter seluruh properti</div>
            </div>
        </a>
    </div>

    {{-- Charts Section --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="card p-5.5">
            <div class="text-sm font-bold text-slate-900 mb-4">Sebaran Properti per Kecamatan</div>
            <div id="districtChart" style="min-height: 280px;"></div>
        </div>
        <div class="card p-5.5">
            <div class="text-sm font-bold text-slate-900 mb-4">Persentase Risiko Genangan Banjir</div>
            <div id="floodChart" style="min-height: 280px;"></div>
        </div>
    </div>

    <div class="mt-6 card p-5.5">
        <div class="text-sm font-bold text-slate-900 mb-4">Tren Rata-rata Harga Properti (per m²)</div>
        <div id="priceTrendChart" style="min-height: 280px;"></div>
    </div>

    {{-- Listing Terbaru --}}
    <div class="mt-6 card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 p-6 pb-4">
            <div>
                <div class="text-sm font-bold text-slate-900">Listing Terbaru</div>
                <div class="text-xs text-slate-500 mt-0.5">Properti yang baru saja diunggah oleh penjual.</div>
            </div>
            <a href="{{ route('admin.listings.index') }}" class="text-xs font-semibold text-brand-primary hover:text-brand-primary/80 flex items-center gap-1 group">
                <span>Lihat Semua</span>
                <span class="transition-transform group-hover:translate-x-0.5">→</span>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100">
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Properti</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Penjual</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/75">
                    @forelse ($recentProperties as $property)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4.5">
                                <div class="flex items-center gap-3">
                                    <div class="grid size-9.5 shrink-0 place-items-center rounded-xl bg-brand-primary/5 border border-brand-primary/10 text-xs font-extrabold text-brand-primary">
                                        {{ strtoupper(substr($property->type, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ route('properties.show', $property->id) }}" target="_blank"
                                           class="block font-bold text-slate-900 hover:text-brand-primary hover:underline truncate">
                                            {{ $property->title }}
                                        </a>
                                        <div class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $property->district_name ?? 'Samarinda' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4.5 text-slate-600 font-medium text-xs">{{ $property->user?->name ?? '-' }}</td>
                            <td class="px-6 py-4.5 font-bold text-brand-primary text-xs whitespace-nowrap">
                                Rp {{ number_format((float) $property->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4.5 whitespace-nowrap">
                                @if ($property->status === 'Terjual')
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs font-bold text-slate-600">Terjual</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-4.5 text-xs text-slate-400 font-medium whitespace-nowrap">{{ $property->created_at->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm font-semibold text-slate-400">
                                Belum ada data listing terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const defaultFont = "'Instrument Sans', 'Inter', system-ui, sans-serif";

            // 1. District Donut Chart
            const districtData = @json($propertiesPerDistrict);
            const districtLabels = districtData.map(d => d.name);
            const districtTotals = districtData.map(d => d.total);

            const districtOptions = {
                series: districtTotals,
                labels: districtLabels,
                chart: {
                    type: 'donut',
                    height: 280,
                    toolbar: { show: false },
                    fontFamily: defaultFont
                },
                colors: ['#0F4C5C', '#E36414', '#FB8B24', '#5F0F40', '#10B981', '#3B82F6', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B'],
                legend: {
                    position: 'bottom',
                    fontSize: '11px',
                    fontFamily: defaultFont,
                    fontWeight: 600,
                    labels: { colors: '#64748B' }
                },
                dataLabels: {
                    enabled: true,
                    style: {
                        fontSize: '10px',
                        fontWeight: 'bold',
                        fontFamily: defaultFont
                    },
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex] + " unit";
                    }
                },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '65%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '12px',
                                    fontWeight: 'bold',
                                    fontFamily: defaultFont,
                                    color: '#64748b'
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontWeight: 'extrabold',
                                    fontFamily: defaultFont,
                                    color: '#0f172a',
                                    formatter: function (val) {
                                        return val + " unit";
                                    }
                                },
                                total: {
                                    show: true,
                                    label: 'Total Properti',
                                    fontFamily: defaultFont,
                                    color: '#64748b',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + " unit";
                                    }
                                }
                            }
                        }
                    }
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
                    fontFamily: defaultFont
                },
                labels: ['Bebas Banjir', 'Rawan Banjir'],
                colors: ['#10b981', '#9A031E'],
                plotOptions: {
                    pie: {
                        donut: {
                           size: '72%',
                           labels: {
                               show: true,
                               total: {
                                   show: true,
                                   label: 'Total Listing',
                                   color: '#64748b',
                                   fontWeight: 600,
                                   fontSize: '12px',
                                   formatter: function (w) {
                                       return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                   }
                               },
                               value: {
                                   fontSize: '20px',
                                   fontWeight: 800,
                                   color: '#1e293b'
                               }
                           }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontWeight: 500,
                    fontSize: '12px',
                    labels: { colors: '#475569' },
                    markers: { radius: 6 }
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
                    fontFamily: defaultFont,
                    zoom: { enabled: false }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3.5
                },
                colors: ['#E36414'],
                xaxis: {
                    categories: trendPeriods,
                    labels: {
                        style: { colors: '#64748b', fontWeight: 500, fontSize: '11px' }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
                        },
                        style: { colors: '#64748b', fontWeight: 500, fontSize: '11px' }
                    }
                },
                grid: {
                    borderColor: '#f1f5f9',
                    strokeDashArray: 3
                },
                markers: {
                    size: 5,
                    colors: ['#E36414'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: { size: 7 }
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
