<x-layouts.seller>
    <div class="grid gap-6 min-w-0">
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
                <div>
                    <h1 class="text-sm font-bold text-slate-900">Informasi Profil Agensi / Developer</h1>
                    <p class="mt-1 text-xs text-slate-500">Lengkapi data profil agensi Anda untuk membangun kepercayaan calon pembeli di halaman publik.</p>
                </div>

                <form method="POST" action="{{ route('seller.profile.update') }}" enctype="multipart/form-data" class="mt-6 grid gap-5">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Nama Kontak Person <span class="text-rose-500">*</span></label>
                            <input name="name" type="text" class="input mt-1.5" value="{{ old('name', $user->name) }}" required />
                            @error('name')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Nama Perusahaan / Agensi <span class="text-slate-400">(Opsional)</span></label>
                            <input name="company_name" type="text" class="input mt-1.5" value="{{ old('company_name', $user->company_name) }}" placeholder="cth. Mahakam Realty Samarinda" />
                            @error('company_name')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Alamat Email <span class="text-rose-500">*</span></label>
                            <input name="email" type="email" class="input mt-1.5" value="{{ old('email', $user->email) }}" required />
                            @error('email')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-bold text-slate-700">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                            <input name="phone" type="tel" class="input mt-1.5" value="{{ old('phone', $user->phone) }}" placeholder="cth. 08123456789" required />
                            @error('phone')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700">Deskripsi / Tentang Kami <span class="text-slate-400">(Opsional)</span></label>
                        <textarea name="description" rows="4" class="input mt-1.5 resize-none" placeholder="Tuliskan pengalaman agensi Anda, wilayah spesialisasi, atau visi misi developer...">{{ old('description', $user->description) }}</textarea>
                        @error('description')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Logo Upload --}}
                    <div>
                        <label class="text-xs font-bold text-slate-700">Logo Agensi / Foto Profil</label>
                        <div class="mt-2.5 flex items-center gap-4">
                            @if ($user->logo_path)
                                <img src="{{ Storage::url($user->logo_path) }}" alt="Logo" class="size-16 object-cover rounded-2xl ring-1 ring-slate-200/50" />
                            @else
                                <div class="size-16 grid place-items-center rounded-2xl bg-brand-primary/8 border border-brand-primary/10 text-brand-primary text-lg font-black uppercase">
                                    {{ substr($user->company_name ?? $user->name, 0, 1) }}
                                </div>
                            @endif
                            <div class="flex-1 max-w-sm">
                                <input name="logo" type="file" accept="image/jpeg,image/png,image/webp" class="file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-brand-primary/8 file:text-brand-primary hover:file:bg-brand-primary/15 text-xs text-slate-400" />
                                <p class="text-[10px] text-slate-400 mt-1.5 font-medium">Maksimal file 2 MB (Format: JPG, PNG, atau WebP)</p>
                            </div>
                        </div>
                        @error('logo')
                            <div class="mt-1.5 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn btn-primary cursor-pointer">Simpan Perubahan</button>
                    </div>
                </form>
            </section>

            {{-- Ganti Password --}}
            <section class="card p-6">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Ganti Kata Sandi</h2>
                    <p class="mt-1 text-xs text-slate-500">Pastikan kata sandi baru Anda minimal terdiri dari 8 karakter.</p>
                </div>

                <form method="POST" action="{{ route('seller.profile.password') }}" class="mt-6 grid gap-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-xs font-bold text-slate-700">Kata Sandi Saat Ini</label>
                        <input name="current_password" type="password" class="input mt-1.5" placeholder="••••••••" required />
                        @error('current_password')
                            <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="text-xs font-bold text-slate-700">Kata Sandi Baru</label>
                            <input name="password" type="password" class="input mt-1.5" placeholder="••••••••" required />
                            @error('password')
                                <div class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-700">Konfirmasi Kata Sandi Baru</label>
                            <input name="password_confirmation" type="password" class="input mt-1.5" placeholder="••••••••" required />
                        </div>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="btn btn-primary cursor-pointer">Perbarui Kata Sandi</button>
                    </div>
                </form>
            </section>
        </div>
</x-layouts.seller>
