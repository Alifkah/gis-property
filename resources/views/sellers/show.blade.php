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
    <div class="relative w-full h-40 sm:h-52 rounded-3xl overflow-hidden bg-gradient-to-r from-brand-primary via-slate-900 to-brand-accent shadow-md">
        <div class="absolute inset-0 opacity-20 pointer-events-none mix-blend-overlay">
            <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 300" preserveAspectRatio="none">
                <g fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M-100,80 C200,30 300,180 500,100 C700,20 800,150 1100,80" stroke-opacity="0.3" />
                    <path d="M-100,120 C200,70 300,220 500,140 C700,60 800,190 1100,120" stroke-opacity="0.3" />
                    <path d="M-100,160 C200,110 300,260 500,180 C700,100 800,230 1100,160" stroke-opacity="0.3" />
                </g>
            </svg>
        </div>
        <div class="absolute inset-0 bg-brand-primary/10 backdrop-blur-[0.5px]"></div>
    </div>

    {{-- Main Container overlapping the banner --}}
    <div class="-mt-16 sm:-mt-24 px-4 sm:px-6 pb-6 relative z-10 grid gap-6 lg:grid-cols-[320px_minmax(0,1fr)]">
        @php
            $topDistrict = $properties->groupBy('district_name')->sortByDesc(fn($group) => $group->count())->keys()->first() ?? 'Samarinda';
        @endphp

        {{-- Seller Info Profile Card --}}
        <aside class="card p-6 h-fit flex flex-col items-center text-center shadow-premium bg-white">
            <div class="-mt-20 sm:-mt-24 mb-4 bg-white p-1.5 rounded-3xl shadow-md">
                @if ($seller->logo_path)
                    <img src="{{ Storage::disk('public')->url($seller->logo_path) }}" alt="{{ $seller->company_name ?? $seller->name }}" class="size-24 sm:size-28 object-cover rounded-2xl ring-1 ring-slate-100" />
                @else
                    <div class="size-24 sm:size-28 grid place-items-center rounded-2xl bg-brand-primary/5 border border-brand-primary/10 text-brand-primary text-3xl font-black uppercase">
                        {{ substr($seller->company_name ?? $seller->name, 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-1.5 mt-2">
                <h1 class="text-base font-extrabold text-slate-900 leading-snug">{{ $seller->company_name ?? $seller->name }}</h1>
                @if ($seller->company_name)
                    <span class="text-brand-primary shrink-0" title="Agen Terverifikasi">
                        <svg class="size-4.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endif
            </div>
            
            @if ($seller->company_name)
                <span class="text-[10px] bg-brand-primary/10 text-brand-primary font-bold px-2 py-0.5 rounded-full mt-1.5">Agen & Developer Terverifikasi</span>
            @else
                <span class="text-[10px] bg-slate-100 text-slate-500 font-bold px-2 py-0.5 rounded-full mt-1.5">Penjual Independen</span>
            @endif

            @if ($seller->description)
                <p class="text-xs text-slate-600 mt-4 leading-relaxed font-medium bg-slate-50 p-3 rounded-xl border border-slate-100/50 text-left w-full whitespace-pre-line">
                    {{ $seller->description }}
                </p>
            @endif

            {{-- Kartu Statistik Dinamis --}}
            <div class="grid grid-cols-2 gap-2 w-full mt-5 pt-4 border-t border-slate-100 text-left">
                <div class="bg-slate-50 border border-slate-200/40 rounded-xl p-2.5 col-span-2">
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Rata-rata Harga Listing</div>
                    <div class="text-xs font-black text-slate-800 mt-0.5">
                        {{ $properties->isNotEmpty() ? 'Rp ' . number_format($properties->avg('price'), 0, ',', '.') : '-' }}
                    </div>
                </div>
                <div class="bg-slate-50 border border-slate-200/40 rounded-xl p-2.5">
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Total Iklan</div>
                    <div class="text-xs font-black text-brand-primary mt-0.5">{{ $properties->count() }} Unit</div>
                </div>
                <div class="bg-slate-50 border border-slate-200/40 rounded-xl p-2.5">
                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wider">Fokus Wilayah</div>
                    <div class="text-xs font-black text-slate-800 mt-0.5 truncate" title="{{ $topDistrict }}">
                        {{ $topDistrict }}
                    </div>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="w-full mt-5 space-y-2.5">
                @if ($seller->phone)
                    @php
                        $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $seller->phone));
                    @endphp
                    <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo ' . ($seller->company_name ?? $seller->name) . ', saya melihat listing Anda di Samarinda Properti GIS.') }}" 
                       target="_blank" rel="noopener" 
                       class="btn bg-[#25D366] text-white hover:bg-[#20ba5a] w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition shadow-xs hover:shadow-md text-xs cursor-pointer">
                        <svg class="size-4.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.746.953 3.71 1.458 5.704 1.459h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                        </svg>
                        <span>WhatsApp Agensi</span>
                    </a>
                    <a href="tel:{{ $seller->phone }}" class="btn btn-outline w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-bold transition text-xs cursor-pointer">
                        <svg class="size-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.387a20.331 20.331 0 0 1-9.974-9.974c-.155-.44.01-1.27.387-1.21l1.293-.97c.362-.272.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25z" />
                        </svg>
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
        <div class="min-w-0 grid gap-6" x-data="{ activeTab: 'semua' }">
            {{-- Map Card --}}
            <section class="card overflow-hidden shadow-sm">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="size-4 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <span>Sebaran Properti Agensi</span>
                    </h2>
                    <span class="text-[10px] text-slate-400 font-semibold">Klik pin untuk melihat detail properti</span>
                </div>
                <div class="relative z-0" style="height:350px">
                    <div id="map" class="h-full w-full"></div>
                </div>
            </section>

            {{-- Listing Cards Grid --}}
            <section>
                <div class="flex flex-wrap items-center justify-between gap-4 mb-4 border-b border-slate-100 pb-3">
                    <h2 class="text-sm font-bold text-slate-900">Daftar Iklan Properti</h2>
                    
                    {{-- Tab Filter Tipe Properti --}}
                    <div class="flex gap-1 bg-slate-100 p-1 rounded-xl">
                        <button type="button" @click="activeTab = 'semua'" :class="activeTab === 'semua' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer border-0">Semua</button>
                        <button type="button" @click="activeTab = 'Rumah'" :class="activeTab === 'Rumah' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer border-0">Rumah</button>
                        <button type="button" @click="activeTab = 'Tanah'" :class="activeTab === 'Tanah' ? 'bg-white text-brand-primary shadow-xs' : 'text-slate-500 hover:text-slate-800'" class="px-3 py-1 rounded-lg text-xs font-bold transition cursor-pointer border-0">Tanah</button>
                    </div>
                </div>

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $hasRumah = $properties->contains('type', 'Rumah');
                        $hasTanah = $properties->contains('type', 'Tanah');
                    @endphp

                    @forelse ($properties as $property)
                        <div x-show="activeTab === 'semua' || activeTab === '{{ $property->type }}'" 
                             class="group relative card p-4 hover:shadow-md transition bg-white flex flex-col justify-between"
                             x-transition>
                            <x-property-card :property="$property" />
                            <div class="mt-4 pt-3.5 border-t border-slate-100 flex justify-end">
                                <a href="{{ route('properties.show', $property) }}" target="_blank" class="btn btn-primary text-xs w-full">
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

                    {{-- Empty state for Rumah tab --}}
                    <div x-show="activeTab === 'Rumah' && !{{ $hasRumah ? 'true' : 'false' }}" x-cloak class="col-span-full flex flex-col items-center justify-center p-12 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                        <div class="text-sm font-extrabold text-slate-700">Tidak ada ruko/rumah aktif</div>
                        <p class="mt-1 text-xs text-slate-500 font-semibold">Agensi belum mempublikasikan tipe properti Rumah saat ini.</p>
                    </div>

                    {{-- Empty state for Tanah tab --}}
                    <div x-show="activeTab === 'Tanah' && !{{ $hasTanah ? 'true' : 'false' }}" x-cloak class="col-span-full flex flex-col items-center justify-center p-12 text-center rounded-2xl bg-slate-50 border border-dashed border-slate-200">
                        <div class="text-sm font-extrabold text-slate-700">Tidak ada tanah aktif</div>
                        <p class="mt-1 text-xs text-slate-500 font-semibold">Agensi belum mempublikasikan tipe properti Tanah saat ini.</p>
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

                const map = L.map('map').setView([-0.5022, 117.1536], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

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
                    const svg = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" fill="none"><rect width="400" height="250" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4f46e5" stop-opacity="0.12"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0.04"/></linearGradient></defs><path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/><text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">${typeUpper}</text></svg>`;
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
