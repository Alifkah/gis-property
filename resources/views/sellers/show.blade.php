<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="grid gap-6 lg:grid-cols-[340px_minmax(0,1fr)]">
        {{-- Seller Info Profile Card --}}
        <aside class="card p-6 h-fit flex flex-col items-center text-center">
            @if ($seller->logo_path)
                <img src="{{ Storage::disk('public')->url($seller->logo_path) }}" alt="{{ $seller->company_name ?? $seller->name }}" class="size-24 object-cover rounded-3xl ring-2 ring-slate-200/50 shadow-sm" />
            @else
                <div class="size-24 grid place-items-center rounded-3xl bg-brand-primary/5 border border-brand-primary/10 text-brand-primary text-3xl font-black uppercase">
                    {{ substr($seller->company_name ?? $seller->name, 0, 1) }}
                </div>
            @endif

            <h1 class="text-base font-extrabold text-slate-900 mt-4 leading-snug">{{ $seller->company_name ?? $seller->name }}</h1>
            
            @if ($seller->company_name)
                <span class="text-xs text-slate-500 font-semibold mt-1">Agen & Developer Terverifikasi</span>
            @else
                <span class="text-xs text-slate-400 font-medium mt-1">Penjual Independen</span>
            @endif

            @if ($seller->description)
                <p class="text-xs text-slate-600 mt-4 leading-relaxed font-medium bg-slate-50 p-3 rounded-xl border border-slate-100/50 text-left">
                    {{ $seller->description }}
                </p>
            @endif

            {{-- Contact Information --}}
            <div class="w-full mt-6 pt-5 border-t border-slate-100 flex flex-col gap-3">
                @if ($seller->phone)
                    @php
                        $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $seller->phone));
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo ' . ($seller->company_name ?? $seller->name) . ', saya melihat listing Anda di Samarinda Properti GIS.') }}" 
                       target="_blank" rel="noopener" 
                       class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition shadow-xs hover:shadow-md text-xs">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span>WhatsApp Agensi</span>
                    </a>
                @endif
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-100/50">
                    <span>Total Listing Aktif</span>
                    <span class="text-brand-primary font-extrabold">{{ $properties->count() }} unit</span>
                </div>
            </div>
        </aside>

        {{-- Map and Grid Listings --}}
        <div class="min-w-0 grid gap-6">
            {{-- Map Card --}}
            <section class="card overflow-hidden">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Sebaran Properti Agensi</h2>
                    <span class="text-[10px] text-slate-400 font-semibold">Klik pin untuk melihat detail properti</span>
                </div>
                <div class="relative z-0" style="height:350px">
                    <div id="map" class="h-full w-full"></div>
                </div>
            </section>

            {{-- Listing Cards Grid --}}
            <section>
                <h2 class="text-sm font-bold text-slate-900 mb-4">Daftar Iklan Properti</h2>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($properties as $property)
                        <div class="group relative card p-4 hover:shadow-md transition bg-white flex flex-col justify-between">
                            <x-property-card :property="$property" />
                            <div class="mt-4 pt-3.5 border-t border-slate-100 flex justify-end">
                                <a href="{{ route('properties.show', $property->id) }}" target="_blank" class="btn btn-primary text-xs w-full">
                                    Lihat Detail &rarr;
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center p-12 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                            <div class="text-sm font-extrabold text-slate-700">Tidak ada properti aktif</div>
                            <p class="mt-1 text-xs text-slate-500 font-semibold">Saat ini agensi belum memiliki properti aktif yang dipasarkan.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const geojson = @json($geojson);

                const map = L.map('map').setView([-0.5022, 117.1536], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

                const markers = L.featureGroup().addTo(map);

                const markerIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:16px;height:16px;border-radius:9999px;background:#E36414;border:3.5px solid #ffffff;box-shadow:0 4px 10px rgba(15,23,42,.35)"></div>',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                if (geojson.features && geojson.features.length > 0) {
                    geojson.features.forEach(f => {
                        const coords = f.geometry.coordinates;
                        if (coords[0] !== 0 && coords[1] !== 0) {
                            const marker = L.marker([coords[1], coords[0]], { icon: markerIcon }).addTo(markers);
                            const priceFormatted = new Intl.NumberFormat('id-ID').format(f.properties.price);
                            marker.bindPopup(`
                                <div class="text-xs">
                                    <div class="font-bold text-slate-800">${f.properties.title}</div>
                                    <div class="text-brand-primary font-extrabold mt-0.5">Rp ${priceFormatted}</div>
                                    <div class="text-[9px] text-slate-400 font-bold uppercase tracking-wider mt-1">${f.properties.type} · ${f.properties.district}</div>
                                    <a href="/properties/${f.properties.id}" target="_blank" class="block text-[10px] text-center bg-brand-primary text-white font-semibold py-1 px-2 rounded-md mt-2.5 hover:bg-brand-primary/95 transition">Lihat Properti</a>
                                </div>
                            `);
                        }
                    });

                    if (markers.getBounds().isValid()) {
                        map.fitBounds(markers.getBounds(), { padding: [30, 30] });
                    }
                }
            });
        </script>
    @endpush
</x-layouts.app>
