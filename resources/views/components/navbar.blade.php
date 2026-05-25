@php
    $items = [
        ['label' => 'Beranda',        'href' => route('home')],
        ['label' => 'Properti',       'href' => route('properties.index')],
        ['label' => 'Eksplorasi Peta','href' => route('explore')],
        ['label' => 'Pasang Iklan',   'href' => route('seller.listings.create')],
    ];
@endphp

<header class="sticky top-0 z-[1050] border-b border-slate-200 bg-white/85 backdrop-blur">
    <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="grid size-9 place-items-center rounded-xl bg-indigo-600 text-white">
                <span class="text-sm font-bold">SP</span>
            </div>
            <div class="leading-tight">
                <div class="text-sm font-extrabold text-slate-900">Samarinda Properti GIS</div>
                <div class="text-xs text-slate-500">Web-GIS Marketplace</div>
            </div>
        </a>

        <nav class="hidden items-center gap-8 text-sm font-semibold text-slate-600 md:flex">
            @foreach ($items as $item)
                <a href="{{ $item['href'] }}"
                   class="transition hover:text-indigo-700 {{ request()->url() === $item['href'] ? 'text-indigo-700' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            @auth
                {{-- Tombol Favorit --}}
                <a href="{{ route('favorites.index') }}"
                   class="grid size-9 place-items-center rounded-xl text-slate-500 transition hover:bg-rose-50 hover:text-rose-500"
                   title="Properti Tersimpan">
                    <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </a>
                @if (auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50">Admin Panel</a>
                @endif
                <a href="{{ route('seller.listings.index') }}" class="btn btn-outline">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary">Keluar</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline">Masuk</a>
                <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
            @endauth
        </div>
    </div>
</header>
