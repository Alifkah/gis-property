<x-layouts.seller>
    <div class="max-w-2xl mx-auto space-y-6" x-data="csvImporter()">
        {{-- Header --}}
        <div>
            <h1 class="text-xl font-extrabold text-slate-900 font-display flex items-center gap-2">
                <i class="ti ti-file-upload text-brand-primary"></i>
                <span>Unggah Massal Listing Properti</span>
            </h1>
            <p class="mt-1.5 text-xs font-semibold text-slate-500">Impor beberapa listing properti sekaligus menggunakan berkas CSV atau Excel (.xlsx). Koordinat spasial (latitude & longitude) wajib dimasukkan secara tepat.</p>
        </div>

        {{-- Alerts --}}
        @if ($errors->has('csv_file'))
            <div class="flex items-center gap-3 rounded-2xl bg-rose-50 px-4 py-3 text-xs font-bold text-rose-700 border border-rose-100">
                <i class="ti ti-alert-triangle text-base text-rose-600 shrink-0"></i>
                <span>{{ $errors->first('csv_file') }}</span>
            </div>
        @endif

        @if ($errors->has('csv_errors'))
            <div class="rounded-2xl bg-rose-50 p-4 border border-rose-100 space-y-2">
                <div class="flex items-center gap-2 text-xs font-bold text-rose-700">
                    <i class="ti ti-circle-x text-base text-rose-600 shrink-0"></i>
                    <span>Ditemukan kesalahan pada file Anda. Impor dibatalkan:</span>
                </div>
                <ul class="max-h-40 overflow-y-auto space-y-1.5 text-[11px] font-semibold text-rose-600 list-disc list-inside">
                    @foreach ($errors->get('csv_errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Upload & Info Card --}}
        <section class="card p-6 bg-white border border-slate-200/50 shadow-sm space-y-6">
            <form method="POST" action="{{ route('seller.listings.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700">Pilih Berkas CSV / Excel (.xlsx) <span class="text-rose-500">*</span></label>
                    
                    {{-- Dropzone area --}}
                    <div id="uploadZone"
                         @dragover.prevent="dragOver = true"
                         @dragleave.prevent="dragOver = false"
                         @drop.prevent="dragOver = false; const files = $event.dataTransfer.files; if(files.length) { document.getElementById('csvFileInput').files = files; handleFileSelect({ target: { files } }) }"
                         class="border-2 border-dashed rounded-2xl p-10 text-center transition-all relative group cursor-pointer"
                         :class="dragOver ? 'border-brand-primary/45 bg-brand-primary/5' : 'border-slate-200 bg-slate-50 hover:border-brand-primary/30 hover:bg-brand-primary/5'">
                        
                        <input id="csvFileInput" type="file" name="csv_file" accept=".csv,text/csv,text/plain,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="handleFileSelect" required />
                        
                        <div class="space-y-3" x-show="!fileSelected">
                            <i class="ti ti-cloud-upload text-4xl text-slate-400 group-hover:text-brand-primary transition"></i>
                            <div>
                                <p class="text-xs font-bold text-slate-700">Seret berkas CSV/Excel Anda ke sini atau klik untuk memilih</p>
                                <p class="text-[10px] text-slate-400 font-semibold mt-1">Maksimal ukuran berkas 5 MB</p>
                            </div>
                        </div>

                        {{-- File details view --}}
                        <div class="flex items-center justify-between gap-4 bg-white border border-slate-200/50 p-4 rounded-xl relative z-20 pointer-events-auto" x-show="fileSelected" x-cloak>
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="grid size-10 place-items-center rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    <i class="ti ti-file-text text-xl"></i>
                                </div>
                                <div class="text-left min-w-0">
                                    <div class="truncate text-xs font-bold text-slate-900" x-text="fileName"></div>
                                    <div class="text-[10px] text-slate-400 font-semibold" x-text="fileSize"></div>
                                </div>
                            </div>
                            <button type="button" @click="removeFile" class="grid size-8 place-items-center rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition border-0 cursor-pointer" title="Hapus Berkas">
                                <i class="ti ti-x text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Client-side Parsed CSV Preview Table --}}
                <div class="space-y-2" x-show="previewRows.length > 0" x-cloak>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pratinjau Data (5 Baris Pertama)</div>
                    <div class="overflow-x-auto rounded-xl border border-slate-200/60 shadow-3xs bg-slate-50">
                        <table class="w-full text-left text-[11px] border-collapse font-sans font-semibold">
                            <thead>
                                <tr class="bg-slate-100/75 border-b border-slate-200/50 text-[9px] font-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-4 py-2">Judul</th>
                                    <th class="px-4 py-2">Tipe</th>
                                    <th class="px-4 py-2">Harga</th>
                                    <th class="px-4 py-2">Luas Tanah</th>
                                    <th class="px-4 py-2">Latitude</th>
                                    <th class="px-4 py-2">Longitude</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-150">
                                <template x-for="(row, idx) in previewRows" :key="idx">
                                    <tr>
                                        <td class="px-4 py-2 text-slate-900 truncate max-w-[120px]" x-text="row.judul || '-'"></td>
                                        <td class="px-4 py-2 text-slate-600" x-text="row.tipe || '-'"></td>
                                        <td class="px-4 py-2 text-brand-accent" x-text="row.harga ? 'Rp ' + Number(row.harga).toLocaleString('id-ID') : '-'"></td>
                                        <td class="px-4 py-2 text-slate-600" x-text="row.luas_tanah ? row.luas_tanah + ' m²' : '-'"></td>
                                        <td class="px-4 py-2 text-slate-600 font-mono" x-text="row.latitude || '-'"></td>
                                        <td class="px-4 py-2 text-slate-600 font-mono" x-text="row.longitude || '-'"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-4 pt-2">
                    <a href="{{ route('seller.listings.import.template') }}" class="btn btn-outline text-xs font-bold py-2.5 px-4 flex items-center gap-1.5">
                        <i class="ti ti-download text-sm"></i>
                        <span>Unduh Template CSV</span>
                    </a>

                    <button type="submit" class="btn btn-primary text-xs font-bold py-2.5 px-5 cursor-pointer border-0 shadow-sm" :disabled="!fileSelected">
                        Mulai Impor
                    </button>
                </div>
            </form>
        </section>

        {{-- Panduan Format --}}
        <section class="card p-6 bg-slate-50 border border-slate-200/50 rounded-2xl">
            <h2 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                <i class="ti ti-info-circle text-brand-primary"></i>
                <span>Ketentuan Format Kolom Berkas</span>
            </h2>
            <ul class="space-y-3.5 text-xs text-slate-600 font-semibold leading-relaxed">
                <li class="flex items-start gap-2.5">
                    <span class="bg-brand-primary/10 text-brand-primary text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">judul</span>
                    <span>Nama iklan properti Anda (Maks. 150 karakter). <strong class="text-rose-600">*Wajib</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="bg-brand-primary/10 text-brand-primary text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">tipe</span>
                    <span>Jenis properti. Nilai wajib berupa <code class="bg-slate-200/80 px-1.5 py-0.5 rounded text-[11px] font-mono text-brand-primary">Rumah</code> atau <code class="bg-slate-200/80 px-1.5 py-0.5 rounded text-[11px] font-mono text-brand-primary">Tanah</code>. <strong class="text-rose-600">*Wajib</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="bg-brand-primary/10 text-brand-primary text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">harga</span>
                    <span>Harga penawaran dalam Rupiah (Angka bulat tanpa simbol/titik/koma). <strong class="text-rose-600">*Wajib</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="bg-brand-primary/10 text-brand-primary text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">luas_tanah</span>
                    <span>Luas tanah properti dalam satuan m² (Angka bulat, minimal 1). <strong class="text-rose-600">*Wajib</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="bg-brand-primary/10 text-brand-primary text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">latitude</span>
                    <span>Koordinat garis lintang geospasial. Contoh: -0.494231. <strong class="text-rose-600">*Wajib</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="bg-brand-primary/10 text-brand-primary text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">longitude</span>
                    <span>Koordinat garis bujur geospasial. Contoh: 117.141203. <strong class="text-rose-600">*Wajib</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="bg-slate-200 text-slate-700 text-[9px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 shrink-0">Lainnya</span>
                    <span>Kolom <code class="bg-slate-200 px-1 py-0.5 rounded font-mono text-slate-800">deskripsi</code>, <code class="bg-slate-200 px-1 py-0.5 rounded font-mono text-slate-800">luas_bangunan</code>, <code class="bg-slate-200 px-1 py-0.5 rounded font-mono text-slate-800">kamar_tidur</code>, <code class="bg-slate-200 px-1 py-0.5 rounded font-mono text-slate-800">kamar_mandi</code> bersifat opsional.</span>
                </li>
            </ul>
        </section>
    </div>

    @push('scripts')
        <script>
            function csvImporter() {
                return {
                    dragOver: false,
                    fileSelected: false,
                    fileName: '',
                    fileSize: '',
                    previewRows: [],
                    handleFileSelect(event) {
                        const file = event.target.files[0];
                        if (!file) return;
                        this.fileName = file.name;
                        this.fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                        this.fileSelected = true;

                        if (file.name.endsWith('.csv')) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const text = e.target.result;
                                const lines = text.split('\n').filter(line => line.trim().length > 0).map(line => {
                                    let arr = [];
                                    let inQuotes = false;
                                    let token = '';
                                    for (let i = 0; i < line.length; i++) {
                                        const c = line[i];
                                        if (c === '\"') {
                                            inQuotes = !inQuotes;
                                        } else if (c === ',' && !inQuotes) {
                                            arr.push(token);
                                            token = '';
                                        } else {
                                            token += c;
                                        }
                                    }
                                    arr.push(token);
                                    return arr;
                                });
                                if (lines.length > 1) {
                                    const headers = lines[0].map(h => h.trim().replace(/\"/g, '').toLowerCase());
                                    const rows = lines.slice(1, 6);
                                    this.previewRows = rows.map(r => {
                                        const obj = {};
                                        headers.forEach((h, idx) => {
                                            obj[h] = r[idx] ? r[idx].trim().replace(/\"/g, '') : '';
                                        });
                                        return obj;
                                    });
                                }
                            };
                            reader.readAsText(file);
                        } else {
                            this.previewRows = [];
                        }
                    },
                    removeFile() {
                        this.fileSelected = false;
                        this.fileName = '';
                        this.fileSize = '';
                        this.previewRows = [];
                        document.getElementById('csvFileInput').value = '';
                    }
                };
            }
        </script>
    @endpush
</x-layouts.seller>
