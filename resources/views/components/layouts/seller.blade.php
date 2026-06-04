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
                   class="fixed inset-y-0 left-0 z-[1045] w-72 max-w-[85vw] bg-white p-5 shadow-2xl border-r border-slate-200/80 transform transition-transform duration-300 ease-out lg:static lg:w-auto lg:max-w-none lg:shadow-sm lg:border lg:border-slate-200/50 lg:rounded-2xl lg:p-5 lg:h-fit lg:block overflow-y-auto bg-white">
                
                <!-- Mobile Header inside Sidebar -->
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-100 lg:hidden">
                    <div class="flex items-center gap-2">
                        <div class="grid size-8 place-items-center rounded-lg bg-brand-primary text-white">
                            <span class="text-xs font-bold">SP</span>
                        </div>
                        <span class="text-sm font-extrabold text-slate-900">Dashboard Penjual</span>
                    </div>
                    <button @click="sidebarOpen = false" 
                            class="grid size-9 place-items-center rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition cursor-pointer">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest text-slate-400/90 hidden lg:block">Dashboard Penjual</div>
                
                <nav class="mt-4 space-y-1">
                    <a href="{{ route('seller.listings.index') }}" 
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('seller.listings.index') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('seller.listings.index') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span>Listing Saya</span>
                    </a>
                    
                    <a href="{{ route('seller.listings.create') }}" 
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('seller.listings.create') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('seller.listings.create') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Tambah Baru</span>
                    </a>
                    
                    <a href="{{ route('seller.listings.import.show') }}" 
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('seller.listings.import.show') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('seller.listings.import.show') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>Unggah Massal (CSV)</span>
                    </a>
                    
                    <a href="{{ route('seller.competitor-analysis.index') }}" 
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('seller.competitor-analysis.*') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('seller.competitor-analysis.*') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Analisis Kompetitor</span>
                    </a>
                    
                    <a href="{{ route('seller.market-demands.heatmap') }}" 
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('seller.market-demands.heatmap') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('seller.market-demands.heatmap') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Peta Panas Permintaan</span>
                    </a>
                    
                    <a href="{{ route('seller.profile.edit') }}" 
                       @click="sidebarOpen = false"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm transition-all border-l-4 {{ request()->routeIs('seller.profile.edit') ? 'border-brand-accent bg-brand-primary/5 text-brand-primary font-bold shadow-xs' : 'border-transparent text-slate-600 font-semibold hover:bg-slate-50/85 hover:text-slate-900 hover:translate-x-1' }}">
                        <svg class="size-4.5 shrink-0 {{ request()->routeIs('seller.profile.edit') ? 'text-brand-primary' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Pengaturan</span>
                    </a>
                </nav>

                {{-- Public Profile Banner Link --}}
                <div class="mt-6 border-t border-slate-100 pt-4">
                    <a href="{{ route('sellers.show', auth()->id()) }}" target="_blank"
                       class="flex flex-col gap-1 text-[11px] font-bold text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 p-3 rounded-xl border border-brand-primary/10 transition-all text-center group">
                        <span class="flex items-center justify-center gap-1">
                            <span>Lihat Profil Publik Anda</span>
                            <svg class="size-3 transition-transform group-hover:translate-x-0.5 text-brand-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                            </svg>
                        </span>
                        <span class="text-[9px] text-slate-400 font-medium">Lihat peta listing agensi Anda</span>
                    </a>
                </div>
            </aside>

            {{-- Main Content --}}
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
                    
                    {{-- Current page indicator --}}
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="grid size-7 place-items-center rounded-lg bg-brand-primary text-white shrink-0">
                            <span class="text-[10px] font-bold">SP</span>
                        </div>
                        <span class="text-xs font-extrabold text-slate-700 truncate">
                            @if(request()->routeIs('seller.listings.index')) Listing Saya
                            @elseif(request()->routeIs('seller.listings.create')) Tambah Listing
                            @elseif(request()->routeIs('seller.listings.import.show')) Unggah Massal
                            @elseif(request()->routeIs('seller.competitor-analysis.*')) Analisis Kompetitor
                            @elseif(request()->routeIs('seller.market-demands.heatmap')) Peta Panas
                            @elseif(request()->routeIs('seller.profile.edit')) Pengaturan
                            @else Dashboard Penjual
                            @endif
                        </span>
                    </div>

                    <a href="{{ route('seller.listings.create') }}" class="btn btn-primary text-xs px-3 py-2 shrink-0">
                        <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden xs:inline">Tambah</span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</x-layouts.app>
