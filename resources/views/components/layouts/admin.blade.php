<x-layouts.app>
    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        {{-- Sidebar --}}
        <aside class="card p-4 h-fit">
            <div class="px-3 pt-2 pb-1 text-xs font-bold uppercase tracking-wider text-slate-400/80">Admin Console</div>
            <nav class="mt-3 grid gap-1.5">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50/80 text-indigo-700 font-bold ring-1 ring-indigo-100/50' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="size-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('admin.listings.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('admin.listings.*') ? 'bg-indigo-50/80 text-indigo-700 font-bold ring-1 ring-indigo-100/50' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="size-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Semua Listing</span>
                </a>

                <a href="{{ route('admin.amenities.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('admin.amenities.*') ? 'bg-indigo-50/80 text-indigo-700 font-bold ring-1 ring-indigo-100/50' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="size-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Fasilitas (POI)</span>
                </a>

                <a href="{{ route('admin.flood-zones.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all {{ request()->routeIs('admin.flood-zones.*') ? 'bg-indigo-50/80 text-indigo-700 font-bold ring-1 ring-indigo-100/50' : 'text-slate-600 font-medium hover:bg-slate-50 hover:text-slate-900' }}">
                    <svg class="size-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Zona Rawan Banjir</span>
                </a>

                <div class="my-3 border-t border-slate-100"></div>

                <a href="{{ route('seller.listings.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all">
                    <svg class="size-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Seller</span>
                </a>
            </nav>
        </aside>

        {{-- Konten --}}
        <div class="min-w-0">
            {{ $slot }}
        </div>
    </div>
</x-layouts.app>
