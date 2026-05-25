<x-layouts.app>
    @php
        $tab = $tab ?? request('tab', 'login');
        $blueprintSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 260" fill="none">'
            . '<rect width="600" height="260" fill="url(#g)"/>'
            . '<defs>'
            . '<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="#0f172a"/>'
            . '<stop offset="50%" stop-color="#1e1b4b"/>'
            . '<stop offset="100%" stop-color="#0f172a"/>'
            . '</linearGradient>'
            . '<pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">'
            . '<path d="M 30 0 L 0 0 0 30" fill="none" stroke="#4f46e5" stroke-width="0.7" stroke-opacity="0.15"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect width="100%" height="100%" fill="url(#grid)"/>'
            . '<circle cx="300" cy="130" r="70" stroke="#6366f1" stroke-width="1.5" stroke-opacity="0.2" stroke-dasharray="5 5"/>'
            . '<path d="M260 160 l40-35 40 35 M270 148 v25 h60 v-25" stroke="#818cf8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.7"/>'
            . '<text x="50%" y="215" dominant-baseline="middle" text-anchor="middle" fill="#a5b4fc" font-family="system-ui,-apple-system,sans-serif" font-weight="900" font-size="13" letter-spacing="4" opacity="0.8">SAMARINDA PROPERTI GIS</text>'
            . '</svg>';
        $illustration = 'data:image/svg+xml;base64,' . base64_encode($blueprintSvg);
    @endphp

    <div class="mx-auto max-w-md my-12 px-4">
        <div class="card overflow-hidden border border-slate-200/50 shadow-2xl transition duration-300 hover:shadow-indigo-500/5">
            <div class="relative aspect-[16/7] overflow-hidden">
                <img src="{{ $illustration }}" alt="Samarinda GIS Illustration" class="h-full w-full object-cover transition-transform duration-700 hover:scale-[1.02]" />
            </div>

            <div class="p-6 sm:p-8">
                <div class="grid grid-cols-2 gap-1 rounded-2xl bg-slate-100/70 p-1 ring-1 ring-slate-200/50">
                    <a href="{{ route('login') }}" class="tab-btn text-center {{ $tab === 'login' ? 'tab-active shadow-2xs' : '' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="tab-btn text-center {{ $tab === 'register' ? 'tab-active shadow-2xs' : '' }}">Daftar</a>
                </div>

                @if ($tab === 'register')
                    <form method="POST" action="{{ route('register.store') }}" class="mt-6 grid gap-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
                            <input name="name" type="text" class="input mt-1.5" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required />
                            @error('name')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                            <input name="email" type="email" class="input mt-1.5" value="{{ old('email') }}" placeholder="nama@email.com" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Nomor WhatsApp / Telepon</label>
                            <input name="phone" type="tel" class="input mt-1.5" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" />
                            @error('phone')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                                <input name="password" type="password" class="input mt-1.5" placeholder="••••••••" required />
                                @error('password')
                                    <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Konfirmasi Sandi</label>
                                <input name="password_confirmation" type="password" class="input mt-1.5" placeholder="••••••••" required />
                            </div>
                        </div>
                        <label class="mt-1 flex items-start gap-2.5 text-xs font-semibold text-slate-500 cursor-pointer">
                            <input name="terms" type="checkbox" class="mt-0.5 size-4 rounded border-slate-300 accent-indigo-600 cursor-pointer" required />
                            <span class="leading-relaxed">Saya menyetujui Ketentuan Layanan & Kebijakan Privasi yang berlaku.</span>
                        </label>
                        <button type="submit" class="btn btn-primary mt-2 w-full py-3 shadow-md hover:shadow-indigo-500/20 transition">Daftar Akun Baru</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('login.store') }}" class="mt-6 grid gap-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Alamat Email</label>
                            <input name="email" type="email" class="input mt-1.5" value="{{ old('email') }}" placeholder="nama@email.com" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kata Sandi</label>
                            <input name="password" type="password" class="input mt-1.5" placeholder="••••••••" required />
                            @error('password')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="mt-1 flex items-start gap-2.5 text-xs font-semibold text-slate-500 cursor-pointer">
                            <input name="terms" type="checkbox" class="mt-0.5 size-4 rounded border-slate-300 accent-indigo-600 cursor-pointer" required />
                            <span class="leading-relaxed">Saya menyetujui Ketentuan Layanan & Kebijakan Privasi yang berlaku.</span>
                        </label>
                        <button type="submit" class="btn btn-primary mt-2 w-full py-3 shadow-md hover:shadow-indigo-500/20 transition">Masuk Ke Platform</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .tab-btn { border-radius:12px; padding:10px 12px; font-size:12px; font-weight:800; color:#64748b; transition:all .2s; cursor:pointer; }
            .tab-btn:hover { color:#4f46e5 }
            .tab-active { background:#ffffff; color:#4f46e5; border: 1px solid rgba(226,232,240,0.8); }
        </style>
    @endpush
</x-layouts.app>
