<x-layouts.app>
    <div x-data="{ sidebarOpen: false }" class="relative">
        <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
            
            <!-- Mobile Sidebar Backdrop Overlay -->
            <div x-show="sidebarOpen" 
                 @click="sidebarOpen = false" 
                 x-cloak 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-[1040] bg-slate-900/50 backdrop-blur-sm lg:hidden">
            </div>

            <!-- Sidebar -->
            <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                   class="fixed inset-y-0 left-0 z-[1045] w-72 max-w-[85vw] bg-white p-5 shadow-2xl border-r border-slate-200/80 transform transition-transform duration-300 ease-out lg:static lg:w-auto lg:max-w-none lg:shadow-sm lg:border lg:border-slate-200/50 lg:rounded-2xl lg:p-5 lg:h-fit lg:block overflow-y-auto">
                
                <!-- Mobile Header inside Sidebar -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 lg:hidden">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-rose-600 text-white">
                            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-sm font-extrabold text-slate-900">Admin Console</span>
                    </div>
                    <button @click="sidebarOpen = false" 
                            class="grid size-9 place-items-center rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400/90 hidden lg:block">Admin Console</div>
                
                <nav class="mt-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('admin.dashboard') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('admin.dashboard') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    
                    <a href="{{ route('admin.listings.index') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('admin.listings.*') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('admin.listings.*') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Semua Listing</span>
                    </a>

                    <a href="{{ route('admin.amenities.index') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('admin.amenities.*') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('admin.amenities.*') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Fasilitas (POI)</span>
                    </a>

                    <a href="{{ route('admin.flood-zones.index') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('admin.flood-zones.*') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('admin.flood-zones.*') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Zona Rawan Banjir</span>
                    </a>

                    <div class="my-3 border-t border-slate-100"></div>

                    <a href="{{ route('seller.listings.index') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 border-transparent text-slate-500 font-semibold hover:bg-slate-50 hover:text-slate-800 hover:translate-x-1">
                        <svg class="size-4.5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali ke Seller</span>
                    </a>
                </nav>
            </aside>

            {{-- Konten --}}
            <div class="min-w-0">
                <!-- Mobile Sidebar Trigger -->
                <div class="mb-4 flex items-center justify-between gap-3 rounded-2xl bg-white p-3 sm:p-4 border border-slate-200/60 shadow-sm lg:hidden">
                    <button @click="sidebarOpen = true" 
                            class="flex items-center gap-2 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 px-3.5 py-2.5 rounded-xl border border-slate-200 transition-all cursor-pointer">
                        <svg class="size-4.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <span>Menu</span>
                    </button>
                    
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="grid size-7 place-items-center rounded-lg bg-rose-600 text-white shrink-0">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-xs font-extrabold text-slate-700 truncate">Admin Console</span>
                    </div>

                    <a href="{{ route('home') }}" class="btn btn-outline text-xs px-3 py-2 shrink-0">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.app>
