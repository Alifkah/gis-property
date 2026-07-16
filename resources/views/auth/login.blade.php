<x-layouts.app title="Masuk / Daftar Akun"
    description="Masuk ke platform Samarinda Properti GIS untuk menyimpan properti favorit Anda, mengatur alarm alarm properti baru, atau mulai mempublikasikan properti Anda.">
    
    @php
        $tab = $tab ?? request('tab', 'login');
    @endphp

    <div class="max-w-5xl mx-auto px-4 py-8 md:py-16">
        <div class="grid gap-8 lg:grid-cols-2 bg-white rounded-3xl border border-slate-200/50 shadow-xl overflow-hidden p-3 md:p-4">
            
            {{-- Left Column: Brand & Tagline Illustration (Hidden on mobile) --}}
            <div class="hidden lg:flex flex-col justify-between p-12 rounded-2xl bg-gradient-to-br from-brand-primary via-brand-primary/95 to-slate-900 text-white relative overflow-hidden min-h-[500px]">
                {{-- Decorative grid overlay --}}
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="auth-grid" width="30" height="30" patternUnits="userSpaceOnUse">
                                <path d="M 30 0 L 0 0 0 30" fill="none" stroke="currentColor" stroke-width="1" />
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#auth-grid)" />
                    </svg>
                </div>
                
                {{-- Logo/Title --}}
                <div class="relative z-10 flex items-center gap-2 font-display font-bold text-lg tracking-wider">
                    <i class="ti ti-map-pin-filled text-brand-accent text-xl"></i>
                    <span>SAMARINDA PROPERTI GIS</span>
                </div>

                {{-- Headline --}}
                <div class="relative z-10 space-y-4">
                    <h2 class="text-3xl font-extrabold tracking-tight text-balance leading-tight font-display">
                        Cari, Temukan, dan Analisis Properti Secara Geospasial.
                    </h2>
                    <p class="text-xs text-slate-300 font-semibold max-w-[40ch] leading-relaxed">
                        Analisis zona rawan banjir, estimasi cicilan KPR, dan pantau fasilitas publik terdekat secara langsung melalui peta interaktif.
                    </p>
                </div>

                {{-- Footer Info --}}
                <div class="relative z-10 text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                    &copy; {{ date('Y') }} Samarinda Properti GIS. All rights reserved.
                </div>
            </div>

            {{-- Right Column: Interactive Forms --}}
            <div class="p-6 sm:p-10 flex flex-col justify-center">
                {{-- Pill Switcher --}}
                <div class="grid grid-cols-2 gap-1 rounded-2xl bg-slate-100 p-1 border border-slate-200/50">
                    <a href="{{ route('login') }}" class="tab-btn text-center {{ $tab === 'login' ? 'tab-active shadow-sm bg-white' : 'text-slate-500' }}">Masuk</a>
                    <a href="{{ route('register') }}" class="tab-btn text-center {{ $tab === 'register' ? 'tab-active shadow-sm bg-white' : 'text-slate-500' }}">Daftar</a>
                </div>

                @if ($tab === 'register')
                    <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Nama Lengkap</label>
                            <input name="name" type="text" class="input" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso" required />
                            @error('name')
                                <div class="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                    <i class="ti ti-alert-circle text-sm"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Alamat Email</label>
                            <input name="email" type="email" class="input" value="{{ old('email') }}" placeholder="nama@email.com" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                    <i class="ti ti-alert-circle text-sm"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Nomor WhatsApp / Telepon</label>
                            <input name="phone" type="tel" class="input" value="{{ old('phone') }}" placeholder="Contoh: 08123456789" />
                            @error('phone')
                                <div class="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                    <i class="ti ti-alert-circle text-sm"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Kata Sandi</label>
                                <input name="password" type="password" class="input" placeholder="••••••••" required />
                                @error('password')
                                    <div class="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                        <i class="ti ti-alert-circle text-sm"></i>
                                        <span>{{ $message }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Konfirmasi Sandi</label>
                                <input name="password_confirmation" type="password" class="input" placeholder="••••••••" required />
                            </div>
                        </div>
                        <label class="mt-1.5 flex items-start gap-2.5 text-xs font-semibold text-slate-500 cursor-pointer">
                            <input name="terms" type="checkbox" class="mt-0.5 size-4 rounded border-slate-300 accent-brand-primary cursor-pointer" required />
                            <span class="leading-relaxed">Saya menyetujui Ketentuan Layanan & Kebijakan Privasi yang berlaku.</span>
                        </label>
                        <button type="submit" class="btn btn-primary mt-4 w-full py-3.5 shadow-md hover:shadow-brand-primary/20 transition cursor-pointer border-0">Daftar Akun Baru</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-4">
                        @csrf
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Alamat Email</label>
                            <input name="email" type="email" class="input" value="{{ old('email') }}" placeholder="nama@email.com" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                    <i class="ti ti-alert-circle text-sm"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Kata Sandi</label>
                            <input name="password" type="password" class="input" placeholder="••••••••" required />
                            @error('password')
                                <div class="mt-1 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                    <i class="ti ti-alert-circle text-sm"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror
                        </div>

                        <label class="mt-1.5 flex items-start gap-2.5 text-xs font-semibold text-slate-500 cursor-pointer">
                            <input name="terms" type="checkbox" class="mt-0.5 size-4 rounded border-slate-300 accent-brand-primary cursor-pointer" required />
                            <span class="leading-relaxed">Saya menyetujui Ketentuan Layanan & Kebijakan Privasi yang berlaku.</span>
                        </label>
                        <button type="submit" class="btn btn-primary mt-4 w-full py-3.5 shadow-md hover:shadow-brand-primary/20 transition cursor-pointer border-0">Masuk Ke Platform</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <style>
            .tab-btn { border-radius:12px; padding:10px 12px; font-size:12px; font-weight:800; color:#64748b; transition:all .2s; cursor:pointer; text-decoration:none; }
            .tab-btn:hover { color:var(--color-brand-primary) }
            .tab-active { background:#ffffff; color:var(--color-brand-primary); font-weight:800; border: 1px solid rgba(226,232,240,0.8); }
        </style>
    @endpush
</x-layouts.app>
