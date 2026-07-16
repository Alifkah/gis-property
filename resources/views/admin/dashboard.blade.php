<x-layouts.admin>
    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Dashboard Admin</h1>
            <p class="text-xs font-semibold text-slate-500 mt-1.5">Pantau statistik properti, kelola fasilitas publik, dan mitigasi area genangan Samarinda.</p>
        </div>
        <div class="flex items-center gap-2.5 print:hidden">
            <a href="{{ route('admin.listings.export') }}" class="btn btn-outline text-xs font-bold flex items-center gap-1.5 py-2.5 shadow-3xs cursor-pointer">
                <i class="ti ti-download text-emerald-600 text-sm"></i>
                <span>Ekspor CSV</span>
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Card 1: Total Listing --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:border-brand-primary/10 transition-all flex items-center justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="grid size-12 place-items-center rounded-xl bg-brand-primary/10 text-brand-primary shrink-0 shadow-3xs">
                    <i class="ti ti-building text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-none">Total Properti</div>
                    <div class="text-3xl font-extrabold text-slate-950 mt-2 tracking-tight leading-none">{{ number_format($totalProperties) }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-[9px] font-bold">
                        <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $availableProperties }} Aktif</span>
                        <span class="text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $soldProperties }} Terjual</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Penjual --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:border-brand-primary/10 transition-all flex items-center justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="grid size-12 place-items-center rounded-xl bg-brand-accent/10 text-brand-accent shrink-0 shadow-3xs">
                    <i class="ti ti-users-group text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-none">Total Penjual</div>
                    <div class="text-3xl font-extrabold text-slate-950 mt-2 tracking-tight leading-none">{{ number_format($totalSellers) }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-[9px] font-bold">
                        <span class="text-brand-accent bg-brand-accent/10 px-2 py-0.5 rounded-full uppercase tracking-wider">Mitra Aktif</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Fasilitas POI --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:border-brand-primary/10 transition-all flex items-center justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="grid size-12 place-items-center rounded-xl bg-amber-500/10 text-amber-600 shrink-0 shadow-3xs">
                    <i class="ti ti-map-pin text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-none">Fasilitas POI</div>
                    <div class="text-3xl font-extrabold text-slate-950 mt-2 tracking-tight leading-none">{{ number_format($totalAmenities) }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-[9px] font-bold">
                        <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Fasilitas Publik</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Zona Banjir --}}
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/50 hover:border-brand-primary/10 transition-all flex items-center justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="grid size-12 place-items-center rounded-xl bg-rose-500/10 text-rose-600 shrink-0 shadow-3xs">
                    <i class="ti ti-alert-triangle text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-none">Zona Rawan</div>
                    <div class="text-3xl font-extrabold text-slate-950 mt-2 tracking-tight leading-none">{{ number_format($totalFloodZones) }}</div>
                    <div class="mt-2 flex items-center gap-1.5 text-[9px] font-bold">
                        <span class="text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full uppercase tracking-wider">Genangan Air</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <a href="{{ route('admin.amenities.create') }}" class="bg-white rounded-2xl p-5 border border-slate-200/50 hover:shadow-md hover:-translate-y-0.5 transition duration-300 flex items-center gap-4 group">
            <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-amber-500/10 text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition shadow-3xs">
                <i class="ti ti-plus text-lg"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-900 group-hover:text-brand-primary transition-colors">Tambah Fasilitas</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Tandai titik POI baru pada peta</div>
            </div>
        </a>
        <a href="{{ route('admin.flood-zones.create') }}" class="bg-white rounded-2xl p-5 border border-slate-200/50 hover:shadow-md hover:-translate-y-0.5 transition duration-300 flex items-center gap-4 group">
            <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-rose-500/10 text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition shadow-3xs">
                <i class="ti ti-plus text-lg"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-900 group-hover:text-brand-primary transition-colors">Tambah Zona Banjir</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Gambar batas wilayah genangan baru</div>
            </div>
        </a>
        <a href="{{ route('admin.listings.index') }}" class="bg-white rounded-2xl p-5 border border-slate-200/50 hover:shadow-md hover:-translate-y-0.5 transition duration-300 flex items-center gap-4 group">
            <div class="grid size-11 shrink-0 place-items-center rounded-xl bg-brand-primary/10 text-brand-primary group-hover:bg-brand-primary group-hover:text-white transition shadow-3xs">
                <i class="ti ti-edit text-lg"></i>
            </div>
            <div>
                <div class="text-sm font-bold text-slate-900 group-hover:text-brand-primary transition-colors">Kelola Listing</div>
                <div class="text-[11px] font-semibold text-slate-500 mt-0.5">Moderasi & filter seluruh properti</div>
            </div>
        </a>
    </div>

    {{-- Charts Section --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-2xl p-6 border border-slate-200/50 shadow-sm">
            <div class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider text-[10px] text-slate-400">Sebaran Properti per Kecamatan</div>
            <div id="districtChart" style="min-height: 280px;"></div>
        </div>
        <div class="bg-white rounded-2xl p-6 border border-slate-200/50 shadow-sm">
            <div class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider text-[10px] text-slate-400">Persentase Risiko Genangan Banjir</div>
            <div id="floodChart" style="min-height: 280px;"></div>
        </div>
    </div>

    <div class="mt-6 bg-white rounded-2xl p-6 border border-slate-200/50 shadow-sm">
        <div class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider text-[10px] text-slate-400">Tren Rata-rata Harga Properti (per m²)</div>
        <div id="priceTrendChart" style="min-height: 280px;"></div>
    </div>

    {{-- Listing Terbaru --}}
    <div class="mt-6 overflow-hidden bg-white rounded-2xl border border-slate-200/50 shadow-sm">
        <div class="flex flex-wrap items-center justify-between border-b border-slate-100 p-6 pb-4 gap-4">
            <div>
                <div class="text-sm font-bold text-slate-900 uppercase tracking-wider text-[10px] text-slate-400">Listing Terbaru</div>
                <p class="text-xs text-slate-500 mt-1 font-semibold">Properti yang baru saja diunggah oleh mitra penjual.</p>
            </div>
            <a href="{{ route('admin.listings.index') }}" class="text-xs font-bold text-brand-primary hover:text-brand-primary/80 flex items-center gap-1 group">
                <span>Lihat Semua</span>
                <span class="transition-transform group-hover:translate-x-0.5">→</span>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Properti</th>
                        <th class="px-6 py-4">Penjual</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-right">Harga</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentProperties as $property)
                        <tr class="hover:bg-slate-50/50 transition duration-150">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    @if ($property->images->isNotEmpty())
                                        <img src="{{ Storage::disk('public')->url($property->images->first()->path) }}" alt="" class="size-11 object-cover rounded-xl border border-slate-100 shrink-0" />
                                    @else
                                        <div class="size-11 grid place-items-center rounded-xl bg-slate-50 border border-slate-100 text-slate-300 shrink-0">
                                            <i class="ti ti-photo-off text-base"></i>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('properties.show', $property->id) }}" target="_blank" class="truncate text-xs font-bold text-slate-900 hover:text-brand-primary hover:underline block leading-snug">
                                            {{ $property->title }}
                                        </a>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mt-1 block">
                                            {{ $property->type }} · {{ $property->district_name ?? 'Samarinda' }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="font-bold text-slate-700">{{ $property->user?->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-semibold mt-0.5">{{ $property->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-3.5">
                                @if ($property->status === 'Terjual')
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[9px] font-bold text-slate-600 border border-slate-200 uppercase tracking-wider">Terjual</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-100 uppercase tracking-wider">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-slate-500 font-semibold whitespace-nowrap">{{ $property->created_at->diffForHumans() }}</td>
                            <td class="px-6 py-3.5 text-right font-extrabold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format((float) $property->price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-semibold bg-white">
                                <i class="ti ti-info-circle text-2xl text-slate-300 mb-2 block"></i>
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
                const defaultFont = "'Geist', 'Inter', system-ui, sans-serif";

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
                                size: '60%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '11px',
                                        fontWeight: 'bold',
                                        fontFamily: defaultFont,
                                        color: '#64748b'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '14px',
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
                                size: '65%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'Total Listing',
                                        color: '#64748b',
                                        fontSize: '11px',
                                        fontWeight: 500,
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    },
                                    value: {
                                        fontSize: '18px',
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
                        fontSize: '11px',
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
                        labels: { style: { colors: '#64748b', fontWeight: 500, fontSize: '11px' } }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(val);
                            },
                            style: { colors: '#64748b', fontWeight: 500, fontSize: '11px' }
                        }
                    },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
                    markers: {
                        size: 4,
                        colors: ['#E36414'],
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
