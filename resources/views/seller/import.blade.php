<x-layouts.seller>
    {{-- Main Content --}}
    <div class="grid gap-6 min-w-0">
            <section class="card p-6">
                <div>
                    <h1 class="text-sm font-bold text-slate-900">Unggah Massal Listing Properti</h1>
                    <p class="mt-1 text-xs text-slate-500 font-medium">Impor beberapa listing properti sekaligus menggunakan file CSV atau Excel (.xlsx). Gunakan koordinat spasial (latitude & longitude) yang tepat agar lokasi dapat terpetakan dengan akurat.</p>
                </div>

                {{-- Alert General Error --}}
                @if ($errors->has('csv_file'))
                    <div class="mt-4 flex items-center gap-3 rounded-2xl bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700 ring-1 ring-rose-200">
                        <svg class="size-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ $errors->first('csv_file') }}</span>
                    </div>
                @endif

                {{-- Alert Row Errors --}}
                @if ($errors->has('csv_errors'))
                    <div class="mt-4 rounded-2xl bg-rose-50 p-4 ring-1 ring-rose-200">
                        <div class="flex items-center gap-2 text-xs font-bold text-rose-700">
                            <svg class="size-4.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Ditemukan kesalahan pada file Anda. Impor dibatalkan:</span>
                        </div>
                        <ul class="mt-2.5 max-h-40 overflow-y-auto space-y-1 text-[11px] font-semibold text-rose-600 list-disc list-inside">
                            @foreach ($errors->get('csv_errors')[0] as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mt-6 grid gap-6 md:grid-cols-2">
                    {{-- Form Upload --}}
                    <div>
                        <form method="POST" action="{{ route('seller.listings.import.store') }}" enctype="multipart/form-data" class="grid gap-4">
                            @csrf
                            <div>
                                <label class="text-xs font-bold text-slate-700 block mb-2">Pilih File CSV / Excel (.xlsx) <span class="text-rose-500">*</span></label>
                                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-6 hover:border-brand-primary/40 hover:bg-brand-primary/5 transition relative group cursor-pointer">
                                    <input type="file" name="csv_file" accept=".csv,text/csv,text/plain,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required />
                                    <div class="text-center">
                                        <svg class="mx-auto size-8 text-slate-400 group-hover:text-brand-primary transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                        </svg>
                                        <p class="mt-2 text-xs font-bold text-slate-700">Klik atau seret file CSV/Excel di sini</p>
                                        <p class="mt-1 text-[10px] text-slate-400 font-medium">Maksimal ukuran file 5 MB</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-4 mt-2">
                                <a href="{{ route('seller.listings.import.template') }}" class="btn btn-outline flex items-center gap-2 text-xs font-bold py-2 px-4 cursor-pointer">
                                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    <span>Unduh Template CSV</span>
                                </a>

                                <button type="submit" class="btn btn-primary text-xs font-bold py-2 px-5 cursor-pointer">
                                    Mulai Impor
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Panduan Format --}}
                    <div class="bg-slate-50 rounded-2xl p-5 ring-1 ring-slate-200/50">
                        <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Ketentuan Format Kolom</h2>
                        <p class="mt-1 text-[11px] text-slate-500 font-medium">Ketentuan nama kolom (header) ini berlaku sama baik untuk file CSV maupun Excel (.xlsx) pada baris pertama.</p>
                        <ul class="mt-3.5 space-y-2.5 text-xs text-slate-600 font-semibold">
                            <li class="flex items-start gap-2">
                                <span class="bg-brand-primary/8 text-brand-primary text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">judul</span>
                                <span class="leading-relaxed">Nama listing properti (Teks, Maks. 150 karakter). <strong class="text-rose-600">*Wajib</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-brand-primary/8 text-brand-primary text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">tipe</span>
                                <span class="leading-relaxed">Jenis properti. Nilai harus <code class="bg-slate-200 px-1 py-0.5 rounded text-[11px] font-mono text-brand-primary">Rumah</code> atau <code class="bg-slate-200 px-1 py-0.5 rounded text-[11px] font-mono text-brand-primary">Tanah</code>. <strong class="text-rose-600">*Wajib</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-brand-primary/8 text-brand-primary text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">harga</span>
                                <span class="leading-relaxed">Harga properti dalam Rupiah (Angka tanpa titik/koma desimal). <strong class="text-rose-600">*Wajib</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-brand-primary/8 text-brand-primary text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">luas_tanah</span>
                                <span class="leading-relaxed">Luas tanah dalam m² (Angka bulat, minimal 1). <strong class="text-rose-600">*Wajib</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-brand-primary/8 text-brand-primary text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">latitude</span>
                                <span class="leading-relaxed">Koordinat Lintang. Contoh: -0.494231 (Antara -90 s/d 90). <strong class="text-rose-600">*Wajib</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-brand-primary/8 text-brand-primary text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">longitude</span>
                                <span class="leading-relaxed">Koordinat Bujur. Contoh: 117.141203 (Antara -180 s/d 180). <strong class="text-rose-600">*Wajib</strong></span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="bg-slate-200 text-slate-700 text-[10px] font-bold px-1.5 py-0.5 rounded uppercase mt-0.5 shrink-0">Lainnya</span>
                                <span class="leading-relaxed">Kolom <code class="bg-slate-200 px-1 py-0.5 rounded text-[11px] font-mono">deskripsi</code>, <code class="bg-slate-200 px-1 py-0.5 rounded text-[11px] font-mono">luas_bangunan</code>, <code class="bg-slate-200 px-1 py-0.5 rounded text-[11px] font-mono">kamar_tidur</code>, <code class="bg-slate-200 px-1 py-0.5 rounded text-[11px] font-mono">kamar_mandi</code> bersifat opsional.</span>
                            </li>
                        </ul>
                    </div>>
                </div>
            </section>
        </div>
</x-layouts.seller>
