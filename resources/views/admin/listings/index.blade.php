<x-layouts.admin>
    <div class="card overflow-hidden">
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-sm font-bold text-slate-900">Semua Listing Properti</h1>
                <p class="mt-1 text-xs text-slate-500">Moderasi dan kelola seluruh iklan properti yang diunggah oleh penjual.</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-xs font-semibold text-slate-600">
                Total: {{ $properties->total() }} listing
            </span>
        </div>

        {{-- Filter Bar --}}
        <div class="p-6 bg-slate-50/50 border-b border-slate-100">
            <form method="GET" action="{{ route('admin.listings.index') }}" class="grid gap-3 sm:grid-cols-[1fr_200px_200px_auto]">
                <div class="relative">
                    <input
                        name="search" type="text" value="{{ request('search') }}"
                        class="input pl-9" placeholder="Cari berdasarkan judul properti..."
                    />
                    <svg class="absolute left-3 top-2.5 size-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <select name="type" class="select">
                    <option value="">Semua Tipe</option>
                    <option value="Rumah" @selected(request('type') === 'Rumah')>Rumah</option>
                    <option value="Tanah" @selected(request('type') === 'Tanah')>Tanah</option>
                </select>
                <select name="status" class="select">
                    <option value="">Semua Status</option>
                    <option value="Tersedia" @selected(request('status') === 'Tersedia')>Tersedia</option>
                    <option value="Terjual" @selected(request('status') === 'Terjual')>Terjual</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary cursor-pointer">Filter</button>
                    @if (request()->hasAny(['search', 'type', 'status']))
                        <a href="{{ route('admin.listings.index') }}" class="btn btn-outline">Reset</a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100">
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Properti</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Penjual</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Harga</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Status</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/75">
                    @forelse ($properties as $property)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3.5">
                                    @php
                                        $isLocalDisk = config('filesystems.disks.public.driver') === 'local';
                                    @endphp
                                    @if ($property->images->isNotEmpty() && (!$isLocalDisk || Storage::disk('public')->exists($property->images->first()->path)))
                                        <img
                                            src="{{ Storage::url($property->images->first()->path) }}"
                                            alt="{{ $property->title }}"
                                            class="size-14 object-cover flex-shrink-0 rounded-xl ring-1 ring-slate-200/50"
                                        />
                                    @else
                                        <div class="size-14 flex-shrink-0 grid place-items-center rounded-xl bg-brand-primary/5 border border-brand-primary/10 text-xs font-extrabold text-brand-primary">
                                            {{ strtoupper(substr($property->type, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('properties.show', $property->id) }}" target="_blank"
                                           class="block font-bold text-slate-900 hover:text-brand-primary hover:underline truncate text-sm">
                                            {{ $property->title }}
                                        </a>
                                        <div class="mt-0.5 text-xs text-slate-400 font-medium flex items-center gap-1.5">
                                            <span>{{ $property->type }}</span>
                                            <span>·</span>
                                            <span>Luas {{ $property->land_area }} m²</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-slate-700">{{ $property->user?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $property->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-6 py-4 font-bold text-brand-primary text-xs whitespace-nowrap">
                                Rp {{ number_format((float) $property->price, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($property->status === 'Terjual')
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Terjual</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <button
                                    type="button"
                                    onclick="openModal('del-listing-{{ $property->id }}')"
                                    class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-rose-600 hover:border-rose-200 hover:bg-rose-50 cursor-pointer"
                                >
                                    Hapus
                                </button>
                            </td>
                        </tr>

                        {{-- Delete Modal --}}
                        <div id="del-listing-{{ $property->id }}" class="modal-overlay">
                            <div class="modal-box max-w-md">
                                <div class="flex items-center gap-3">
                                    <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                                        <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-base font-bold text-slate-900">Konfirmasi Hapus Listing</div>
                                        <div class="text-xs text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</div>
                                    </div>
                                </div>
                                
                                <div class="mt-4 text-sm text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    Apakah Anda yakin ingin menghapus iklan properti <span class="font-bold text-slate-900">"{{ $property->title }}"</span> milik <span class="font-semibold text-slate-800">{{ $property->user?->name }}</span>? Seluruh data gambar terkait akan dihapus dari penyimpanan.
                                </div>
                                
                                <div class="mt-5 flex gap-3">
                                    <button
                                        type="button"
                                        onclick="closeModal('del-listing-{{ $property->id }}')"
                                        class="btn btn-outline flex-1 cursor-pointer"
                                    >Batal</button>
                                    <form method="POST" action="{{ route('admin.listings.destroy', $property->id) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        @foreach (request()->only(['search', 'type', 'status', 'page']) as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                                        @endforeach
                                        <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700 cursor-pointer">Ya, Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm font-semibold text-slate-400">
                                Tidak ada listing properti yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($properties->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $properties->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
