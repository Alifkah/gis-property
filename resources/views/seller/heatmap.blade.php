<x-layouts.seller>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <section class="card p-6">
        <div>
            <h1 class="text-sm font-bold text-slate-900">Peta Panas Permintaan Pasar (Market Demand Hotspot)</h1>
            <p class="mt-1 text-xs text-slate-500 font-medium">Visualisasi peta panas berdasarkan data pencarian rute, wilayah pencarian pembeli, dan jumlah klik detail properti Anda secara spasial di Samarinda.</p>
        </div>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_280px]">
            {{-- Map Card --}}
            <div class="relative rounded-2xl border border-slate-200/60 overflow-hidden shadow-xs" style="height: 500px;">
                <div id="heatmap" class="h-full w-full relative z-0"></div>
            </div>

            {{-- Info Panel --}}
            <div class="flex flex-col gap-4">
                <div class="rounded-2xl bg-brand-primary/5 border border-brand-primary/10 p-4">
                    <div class="text-[10px] font-bold text-brand-primary uppercase tracking-wider">Total Jejak Aktivitas</div>
                    <div id="totalPoints" class="mt-2 text-2xl font-black text-brand-accent">-</div>
                    <p class="text-[10px] text-slate-400 mt-1 font-semibold leading-relaxed">Titik lokasi yang berhasil dikumpulkan dari akumulasi pencarian peta dan detail view oleh pengunjung.</p>
                </div>

                <div class="rounded-2xl bg-slate-50 border border-slate-200/50 p-4">
                    <h3 class="text-xs font-bold text-slate-900 mb-2">Penjelasan Warna Peta</h3>
                    <ul class="space-y-2 text-[11px] font-semibold text-slate-600">
                        <li class="flex items-center gap-2">
                            <span class="size-3 rounded-full bg-rose-500 shrink-0"></span>
                            <span>Merah: Kepadatan Tinggi (Hotspot)</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="size-3 rounded-full bg-amber-500 shrink-0"></span>
                            <span>Kuning/Oranye: Kepadatan Sedang</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="size-3 rounded-full bg-emerald-500 shrink-0"></span>
                            <span>Hijau/Biru: Kepadatan Rendah</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const map = L.map('heatmap').setView([-0.5022, 117.1536], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                fetch('{{ route('api.seller.market-demands') }}')
                    .then(res => res.json())
                    .then(points => {
                        document.getElementById('totalPoints').textContent = points.length + ' titik';
                        
                        if (points.length > 0) {
                            const heatPoints = points.map(p => [parseFloat(p.lat), parseFloat(p.lng), 1.0]);
                            
                            // Initialize Leaflet heat layer
                            L.heatLayer(heatPoints, {
                                radius: 25,
                                blur: 15,
                                maxZoom: 17,
                                gradient: {
                                    0.4: 'blue',
                                    0.6: 'lime',
                                    0.8: 'orange',
                                    1.0: 'red'
                                }
                            }).addTo(map);
                        }
                    })
                    .catch(err => console.error(err));
            });
        </script>
    @endpush
</x-layouts.seller>
