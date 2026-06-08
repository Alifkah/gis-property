<x-layouts.seller>

        {{-- Main Content --}}
        <div class="min-w-0 grid gap-6">
            {{-- Header Card --}}
        <section class="card p-4 sm:p-6">
                <div class="flex flex-wrap items-start sm:items-center justify-between gap-3">
                    <div>
                        <h1 class="text-base sm:text-lg font-bold text-slate-900">Dashboard Penjual</h1>
                        <p class="mt-1 text-xs text-slate-500">Kelola listing properti Anda dan pantau kinerja promosi secara real-time.</p>
                    </div>
                    <div class="flex items-center gap-2 print:hidden flex-wrap">
                        <a href="{{ route('seller.listings.export') }}" class="btn btn-outline hover:border-slate-300 text-xs sm:text-sm px-3">
                            <svg class="size-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span class="hidden sm:inline">Ekspor CSV</span>
                            <span class="sm:hidden">CSV</span>
                        </a>
                        <a href="{{ route('seller.listings.create') }}" class="btn btn-primary shadow-sm cursor-pointer text-xs sm:text-sm px-3">+ Tambah</a>
                    </div>
                </div>

                {{-- Analytics Cards --}}
                <div class="grid gap-3 sm:gap-4 grid-cols-2 lg:grid-cols-4 mt-5 sm:mt-6">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Properti</div>
                        <div class="text-2xl font-extrabold text-slate-900 mt-1.5">{{ number_format($properties->count()) }}</div>
                        <div class="text-[10px] text-slate-400 mt-1 font-medium">Unit iklan aktif</div>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Dilihat</div>
                        <div class="text-2xl font-extrabold text-brand-primary mt-1.5">{{ number_format($totalViews) }}</div>
                        <div class="text-[10px] text-slate-400 mt-1 font-medium">Kali halaman detail dibuka</div>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Klik Kontak WhatsApp</div>
                        <div class="text-2xl font-extrabold text-emerald-600 mt-1.5">{{ number_format($totalClicks) }}</div>
                        <div class="text-[10px] text-slate-400 mt-1 font-medium">Potensi calon pembeli</div>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/50 p-4 transition-all">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rasio Konversi</div>
                        @php
                            $conversion = $totalViews > 0 ? ($totalClicks / $totalViews) * 100 : 0;
                        @endphp
                        <div class="text-2xl font-extrabold text-brand-accent mt-1.5">{{ number_format($conversion, 1) }}%</div>
                        <div class="text-[10px] text-slate-400 mt-1 font-medium">Konversi klik vs tayangan</div>
                    </div>
                </div>
            </section>

            {{-- Top viewed listings table --}}
            @if ($properties->isNotEmpty())
                <section class="card overflow-hidden">
                    <div class="p-4 sm:p-6 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900">Performa Iklan Terbaik</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Top 5 properti yang paling sering dilihat pembeli.</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[520px]">
                            <thead>
                                <tr class="bg-slate-50/75 border-b border-slate-100">
                                    <th class="px-4 sm:px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Properti</th>
                                    <th class="px-4 sm:px-6 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-400">Views</th>
                                    <th class="px-4 sm:px-6 py-3.5 text-center text-xs font-bold uppercase tracking-wider text-slate-400">Klik WA</th>
                                    <th class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-400">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100/75">
                                @foreach ($topProperties as $p)
                                    <tr class="hover:bg-slate-50/30 transition-colors">
                                        <td class="px-4 sm:px-6 py-3">
                                            <div class="flex items-center gap-3">
                                                @php
                                                    $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
                                                @endphp
                                                @if ($p->images->isNotEmpty() && (!$isLocalDisk || Storage::disk('public')->exists($p->images->first()->path)))
                                                    <img src="{{ Storage::disk('public')->url($p->images->first()->path) }}" alt="{{ $p->title }}" class="size-9 sm:size-10 object-cover rounded-lg ring-1 ring-slate-200/50 shrink-0" />
                                                @else
                                                    <div class="size-9 sm:size-10 grid place-items-center rounded-lg bg-brand-primary/8 border border-brand-primary/10 text-[10px] font-extrabold text-brand-primary shrink-0">
                                                        {{ strtoupper(substr($p->type, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <a href="{{ route('properties.show', $p->id) }}" target="_blank" class="text-xs font-bold text-slate-900 hover:text-brand-primary hover:underline truncate max-w-[140px] sm:max-w-[240px]">
                                                    {{ $p->title }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 text-center font-bold text-brand-primary text-xs">{{ number_format($p->views_count) }}</td>
                                        <td class="px-4 sm:px-6 py-3 text-center font-bold text-emerald-600 text-xs">{{ number_format($p->whatsapp_clicks_count) }}</td>
                                        <td class="px-4 sm:px-6 py-3 text-right font-bold text-slate-700 text-xs">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>
            @endif

            {{-- Listing Saya Card --}}
            <section class="card p-6">
                <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Daftar Properti Saya</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Kelola informasi, lokasi, dan analisis kompetitor properti Anda.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                        {{ $properties->count() }} Properti
                    </span>
                </div>

                <div class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($properties as $property)
                        <div class="group relative flex flex-col justify-between rounded-2xl border border-slate-200/60 p-4 transition-all hover:shadow-md hover:border-slate-300 bg-white">
                            <div class="relative">
                                <x-property-card :property="$property" />
                                
                                {{-- Inline Stats view --}}
                                <div class="mt-3.5 flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-[10px] font-bold text-slate-500 border border-slate-100">
                                    <span class="flex items-center gap-1">
                                        <svg class="size-3.5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>{{ number_format($property->views_count) }} Views</span>
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="size-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ number_format($property->whatsapp_clicks_count) }} Klik WA</span>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-4 flex items-center gap-2 pt-2.5 border-t border-slate-100">
                                <a href="{{ route('seller.listings.edit', ['property' => $property->id]) }}" class="btn btn-outline text-xs px-2.5 py-1.5 flex-1 border-slate-200 hover:bg-slate-50">Edit</a>
                                <a href="{{ route('seller.listings.location.edit', ['property' => $property->id]) }}" class="btn btn-outline text-xs px-2.5 py-1.5 flex-1 border-slate-200 hover:bg-slate-50">Lokasi</a>
                                  <a href="{{ route('seller.competitor-analysis.index') }}?property={{ $property->id }}" class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-brand-primary hover:border-brand-primary/20 hover:bg-brand-primary/5 shrink-0" title="Analisis Pesaing">
                                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </a>

                                <button
                                    type="button"
                                    onclick="openModal('del-{{ $property->id }}')"
                                    class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-rose-600 hover:border-rose-200 hover:bg-rose-50 cursor-pointer shrink-0"
                                >
                                    Hapus
                                </button>

                                <div id="del-{{ $property->id }}" class="modal-overlay">
                                    <div class="modal-box text-left max-w-md">
                                        <div class="flex items-center gap-3">
                                            <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                                                <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-slate-900">Hapus Iklan Properti?</div>
                                                <div class="text-xs text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 text-sm text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            Apakah Anda yakin ingin menghapus listing <span class="font-bold text-slate-900">"{{ $property->title }}"</span>? Seluruh data gambar dan analitik kunjungan terkait akan dihapus secara permanen.
                                        </div>
                                        
                                        <div class="mt-5 flex gap-3">
                                            <button
                                                type="button"
                                                onclick="closeModal('del-{{ $property->id }}')"
                                                class="btn btn-outline flex-1 cursor-pointer"
                                            >Batal</button>
                                            <form method="POST" action="{{ route('seller.listings.destroy', ['property' => $property->id]) }}" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700 cursor-pointer">Ya, Hapus</button>
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

            {{-- Charts Section --}}
            @if ($properties->isNotEmpty())
                <div x-data="{ open: false }" class="border-t border-slate-100 pt-2 print:block">
                    <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-2xl bg-slate-50 p-4 font-bold text-slate-700 ring-1 ring-slate-200/60 hover:bg-slate-100 transition print:hidden">
                        <span class="flex items-center gap-2.5">
                            <svg class="size-5 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Statistik Wilayah & Tren Harga Pasar</span>
                        </span>
                        <svg class="size-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open || window.matchMedia('print').matches" x-cloak class="mt-4 grid gap-6 lg:grid-cols-2">
                        <div class="card p-5 bg-white border border-slate-100">
                            <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">Sebaran Properti per Kecamatan</div>
                            <div id="districtChart" style="min-height: 250px;"></div>
                        </div>
                        <div class="card p-5 bg-white border border-slate-100">
                            <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">Status Kerawanan Banjir</div>
                            <div id="floodChart" style="min-height: 250px;"></div>
                        </div>
                        <div class="lg:col-span-2 card p-5 bg-white border border-slate-100">
                            <div class="text-xs font-extrabold uppercase tracking-widest text-slate-400 mb-4">Tren Harga Properti Saya vs Rata-rata Pasar (per m²)</div>
                            <div id="priceTrendChart" style="min-height: 250px;"></div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    @if ($properties->isNotEmpty())
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const defaultFont = "'Instrument Sans', 'Inter', system-ui, sans-serif";

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
                        fontFamily: defaultFont
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                            barHeight: '50%'
                        }
                    },
                    colors: ['#0F4C5C'],
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: districtLabels,
                        labels: { style: { colors: '#64748b', fontWeight: 500, fontSize: '11px' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#64748b', fontWeight: 500, fontSize: '11px' } }
                    },
                    grid: { borderColor: '#f1f5f9', strokeDashArray: 3 }
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
                        fontFamily: defaultFont,
                        zoom: { enabled: false }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [3.5, 2.5],
                        dashArray: [0, 4]
                    },
                    colors: ['#E36414', '#0F4C5C'],
                    xaxis: {
                        categories: periods,
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
                        colors: ['#E36414', '#0F4C5C'],
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
</x-layouts.seller>
