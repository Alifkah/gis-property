@props([
    'property',
    'showFavorite' => true,
    'isFavorited'  => null,
])

@php
    $firstImage = $property->relationLoaded('images') ? $property->images->first() : null;
    $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
    if ($firstImage && (!$isLocalDisk || Storage::disk('public')->exists($firstImage->path))) {
        $imageUrl = Storage::disk('public')->url($firstImage->path);
    } else {
        $placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300" fill="none">'
            . '<rect width="400" height="300" fill="url(#g)"/>'
            . '<defs>'
            . '<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="#0F4C5C" stop-opacity="0.1"/>'
            . '<stop offset="100%" stop-color="#0F4C5C" stop-opacity="0.02"/>'
            . '</linearGradient>'
            . '</defs>'
            . '<path d="M170 160 l30-25 30 25 M180 152 v18 h40 v-18" stroke="#0F4C5C" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.3"/>'
            . '<text x="50%" y="210" dominant-baseline="middle" text-anchor="middle" fill="#0F4C5C" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.4">'
            . strtoupper($property->type)
            . '</text>'
            . '</svg>';
        $imageUrl = 'data:image/svg+xml;base64,' . base64_encode($placeholderSvg);
    }
    $isSold = ($property->status ?? 'Tersedia') === 'Terjual';

    if ($isFavorited === null) {
        $isFavorited = auth()->check()
            ? auth()->user()->favorites->contains('property_id', $property->id)
            : false;
    }
@endphp

<div class="group card overflow-hidden relative transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-white cursor-pointer">
    {{-- Main click link for the entire card (Z-index: 10) --}}
    <a href="{{ route('properties.show', ['property' => $property]) }}" class="absolute inset-0 z-10"><span class="sr-only">Lihat Detail {{ $property->title }}</span></a>

    {{-- Image Section --}}
    <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
        <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105 {{ $isSold ? 'opacity-60' : '' }}" loading="lazy" />
        
        {{-- Gradient overlay at the bottom of the image for readability of badges/price --}}
        <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent pointer-events-none"></div>

        <div class="absolute left-3 top-3 flex flex-wrap gap-2 z-20">
            @if ($isSold)
                <span class="inline-flex items-center rounded-full bg-slate-800/90 backdrop-blur-xs px-2.5 py-0.5 text-[10px] font-bold text-white uppercase tracking-wider">Terjual</span>
            @else
                @if (! empty($property->is_new))
                    <x-badge variant="new">Rumah Baru</x-badge>
                @endif
                @if (! empty($property->is_flood_safe))
                    <x-badge variant="safe">Bebas Banjir</x-badge>
                @endif
            @endif
        </div>
    </div>

    {{-- Favorite Button (Z-index: 20 so it remains clickable above the card link) --}}
    @if ($showFavorite)
        <div class="absolute top-3 right-3 z-20">
            @auth
                <button
                    type="button"
                    data-favorite-id="{{ $property->id }}"
                    onclick="toggleFavorite(this, {{ $property->id }})"
                    class="fav-btn grid size-9 place-items-center rounded-full shadow-sm transition-all duration-200 cursor-pointer hover:scale-110 active:scale-90
                           {{ $isFavorited ? 'bg-rose-500 text-white ring-1 ring-rose-400' : 'bg-white/95 text-slate-700 hover:bg-rose-50 hover:text-rose-500' }}"
                    title="{{ $isFavorited ? 'Hapus dari favorit' : 'Simpan ke favorit' }}"
                >
                    <i class="{{ $isFavorited ? 'ti ti-heart-filled text-white text-base' : 'ti ti-heart text-slate-700 text-base' }}"></i>
                </button>
            @else
                <a
                    href="{{ route('login') }}"
                    class="grid size-9 place-items-center rounded-full bg-white/95 text-slate-700 shadow-sm transition-all hover:scale-110 hover:bg-rose-50 hover:text-rose-500"
                    title="Login untuk menyimpan"
                >
                    <i class="ti ti-heart text-slate-700 text-base"></i>
                </a>
            @endauth
        </div>
    @endif

    {{-- Info Section --}}
    <div class="p-4 relative">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="truncate text-base font-bold text-slate-900 leading-snug">{{ $property->title }}</div>
                <div class="mt-1 truncate text-sm text-slate-500 flex items-center gap-1">
                    <i class="ti ti-map-pin text-slate-400 text-base"></i>
                    <span>{{ $property->district_name ?? 'Samarinda' }}</span>
                </div>
            </div>
            <div class="shrink-0 text-right">
                <div class="text-lg font-extrabold leading-snug {{ $isSold ? 'text-slate-400' : 'text-brand-accent' }}">
                    Rp {{ number_format((float) $property->price, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Stats Row (bed/bath/area) --}}
        <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-semibold text-slate-600 relative z-20">
            <div class="flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/40 px-2 py-2" title="Kamar Tidur">
                <i class="ti ti-bed text-slate-400 text-base"></i>
                <span>{{ (int) $property->bedroom }}</span>
            </div>
            <div class="flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/40 px-2 py-2" title="Kamar Mandi">
                <i class="ti ti-bath text-slate-400 text-base"></i>
                <span>{{ (int) $property->bathroom }}</span>
            </div>
            <div class="flex items-center justify-center gap-1.5 rounded-lg bg-slate-50 border border-slate-200/40 px-2 py-2" title="Luas Tanah">
                <i class="ti ti-maximize text-slate-400 text-base"></i>
                <span>{{ (int) $property->land_area }} m²</span>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
            function toggleFavorite(btn, propertyId) {
                fetch(`/favorites/${propertyId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                })
                .then(r => r.json())
                .then(data => {
                    const icon = btn.querySelector('i');
                    if (data.favorited) {
                        btn.classList.add('bg-rose-500', 'text-white', 'ring-rose-400');
                        btn.classList.remove('bg-white/95', 'text-slate-700', 'hover:bg-rose-50', 'hover:text-rose-500');
                        icon.className = 'ti ti-heart-filled text-white text-base';
                        btn.title = 'Hapus dari favorit';
                    } else {
                        btn.classList.remove('bg-rose-500', 'text-white', 'ring-rose-400');
                        btn.classList.add('bg-white/95', 'text-slate-700', 'hover:bg-rose-50', 'hover:text-rose-500');
                        icon.className = 'ti ti-heart text-slate-700 text-base';
                        btn.title = 'Simpan ke favorit';
                    }
                })
                .catch(() => alert('Gagal memproses. Coba lagi.'));
            }
        </script>
    @endpush
@endonce
