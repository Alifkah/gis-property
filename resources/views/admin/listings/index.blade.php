<x-layouts.admin>
    <div class="space-y-6" x-data="{
        selectedIds: [],
        allIds: [
            @foreach ($properties as $property)
                {{ $property->id }},
            @endforeach
        ],
        get allSelected() {
            return this.selectedIds.length === this.allIds.length && this.allIds.length > 0;
        },
        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedIds = [];
            } else {
                this.selectedIds = [...this.allIds];
            }
        },
        isDeleting: false,
        async deleteSelected() {
            if (this.selectedIds.length === 0) return;
            if (!confirm('Apakah Anda yakin ingin menghapus ' + this.selectedIds.length + ' properti terpilih secara permanen?')) return;
            
            this.isDeleting = true;
            
            // Loop through each selected ID and send DELETE request
            for (const id of this.selectedIds) {
                try {
                    await fetch('{{ url('/admin/listings') }}/' + id, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-HTTP-Method-Override': 'DELETE'
                        },
                        body: JSON.stringify({
                            _method: 'DELETE',
                            _token: '{{ csrf_token() }}'
                        })
                    });
                } catch (e) {
                    console.error('Gagal menghapus properti ID: ' + id, e);
                }
            }
            
            this.isDeleting = false;
            window.location.reload();
        }
    }">
        {{-- Page Header --}}
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                    <span>Semua Listing Properti</span>
                    <span class="bg-brand-primary/10 text-brand-primary text-xs font-bold px-2.5 py-0.5 rounded-full">
                        {{ $properties->total() }} Listing
                    </span>
                </h1>
                <p class="mt-1.5 text-xs font-semibold text-slate-500">Moderasi, filter, dan kelola seluruh iklan properti yang diunggah oleh mitra penjual.</p>
            </div>
            <div class="flex items-center gap-2.5">
                <a href="{{ route('admin.listings.export') }}" class="btn btn-outline text-xs font-bold flex items-center gap-1.5 py-2.5 shadow-3xs cursor-pointer">
                    <i class="ti ti-download text-emerald-600 text-sm"></i>
                    <span>Ekspor CSV</span>
                </a>
            </div>
        </div>

        {{-- Flash notifications --}}
        @if (session('success'))
            <div class="flex items-center gap-3 rounded-2xl bg-emerald-50 px-4 py-3.5 text-xs font-bold text-emerald-700 border border-emerald-100 shadow-2xs">
                <i class="ti ti-circle-check text-emerald-600 text-lg shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Search and Filter Bar --}}
        <div class="bg-white rounded-2xl border border-slate-200/50 p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.listings.index') }}" class="grid gap-3 sm:grid-cols-[1fr_180px_180px_auto]">
                <div class="relative">
                    <div class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                        <i class="ti ti-search text-base"></i>
                    </div>
                    <input name="search" type="text" value="{{ request('search') }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 pl-9.5 pr-4 text-xs font-semibold text-slate-800 placeholder-slate-400 focus:outline-hidden focus:border-brand-primary focus:ring-1 focus:ring-brand-primary" placeholder="Cari berdasarkan judul properti..." />
                </div>
                <div>
                    <select name="type" class="select text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3">
                        <option value="">Semua Tipe</option>
                        <option value="Rumah" @selected(request('type') === 'Rumah')>Rumah</option>
                        <option value="Tanah" @selected(request('type') === 'Tanah')>Tanah</option>
                    </select>
                </div>
                <div>
                    <select name="status" class="select text-xs font-bold text-slate-700 bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-3">
                        <option value="">Semua Status</option>
                        <option value="Tersedia" @selected(request('status') === 'Tersedia')>Tersedia</option>
                        <option value="Terjual" @selected(request('status') === 'Terjual')>Terjual</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary text-xs font-bold px-6 py-2.5 border-0 shadow-xs cursor-pointer">Filter</button>
                    @if (request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.listings.index') }}" class="btn btn-outline text-xs font-bold px-4 py-2.5 shadow-3xs">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="overflow-hidden bg-white rounded-2xl border border-slate-200/50 shadow-sm relative">
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            <th class="px-5 py-4 w-10">
                                <label class="inline-flex items-center justify-center cursor-pointer">
                                    <input type="checkbox" :checked="allSelected" @change="toggleSelectAll" class="rounded border-slate-300 text-brand-primary focus:ring-brand-primary/20 size-4 cursor-pointer" />
                                </label>
                            </th>
                            <th class="px-5 py-4">Properti</th>
                            <th class="px-5 py-4">Penjual</th>
                            <th class="px-5 py-4">Status</th>
                            <th class="px-5 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($properties as $property)
                            <tr class="hover:bg-slate-50/50 transition duration-150" :class="selectedIds.includes({{ $property->id }}) ? 'bg-brand-primary/5/30' : ''">
                                <td class="px-5 py-4 w-10">
                                    <label class="inline-flex items-center justify-center cursor-pointer">
                                        <input type="checkbox" value="{{ $property->id }}" x-model="selectedIds" class="listing-checkbox rounded border-slate-300 text-brand-primary focus:ring-brand-primary/20 size-4 cursor-pointer" />
                                    </label>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
                                        @endphp
                                        @if ($property->images->isNotEmpty() && (!$isLocalDisk || Storage::disk('public')->exists($property->images->first()->path)))
                                            <img src="{{ Storage::disk('public')->url($property->images->first()->path) }}" alt="" class="size-12 object-cover rounded-xl border border-slate-100 shrink-0" />
                                        @else
                                            <div class="size-12 grid place-items-center rounded-xl bg-slate-50 border border-slate-100 text-slate-300 shrink-0">
                                                <i class="ti ti-photo-off text-base"></i>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <a href="{{ route('properties.show', $property->id) }}" target="_blank" class="truncate text-xs font-bold text-slate-900 hover:text-brand-primary hover:underline block leading-snug">
                                                {{ $property->title }}
                                            </a>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-none mt-1 block">
                                                {{ $property->type }} · {{ $property->land_area }} m² · {{ $property->district_name ?? 'Samarinda' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <div class="grid size-8 place-items-center rounded-lg bg-slate-100 font-bold text-slate-700 text-xs shrink-0">
                                            {{ strtoupper(substr($property->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-slate-700 truncate max-w-[120px]">{{ $property->user?->name ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-400 font-semibold mt-0.5 truncate max-w-[120px]">{{ $property->user?->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5">
                                    @if ($property->status === 'Terjual')
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[9px] font-bold text-slate-600 border border-slate-200 uppercase tracking-wider">Terjual</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-[9px] font-bold text-emerald-700 border border-emerald-100 uppercase tracking-wider">Tersedia</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('properties.show', $property->id) }}" target="_blank" class="grid size-8 place-items-center rounded-xl bg-slate-50 border border-slate-200/50 text-slate-600 hover:bg-slate-100 transition shadow-3xs" title="Pratinjau Publik">
                                            <i class="ti ti-eye text-base"></i>
                                        </a>
                                        <button type="button" onclick="openModal('del-listing-{{ $property->id }}')" class="grid size-8 place-items-center rounded-xl bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 hover:border-rose-200 transition shadow-3xs cursor-pointer" title="Hapus Properti">
                                            <i class="ti ti-trash text-base"></i>
                                        </button>
                                    </div>

                                    {{-- Delete Modal --}}
                                    <div id="del-listing-{{ $property->id }}" class="modal-overlay">
                                        <div class="modal-box text-left max-w-md p-6">
                                            <div class="flex items-center gap-3">
                                                <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                                                    <i class="ti ti-alert-triangle text-xl"></i>
                                                </div>
                                                <div>
                                                    <div class="text-base font-bold text-slate-900 leading-snug">Konfirmasi Hapus Listing</div>
                                                    <div class="text-xs text-slate-400 mt-0.5 font-semibold">Tindakan ini tidak dapat dibatalkan secara permanen.</div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 text-xs font-semibold leading-relaxed text-slate-600 bg-slate-50 border border-slate-100/50 p-4 rounded-xl">
                                                Apakah Anda yakin ingin menghapus iklan properti <span class="font-extrabold text-slate-900">"{{ $property->title }}"</span> milik <span class="font-bold text-slate-800">{{ $property->user?->name }}</span>? Seluruh data gambar terkait akan dihapus dari penyimpanan.
                                            </div>
                                            
                                            <div class="mt-5 flex gap-3">
                                                <button type="button" onclick="closeModal('del-listing-{{ $property->id }}')" class="btn btn-outline flex-1 cursor-pointer">Batal</button>
                                                <form method="POST" action="{{ route('admin.listings.destroy', $property->id) }}" class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    @foreach (request()->only(['search', 'type', 'status', 'page']) as $key => $value)
                                                        <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                                                    @endforeach
                                                    <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700 shadow-xs cursor-pointer border-0 font-bold">Ya, Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-semibold bg-white">
                                    <i class="ti ti-info-circle text-2xl text-slate-300 mb-2 block"></i>
                                    Tidak ada listing properti yang sesuai dengan kriteria filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($properties->hasPages())
                <div class="p-5 border-t border-slate-100">
                    {{ $properties->links() }}
                </div>
            @endif
        </div>

        {{-- Premium Floating Bulk Action Bar --}}
        <div id="bulkActionBar" 
             style="display:none;" 
             x-show="selectedIds.length > 0" 
             x-cloak 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-24 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0 opacity-100"
             x-transition:leave-end="translate-y-24 opacity-0"
             class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[1050] bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-center justify-between gap-5 border border-slate-800 w-[90%] max-w-lg">
            
            <div class="flex items-center gap-3">
                <div class="size-2 rounded-full bg-rose-500 animate-pulse"></div>
                <div class="text-xs font-bold">
                    <span x-text="selectedIds.length"></span> properti terpilih
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button type="button" @click="selectedIds = []" class="btn text-xs font-bold text-slate-300 hover:text-white px-3 py-1.5 bg-transparent border-0 cursor-pointer">Batal</button>
                <button type="button" @click="deleteSelected" :disabled="isDeleting" class="btn text-xs font-bold bg-rose-600 text-white hover:bg-rose-700 px-4 py-1.5 border-0 shadow-xs flex items-center gap-1.5 cursor-pointer disabled:opacity-40">
                    <template x-if="isDeleting">
                        <i class="ti ti-loader animate-spin text-sm"></i>
                    </template>
                    <template x-if="!isDeleting">
                        <i class="ti ti-trash text-sm"></i>
                    </template>
                    <span>Hapus Terpilih</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.admin>
