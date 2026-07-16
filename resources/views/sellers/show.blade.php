<x-layouts.app>
    @php
        $seoTitle = 'Profil Agen ' . ($seller->company_name ?? $seller->name) . ' - Samarinda Properti GIS';
        $seoDescription = 'Temukan properti aktif yang dipasarkan oleh ' . ($seller->company_name ?? $seller->name) . ' di Kota Samarinda. Cek reputasi agensi, nomor telepon, dan portofolio listing terlengkap.';
        $ogImage = $seller->logo_path ? Storage::disk('public')->url($seller->logo_path) : null;
    @endphp
    <x-slot:title>{{ $seoTitle }}</x-slot:title>
    <x-slot:description>{{ $seoDescription }}</x-slot:description>
    <x-slot:ogImage>{{ $ogImage }}</x-slot:ogImage>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <style>
            .leaflet-popup-content-wrapper {
                border-radius: 16px !important;
                padding: 4px !important;
                box-shadow: 0 10px 30px rgba(15,23,42,0.15) !important;
                border: 1px solid rgba(226,232,240,0.8) !important;
            }
            .leaflet-popup-tip {
                box-shadow: 0 10px 30px rgba(15,23,42,0.15) !important;
            }
            .leaflet-container a.leaflet-popup-close-button {
                top: 10px !important;
                right: 10px !important;
                color: #64748b !important;
                font-size: 16px !important;
                font-weight: bold !important;
            }
        </style>
    @endpush

    {{-- Banner Cover --}}
    <div class="relative w-full h-40 sm:h-52 rounded-3xl overflow-hidden bg-gradient-to-r from-brand-primary via-brand-primary/95 to-brand-primary/80 shadow-md">
        <div class="absolute inset-0 bg-black/10 backdrop-blur-[0.5px]"></div>
    </div>

    {{-- Main Container overlapping the banner --}}
    <div class="-mt-16 sm:-mt-24 px-4 sm:px-6 pb-6 relative z-10 grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">
        @php
            $topDistrict = $properties->groupBy('district_name')->sortByDesc(fn($group) => $group->count())->keys()->first() ?? 'Samarinda';
        @endphp

        {{-- Seller Info Profile Card --}}
        <aside class="card p-6 h-fit flex flex-col items-center text-center shadow-lg bg-white border border-slate-200/50">
            <div class="-mt-20 sm:-mt-24 mb-4 bg-white p-1 rounded-2xl shadow-md border border-slate-100">
                @if ($seller->logo_path)
                    <img src="{{ Storage::disk('public')->url($seller->logo_path) }}" alt="{{ $seller->company_name ?? $seller->name }}" class="size-20 object-cover rounded-xl" />
                @else
                    <div class="size-20 grid place-items-center rounded-xl bg-brand-primary/5 border border-brand-primary/10 text-brand-primary text-2xl font-black uppercase">
                        {{ substr($seller->company_name ?? $seller->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-1.5 mt-2 justify-center">
                <h1 class="text-base font-extrabold text-slate-900 leading-snug">{{ $seller->company_name ?? $seller->name }}</h1>
                @if ($seller->company_name)
                    <i class="ti ti-circle-check-filled text-brand-primary text-base" title="Agen Terverifikasi"></i>
                @endif
            </div>
            
            @if ($seller->company_name)
                <span class="text-[9px] bg-brand-primary/10 text-brand-primary font-bold px-2 py-0.5 rounded-full mt-1.5 uppercase tracking-wider">Developer Terverifikasi</span>
            @else
                <span class="text-[9px] bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-full mt-1.5 uppercase tracking-wider">Penjual Independen</span>
            @endif

            {{-- Inline Stats --}}
            <div class="flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-xs text-slate-500 font-semibold mt-4">
                <div class="flex items-center gap-1">
                    <i class="ti ti-building text-brand-primary"></i>
                    <span>{{ $properties->count() }} Listing</span>
                </div>
                <div class="size-1 rounded-full bg-slate-300"></div>
                <div class="flex items-center gap-1">
                    <i class="ti ti-map-pin text-brand-primary"></i>
                    <span class="truncate max-w-[100px]">{{ $topDistrict }}</span>
                </div>
            </div>

            @if ($seller->description)
                <p class="text-xs text-slate-600 mt-4 leading-relaxed font-medium bg-slate-50 p-3 rounded-xl border border-slate-100/50 text-left w-full whitespace-pre-line">
                    {{ $seller->description }}
                </p>
            @endif

            {{-- Contact Information --}}
            <div class="w-full mt-5 space-y-2">
                @if ($seller->phone)
                    @php
                        $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $seller->phone));
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo ' . ($seller->company_name ?? $seller->name) . ', saya melihat listing Anda di Samarinda Properti GIS.') }}" 
                       target="_blank" rel="noopener" 
                       class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition shadow-xs hover:shadow-md text-xs cursor-pointer border-0">
                        <i class="ti ti-brand-whatsapp text-base"></i>
                        <span>WhatsApp Agensi</span>
                    </a>
                    <a href="tel:{{ $seller->phone }}" class="btn btn-outline w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition text-xs cursor-pointer">
                        <i class="ti ti-phone text-base"></i>
                        <span>Hubungi Telepon</span>
                    </a>
                @endif
                @if ($seller->email)
                    <div class="flex items-center justify-between text-xs font-semibold text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-100/50">
                        <span class="shrink-0 text-slate-400">Email</span>
                        <span class="truncate text-slate-700 text-right w-full ml-2" title="{{ $seller->email }}">{{ $seller->email }}</span>
                    </div>
                @endif
            </div>
        </aside>

        {{-- Map and Grid Listings --}}
        <div class="min-w-0 space-y-6" x-data="{ activeTab: 'semua' }">
            {{-- Map Card --}}
            <section class="card overflow-hidden shadow-sm border border-slate-200/50 bg-white rounded-2xl">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5 font-display">
                        <i class="ti ti-map-2 text-brand-primary text-base"></i>
                        <span>Sebaran Properti Agensi</span>
                    </h2>
                    <span class="text-[10px] text-slate-400 font-semibold">Klik pin untuk melihat detail properti</span>
                </div>
                <div class="relative z-0" style="height:300px">
                    <div id="map" class="h-full w-full"></div>
                </div>
            </section>

            {{-- Listing Cards Grid --}}
            <section>
                {{-- Sticky tab filters --}}
                <div class="sticky top-20 bg-brand-bg py-3 z-10 flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 mb-6">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Iklan Properti</h2>
                    
                    {{-- Tab Filter Tipe Properti --}}
                    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
                        <button type="button" @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer border-0">Semua</button>
                        <button type="button" @click="activeTab = 'Rumah'" :class="activeTab === 'Rumah' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer border-0">Rumah</button>
                        <button type="button" @click="activeTab = 'Tanah'" :class="activeTab === 'Tanah' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition cursor-pointer border-0">Tanah</button>
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $hasRumah = $properties->contains('type', 'Rumah');
                        $hasTanah = $properties->contains('type', 'Tanah');
                    @endphp

                    @forelse ($properties as $property)
                        <div x-show="activeTab === 'semua' || activeTab === '{{ $property->type }}'" 
                             x-transition
                             class="contents">
                            <x-property-card :property="$property" />
                        </div>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center p-16 text-center rounded-2xl bg-white border border-slate-200">
                            <div class="grid size-12 place-items-center rounded-xl bg-slate-50 text-slate-300 border mb-4">
                                <i class="ti ti-building-off text-2xl"></i>
                            </div>
                            <div class="text-sm font-extrabold text-slate-700">Tidak ada properti aktif</div>
                            <p class="mt-1 text-xs text-slate-500 font-semibold max-w-xs">Saat ini agensi belum memiliki properti aktif yang dipasarkan di platform kami.</p>
                        </div>
                    @endforelse

                    {{-- Empty state for Rumah tab --}}
                    <div x-show="activeTab === 'Rumah' && !{{ $hasRumah ? 'true' : 'false' }}" x-cloak class="col-span-full flex flex-col items-center justify-center p-16 text-center rounded-2xl bg-white border border-slate-200">
                        <div class="grid size-12 place-items-center rounded-xl bg-slate-50 text-slate-300 border mb-4">
                            <i class="ti ti-building-off text-2xl"></i>
                        </div>
                        <div class="text-sm font-extrabold text-slate-700">Tidak ada properti Rumah</div>
                        <p class="mt-1 text-xs text-slate-500 font-semibold max-w-xs">Agensi belum mempublikasikan tipe properti Rumah saat ini.</p>
                    </div>

                    {{-- Empty state for Tanah tab --}}
                    <div x-show="activeTab === 'Tanah' && !{{ $hasTanah ? 'true' : 'false' }}" x-cloak class="col-span-full flex flex-col items-center justify-center p-16 text-center rounded-2xl bg-white border border-slate-200">
                        <div class="grid size-12 place-items-center rounded-xl bg-slate-50 text-slate-300 border mb-4">
                            <i class="ti ti-building-off text-2xl"></i>
                        </div>
                        <div class="text-sm font-extrabold text-slate-700">Tidak ada properti Tanah</div>
                        <p class="mt-1 text-xs text-slate-500 font-semibold max-w-xs">Agensi belum mempublikasikan tipe properti Tanah saat ini.</p>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const geojson = @json($geojson);

                const map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-0.5022, 117.1536], 12);
                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    maxZoom: 20,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                    subdomains: 'abcd'
                }).addTo(map);

                const markers = L.featureGroup().addTo(map);

                const markerIcon = L.divIcon({
                    className: '',
                    html: '<div style="width:16px;height:16px;border-radius:9999px;background:#E36414;border:3.5px solid #ffffff;box-shadow:0 4px 10px rgba(15,23,42,.35)"></div>',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                function getImageUrl(type, imgUrl) {
                    if (imgUrl) return imgUrl;
                    const typeUpper = (type || 'PROPERTI').toUpperCase();
                    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" fill="none"><rect width="400" height="250" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#0F4C5C" stop-opacity="0.12"/><stop offset="100%" stop-color="#0F4C5C" stop-opacity="0.04"/></linearGradient></defs><path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#0F4C5C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/><text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#0F4C5C" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">${typeUpper}</text></svg>`;
                    return 'data:image/svg+xml;base64,' + btoa(svg);
                }

                if (geojson.features && geojson.features.length > 0) {
                    geojson.features.forEach(f => {
                        const coords = f.geometry.coordinates;
                        if (coords[0] !== 0 && coords[1] !== 0) {
                            const marker = L.marker([coords[1], coords[0]], { icon: markerIcon }).addTo(markers);
                            const priceFormatted = new Intl.NumberFormat('id-ID').format(f.properties.price);
                            const imgSrc = getImageUrl(f.properties.type, f.properties.image_url);
                            marker.bindPopup(`
                                <div style="width:220px; font-family:'Inter', sans-serif">
                                    <img src="${imgSrc}" alt="${f.properties.title}" style="width:100%;height:110px;object-fit:cover;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.06)" />
                                    <div style="padding:10px 0 0 0">
                                        <div style="font-size:13px;font-weight:900;color:#0F4C5C">Rp ${priceFormatted}</div>
                                        <div style="margin-top:4px;font-size:11px;font-weight:800;color:#0f172a;line-height:1.4">${f.properties.title}</div>
                                        <div style="margin-top:2px;font-size:9px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:0.5px">${f.properties.type} · ${f.properties.district}</div>
                                        <a href="/properties/${f.properties.slug}" target="_blank" style="margin-top:10px;display:inline-flex;align-items:center;justify-content:center;padding:8px 10px;border-radius:10px;background:#E36414;color:#fff;font-size:10px;font-weight:800;text-decoration:none;width:100%;box-shadow:0 4px 12px rgba(227,100,20,0.15)">Lihat Detail Properti</a>
                                    </div>
                                </div>
                            `, { className: 'rounded-xl' });
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
