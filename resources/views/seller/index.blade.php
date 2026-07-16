<x-layouts.seller>
    <div class="space-y-6" x-data="listingsIndex()">
        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Listing Saya</span>
                    <span class="bg-brand-primary/10 text-brand-primary text-xs font-bold px-2.5 py-0.5 rounded-full">
                        {{ $properties->count() }} Unit
                    </span>
                </h1>
                <p class="mt-1.5 text-xs font-semibold text-slate-500">Kelola listing properti Anda dan pantau kinerja promosi secara real-time.</p>
            </div>
            <div class="flex items-center gap-2 print:hidden">
                <a href="{{ route('seller.listings.export') }}" class="btn btn-outline text-xs flex items-center gap-1.5 py-2.5 shadow-3xs cursor-pointer">
                    <i class="ti ti-download text-emerald-600 text-sm"></i>
                    <span>Ekspor CSV</span>
                </a>
                <a href="{{ route('seller.listings.create') }}" class="btn btn-primary text-xs font-bold flex items-center gap-1 border-0 py-2.5 shadow-xs cursor-pointer">
                    <i class="ti ti-plus text-sm"></i>
                    <span>Tambah Baru</span>
                </a>
            </div>
        </div>

        {{-- Stat Cards Row (3-col) --}}
        <div class="grid gap-4 sm:grid-cols-3">
            {{-- Total Listing --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm hover:border-brand-primary/10 transition flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Properti</div>
                    <div class="text-2xl font-extrabold text-slate-950 mt-1 leading-none">{{ $properties->count() }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-semibold">Unit terdaftar</div>
                </div>
                <div class="grid size-10 place-items-center rounded-xl bg-brand-primary/5 text-brand-primary">
                    <i class="ti ti-building text-lg"></i>
                </div>
            </div>

            {{-- Total Views --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm hover:border-brand-primary/10 transition flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Tayangan</div>
                    <div class="text-2xl font-extrabold text-emerald-600 mt-1 leading-none">{{ number_format($totalViews) }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-semibold">Halaman detail dibuka</div>
                </div>
                <div class="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600">
                    <i class="ti ti-eye text-lg"></i>
                </div>
            </div>

            {{-- Active Listings --}}
            <div class="bg-white rounded-2xl p-5 border border-slate-200/50 shadow-sm hover:border-brand-primary/10 transition flex items-center justify-between">
                <div>
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Listing Aktif</div>
                    <div class="text-2xl font-extrabold text-amber-600 mt-1 leading-none">{{ $properties->where('status', 'Tersedia')->count() }}</div>
                    <div class="text-[10px] text-slate-400 mt-1 font-semibold">Berstatus Tersedia</div>
                </div>
                <div class="grid size-10 place-items-center rounded-xl bg-amber-50 text-amber-600">
                    <i class="ti ti-checks text-lg"></i>
                </div>
            </div>
        </div>

        {{-- Filter / Search Bar --}}
        <div class="flex flex-col sm:flex-row items-center gap-3 bg-white p-3.5 rounded-2xl border border-slate-200/50 shadow-sm">
            <div class="relative w-full sm:flex-1">
                <div class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                    <i class="ti ti-search text-base"></i>
                </div>
                <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Cari berdasarkan judul properti..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2 pl-9 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-brand-primary focus:ring-1 focus:ring-brand-primary" />
            </div>

            <div class="w-full sm:w-auto shrink-0">
                <select x-model="statusFilter" @change="currentPage = 1" class="select bg-slate-50 py-2 px-3 text-xs w-full sm:w-auto font-semibold">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia (Aktif)</option>
                    <option value="Terjual">Terjual</option>
                    <option value="Draft">Draft</option>
                </select>
            </div>
        </div>

        {{-- Desktop Table (hidden on mobile) --}}
        <div class="hidden md:block overflow-hidden bg-white rounded-2xl border border-slate-200/50 shadow-sm">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Properti</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Tayangan</th>
                        <th class="px-6 py-4 text-center">Klik WA</th>
                        <th class="px-6 py-4 text-right">Harga</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="p in paginatedProperties" :key="p.id">
                        <tr class="hover:bg-slate-50/50 transition duration-150">
                            <td class="px-6 py-3.5">
                                <div class="flex items-center gap-3">
                                    <template x-if="p.image_url">
                                        <img :src="p.image_url" alt="" class="size-11 object-cover rounded-xl border border-slate-100 shrink-0" />
                                    </template>
                                    <template x-if="!p.image_url">
                                        <div class="size-11 grid place-items-center rounded-xl bg-slate-50 border border-slate-100 text-slate-300 shrink-0">
                                            <i class="ti ti-photo-off text-base"></i>
                                        </div>
                                    </template>
                                    <div class="min-w-0">
                                        <a :href="'/properties/' + p.slug" target="_blank" class="truncate text-xs font-bold text-slate-900 hover:text-brand-primary hover:underline block leading-snug" x-text="p.title"></a>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mt-1 block">ID: <span x-text="p.id"></span></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="font-bold text-slate-700" x-text="p.type"></span>
                            </td>
                            <td class="px-6 py-3.5">
                                <template x-if="p.status === 'Tersedia'">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-100 uppercase tracking-wider">Aktif</span>
                                </template>
                                <template x-if="p.status === 'Terjual'">
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[9px] font-bold text-slate-600 border border-slate-200 uppercase tracking-wider">Terjual</span>
                                </template>
                                <template x-if="p.status !== 'Tersedia' && p.status !== 'Terjual'">
                                    <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[9px] font-bold text-amber-700 border border-amber-100 uppercase tracking-wider">Draft</span>
                                </template>
                            </td>
                            <td class="px-6 py-3.5 text-center font-bold text-slate-800" x-text="p.views"></td>
                            <td class="px-6 py-3.5 text-center font-bold text-emerald-600" x-text="p.clicks"></td>
                            <td class="px-6 py-3.5 text-right font-extrabold text-slate-900" x-text="p.price_formatted"></td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a :href="'/properties/' + p.slug" target="_blank" class="grid size-8 place-items-center rounded-xl bg-slate-50 border border-slate-200/50 text-slate-600 hover:bg-slate-100 transition shadow-3xs" title="Lihat Detail">
                                        <i class="ti ti-eye text-base"></i>
                                    </a>
                                    <a :href="p.edit_url" class="grid size-8 place-items-center rounded-xl bg-slate-50 border border-slate-200/50 text-slate-600 hover:bg-slate-100 transition shadow-3xs" title="Edit Data">
                                        <i class="ti ti-edit text-base"></i>
                                    </a>
                                    <a :href="p.location_url" class="grid size-8 place-items-center rounded-xl bg-slate-50 border border-slate-200/50 text-slate-600 hover:bg-slate-100 transition shadow-3xs" title="Atur Lokasi">
                                        <i class="ti ti-map-pin text-base"></i>
                                    </a>
                                    <a :href="p.analysis_url" class="grid size-8 place-items-center rounded-xl bg-slate-50 border border-slate-200/50 text-brand-primary hover:border-brand-primary/25 hover:bg-brand-primary/5 transition shadow-3xs" title="Analisis Pesaing">
                                        <i class="ti ti-chart-bar text-base"></i>
                                    </a>
                                    <button type="button" @click="confirmDelete(p.id, p.title, '/seller/listings/' + p.id)" class="grid size-8 place-items-center rounded-xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 hover:border-rose-200 transition shadow-3xs cursor-pointer" title="Hapus Listing">
                                        <i class="ti ti-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredProperties.length === 0">
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-semibold bg-white">
                                <i class="ti ti-info-circle text-2xl text-slate-300 mb-2 block"></i>
                                Tidak ada properti ditemukan.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Mobile Card Layout --}}
        <div class="md:hidden space-y-4">
            <template x-for="p in paginatedProperties" :key="p.id">
                <div class="bg-white rounded-2xl border border-slate-200/50 p-4 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <template x-if="p.image_url">
                            <img :src="p.image_url" alt="" class="size-16 object-cover rounded-xl border shrink-0" />
                        </template>
                        <template x-if="!p.image_url">
                            <div class="size-16 grid place-items-center rounded-xl bg-slate-50 border text-slate-300 shrink-0">
                                <i class="ti ti-photo-off text-lg"></i>
                            </div>
                        </template>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-[9px] font-bold text-brand-primary bg-brand-primary/5 px-2 py-0.5 rounded-full uppercase" x-text="p.type"></span>
                                <template x-if="p.status === 'Tersedia'">
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full uppercase">Aktif</span>
                                </template>
                                <template x-if="p.status === 'Terjual'">
                                    <span class="text-[9px] font-bold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full uppercase">Terjual</span>
                                </template>
                            </div>
                            <a :href="'/properties/' + p.slug" target="_blank" class="truncate text-xs font-bold text-slate-900 block mt-1.5 leading-snug" x-text="p.title"></a>
                            <div class="text-[11px] font-extrabold text-brand-accent mt-1" x-text="p.price_formatted"></div>
                        </div>
                    </div>

                    {{-- Stats Summary Row --}}
                    <div class="grid grid-cols-2 gap-2 bg-slate-50 rounded-xl p-2.5 text-[10px] font-bold text-slate-500 border border-slate-100">
                        <div class="flex items-center gap-1">
                            <i class="ti ti-eye text-brand-primary"></i>
                            <span><span x-text="p.views"></span> Tayangan</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <i class="ti ti-brand-whatsapp text-emerald-500"></i>
                            <span><span x-text="p.clicks"></span> Hubungi WA</span>
                        </div>
                    </div>

                    {{-- Mobile action triggers --}}
                    <div class="grid grid-cols-5 gap-1.5 pt-3 border-t border-slate-100">
                        <a :href="'/properties/' + p.slug" target="_blank" class="btn btn-outline text-slate-600 py-2 rounded-xl flex items-center justify-center" title="Buka Detail">
                            <i class="ti ti-eye text-sm"></i>
                        </a>
                        <a :href="p.edit_url" class="btn btn-outline text-slate-600 py-2 rounded-xl flex items-center justify-center" title="Edit">
                            <i class="ti ti-edit text-sm"></i>
                        </a>
                        <a :href="p.location_url" class="btn btn-outline text-slate-600 py-2 rounded-xl flex items-center justify-center" title="Lokasi">
                            <i class="ti ti-map-pin text-sm"></i>
                        </a>
                        <a :href="p.analysis_url" class="btn btn-outline text-brand-primary py-2 rounded-xl flex items-center justify-center" title="Analisis">
                            <i class="ti ti-chart-bar text-sm"></i>
                        </a>
                        <button type="button" @click="confirmDelete(p.id, p.title, '/seller/listings/' + p.id)" class="btn bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 py-2 rounded-xl flex items-center justify-center cursor-pointer" title="Hapus">
                            <i class="ti ti-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </template>

            <template x-if="filteredProperties.length === 0">
                <div class="bg-white rounded-2xl border border-slate-200/50 p-12 text-center text-slate-400 font-semibold">
                    <i class="ti ti-info-circle text-2xl text-slate-300 mb-2 block"></i>
                    Tidak ada properti ditemukan.
                </div>
            </template>
        </div>

        {{-- Custom Compact Pagination --}}
        <template x-if="totalPages > 1">
            <div class="mt-6 flex items-center justify-between border-t border-slate-200/60 pt-4">
                <button type="button" @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="btn btn-outline py-2 px-3 text-xs font-bold flex items-center gap-1 cursor-pointer disabled:opacity-40 disabled:pointer-events-none">
                    <i class="ti ti-chevron-left text-sm"></i>
                    <span>Sebelumnya</span>
                </button>
                <div class="text-xs font-bold text-slate-600">
                    Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
                </div>
                <button type="button" @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="btn btn-outline py-2 px-3 text-xs font-bold flex items-center gap-1 cursor-pointer disabled:opacity-40 disabled:pointer-events-none">
                    <span>Selanjutnya</span>
                    <i class="ti ti-chevron-right text-sm"></i>
                </button>
            </div>
        </template>

        {{-- Inline Charts Collapse Section --}}
        @if ($properties->isNotEmpty())
            <div x-data="{ open: false }" class="border-t border-slate-200/60 pt-4 print:block">
                <button @click="open = !open" class="flex w-full items-center justify-between gap-3 rounded-2xl bg-white p-4 font-bold text-slate-700 border border-slate-200/50 shadow-sm hover:bg-slate-50 transition cursor-pointer print:hidden">
                    <span class="flex items-center gap-2.5 text-sm font-extrabold text-slate-900">
                        <i class="ti ti-chart-area text-brand-primary text-base"></i>
                        <span>Statistik Wilayah & Tren Harga Pasar</span>
                    </span>
                    <i class="ti ti-chevron-down text-base transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open || window.matchMedia('print').matches" x-cloak class="mt-4 grid gap-6 lg:grid-cols-2">
                    <div class="card p-5 bg-white border border-slate-200/50 shadow-sm">
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-4">Sebaran Properti per Kecamatan</div>
                        <div id="districtChart" style="min-height: 250px;"></div>
                    </div>
                    <div class="card p-5 bg-white border border-slate-200/50 shadow-sm">
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-4">Status Kerawanan Banjir</div>
                        <div id="floodChart" style="min-height: 250px;"></div>
                    </div>
                    <div class="lg:col-span-2 card p-5 bg-white border border-slate-200/50 shadow-sm">
                        <div class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 mb-4">Tren Harga Properti Saya vs Rata-rata Pasar (per m²)</div>
                        <div id="priceTrendChart" style="min-height: 250px;"></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Global Single Deletion Modal --}}
        <div id="deleteConfirmModal" style="display:none;" x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[2000] items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs flex" @click="deleteModalOpen = false">
            <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 relative ring-1 ring-slate-100 animate-in fade-in zoom-in-95 duration-200" @click.stopPropagation()>
                <button type="button" @click="deleteModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 cursor-pointer bg-transparent border-0">
                    <i class="ti ti-x text-lg"></i>
                </button>
                
                <div class="flex items-center gap-3">
                    <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                        <i class="ti ti-alert-triangle text-xl"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-slate-900">Hapus Iklan Properti?</div>
                        <div class="text-xs text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</div>
                    </div>
                </div>
                
                <div class="mt-4 text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-100/50 leading-relaxed">
                    Apakah Anda yakin ingin menghapus listing <span class="font-extrabold text-slate-900" x-text="deleteTitle"></span>? Seluruh data gambar dan analitik kunjungan terkait akan dihapus secara permanen.
                </div>
                
                <div class="mt-5 flex gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn btn-outline flex-1 cursor-pointer">Batal</button>
                    <form method="POST" :action="deleteAction" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700 cursor-pointer border-0 font-bold">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($properties->isNotEmpty())
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
                            height: 240,
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

    @push('scripts')
        <script>
            function listingsIndex() {
                return {
                    searchQuery: '',
                    statusFilter: '',
                    currentPage: 1,
                    itemsPerPage: 8,
                    deleteModalOpen: false,
                    deletePropertyId: null,
                    deleteTitle: '',
                    deleteAction: '',
                    properties: [
                        @foreach ($properties as $p)
                            {
                                id: {{ $p->id }},
                                title: {!! json_encode($p->title) !!},
                                price: {{ (float) $p->price }},
                                price_formatted: 'Rp {{ number_format((float) $p->price, 0, ',', '.') }}',
                                type: '{{ $p->type }}',
                                status: '{{ $p->status ?? 'Tersedia' }}',
                                views: {{ $p->views_count }},
                                clicks: {{ $p->whatsapp_clicks_count }},
                                slug: '{{ $p->slug }}',
                                edit_url: '{{ route('seller.listings.edit', $p->id) }}',
                                location_url: '{{ route('seller.listings.location.edit', $p->id) }}',
                                analysis_url: '{{ route('seller.competitor-analysis.index') }}?property={{ $p->id }}',
                                image_url: '{{ $p->images->isNotEmpty() ? Storage::disk('public')->url($p->images->first()->path) : '' }}'
                            },
                        @endforeach
                    ],
                    get filteredProperties() {
                        return this.properties.filter(p => {
                            const matchesSearch = p.title.toLowerCase().includes(this.searchQuery.toLowerCase()) || p.type.toLowerCase().includes(this.searchQuery.toLowerCase());
                            const matchesStatus = this.statusFilter === '' || p.status === this.statusFilter;
                            return matchesSearch && matchesStatus;
                        });
                    },
                    get paginatedProperties() {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        return this.filteredProperties.slice(start, start + this.itemsPerPage);
                    },
                    get totalPages() {
                        return Math.ceil(this.filteredProperties.length / this.itemsPerPage) || 1;
                    },
                    confirmDelete(id, title, action) {
                        this.deletePropertyId = id;
                        this.deleteTitle = title;
                        this.deleteAction = action;
                        this.deleteModalOpen = true;
                    }
                };
            }
        </script>
    @endpush
</x-layouts.seller>
