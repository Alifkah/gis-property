<x-layouts.admin>
    <div class="card p-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Zona Rawan Banjir</div>
                <div class="mt-1 text-sm text-slate-500">Kelola kawasan rawan genangan yang mempengaruhi penilaian properti.</div>
            </div>
            <a href="{{ route('admin.flood-zones.create') }}" class="btn btn-primary">+ Tambah Zona</a>
        </div>

        <div class="mt-6 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Nama Area</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Tingkat Risiko</th>
                        <th class="pb-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse ($floodZones as $zone)
                        <tr>
                            <td class="py-3 font-semibold text-slate-900">{{ $zone->area_name }}</td>
                            <td class="py-3">
                                @php
                                    $riskColors = [
                                        'Tinggi' => 'bg-rose-50 text-rose-700 ring-rose-200/70',
                                        'Sedang' => 'bg-amber-50 text-amber-700 ring-amber-200/70',
                                        'Rendah' => 'bg-sky-50 text-sky-700 ring-sky-200/70',
                                    ];
                                    $colorClass = $riskColors[$zone->risk_level] ?? 'bg-slate-50 text-slate-700 ring-slate-200/70';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold ring-1 {{ $colorClass }}">
                                    {{ $zone->risk_level }}
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.flood-zones.edit', $zone->id) }}" class="btn btn-outline">Edit</a>

                                    <button
                                        type="button"
                                        onclick="openModal('del-zone-{{ $zone->id }}')"
                                        class="btn btn-outline text-rose-600 hover:border-rose-300 hover:bg-rose-50"
                                    >Hapus</button>

                                    <div id="del-zone-{{ $zone->id }}" class="modal-overlay">
                                        <div class="modal-box">
                                            <div class="text-sm font-extrabold text-slate-900">Hapus Zona Banjir?</div>
                                            <div class="mt-2 text-sm text-slate-600">
                                                <span class="font-semibold">{{ $zone->area_name }}</span> akan dihapus permanen dari sistem.
                                                Properti dalam area ini tidak akan lagi ditandai rawan banjir.
                                            </div>
                                            <div class="mt-5 flex gap-3">
                                                <button
                                                    type="button"
                                                    onclick="closeModal('del-zone-{{ $zone->id }}')"
                                                    class="btn btn-outline flex-1"
                                                >Batal</button>
                                                <form method="POST" action="{{ route('admin.flood-zones.destroy', $zone->id) }}" class="flex-1">
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
                            <td colspan="3" class="py-8 text-center text-sm font-semibold text-slate-500">
                                Belum ada zona banjir. Klik "Tambah Zona" untuk mulai menggambar polygon.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
