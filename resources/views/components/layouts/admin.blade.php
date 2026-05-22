<x-layouts.app>
    <div class="grid gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
        {{-- Sidebar --}}
        <aside class="card p-4">
            <div class="px-2 pt-2 text-xs font-extrabold uppercase tracking-widest text-slate-400">Admin Panel</div>
            <nav class="mt-3 grid gap-1">
                <a href="{{ route('admin.dashboard') }}"
                   class="rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-50' }} px-3 py-2 text-sm font-semibold">
                    Dashboard
                </a>
                <a href="{{ route('admin.listings.index') }}"
                   class="rounded-xl {{ request()->routeIs('admin.listings.*') ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-50' }} px-3 py-2 text-sm font-semibold">
                    Semua Listing
                </a>
                <a href="{{ route('admin.amenities.index') }}"
                   class="rounded-xl {{ request()->routeIs('admin.amenities.*') ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-50' }} px-3 py-2 text-sm font-semibold">
                    Fasilitas (POI)
                </a>
                <a href="{{ route('admin.flood-zones.index') }}"
                   class="rounded-xl {{ request()->routeIs('admin.flood-zones.*') ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-50' }} px-3 py-2 text-sm font-semibold">
                    Zona Rawan Banjir
                </a>
                <div class="my-2 border-t border-slate-100"></div>
                <a href="{{ route('seller.listings.index') }}"
                   class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-500 hover:bg-slate-50">
                    ← Kembali ke Seller
                </a>
            </nav>
        </aside>

        {{-- Konten --}}
        <div>
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
