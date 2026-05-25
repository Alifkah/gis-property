<x-layouts.app>
    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="card p-4">
            <div class="px-2 pt-2 text-sm font-extrabold text-slate-900">Dashboard Penjual</div>
            <nav class="mt-3 grid gap-1">
                <a href="{{ route('seller.listings.index') }}" class="rounded-xl bg-indigo-50 px-3 py-2 text-sm font-extrabold text-indigo-700 ring-1 ring-indigo-100">Listing Saya</a>
                <a href="{{ route('seller.listings.create') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Tambah Baru</a>
                <a href="{{ route('seller.competitor-analysis.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Analisis Kompetitor</a>
                <a href="{{ route('seller.profile.edit') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Pengaturan</a>
            </nav>
        </aside>

        <section class="card p-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="text-sm font-extrabold text-slate-900">Listing Saya</div>
                    <div class="mt-1 text-sm text-slate-600">Kelola properti yang kamu pasang.</div>
                </div>
                <div class="flex items-center gap-2 print:hidden">
                    <a href="{{ route('seller.listings.export') }}" class="btn btn-outline flex items-center gap-2">
                        <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Ekspor CSV</span>
                    </a>
                    <button onclick="window.print()" class="btn btn-outline flex items-center gap-2">
                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>Cetak</span>
                    </button>
                    <a href="{{ route('seller.listings.create') }}" class="btn btn-primary">Tambah Listing</a>
                </div>
            </div>

            {{-- Charts Section --}}
            @if ($properties->isNotEmpty())
                <div x-data="{ open: false }" class="mt-6 border-b border-slate-100 pb-6 print:block">
                    <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-50 p-4 font-bold text-slate-700 ring-1 ring-slate-200/60 hover:bg-slate-100 transition print:hidden">
                        <span class="flex items-center gap-2">
                            <svg class="size-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Statistik & Analisis Listing Saya
                        </span>
                        <svg class="size-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open || window.matchMedia('print').matches" x-cloak class="mt-4 grid gap-6 lg:grid-cols-2">
                        <div class="card p-4 bg-white border border-slate-100">
                            <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">Sebaran Properti per Kecamatan</div>
                            <div id="districtChart" style="min-height: 250px;"></div>
                        </div>
                        <div class="card p-4 bg-white border border-slate-100">
                            <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">Status Kerawanan Banjir</div>
                            <div id="floodChart" style="min-height: 250px;"></div>
                        </div>
                        <div class="lg:col-span-2 card p-4 bg-white border border-slate-100">
                            <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">Tren Harga Properti Saya vs Rata-rata Pasar (per m²)</div>
                            <div id="priceTrendChart" style="min-height: 250px;"></div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse ($properties as $property)
                    <div class="group relative">
                        <x-property-card :property="$property" />
                        <div class="mt-3 flex items-center gap-2">
                            <a href="{{ route('seller.listings.edit', ['property' => $property->id]) }}" class="btn btn-outline">Edit</a>
                            <a href="{{ route('seller.listings.location.edit', ['property' => $property->id]) }}" class="btn btn-outline">Lokasi</a>
                            <a href="{{ route('seller.competitor-analysis.index') }}?property={{ $property->id }}" class="btn btn-outline text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50" title="Analisis Kompetitor">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </a>

                            <button
                                type="button"
                                onclick="openModal('del-{{ $property->id }}')"
                                class="btn btn-outline text-rose-600 hover:border-rose-300 hover:bg-rose-50"
                            >Hapus</button>

                            <div id="del-{{ $property->id }}" class="modal-overlay">
                                <div class="modal-box">
                                    <div class="grid place-items-center" style="width:48px;height:48px;background:#fee2e2;border-radius:1rem;margin-bottom:1rem">
                                        <svg style="width:24px;height:24px;color:#dc2626" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-extrabold text-slate-900">Hapus Listing?</div>
                                    <div class="mt-1 text-sm text-slate-600">
                                        <span class="font-semibold">{{ $property->title }}</span> akan dihapus permanen beserta semua fotonya.
                                    </div>
                                    <div class="mt-5 flex gap-3">
                                        <button
                                            type="button"
                                            onclick="closeModal('del-{{ $property->id }}')"
                                            class="btn btn-outline flex-1"
                                        >Batal</button>
                                        <form method="POST" action="{{ route('seller.listings.destroy', ['property' => $property->id]) }}" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center p-12 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                        <div class="grid size-12 place-items-center rounded-xl bg-slate-100 mb-3 text-slate-400">
                            <svg class="size-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="text-sm font-extrabold text-slate-700">Belum ada listing aktif</div>
                        <div class="mt-1 text-xs font-semibold text-slate-500">Mulai pasang iklan properti pertama Anda di platform Samarinda Properti GIS.</div>
                        <a href="{{ route('seller.listings.create') }}" class="btn btn-primary mt-4 btn-sm">Mulai Pasang Iklan</a>
                    </div>
                @endforelse
            </div>
        </section>
    </div>

    @if ($properties->isNotEmpty())
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
                        name: 'Properti Saya',
                        data: districtTotals
                    }],
                    chart: {
                        type: 'bar',
                        height: 240,
                        toolbar: { show: false },
                        fontFamily: 'Inter, system-ui, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                            barHeight: '50%'
                        }
                    },
                    colors: ['#4f46e5'],
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: districtLabels,
                        labels: { style: { colors: '#64748b', fontWeight: 600 } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#64748b', fontWeight: 600 } }
                    },
                    grid: { borderColor: '#f1f5f9' }
                };
                new ApexCharts(document.querySelector("#districtChart"), districtOptions).render();

                // 2. Flood Donut Chart
                const floodSafe = {{ $floodSafeCount }};
                const floodRisk = {{ $floodRiskCount }};

                const floodOptions = {
                    series: [floodSafe, floodRisk],
                    chart: {
                        type: 'donut',
                        height: 240,
                        fontFamily: 'Inter, system-ui, sans-serif'
                    },
                    labels: ['Bebas Banjir', 'Rawan Banjir'],
                    colors: ['#10b981', '#f43f5e'],
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

                // 3. Price Trend Comparison Chart
                const sellerTrend = @json($sellerPriceTrend);
                const marketTrend = @json($marketPriceTrend);

                const periods = Array.from(new Set([
                    ...sellerTrend.map(t => t.period),
                    ...marketTrend.map(t => t.period)
                ])).sort();

                const sellerPrices = periods.map(p => {
                    const item = sellerTrend.find(t => t.period === p);
                    return item ? Math.round(item.avg_price_per_sqm) : null;
                });

                const marketPrices = periods.map(p => {
                    const item = marketTrend.find(t => t.period === p);
                    return item ? Math.round(item.avg_price_per_sqm) : null;
                });

                const trendOptions = {
                    series: [
                        {
                            name: 'Properti Saya',
                            data: sellerPrices
                        },
                        {
                            name: 'Rata-rata Pasar',
                            data: marketPrices
                        }
                    ],
                    chart: {
                        type: 'line',
                        height: 260,
                        toolbar: { show: false },
                        fontFamily: 'Inter, system-ui, sans-serif',
                        zoom: { enabled: false }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [3, 2],
                        dashArray: [0, 5]
                    },
                    colors: ['#4f46e5', '#94a3b8'],
                    xaxis: {
                        categories: periods,
                        labels: { style: { colors: '#64748b', fontWeight: 600 } }
                    },
                    yaxis: {
                        labels: {
                            formatter: function (val) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                            },
                            style: { colors: '#64748b', fontWeight: 600 }
                        }
                    },
                    grid: { borderColor: '#f1f5f9' },
                    markers: {
                        size: 4,
                        colors: ['#4f46e5', '#94a3b8'],
                        strokeColors: '#fff',
                        strokeWidth: 2
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                if (val === null) return 'N/A';
                                return 'Rp ' + new Intl.NumberFormat('id-ID').format(val) + ' / m²';
                            }
                        }
                    }
                };
                new ApexCharts(document.querySelector("#priceTrendChart"), trendOptions).render();
            });
        </script>
        @endpush
    @endif
</x-layouts.app>
