@php
    $items = [
        ['label' => 'Beranda', 'href' => route('home'), 'icon' => 'ti ti-home'],
        ['label' => 'Properti', 'href' => route('properties.index'), 'icon' => 'ti ti-building-cottage'],
        ['label' => 'Eksplorasi Peta', 'href' => route('explore'), 'icon' => 'ti ti-map-2'],
        ['label' => 'Pasang Iklan', 'href' => route('seller.listings.create'), 'icon' => 'ti ti-plus'],
    ];
@endphp

<header x-data="{ mobileMenuOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 0 })"
    :class="scrolled ? 'border-slate-200/60 shadow-xs bg-white/95' : 'border-transparent bg-white/80'"
    class="sticky top-0 z-[1050] backdrop-blur-lg transition-all duration-300 border-b"
    @keydown.escape.window="mobileMenuOpen = false">
    
    <div class="flex h-16 lg:h-[72px] w-full items-center justify-between gap-3 px-6 sm:px-10 lg:px-16 xl:px-24">
        {{-- Logo (Max height 40px / h-10) --}}
        <a href="{{ route('home') }}" class="flex items-center shrink-0 group">
            <img src="{{ asset('images/logo.png') }}" alt="Samarinda Properti Logo" class="h-10 w-auto transition-transform duration-300 group-hover:scale-105" />
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 lg:flex">
            @foreach ($items as $item)
                <a href="{{ $item['href'] }}"
                    class="relative py-1 transition-colors hover:text-brand-primary {{ request()->url() === $item['href'] ? 'text-brand-primary font-semibold' : '' }}">
                    {{ $item['label'] }}
                    @if (request()->url() === $item['href'])
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-4 rounded-full bg-brand-accent animate-scale-in"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Desktop Auth Actions --}}
        <div class="hidden items-center gap-2 lg:flex shrink-0">
            @auth
                {{-- Favorit (Tabler Icon) --}}
                <a href="{{ route('favorites.index') }}"
                    class="grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-500"
                    title="Properti Tersimpan">
                    <i class="ti ti-heart text-lg"></i>
                </a>

                {{-- Notification Dropdown --}}
                @php
                    $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->get();
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="relative grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-brand-primary cursor-pointer"
                        title="Notifikasi">
                        <i class="ti ti-bell text-lg"></i>
                        @if ($unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 size-2 rounded-full bg-brand-accent ring-2 ring-white animate-pulse"></span>
                        @endif
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-[360px] rounded-2xl bg-white p-4 shadow-2xl border border-slate-200/50 z-[9999]">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                            <span class="text-xs font-black text-slate-900 uppercase">Notifikasi</span>
                            <span class="text-[10px] bg-brand-primary/10 text-brand-primary font-bold px-2 py-0.5 rounded-full">
                                {{ $unreadNotifications->count() }} Baru
                            </span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto divide-y divide-slate-50">
                            @forelse ($unreadNotifications as $notif)
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}"
                                    class="py-2.5 hover:bg-slate-50 transition-all rounded-lg px-2 block">
                                    @csrf
                                    <button type="submit" class="text-left w-full block cursor-pointer">
                                        <div class="text-[11px] font-bold text-slate-900 leading-snug flex items-start gap-1.5">
                                            <span class="size-1.5 rounded-full bg-brand-accent mt-1.5 shrink-0 animate-pulse"></span>
                                            <span>{{ $notif->title }}</span>
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-semibold mt-1 pl-3 leading-relaxed">
                                            {{ $notif->message }}</div>
                                        <div class="text-[8px] text-slate-400 font-bold mt-1.5 pl-3 font-mono">
                                            {{ $notif->created_at->diffForHumans() }}</div>
                                    </button>
                                </form>
                            @empty
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <div class="size-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-3 border border-slate-100">
                                        <i class="ti ti-bell-off text-3xl"></i>
                                    </div>
                                    <div class="text-xs font-extrabold text-slate-700 font-display">Belum ada notifikasi</div>
                                    <div class="text-[10px] text-slate-400 mt-1">Kami akan mengabari Anda setelah ada aktivitas baru.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}"
                        class="btn btn-outline text-brand-primary hover:border-brand-primary/30 hover:bg-brand-primary/5 text-xs px-3">Admin</a>
                @endif
                <a href="{{ route('seller.listings.index') }}" class="btn btn-outline text-xs px-3">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary text-xs px-3">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-accent">Daftar</a>
            @endauth
        </div>

        {{-- Mobile Right Section (notifications + hamburger) --}}
        <div class="flex items-center gap-1 lg:hidden">
            @auth
                {{-- Mobile Notification --}}
                @php
                    $unreadNotifications = isset($unreadNotifications) ? $unreadNotifications : auth()->user()->notifications()->where('is_read', false)->get();
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="relative grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-brand-primary cursor-pointer">
                        <i class="ti ti-bell text-lg"></i>
                        @if ($unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 size-2 rounded-full bg-brand-accent ring-2 ring-white animate-pulse"></span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-[calc(100vw-2rem)] max-w-xs rounded-2xl bg-white p-4 shadow-xl border border-slate-200/50 z-[9999]">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                            <span class="text-xs font-black text-slate-900 uppercase">Notifikasi</span>
                            <button @click="open = false" class="text-slate-400 hover:text-slate-600 p-0.5 cursor-pointer">
                                <i class="ti ti-x text-base"></i>
                            </button>
                        </div>
                        <div class="max-h-56 overflow-y-auto divide-y divide-slate-50">
                            @forelse ($unreadNotifications as $notif)
                                <form method="POST" action="{{ route('notifications.read', $notif->id) }}"
                                    class="py-2 hover:bg-slate-50 transition-all rounded-lg px-2 block">
                                    @csrf
                                    <button type="submit" class="text-left w-full block cursor-pointer">
                                        <div class="text-[11px] font-bold text-slate-900 leading-snug">{{ $notif->title }}</div>
                                        <div class="text-[10px] text-slate-500 font-semibold mt-1 leading-relaxed">
                                            {{ $notif->message }}</div>
                                        <div class="text-[8px] text-slate-400 font-bold mt-1.5">
                                            {{ $notif->created_at->diffForHumans() }}</div>
                                    </button>
                                </form>
                            @empty
                                <div class="py-12 flex flex-col items-center justify-center text-center">
                                    <div class="size-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-3 border border-slate-100">
                                        <i class="ti ti-bell-off text-3xl"></i>
                                    </div>
                                    <div class="text-xs font-extrabold text-slate-700 font-display">Belum ada notifikasi</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Hamburger Button (Tabler Icon) --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="grid size-9 place-items-center rounded-xl text-slate-600 transition hover:bg-slate-100 cursor-pointer"
                :aria-expanded="mobileMenuOpen" aria-label="Toggle menu">
                <i x-show="!mobileMenuOpen" class="ti ti-menu-2 text-lg"></i>
                <i x-show="mobileMenuOpen" x-cloak class="ti ti-x text-lg"></i>
            </button>
        </div>
    </div>

    {{-- Backdrop Overlay for drawer --}}
    <div x-show="mobileMenuOpen" x-transition:opacity
        class="fixed inset-0 z-[1040] bg-black/20 backdrop-blur-xs lg:hidden"
        @click="mobileMenuOpen = false" x-cloak></div>

    {{-- Mobile Menu Drawer (Slides from Right) --}}
    <div x-show="mobileMenuOpen" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-[1045] w-full max-w-[280px] bg-white shadow-2xl flex flex-col justify-between p-6 lg:hidden"
        x-cloak>
        
        <div>
            {{-- Header drawer --}}
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-4">
                <a href="{{ route('home') }}" class="flex items-center shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto" />
                </a>
                <button @click="mobileMenuOpen = false" class="grid size-8 place-items-center rounded-xl hover:bg-slate-100 text-slate-500 cursor-pointer">
                    <i class="ti ti-x text-lg"></i>
                </button>
            </div>

            {{-- User Avatar / Profile Section --}}
            @auth
                <div class="flex items-center gap-3 bg-slate-50 border border-slate-200/50 rounded-2xl p-3 mb-4">
                    <div class="grid size-10 place-items-center rounded-full bg-brand-primary/10 text-brand-primary text-sm font-bold shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            @endauth

            {{-- Nav Links --}}
            <nav class="grid gap-1">
                @foreach ($items as $item)
                    <a href="{{ $item['href'] }}" @click="mobileMenuOpen = false" 
                        class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition
                              {{ request()->url() === $item['href']
                                ? 'bg-brand-primary/5 text-brand-primary font-semibold'
                                : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                        <i class="{{ $item['icon'] }} text-lg shrink-0 {{ request()->url() === $item['href'] ? 'text-brand-primary' : 'text-slate-400' }}"></i>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Bottom drawer items --}}
        <div class="border-t border-slate-100 pt-4 space-y-2">
            @auth
                <a href="{{ route('seller.listings.index') }}" @click="mobileMenuOpen = false"
                    class="btn btn-outline w-full justify-center text-xs">
                    <i class="ti ti-layout-dashboard text-base"></i>
                    <span>Dashboard</span>
                </a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false"
                        class="btn btn-outline w-full justify-center text-xs text-brand-primary">
                        <i class="ti ti-shield text-base"></i>
                        <span>Admin</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full justify-center text-sm">
                        <i class="ti ti-logout text-base"></i>
                        <span>Keluar</span>
                    </button>
                </form>
            @else
                <div class="grid gap-2">
                    <a href="{{ route('login') }}" @click="mobileMenuOpen = false" class="btn btn-outline w-full justify-center">Masuk</a>
                    <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="btn btn-accent w-full justify-center">Daftar</a>
                </div>
            @endauth
        </div>
    </div>
</header>