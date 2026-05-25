<x-layouts.app>
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    @endpush

    <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
        <div>
            {{-- Galeri foto --}}
            @if ($property->images->isNotEmpty())
                <section class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <div class="overflow-hidden rounded-2xl bg-slate-100">
                        <img src="{{ Storage::url($property->images->first()->path) }}" alt="{{ $property->title }}" class="h-full w-full object-cover" />
                    </div>
                    @if ($property->images->count() > 1)
                        <div class="grid gap-3">
                            @foreach ($property->images->skip(1)->take(2) as $image)
                                <div class="overflow-hidden rounded-2xl bg-slate-100">
                                    <img src="{{ Storage::url($image->path) }}" alt="{{ $property->title }}" class="h-full w-full object-cover" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>
            @else
                @php
                    $svg1 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#4f46e5" stop-opacity="0.15"/><stop offset="100%" stop-color="#6366f1" stop-opacity="0.05"/></linearGradient></defs><path d="M220 240 l80-60 80 60 M240 220 v60h120v-60 M280 280 v-30h40v30" stroke="#4f46e5" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">EKSTERIOR</text></svg>';
                    $svg2 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#06b6d4" stop-opacity="0.15"/><stop offset="100%" stop-color="#4f46e5" stop-opacity="0.05"/></linearGradient></defs><path d="M220 260 h160 M240 260 v-30 h120 v30 M250 210 h100" stroke="#0891b2" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#0891b2" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">INTERIOR</text></svg>';
                    $svg3 = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 400" fill="none"><rect width="600" height="400" fill="url(#g)"/><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#8b5cf6" stop-opacity="0.15"/><stop offset="100%" stop-color="#ec4899" stop-opacity="0.05"/></linearGradient></defs><path d="M220 280 v-50 h160 v50 M250 230 h100" stroke="#7c3aed" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.5"/><text x="50%" y="330" dominant-baseline="middle" text-anchor="middle" fill="#7c3aed" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="14" letter-spacing="2" opacity="0.6">RUANGAN</text></svg>';
                    $img1 = 'data:image/svg+xml;base64,'.base64_encode($svg1);
                    $img2 = 'data:image/svg+xml;base64,'.base64_encode($svg2);
                    $img3 = 'data:image/svg+xml;base64,'.base64_encode($svg3);
                @endphp
                <section class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_280px]">
                    <div class="overflow-hidden rounded-2xl bg-slate-100">
                        <img src="{{ $img1 }}" alt="{{ $property->title }}" class="h-full w-full object-cover" />
                    </div>
                    <div class="grid gap-3">
                        <div class="overflow-hidden rounded-2xl bg-slate-100">
                            <img src="{{ $img2 }}" alt="{{ $property->title }}" class="h-full w-full object-cover" />
                        </div>
                        <div class="overflow-hidden rounded-2xl bg-slate-100">
                            <img src="{{ $img3 }}" alt="{{ $property->title }}" class="h-full w-full object-cover" />
                        </div>
                    </div>
                </section>
            @endif

            <section class="mt-8">
                <div class="flex flex-wrap items-center gap-2">
                    @if (($property->status ?? 'Tersedia') === 'Terjual')
                        <span class="inline-flex items-center rounded-full bg-slate-700 px-2.5 py-1 text-xs font-semibold text-white">Terjual</span>
                    @endif
                    @if ($isNew)
                        <x-badge variant="new">Rumah Baru</x-badge>
                    @endif
                    @if ($isFloodSafe)
                        <x-badge variant="safe">Bebas Banjir</x-badge>
                    @endif
                </div>

                <h1 class="mt-3 text-2xl font-extrabold text-slate-900">{{ $property->title }}</h1>
                <div class="mt-1 text-sm font-semibold text-slate-600">{{ $districtName ?? 'Kota Samarinda' }}</div>

                <div class="mt-6 grid gap-3 sm:grid-cols-4">
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-semibold text-slate-500">Luas Tanah</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->land_area }} m²</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-semibold text-slate-500">Luas Bangunan</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->building_area }} m²</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-semibold text-slate-500">Kamar Tidur</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->bedroom }}</div>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-semibold text-slate-500">Kamar Mandi</div>
                        <div class="mt-1 text-lg font-extrabold text-slate-900">{{ (int) $property->bathroom }}</div>
                    </div>
                </div>
            </section>

            <section class="mt-8 card p-6">
                <div class="text-sm font-extrabold text-slate-900">GIS Analysis</div>
                <div class="mt-4 grid gap-4 lg:grid-cols-[340px_minmax(0,1fr)]">
                    <div class="rounded-2xl bg-slate-100" style="height:260px;min-height:260px">
                        <div id="miniMap" class="relative z-0" style="height:260px;width:100%"></div>
                    </div>
                    <div class="grid gap-4">
                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                            <div class="text-xs font-semibold text-slate-500">Status Banjir</div>
                            <div class="mt-2 flex items-center gap-2 text-sm font-extrabold text-slate-900">
                                <span class="grid size-7 place-items-center rounded-full {{ $isFloodSafe ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white' }}">
                                    @if ($isFloodSafe)
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M20 6 9 17l-5-5" />
                                        </svg>
                                    @else
                                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M18 6 6 18" />
                                            <path d="M6 6l12 12" />
                                        </svg>
                                    @endif
                                </span>
                                <span>{{ $isFloodSafe ? 'Zona Aman' : 'Zona Rawan' }}</span>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                            <div class="text-xs font-semibold text-slate-500">Fasilitas Terdekat</div>
                            <div class="mt-3 grid gap-2">
                                @foreach ($nearestAmenities as $amenity)
                                    <div class="flex items-center justify-between gap-4 rounded-xl bg-white px-3 py-2 ring-1 ring-slate-200/60">
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-bold text-slate-900">{{ $amenity->name }}</div>
                                            <div class="truncate text-xs font-semibold text-slate-500">{{ $amenity->type }}</div>
                                        </div>
                                        <div class="shrink-0 text-xs font-extrabold text-indigo-700">
                                            {{ number_format(((float) $amenity->distance_m) / 1000, 1) }} km
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mt-8 card p-6">
                <div class="text-sm font-extrabold text-slate-900">Deskripsi</div>
                <p class="mt-3 text-sm leading-relaxed text-slate-700">
                    {{ $property->description ?? 'Properti ini berada di area Kota Samarinda dan cocok untuk hunian maupun investasi. Jelajahi detail lokasi melalui peta mini dan gunakan analisis fasilitas terdekat untuk mengambil keputusan.' }}
                </p>
            </section>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <div class="card p-6">
                <div class="text-2xl font-extrabold text-indigo-700">Rp {{ number_format((float) $property->price, 0, ',', '.') }}</div>

                @if (($property->status ?? 'Tersedia') === 'Terjual')
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-extrabold text-slate-600 ring-1 ring-slate-200">
                        <span class="size-2 rounded-full bg-slate-400"></span>
                        Properti Sudah Terjual
                    </div>
                @else
                    <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-extrabold text-emerald-700 ring-1 ring-emerald-200">
                        <span class="size-2 rounded-full bg-emerald-500"></span>
                        Tersedia
                    </div>
                @endif

                <div class="mt-4 grid gap-2">
                    @if ($property->user?->phone)
                        @php
                            $waNumber = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $property->user->phone));
                        @endphp
                        <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo, saya tertarik dengan properti: '.$property->title) }}" target="_blank" rel="noopener" class="btn btn-primary w-full">
                            Hubungi via WhatsApp
                        </a>
                    @else
                        <button type="button" class="btn btn-primary w-full" disabled>Hubungi via WhatsApp</button>
                    @endif
                    <button type="button" class="btn btn-outline w-full">Jadwalkan Kunjungan</button>
                </div>

                <div class="mt-6 rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                    <div class="flex items-center gap-3">
                        <div class="grid size-11 shrink-0 place-items-center rounded-2xl bg-indigo-600 font-extrabold text-white">
                            {{ strtoupper(substr($property->user?->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="truncate text-sm font-extrabold text-slate-900">{{ $property->user?->name ?? 'Penjual' }}</div>
                            <div class="truncate text-xs font-semibold text-slate-500">{{ $property->user?->phone ?? 'Nomor tidak tersedia' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4 card p-6">
                <div class="text-sm font-extrabold text-slate-900">Simulasi KPR</div>
                <div class="mt-4 grid gap-3">
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Uang Muka</label>
                        <input id="dpInputDisplay" type="text" class="input mt-1" placeholder="100.000.000" />
                        <input id="dpInput" type="hidden" value="100000000" />
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">Jangka Waktu (tahun)</label>
                        <input id="termInput" type="number" class="input mt-1" value="15" min="1" max="30" />
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4 ring-1 ring-slate-200/70">
                        <div class="text-xs font-semibold text-slate-500">Estimasi Cicilan / bulan</div>
                        <div id="installment" class="mt-1 text-xl font-extrabold text-indigo-700">-</div>
                        <div class="mt-1 text-xs font-semibold text-slate-500">Asumsi bunga 8% per tahun</div>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script>
            const point = @json($point);
            const miniMap = L.map('miniMap', { zoomControl: false, attributionControl: false }).setView([point.lat, point.lng], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(miniMap);
            L.marker([point.lat, point.lng]).addTo(miniMap);

            // Force correct size: immediately + after layout settles
            miniMap.invalidateSize({ animate: false });
            setTimeout(() => miniMap.invalidateSize({ animate: false }), 200);
            setTimeout(() => miniMap.invalidateSize({ animate: false }), 600);
            window.addEventListener('load', () => miniMap.invalidateSize({ animate: false }));

            const price = {{ (float) $property->price }};
            const dpInput = document.getElementById('dpInput');
            const dpInputDisplay = document.getElementById('dpInputDisplay');
            const termInput = document.getElementById('termInput');
            const out = document.getElementById('installment');

            function formatCurrency(value) {
                return new Intl.NumberFormat('id-ID').format(Math.round(value));
            }

            function calc() {
                const dp = Math.max(0, Number(dpInput.value || 0));
                const years = Math.max(1, Number(termInput.value || 1));
                const principal = Math.max(0, price - dp);
                const r = 0.08 / 12;
                const n = years * 12;
                const m = principal === 0 ? 0 : (principal * r) / (1 - Math.pow(1 + r, -n));
                out.textContent = `Rp ${formatCurrency(m)}`;
            }

            function formatNumberString(str) {
                return str.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            }

            function updateDp() {
                let cleanVal = dpInputDisplay.value.replace(/\D/g, '');
                if (cleanVal === '') {
                    dpInput.value = '0';
                    dpInputDisplay.value = '';
                    calc();
                    return;
                }
                const num = parseInt(cleanVal, 10);
                dpInput.value = num;
                dpInputDisplay.value = formatNumberString(num);
                calc();
            }

            dpInputDisplay.addEventListener('input', updateDp);
            termInput.addEventListener('input', calc);

            // Initialize formatting
            if (dpInput.value) {
                const initialVal = parseInt(dpInput.value, 10);
                if (!isNaN(initialVal)) {
                    dpInputDisplay.value = formatNumberString(initialVal);
                }
            }
            calc();
        </script>
    @endpush
</x-layouts.app>

