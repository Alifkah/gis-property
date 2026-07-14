<x-layouts.app>
    @php
        $blueprintSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 260" fill="none">'
            . '<rect width="600" height="260" fill="url(#g)"/>'
            . '<defs>'
            . '<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="#07222a"/>'
            . '<stop offset="50%" stop-color="#0F4C5C"/>'
            . '<stop offset="100%" stop-color="#07222a"/>'
            . '</linearGradient>'
            . '<pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">'
            . '<path d="M 30 0 L 0 0 0 30" fill="none" stroke="#E36414" stroke-width="0.7" stroke-opacity="0.15"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect width="100%" height="100%" fill="url(#grid)"/>'
            . '<circle cx="300" cy="130" r="70" stroke="#E36414" stroke-width="1.5" stroke-opacity="0.2" stroke-dasharray="5 5"/>'
            . '<path d="M260 160 l40-35 40 35 M270 148 v25 h60 v-25" stroke="#E36414" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.9"/>'
            . '<text x="50%" y="215" dominant-baseline="middle" text-anchor="middle" fill="#FAF7F2" font-family="Outfit,system-ui,-apple-system,sans-serif" font-weight="900" font-size="13" letter-spacing="4" opacity="0.8">VERIFIKASI EMAIL</text>'
            . '</svg>';
        $illustration = 'data:image/svg+xml;base64,' . base64_encode($blueprintSvg);
    @endphp

    <div class="mx-auto max-w-md my-12 px-4">
        <div class="card overflow-hidden border border-slate-200/50 shadow-2xl transition duration-300 hover:shadow-brand-primary/5">
            <div class="relative aspect-[16/7] overflow-hidden">
                <img src="{{ $illustration }}" alt="Samarinda GIS Illustration" class="h-full w-full object-cover transition-transform duration-700 hover:scale-[1.02]" />
            </div>

            <div class="p-6 sm:p-8 space-y-6">
                <div class="text-center">
                    <h2 class="text-xl font-black text-slate-900 tracking-tight font-display">Verifikasi Alamat Email Anda</h2>
                    <p class="mt-2 text-xs leading-relaxed text-slate-500">
                        Terima kasih telah mendaftar! Sebelum mempublikasikan iklan properti pertama Anda di **Samarinda Properti GIS**, silakan verifikasi alamat email Anda terlebih dahulu dengan mengklik tautan yang baru saja kami kirimkan ke email Anda.
                    </p>
                </div>

                @if (session('success'))
                    <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                        <svg class="size-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="border-t border-slate-100 pt-4 flex flex-col gap-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-full py-3 shadow-md hover:shadow-brand-primary/20 transition">
                            Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline border border-slate-200 text-slate-600 hover:bg-slate-50 w-full py-2.5 transition text-xs font-bold rounded-xl">
                            Keluar & Masuk Akun Lain
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
