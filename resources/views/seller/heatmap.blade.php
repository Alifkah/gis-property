<x-layouts.seller>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <section class="card p-6 bg-white border border-slate-200/50 shadow-sm">
        <div class="mb-5">
            <h1 class="text-base font-extrabold text-slate-900 font-display flex items-center gap-2 leading-none">
                <i class="ti ti-flame text-brand-accent"></i>
                <span>Peta Panas Permintaan Pasar (Market Demand Hotspot)</span>
            </h1>
            <p class="mt-1.5 text-xs font-semibold text-slate-500">Visualisasi peta panas berdasarkan data pencarian rute, wilayah pencarian pembeli, dan jumlah klik detail properti Anda secara spasial di Samarinda.</p>
        </div>

        <div class="relative rounded-2xl border border-slate-200/60 overflow-hidden shadow-sm" style="height: 500px;">
            <div id="heatmap" class="h-full w-full relative z-0"></div>

            {{-- Floating Info Panel (Top-Right, z-[999]) --}}
            <div class="absolute top-4 right-4 w-[280px] bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/50 p-4 z-[999] shadow-lg space-y-4">
                <div>
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Jejak Aktivitas</div>
                    <div id="totalPoints" class="mt-1.5 text-2xl font-black text-brand-primary leading-none">-</div>
                    <p class="text-[9px] text-slate-400 font-semibold leading-relaxed mt-1">Titik lokasi yang berhasil dikumpulkan dari akumulasi pencarian peta dan detail view oleh pengunjung.</p>
                </div>

                <div class="border-t border-slate-100 pt-3">
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Kepadatan Permintaan</div>
                    <div class="w-full h-2 bg-gradient-to-r from-blue-500 via-emerald-400 via-yellow-400 to-red-500 rounded-full border border-slate-100"></div>
                    <div class="flex items-center justify-between text-[9px] font-extrabold text-slate-500 uppercase tracking-wider mt-1.5">
                        <span>Rendah (Hijau/Biru)</span>
                        <span>Tinggi (Merah)</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="https://cdn.jsdelivr.net/npm/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const map = L.map('heatmap', { zoomControl: false, attributionControl: false }).setView([-0.5022, 117.1536], 12);
                L.control.zoom({ position: 'bottomright' }).addTo(map);

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd'
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

                // Invalidate size immediately
                map.invalidateSize();
                setTimeout(() => map.invalidateSize(), 500);
            });
        </script>
    @endpush
</x-layouts.seller>
