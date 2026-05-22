<x-layouts.app>
    <div class="mb-6 flex items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Properti Tersimpan</h1>
            <p class="mt-1 text-sm text-slate-500">Properti yang kamu simpan akan muncul di sini.</p>
        </div>
        <a href="{{ route('properties.index') }}" class="btn btn-outline">← Semua Properti</a>
    </div>

    @if ($properties->isEmpty())
        <div class="card flex flex-col items-center gap-5 p-16 text-center border border-slate-200/60 shadow-xl bg-gradient-to-b from-white to-slate-50/50">
            <div class="grid size-20 place-items-center rounded-3xl bg-rose-50/80 ring-8 ring-rose-500/5 shadow-inner">
                <svg class="size-10 text-rose-500 animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8Z" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="max-w-xs">
                <h3 class="text-base font-extrabold text-slate-900">Belum ada properti tersimpan</h3>
                <p class="mt-2 text-sm font-medium text-slate-500 leading-relaxed">Simpan properti impian Anda dengan menekan tombol ❤️ saat menjelajah.</p>
            </div>
            <a href="{{ route('properties.index') }}" class="btn btn-primary px-6 py-2.5 shadow-md shadow-indigo-600/10 hover:shadow-indigo-600/20 transition">Jelajahi Properti Sekarang</a>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach ($properties as $property)
                <x-property-card :property="$property" :show-favorite="true" :is-favorited="true" />
            @endforeach
        </div>
    @endif
</x-layouts.app>
