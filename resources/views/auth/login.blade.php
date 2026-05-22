<x-layouts.app>
    @php
        $tab = $tab ?? request('tab', 'login');
        $blueprintSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 260" fill="none">'
            . '<rect width="600" height="260" fill="url(#g)"/>'
            . '<defs>'
            . '<linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="#1e1b4b"/>'
            . '<stop offset="50%" stop-color="#312e81"/>'
            . '<stop offset="100%" stop-color="#1e1b4b"/>'
            . '</linearGradient>'
            . '<pattern id="grid" width="30" height="30" patternUnits="userSpaceOnUse">'
            . '<path d="M 30 0 L 0 0 0 30" fill="none" stroke="#4f46e5" stroke-width="0.7" stroke-opacity="0.2"/>'
            . '</pattern>'
            . '</defs>'
            . '<rect width="100%" height="100%" fill="url(#grid)"/>'
            . '<circle cx="300" cy="130" r="80" stroke="#6366f1" stroke-width="1.5" stroke-opacity="0.25" stroke-dasharray="5 5"/>'
            . '<path d="M260 160 l40-35 40 35 M270 148 v25 h60 v-25" stroke="#818cf8" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" stroke-opacity="0.8"/>'
            . '<text x="50%" y="215" dominant-baseline="middle" text-anchor="middle" fill="#a5b4fc" font-family="system-ui,-apple-system,sans-serif" font-weight="900" font-size="14" letter-spacing="3" opacity="0.9">SAMARINDA PROPERTI GIS</text>'
            . '</svg>';
        $illustration = 'data:image/svg+xml;base64,' . base64_encode($blueprintSvg);
    @endphp

    <div class="mx-auto max-w-md my-8">
        <div class="card overflow-hidden border border-slate-200/50 shadow-2xl transition hover:shadow-indigo-500/5">
            <div class="relative aspect-[16/7] overflow-hidden">
                <img src="{{ $illustration }}" alt="Samarinda GIS Illustration" class="h-full w-full object-cover" />
            </div>

            <div class="p-6">
                <div class="grid grid-cols-2 gap-2 rounded-2xl bg-slate-50 p-1 ring-1 ring-slate-200/70">
                    <a href="{{ route('login') }}" class="tab-btn text-center {{ $tab === 'login' ? 'tab-active' : '' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="tab-btn text-center {{ $tab === 'register' ? 'tab-active' : '' }}">Daftar</a>
                </div>

                @if ($tab === 'register')
                    <form method="POST" action="{{ route('register.store') }}" class="mt-5 grid gap-3">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Nama</label>
                            <input name="name" type="text" class="input mt-1" value="{{ old('name') }}" placeholder="Nama lengkap" required />
                            @error('name')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Email</label>
                            <input name="email" type="email" class="input mt-1" value="{{ old('email') }}" placeholder="nama@email.com" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">WhatsApp / Telepon</label>
                            <input name="phone" type="tel" class="input mt-1" value="{{ old('phone') }}" placeholder="+62..." />
                            @error('phone')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Kata Sandi</label>
                                <input name="password" type="password" class="input mt-1" placeholder="••••••••" required />
                                @error('password')
                                    <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-slate-600">Ulangi Kata Sandi</label>
                                <input name="password_confirmation" type="password" class="input mt-1" placeholder="••••••••" required />
                            </div>
                        </div>
                        <label class="mt-2 flex items-start gap-3 text-sm font-semibold text-slate-600">
                            <input name="terms" type="checkbox" class="mt-0.5 size-4 rounded border-slate-300 accent-indigo-600" required />
                            <span>Saya setuju dengan Terms & Privacy Policy</span>
                        </label>
                        <button type="submit" class="btn btn-primary mt-2 w-full">Daftar</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('login.store') }}" class="mt-5 grid gap-3">
                        @csrf
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Email</label>
                            <input name="email" type="email" class="input mt-1" value="{{ old('email') }}" placeholder="nama@email.com" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Kata Sandi</label>
                            <input name="password" type="password" class="input mt-1" placeholder="••••••••" required />
                            @error('password')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <label class="mt-2 flex items-start gap-3 text-sm font-semibold text-slate-600">
                            <input name="terms" type="checkbox" class="mt-0.5 size-4 rounded border-slate-300 accent-indigo-600" required />
                            <span>Saya setuju dengan Terms & Privacy Policy</span>
                        </label>
                        <button type="submit" class="btn btn-primary mt-2 w-full">Masuk Ke Platform</button>
                        <button type="button" class="btn btn-outline w-full">Login dengan Google</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .tab-btn { border-radius:16px; padding:10px 12px; font-size:13px; font-weight:800; color:#475569; transition:all .2s }
            .tab-btn:hover { background:#e2e8f0 }
            .tab-active { background:#4f46e5; color:#fff }
        </style>
    @endpush
</x-layouts.app>
