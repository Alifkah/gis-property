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
        $placeholderSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 250" fill="none">'
            . '<rect width="400" height="250" fill="url(#g)"/>'
            . '<defs>'
            . '<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="#4f46e5" stop-opacity="0.12"/>'
            . '<stop offset="100%" stop-color="#6366f1" stop-opacity="0.04"/>'
            . '</linearGradient>'
            . '</defs>'
            . '<path d="M170 140 l30-25 30 25 M180 132 v18 h40 v-18" stroke="#4f46e5" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.4"/>'
            . '<text x="50%" y="185" dominant-baseline="middle" text-anchor="middle" fill="#4338ca" font-family="system-ui,-apple-system,sans-serif" font-weight="800" font-size="12" letter-spacing="1" opacity="0.5">'
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

<div class="group card overflow-hidden transition hover:-translate-y-0.5 hover:shadow-md">
    <a href="{{ route('properties.show', ['property' => $property]) }}" class="block">
        <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
            <img src="{{ $imageUrl }}" alt="{{ $property->title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105 {{ $isSold ? 'opacity-60' : '' }}" loading="lazy" />
            <div class="absolute left-3 top-3 flex flex-wrap gap-2">
                @if ($isSold)
                    <span class="inline-flex items-center rounded-full bg-slate-700 px-2 py-1 text-xs font-semibold text-white">Terjual</span>
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
    </a>

    {{-- Tombol Favorit --}}
    @if ($showFavorite)
        @auth
            <button
                type="button"
                data-favorite-id="{{ $property->id }}"
                onclick="toggleFavorite(this, {{ $property->id }})"
                style="position:absolute;top:12px;right:12px"
                class="fav-btn grid size-9 place-items-center rounded-xl shadow-sm ring-1 transition {{ $isFavorited ? 'bg-rose-500 text-white ring-rose-400' : 'bg-white/90 text-slate-700 ring-slate-200/70 hover:bg-rose-50 hover:text-rose-500' }}"
                title="{{ $isFavorited ? 'Hapus dari favorit' : 'Simpan ke favorit' }}"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="{{ $isFavorited ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" />
                </svg>
            </button>
        @else
            <a
                href="{{ route('login') }}"
                style="position:absolute;top:12px;right:12px"
                class="grid size-9 place-items-center rounded-xl bg-white/90 text-slate-700 shadow-sm ring-1 ring-slate-200/70 hover:bg-rose-50 hover:text-rose-500 transition"
                title="Login untuk menyimpan"
            >
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" />
                </svg>
            </a>
        @endauth
    @endif

    <a href="{{ route('properties.show', ['property' => $property]) }}" class="block p-4">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="truncate text-sm font-bold text-slate-900">{{ $property->title }}</div>
                <div class="mt-1 truncate text-xs font-semibold text-slate-500">{{ $property->district_name ?? 'Kota Samarinda' }}</div>
            </div>
            <div class="shrink-0 text-right">
                <div class="text-sm font-extrabold {{ $isSold ? 'text-slate-400' : 'text-brand-accent' }}">Rp {{ number_format((float) $property->price, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-2 text-xs font-semibold text-slate-600">
            <div class="flex items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-2 ring-1 ring-slate-200/60" title="Kamar Tidur">
                <svg class="size-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 4v16" />
                    <path d="M2 17h20" />
                    <path d="M22 8v12" />
                    <path d="M2 8h20" />
                    <path d="M6 12h4a2 2 0 0 0 2-2V8H4v2a2 2 0 0 0 2 2Z" />
                </svg>
                <span>{{ (int) $property->bedroom }}</span>
            </div>
            <div class="flex items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-2 ring-1 ring-slate-200/60" title="Kamar Mandi">
                <svg class="size-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 6 6.5 3.5a1.5 1.5 0 0 0-1-1C4.3 2.5 3 4.3 3.5 5.5l2 2" />
                    <path d="M3 11h18a2 2 0 0 1 2 2v2a6 6 0 0 1-6 6H7a6 6 0 0 1-6-6v-2a2 2 0 0 1-2-2Z" />
                    <path d="M7 21v2M17 21v2" />
                </svg>
                <span>{{ (int) $property->bathroom }}</span>
            </div>
            <div class="flex items-center gap-1.5 rounded-xl bg-slate-50 px-2 py-2 ring-1 ring-slate-200/60" title="Luas Tanah">
                <svg class="size-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h7v7H4z" /><path d="M13 13h7v7h-7z" /><path d="M13 4h7v7h-7z" /><path d="M4 13h7v7H4z" />
                </svg>
                <span>{{ (int) $property->land_area }} m²</span>
            </div>
        </div>
    </a>
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
                    const svg = btn.querySelector('svg');
                    if (data.favorited) {
                        btn.classList.add('bg-rose-500', 'text-white', 'ring-rose-400');
                        btn.classList.remove('bg-white/90', 'text-slate-700', 'ring-slate-200/70', 'hover:bg-rose-50', 'hover:text-rose-500');
                        svg.setAttribute('fill', 'currentColor');
                        btn.title = 'Hapus dari favorit';
                    } else {
                        btn.classList.remove('bg-rose-500', 'text-white', 'ring-rose-400');
                        btn.classList.add('bg-white/90', 'text-slate-700', 'ring-slate-200/70', 'hover:bg-rose-50', 'hover:text-rose-500');
                        svg.setAttribute('fill', 'none');
                        btn.title = 'Simpan ke favorit';
                    }
                })
                .catch(() => alert('Gagal memproses. Coba lagi.'));
            }
        </script>
    @endpush
@endonce
