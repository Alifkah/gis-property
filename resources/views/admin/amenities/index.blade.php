<x-layouts.admin>
    <div class="space-y-6" x-data="amenitiesPage()">
        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Fasilitas (POI)</span>
                    <span class="bg-brand-primary/10 text-brand-primary text-xs font-bold px-2.5 py-0.5 rounded-full">
                        {{ count($amenities) }} Titik
                    </span>
                </h1>
                <p class="mt-1.5 text-xs font-semibold text-slate-500">Kelola titik lokasi fasilitas publik yang berdampak pada penilaian harga properti secara otomatis.</p>
            </div>
            <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary text-xs font-bold flex items-center gap-1 border-0 py-2.5 shadow-xs cursor-pointer">
                <i class="ti ti-plus text-sm"></i>
                <span>Tambah Fasilitas</span>
            </a>
        </div>

        {{-- Flash notifications --}}
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-700 border border-emerald-100 shadow-2xs">
                <i class="ti ti-circle-check text-emerald-600 text-lg shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Search bar --}}
        <div class="flex items-center bg-white p-3.5 rounded-2xl border border-slate-200/50 shadow-sm">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                    <i class="ti ti-search text-base"></i>
                </div>
                <input type="text" x-model="searchQuery" @input="currentPage = 1" placeholder="Cari berdasarkan nama atau kategori fasilitas..." class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-9.5 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-brand-primary focus:ring-1 focus:ring-brand-primary" />
            </div>
        </div>

        {{-- Desktop Table --}}
        <div class="overflow-hidden bg-white rounded-2xl border border-slate-200/50 shadow-sm">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Nama Fasilitas</th>
                        <th class="px-6 py-4">Tipe Kategori</th>
                        <th class="px-6 py-4">Koordinat Spasial</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <template x-for="a in paginatedAmenities" :key="a.id">
                        <tr class="hover:bg-slate-50/50 transition duration-150">
                            <td class="px-6 py-4 font-bold text-slate-900" x-text="a.name"></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider border" :class="badgeClass(a.type)" x-text="a.type"></span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-mono text-slate-500 font-semibold flex items-center gap-1">
                                <i class="ti ti-map-pin text-slate-400 text-sm"></i>
                                <span x-text="Number(a.lat).toFixed(6) + ', ' + Number(a.lng).toFixed(6)"></span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <a :href="a.edit_url" class="grid size-8 place-items-center rounded-xl bg-slate-50 border border-slate-200/50 text-slate-600 hover:bg-slate-100 transition shadow-3xs" title="Edit Fasilitas">
                                        <i class="ti ti-edit text-base"></i>
                                    </a>
                                    <button type="button" @click="confirmDelete(a.id, a.name, a.delete_url)" class="grid size-8 place-items-center rounded-xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 hover:border-rose-200 transition shadow-3xs cursor-pointer" title="Hapus Fasilitas">
                                        <i class="ti ti-trash text-base"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <template x-if="filteredAmenities.length === 0">
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-semibold bg-white">
                                <i class="ti ti-info-circle text-2xl text-slate-300 mb-2 block"></i>
                                Tidak ada fasilitas ditemukan.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Custom Pagination --}}
        <template x-if="totalPages > 1">
            <div class="mt-4 flex items-center justify-between border-t border-slate-200/60 pt-4">
                <button type="button" @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1" class="btn btn-outline py-2 px-3 text-xs font-bold flex items-center gap-1 cursor-pointer disabled:opacity-40 disabled:pointer-events-none">
                    <i class="ti ti-chevron-left text-sm"></i>
                    <span>Sebelumnya</span>
                </button>
                <div class="text-xs font-bold text-slate-600">
                    Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
                </div>
                <button type="button" @click="currentPage = Math.min(totalPages, currentPage + 1)" :disabled="currentPage === totalPages" class="btn btn-outline py-2 px-3 text-xs font-bold flex items-center gap-1 cursor-pointer disabled:opacity-40 disabled:pointer-events-none">
                    <span>Selanjutnya</span>
                    <i class="ti ti-chevron-right text-sm"></i>
                </button>
            </div>
        </template>

        {{-- Delete Confirmation Modal --}}
        <div id="deleteConfirmModal" style="display:none;" x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[2000] items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs flex" @click="deleteModalOpen = false">
            <div class="bg-white rounded-3xl shadow-xl max-w-md w-full p-6 relative ring-1 ring-slate-100 animate-in fade-in zoom-in-95 duration-200" @click.stopPropagation()>
                <button type="button" @click="deleteModalOpen = false" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 cursor-pointer bg-transparent border-0">
                    <i class="ti ti-x text-lg"></i>
                </button>
                
                <div class="flex items-center gap-3">
                    <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                        <i class="ti ti-alert-triangle text-xl"></i>
                    </div>
                    <div>
                        <div class="text-base font-bold text-slate-900">Hapus Fasilitas POI?</div>
                        <div class="text-xs text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</div>
                    </div>
                </div>
                
                <div class="mt-4 text-xs font-semibold text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-100/50 leading-relaxed">
                    Apakah Anda yakin ingin menghapus fasilitas <span class="font-extrabold text-slate-900" x-text="deleteName"></span>? Kawasan properti di sekitar tidak akan lagi mengukur jarak spasial ke titik ini.
                </div>
                
                <div class="mt-5 flex gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn btn-outline flex-1 cursor-pointer">Batal</button>
                    <form method="POST" :action="deleteAction" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700 cursor-pointer border-0 font-bold">Ya, Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function amenitiesPage() {
                return {
                    searchQuery: '',
                    currentPage: 1,
                    itemsPerPage: 10,
                    deleteModalOpen: false,
                    deleteAmenityId: null,
                    deleteName: '',
                    deleteAction: '',
                    amenities: [
                        @foreach ($amenities as $a)
                            {
                                id: {{ $a->id }},
                                name: {!! json_encode($a->name) !!},
                                type: '{{ $a->type }}',
                                lat: {{ (float) $a->lat }},
                                lng: {{ (float) $a->lng }},
                                edit_url: '{{ route('admin.amenities.edit', $a->id) }}',
                                delete_url: '{{ route('admin.amenities.destroy', $a->id) }}'
                            },
                        @endforeach
                    ],
                    get filteredAmenities() {
                        return this.amenities.filter(a => {
                            return a.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                                   a.type.toLowerCase().includes(this.searchQuery.toLowerCase());
                        });
                    },
                    get paginatedAmenities() {
                        const start = (this.currentPage - 1) * this.itemsPerPage;
                        return this.filteredAmenities.slice(start, start + this.itemsPerPage);
                    },
                    get totalPages() {
                        return Math.ceil(this.filteredAmenities.length / this.itemsPerPage) || 1;
                    },
                    confirmDelete(id, name, action) {
                        this.deleteAmenityId = id;
                        this.deleteName = name;
                        this.deleteAction = action;
                        this.deleteModalOpen = true;
                    },
                    badgeClass(type) {
                        type = type.toLowerCase();
                        if (type.includes('sekolah') || type.includes('kampus') || type.includes('universitas') || type.includes('pendidikan')) {
                            return 'bg-blue-50 text-blue-700 border-blue-100';
                        } else if (type.includes('sakit') || type.includes('klinik') || type.includes('medis') || type.includes('puskesmas') || type.includes('dokter')) {
                            return 'bg-rose-50 text-rose-700 border-rose-100';
                        } else if (type.includes('mall') || type.includes('pusat perbelanjaan')) {
                            return 'bg-purple-50 text-purple-700 border-purple-100';
                        } else if (type.includes('supermarket') || type.includes('swalayan')) {
                            return 'bg-amber-50 text-amber-700 border-amber-100';
                        } else if (type.includes('pasar') || type.includes('tradisional')) {
                            return 'bg-orange-50 text-orange-700 border-orange-100';
                        } else if (type.includes('ibadah') || type.includes('masjid') || type.includes('gereja')) {
                            return 'bg-emerald-50 text-emerald-700 border-emerald-100';
                        }
                        return 'bg-slate-50 text-slate-700 border-slate-200/60';
                    }
                };
            }
        </script>
    @endpush
</x-layouts.admin>
