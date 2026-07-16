<x-layouts.app>
    <div class="mx-auto max-w-md my-16 px-4">
        <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-200/50 space-y-6 text-center">
            
            {{-- Big mail icon inside a circular background of brand-primary/10 --}}
            <div class="flex justify-center">
                <div class="grid size-24 place-items-center rounded-full bg-brand-primary/10 text-brand-primary ring-8 ring-brand-primary/5">
                    <i class="ti ti-mail-opened text-5xl"></i>
                </div>
            </div>

            {{-- Title + instruction text --}}
            <div class="space-y-2">
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Verifikasi Alamat Email</h2>
                <p class="text-xs leading-relaxed text-slate-500 font-semibold max-w-[34ch] mx-auto">
                    Terima kasih telah mendaftar! Silakan periksa kotak masuk email Anda dan klik tautan verifikasi yang baru saja kami kirimkan.
                </p>
            </div>

            @if (session('success'))
                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-3.5 text-xs font-semibold text-emerald-800 flex items-center justify-center gap-2">
                    <i class="ti ti-circle-check text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Actions --}}
            <div class="space-y-4 pt-2">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full py-3.5 shadow-md hover:shadow-brand-primary/15 transition border-0 font-bold text-xs cursor-pointer">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <div class="border-t border-slate-100 pt-4 text-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-slate-400 hover:text-brand-primary underline transition cursor-pointer bg-transparent border-0">
                            Kembali ke Halaman Login
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
