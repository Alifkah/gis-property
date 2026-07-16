<x-layouts.seller>
    <div class="max-w-2xl mx-auto space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 font-display flex items-center gap-2">
                <i class="ti ti-settings text-brand-primary"></i>
                <span>Pengaturan Profil & Akun</span>
            </h1>
            <p class="mt-1.5 text-xs font-semibold text-slate-500 font-medium">Perbarui profil publik agensi Anda dan konfigurasikan keamanan kata sandi.</p>
        </div>

        {{-- Flash message --}}
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-700 border border-emerald-100 shadow-2xs">
                <i class="ti ti-circle-check text-emerald-600 text-lg shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Card 1: Informasi Perusahaan --}}
            <section class="bg-white rounded-2xl p-6 border border-slate-200/50 shadow-sm space-y-5">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2 leading-none">
                    <i class="ti ti-building text-brand-primary text-base"></i>
                    <span>Informasi Perusahaan / Agensi</span>
                </h3>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Nama Kontak Person <span class="text-rose-500">*</span></label>
                        <input name="name" type="text" class="input" value="{{ old('name', $user->name) }}" placeholder="Contoh: Budi Santoso" required />
                        @error('name')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Nama Perusahaan / Agensi <span class="text-slate-400">(Opsional)</span></label>
                        <input name="company_name" type="text" class="input" value="{{ old('company_name', $user->company_name) }}" placeholder="Contoh: Mahakam Realty Samarinda" />
                        @error('company_name')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Deskripsi / Tentang Kami <span class="text-slate-400">(Opsional)</span></label>
                    <textarea name="description" rows="4" class="input resize-none" placeholder="Tuliskan keahlian agensi Anda, wilayah spesialisasi pemasaran, atau pengalaman developer...">{{ old('description', $user->description) }}</textarea>
                    @error('description')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Logo upload section with rounded-full preview circle --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Logo Agensi / Foto Profil</label>
                    <div class="flex items-center gap-5">
                        <div class="relative group size-20 rounded-2xl overflow-hidden border border-slate-200/50 shadow-sm shrink-0 bg-slate-50 flex items-center justify-center">
                            @if ($user->logo_path)
                                <img src="{{ Storage::disk('public')->url($user->logo_path) }}" alt="Logo" class="size-full object-cover" />
                            @else
                                <div class="size-full grid place-items-center bg-brand-primary/8 text-brand-primary text-2xl font-black uppercase">
                                    {{ substr($user->company_name ?? $user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <input name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-wider file:bg-brand-primary/10 file:text-brand-primary hover:file:bg-brand-primary/15 text-xs text-slate-400 font-semibold" />
                            <p class="text-[9px] text-slate-400 mt-1.5 font-bold uppercase tracking-wider">Format: JPG, PNG, atau WebP (Maksimal 2 MB)</p>
                        </div>
                    </div>
                    @error('logo')
                        <div class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
            </section>

            {{-- Card 2: Kontak --}}
            <section class="bg-white rounded-2xl p-6 border border-slate-200/50 shadow-sm space-y-5">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2 leading-none">
                    <i class="ti ti-mail text-brand-primary text-base"></i>
                    <span>Informasi Kontak</span>
                </h3>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Alamat Email <span class="text-rose-500">*</span></label>
                        <input name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required />
                        @error('email')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <input name="phone" type="tel" class="input" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 08123456789" required />
                        @error('phone')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Mockup Alamat field to respect guidelines --}}
                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Alamat Kantor / Cabang</label>
                    <input name="address" type="text" class="input text-slate-600" value="Kota Samarinda, Kalimantan Timur" placeholder="Contoh: Jl. Mulawarman No. 12, Samarinda" />
                    <p class="text-[10px] text-slate-400 font-semibold mt-1">Alamat resmi kantor pemasaran agensi atau developer Anda.</p>
                </div>
            </section>

            {{-- Save Profile Action --}}
            <div class="flex justify-end">
                <button type="submit" class="btn btn-primary text-xs font-bold py-2.5 px-6 border-0 shadow-sm cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>

        {{-- Card 3: Keamanan --}}
        <section class="bg-white rounded-2xl p-6 border border-slate-200/50 shadow-sm space-y-5">
            <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3 flex items-center gap-2 leading-none">
                <i class="ti ti-lock text-brand-primary text-base"></i>
                <span>Keamanan Kata Sandi</span>
            </h3>

            <form method="POST" action="{{ route('seller.profile.password') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="text-xs font-bold text-slate-700 block mb-1.5">Kata Sandi Saat Ini</label>
                    <input name="current_password" type="password" class="input" placeholder="••••••••" required />
                    @error('current_password')
                        <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Kata Sandi Baru</label>
                        <input name="password" type="password" class="input" placeholder="••••••••" required />
                        @error('password')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1.5">Konfirmasi Kata Sandi Baru</label>
                        <input name="password_confirmation" type="password" class="input" placeholder="••••••••" required />
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn btn-primary text-xs font-bold py-2.5 px-6 border-0 shadow-sm cursor-pointer">Perbarui Kata Sandi</button>
                </div>
            </form>
        </section>
    </div>
</x-layouts.seller>
