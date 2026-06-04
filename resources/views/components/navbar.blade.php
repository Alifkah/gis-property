@php
    $items = [
        ['label' => 'Beranda', 'href' => route('home'), 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Properti', 'href' => route('properties.index'), 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
        ['label' => 'Eksplorasi Peta', 'href' => route('explore'), 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
        ['label' => 'Pasang Iklan', 'href' => route('seller.listings.create'), 'icon' => 'M12 4v16m8-8H4'],
    ];
@endphp

<header x-data="{ mobileMenuOpen: false }"
    class="sticky top-0 z-[1050] border-b border-slate-200/50 bg-white/80 backdrop-blur-lg"
    @keydown.escape.window="mobileMenuOpen = false">
    <div class="mx-auto flex h-14 w-full max-w-7xl items-center justify-between gap-3 px-4 sm:h-16 sm:px-6 lg:px-8">
        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 shrink-0 group">
            <div class="grid size-9 place-items-center rounded-xl bg-brand-primary text-white shadow-sm shadow-brand-primary/20 transition-transform duration-300 group-hover:scale-105">
                <span class="text-sm font-bold tracking-wider">SP</span>
            </div>
            <div class="leading-tight">
                <div class="text-sm font-black text-slate-900 tracking-tight transition-colors group-hover:text-brand-primary">Samarinda Properti</div>
                <div class="text-[10px] font-bold text-slate-500 tracking-wider">Web-Property Marketplace</div>
            </div>
        </a>

        {{-- Desktop Nav --}}
        <nav class="hidden items-center gap-6 text-sm font-bold text-slate-600 lg:flex">
            @foreach ($items as $item)
                <a href="{{ $item['href'] }}"
                    class="relative py-1 transition-colors hover:text-brand-primary {{ request()->url() === $item['href'] ? 'text-brand-primary' : '' }}">
                    {{ $item['label'] }}
                    @if (request()->url() === $item['href'])
                        <span class="absolute bottom-0 left-1/2 -translate-x-1/2 h-1 w-4 rounded-full bg-brand-accent"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Desktop Auth Actions --}}
        <div class="hidden items-center gap-2 lg:flex shrink-0">
            @auth
                {{-- Favorit --}}
                <a href="{{ route('favorites.index') }}"
                    class="grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-500"
                    title="Properti Tersimpan">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path
                            d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>

                {{-- Notification Dropdown --}}
                @php
                    $unreadNotifications = auth()->user()->notifications()->where('is_read', false)->get();
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="relative grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-brand-primary cursor-pointer"
                        title="Notifikasi">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if ($unreadNotifications->count() > 0)
                            <span
                                class="absolute top-1.5 right-1.5 size-2 rounded-full bg-brand-accent ring-2 ring-white animate-pulse"></span>
                        @endif
                    </button>

                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-80 rounded-2xl bg-white p-4 shadow-xl border border-slate-200/50 z-[9999]">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                            <span class="text-xs font-black text-slate-900 uppercase">Notifikasi</span>
                            <span class="text-[10px] bg-brand-primary/10 text-brand-primary font-bold px-2 py-0.5 rounded-full">
                                {{ $unreadNotifications->count() }} Baru
                            </span>
                        </div>
                        <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
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
                                <div class="py-6 text-center text-xs font-semibold text-slate-400">Tidak ada notifikasi baru.
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
                {{-- Mobile Notification (compact) --}}
                @php
                    $unreadNotifications = isset($unreadNotifications) ? $unreadNotifications : auth()->user()->notifications()->where('is_read', false)->get();
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="relative grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-slate-100 hover:text-brand-primary cursor-pointer">
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if ($unreadNotifications->count() > 0)
                            <span
                                class="absolute top-1.5 right-1.5 size-2 rounded-full bg-brand-accent ring-2 ring-white animate-pulse"></span>
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition x-cloak
                        class="absolute right-0 mt-2 w-[calc(100vw-2rem)] max-w-xs rounded-2xl bg-white p-4 shadow-xl border border-slate-200/50 z-[9999]">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2 mb-2">
                            <span class="text-xs font-black text-slate-900 uppercase">Notifikasi</span>
                            <button @click="open = false" class="text-slate-400 hover:text-slate-600 p-0.5">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
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
                                <div class="py-4 text-center text-xs font-semibold text-slate-400">Tidak ada notifikasi baru.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endauth

            {{-- Hamburger Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="grid size-9 place-items-center rounded-xl text-slate-600 transition hover:bg-slate-100 cursor-pointer"
                :aria-expanded="mobileMenuOpen" aria-label="Toggle menu">
                <svg x-show="!mobileMenuOpen" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                    stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak class="size-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Drawer --}}
    <div x-show="mobileMenuOpen" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" @click.outside="mobileMenuOpen = false"
        class="lg:hidden border-t border-slate-100 bg-white shadow-lg">
        <div class="mx-auto max-w-7xl px-4 py-3 sm:px-6">
            {{-- Nav Links --}}
            <nav class="grid gap-0.5">
                @foreach ($items as $item)
                            <a href="{{ $item['href'] }}" @click="mobileMenuOpen = false" class="flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-semibold transition
                                          {{ request()->url() === $item['href']
                    ? 'bg-brand-primary/5 text-brand-primary'
                    : 'text-slate-700 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="size-4.5 shrink-0 {{ request()->url() === $item['href'] ? 'text-brand-primary' : 'text-slate-400' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                </svg>
                                {{ $item['label'] }}
                            </a>
                @endforeach
            </nav>

            {{-- Auth Actions --}}
            <div class="mt-3 pt-3 border-t border-slate-100">
                @auth
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 px-3 py-2">
                            <div
                                class="grid size-8 place-items-center rounded-full bg-brand-primary/10 text-brand-primary text-xs font-bold shrink-0">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate">{{ auth()->user()->name }}</div>
                                <div class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</div>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            @if (auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" @click="mobileMenuOpen = false"
                                    class="btn btn-outline text-xs justify-center">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Admin
                                </a>
                            @endif
                            <a href="{{ route('seller.listings.index') }}" @click="mobileMenuOpen = false"
                                class="btn btn-outline text-xs justify-center {{ auth()->user()->isAdmin() ? '' : 'col-span-2' }}">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                Dashboard
                            </a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-full justify-center text-sm">
                                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-2">
                        <a href="{{ route('login') }}" @click="mobileMenuOpen = false"
                            class="btn btn-outline w-full justify-center">Masuk</a>
                        <a href="{{ route('register') }}" @click="mobileMenuOpen = false"
                            class="btn btn-accent w-full justify-center">Daftar</a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</header>