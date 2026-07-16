<x-layouts.app title="Panel Penjual"
    description="Kelola portofolio listing properti, analisis persaingan harga kompetitor, dan pantau peta panas pencarian calon pembeli di Samarinda Properti GIS.">
    
    <div x-data="{ sidebarOpen: false }" class="relative min-h-screen bg-brand-bg">
        <div class="grid gap-0 lg:grid-cols-[260px_minmax(0,1fr)] min-h-screen">
            
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
                   class="fixed inset-y-0 left-0 z-[1045] w-[260px] max-w-[85vw] bg-white border-r border-slate-200/60 transform transition-transform duration-300 ease-out lg:static lg:translate-x-0 lg:w-[260px] lg:h-screen lg:sticky lg:top-0 overflow-y-auto rounded-r-2xl lg:rounded-none flex flex-col justify-between shrink-0">
                
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <!-- Logo section -->
                        <div class="h-[72px] border-b border-slate-100 px-5 flex items-center gap-2.5">
                            <div class="grid size-8 place-items-center rounded-lg bg-brand-primary text-white shrink-0">
                                <i class="ti ti-map-pin text-base"></i>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="text-xs font-black text-slate-900 uppercase tracking-wider font-display truncate">Seller Panel</span>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest leading-none mt-0.5 truncate">Samarinda Properti</span>
                            </div>
                        </div>

                        <!-- Navigation Items -->
                        <nav class="p-4 space-y-1">
                            <a href="{{ route('seller.listings.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('seller.listings.index') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-building text-lg shrink-0"></i>
                                <span>Listing Saya</span>
                            </a>
                            
                            <a href="{{ route('seller.listings.create') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('seller.listings.create') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-plus text-lg shrink-0"></i>
                                <span>Tambah Baru</span>
                            </a>
                            
                            <a href="{{ route('seller.listings.import.show') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('seller.listings.import.show') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-file-upload text-lg shrink-0"></i>
                                <span>Unggah Massal</span>
                            </a>
                            
                            <a href="{{ route('seller.competitor-analysis.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('seller.competitor-analysis.*') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-chart-bar text-lg shrink-0"></i>
                                <span>Analisis Kompetitor</span>
                            </a>
                            
                            <a href="{{ route('seller.market-demands.heatmap') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('seller.market-demands.heatmap') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-flame text-lg shrink-0"></i>
                                <span>Peta Panas</span>
                            </a>
                            
                            <a href="{{ route('seller.profile.edit') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('seller.profile.edit') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-settings text-lg shrink-0"></i>
                                <span>Pengaturan</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Bottom profile details & link -->
                    <div class="p-4 border-t border-slate-100 space-y-3">
                        <a href="{{ route('sellers.show', auth()->id()) }}" target="_blank"
                           class="flex items-center justify-center gap-1.5 text-xs font-bold text-brand-primary bg-brand-primary/5 hover:bg-brand-primary/10 py-2.5 rounded-xl border border-brand-primary/10 transition-all text-center">
                            <i class="ti ti-external-link text-sm"></i>
                            <span>Lihat Profil Publik</span>
                        </a>
                        
                        <div class="flex items-center gap-2.5 px-1.5 py-1">
                            <div class="grid size-8 place-items-center rounded-xl bg-brand-primary font-black text-white text-xs shrink-0 shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-xs font-bold text-slate-800 leading-snug">{{ auth()->user()->company_name ?? auth()->user()->name }}</div>
                                <div class="truncate text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none mt-0.5">{{ auth()->user()->phone ?? 'Akun Seller' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Content Area -->
            <main class="p-6 lg:p-8 min-w-0">
                <!-- Mobile Navigation Trigger & Title Indicator -->
                <div class="mb-6 flex items-center justify-between gap-4 rounded-2xl bg-white p-3.5 border border-slate-200/60 shadow-xs lg:hidden">
                    <button @click="sidebarOpen = true" 
                            class="flex items-center gap-1.5 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 active:bg-slate-200 px-3.5 py-2.5 rounded-xl border border-slate-200 transition-all cursor-pointer">
                        <i class="ti ti-menu-2 text-base text-slate-500"></i>
                        <span>Menu</span>
                    </button>
                    
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="grid size-7 place-items-center rounded-lg bg-brand-primary text-white shrink-0">
                            <i class="ti ti-map-pin text-sm"></i>
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

                    <a href="{{ route('seller.listings.create') }}" class="btn btn-primary text-xs px-3.5 py-2 shrink-0 flex items-center gap-1 border-0 font-bold shadow-xs">
                        <i class="ti ti-plus text-sm"></i>
                        <span class="hidden xs:inline">Tambah</span>
                    </a>
                </div>

                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.app>
