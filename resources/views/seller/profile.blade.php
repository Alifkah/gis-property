<x-layouts.app>
    <div class="grid gap-6 lg:grid-cols-[260px_minmax(0,1fr)]">
        <aside class="card p-4">
            <div class="px-2 pt-2 text-sm font-extrabold text-slate-900">Dashboard Penjual</div>
            <nav class="mt-3 grid gap-1">
                <a href="{{ route('seller.listings.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Listing Saya</a>
                <a href="{{ route('seller.listings.create') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Tambah Baru</a>
                <a href="{{ route('seller.competitor-analysis.index') }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50">Analisis Kompetitor</a>
                <a href="{{ route('seller.profile.edit') }}" class="rounded-xl bg-indigo-50 px-3 py-2 text-sm font-extrabold text-indigo-700 ring-1 ring-indigo-100">Pengaturan</a>
            </nav>
        </aside>

        <div class="grid gap-6">
            {{-- Flash message --}}
            @if (session('success'))
                <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700 ring-1 ring-emerald-200">
                    <svg class="size-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 6 9 17l-5-5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- Info Profil --}}
            <section class="card p-6">
                <div class="text-sm font-extrabold text-slate-900">Informasi Profil</div>
                <div class="mt-1 text-sm text-slate-600">Perbarui nama, email, dan nomor WhatsApp kamu.</div>

                <form method="POST" action="{{ route('seller.profile.update') }}" class="mt-6 grid gap-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Nama</label>
                        <input name="name" type="text" class="input mt-1" value="{{ old('name', $user->name) }}" required />
                        @error('name')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Email</label>
                        <input name="email" type="email" class="input mt-1" value="{{ old('email', $user->email) }}" required />
                        @error('email')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-slate-600">
                            Nomor WhatsApp
                            <span class="font-normal text-slate-400">(digunakan untuk tombol hubungi di halaman properti)</span>
                        </label>
                        <input name="phone" type="tel" class="input mt-1" value="{{ old('phone', $user->phone) }}" placeholder="+628123456789 atau 08123456789" />
                        @error('phone')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Simpan Profil</button>
                    </div>
                </form>
            </section>

            {{-- Ganti Password --}}
            <section class="card p-6">
                <div class="text-sm font-extrabold text-slate-900">Ganti Kata Sandi</div>
                <div class="mt-1 text-sm text-slate-600">Pastikan kata sandi baru minimal 8 karakter.</div>

                <form method="POST" action="{{ route('seller.profile.password') }}" class="mt-6 grid gap-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-semibold text-slate-600">Kata Sandi Saat Ini</label>
                        <input name="current_password" type="password" class="input mt-1" placeholder="••••••••" required />
                        @error('current_password')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Kata Sandi Baru</label>
                            <input name="password" type="password" class="input mt-1" placeholder="••••••••" required />
                            @error('password')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">Ulangi Kata Sandi Baru</label>
                            <input name="password_confirmation" type="password" class="input mt-1" placeholder="••••••••" required />
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">Ganti Kata Sandi</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</x-layouts.app>
