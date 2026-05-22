<x-layouts.admin>
    <div class="card p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Fasilitas / POI</div>
                <div class="mt-1 text-sm text-slate-500">Kelola titik fasilitas publik yang mempengaruhi nilai properti.</div>
            </div>
            <a href="{{ route('admin.amenities.create') }}" class="btn btn-primary">+ Tambah Fasilitas</a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Nama</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Tipe</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Koordinat</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($amenities as $amenity)
                        <tr>
                            <td class="py-3 font-semibold text-slate-900">{{ $amenity->name }}</td>
                            <td class="py-3">
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200/70">
                                    {{ $amenity->type }}
                                </span>
                            </td>
                            <td class="py-3 font-mono text-xs text-slate-500">
                                {{ number_format((float) $amenity->lat, 6) }}, {{ number_format((float) $amenity->lng, 6) }}
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.amenities.edit', $amenity->id) }}" class="btn btn-outline">Edit</a>

                                    <button
                                        type="button"
                                        onclick="openModal('del-amenity-{{ $amenity->id }}')"
                                        class="btn btn-outline text-rose-600 hover:border-rose-300 hover:bg-rose-50"
                                    >Hapus</button>

                                    <div id="del-amenity-{{ $amenity->id }}" class="modal-overlay">
                                        <div class="modal-box">
                                            <div class="text-sm font-extrabold text-slate-900">Hapus Fasilitas?</div>
                                            <div class="mt-2 text-sm text-slate-600">
                                                <span class="font-semibold">{{ $amenity->name }}</span> akan dihapus permanen.
                                            </div>
                                            <div class="mt-5 flex gap-3">
                                                <button
                                                    type="button"
                                                    onclick="closeModal('del-amenity-{{ $amenity->id }}')"
                                                    class="btn btn-outline flex-1"
                                                >Batal</button>
                                                <form method="POST" action="{{ route('admin.amenities.destroy', $amenity->id) }}" class="flex-1">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-sm font-semibold text-slate-500">
                                Belum ada fasilitas. Klik "Tambah Fasilitas" untuk mulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
