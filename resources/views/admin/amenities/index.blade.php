<x-layouts.admin>
    <div class="card overflow-hidden">
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-sm font-bold text-slate-900">Fasilitas / Point of Interest (POI)</h1>
                <p class="mt-1 text-xs text-slate-500">Kelola titik lokasi fasilitas publik yang berdampak pada penilaian harga properti.</p>
            </div>
            <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary cursor-pointer">+ Tambah Fasilitas</a>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100">
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Nama Fasilitas</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Tipe Kategori</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Koordinat</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/75">
                    @forelse ($amenities as $amenity)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $amenity->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/50">
                                    {{ $amenity->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 font-mono text-xs text-slate-500">
                                    <svg class="size-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <span>{{ number_format((float) $amenity->lat, 6) }}, {{ number_format((float) $amenity->lng, 6) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.amenities.edit', $amenity->id) }}" class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        onclick="openModal('del-amenity-{{ $amenity->id }}')"
                                        class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-rose-600 hover:border-rose-200 hover:bg-rose-50 cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>

                                {{-- Delete Modal --}}
                                <div id="del-amenity-{{ $amenity->id }}" class="modal-overlay">
                                    <div class="modal-box text-left max-w-md">
                                        <div class="flex items-center gap-3">
                                            <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                                                <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-slate-900">Hapus Fasilitas Publik?</div>
                                                <div class="text-xs text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 text-sm text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            Apakah Anda yakin ingin menghapus fasilitas <span class="font-bold text-slate-900">"{{ $amenity->name }}"</span> (Tipe: {{ $amenity->type }})? Properti terdekat tidak akan lagi mengukur jarak ke titik ini.
                                        </div>
                                        
                                        <div class="mt-5 flex gap-3">
                                            <button
                                                type="button"
                                                onclick="closeModal('del-amenity-{{ $amenity->id }}')"
                                                class="btn btn-outline flex-1 cursor-pointer"
                                            >Batal</button>
                                            <form method="POST" action="{{ route('admin.amenities.destroy', $amenity->id) }}" class="flex-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700 cursor-pointer">Ya, Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm font-semibold text-slate-400">
                                Belum ada titik fasilitas yang ditambahkan. Klik tombol di atas untuk menambah.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
