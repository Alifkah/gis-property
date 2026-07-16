<x-layouts.app title="Konsol Admin"
    description="Kelola seluruh listing properti, kelola fasilitas Point of Interest (POI), gambar daerah genangan banjir Samarinda, dan pantau statistik makro aplikasi.">
    
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
                   class="fixed inset-y-0 left-0 z-[1045] w-[260px] max-w-[85vw] bg-white border-r border-slate-200/60 transform transition-transform duration-300 ease-out lg:static lg:translate-x-0 lg:w-[260px] lg:h-screen lg:sticky lg:top-0 overflow-y-auto rounded-r-2xl lg:rounded-2xl lg:border lg:border-slate-200/60 flex flex-col justify-between shrink-0">
                
                <div class="flex flex-col h-full justify-between">
                    <div>
                        <!-- Logo section with Admin badge -->
                        <div class="h-[72px] border-b border-slate-100 px-5 flex items-center justify-between gap-2.5">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="grid size-8 place-items-center rounded-lg bg-rose-600 text-white shrink-0 shadow-sm shadow-rose-600/10">
                                    <i class="ti ti-shield text-base"></i>
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <span class="text-xs font-black text-slate-900 uppercase tracking-wider font-display truncate">Admin Console</span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest leading-none mt-0.5 truncate">Samarinda Properti</span>
                                </div>
                            </div>
                            <span class="bg-rose-500 text-white rounded-full text-[10px] font-bold px-2 py-0.5 shrink-0 shadow-2xs">Admin</span>
                        </div>

                        <!-- Navigation Items -->
                        <nav class="p-4 space-y-1">
                            <a href="{{ route('admin.dashboard') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('admin.dashboard') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-dashboard text-lg shrink-0"></i>
                                <span>Dashboard</span>
                            </a>
                            
                            <a href="{{ route('admin.listings.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('admin.listings.*') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-building text-lg shrink-0"></i>
                                <span>Semua Listing</span>
                            </a>
                            
                            <a href="{{ route('admin.amenities.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('admin.amenities.*') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-map-pin text-lg shrink-0"></i>
                                <span>Fasilitas (POI)</span>
                            </a>
                            
                            <a href="{{ route('admin.flood-zones.index') }}" 
                               @click="sidebarOpen = false"
                               class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm transition-all border-l-2 {{ request()->routeIs('admin.flood-zones.*') ? 'border-brand-primary bg-brand-primary/5 text-brand-primary font-semibold' : 'border-transparent text-slate-600 hover:bg-slate-50' }}">
                                <i class="ti ti-alert-triangle text-lg shrink-0"></i>
                                <span>Zona Rawan Banjir</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Bottom panel and back link -->
                    <div class="p-4 border-t border-slate-100 space-y-3">
                        <a href="{{ route('seller.listings.index') }}"
                           class="flex items-center justify-center gap-1.5 text-xs font-bold text-slate-700 bg-slate-50 hover:bg-slate-100 py-2.5 rounded-xl border border-slate-200/50 transition-all text-center">
                            <i class="ti ti-arrow-left text-sm"></i>
                            <span>Kembali ke Seller</span>
                        </a>
                        
                        <div class="flex items-center gap-2.5 px-1.5 py-1">
                            <div class="grid size-8 place-items-center rounded-xl bg-rose-600 font-black text-white text-xs shrink-0 shadow-xs">
                                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-xs font-bold text-slate-800 leading-snug">{{ auth()->user()->name }}</div>
                                <div class="truncate text-[9px] text-slate-400 font-bold uppercase tracking-wider leading-none mt-0.5">{{ auth()->user()->email }}</div>
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
                        <div class="grid size-7 place-items-center rounded-lg bg-rose-600 text-white shrink-0">
                            <i class="ti ti-shield text-sm"></i>
                        </div>
                        <span class="text-xs font-extrabold text-slate-700 truncate">
                            @if(request()->routeIs('admin.dashboard')) Dashboard Admin
                            @elseif(request()->routeIs('admin.listings.index')) Kelola Listing
                            @elseif(request()->routeIs('admin.amenities.*')) Kelola Fasilitas POI
                            @elseif(request()->routeIs('admin.flood-zones.*')) Kawasan Rawan Banjir
                            @else Admin Console
                            @endif
                        </span>
                    </div>

                    <a href="{{ route('home') }}" class="btn btn-outline text-xs px-3.5 py-2 shrink-0 flex items-center justify-center">
                        <i class="ti ti-home text-sm text-slate-500"></i>
                    </a>
                </div>

                {{ $slot }}
            </main>
        </div>
    </div>
</x-layouts.app>
