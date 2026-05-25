<x-layouts.admin>
    <div class="card overflow-hidden">
        {{-- Header --}}
        <div class="p-6 border-b border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-sm font-bold text-slate-900">Kawasan Rawan Banjir</h1>
                <p class="mt-1 text-xs text-slate-500">Kelola polygon area genangan air untuk mengidentifikasi tingkat kerawanan banjir properti secara otomatis.</p>
            </div>
            <a href="{{ route('admin.flood-zones.create') }}" class="btn btn-primary cursor-pointer">+ Tambah Zona</a>
        </div>

        {{-- Table Container --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-100">
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Nama Area / Kawasan</th>
                        <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400">Tingkat Risiko</th>
                        <th class="px-6 py-3.5 text-right text-xs font-bold uppercase tracking-wider text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100/75">
                    @forelse ($floodZones as $zone)
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-900">{{ $zone->area_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $riskColors = [
                                        'Tinggi' => 'bg-rose-50 text-rose-700 ring-rose-200/70',
                                        'Sedang' => 'bg-amber-50 text-amber-700 ring-amber-200/70',
                                        'Rendah' => 'bg-sky-50 text-sky-700 ring-sky-200/70',
                                    ];
                                    $colorClass = $riskColors[$zone->risk_level] ?? 'bg-slate-50 text-slate-700 ring-slate-200/70';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 {{ $colorClass }}">
                                    {{ $zone->risk_level }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('admin.flood-zones.edit', $zone->id) }}" class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                    <button
                                        type="button"
                                        onclick="openModal('del-zone-{{ $zone->id }}')"
                                        class="btn btn-outline text-xs px-2.5 py-1.5 border-slate-200 text-rose-600 hover:border-rose-200 hover:bg-rose-50 cursor-pointer"
                                    >
                                        Hapus
                                    </button>
                                </div>

                                {{-- Delete Modal --}}
                                <div id="del-zone-{{ $zone->id }}" class="modal-overlay">
                                    <div class="modal-box text-left max-w-md">
                                        <div class="flex items-center gap-3">
                                            <div class="grid size-10 shrink-0 place-items-center rounded-full bg-rose-50 text-rose-600">
                                                <svg class="size-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-base font-bold text-slate-900">Hapus Zona Rawan Banjir?</div>
                                                <div class="text-xs text-slate-400 mt-0.5">Tindakan ini tidak dapat dibatalkan.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 text-sm text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            Apakah Anda yakin ingin menghapus zona rawan banjir <span class="font-bold text-slate-900">"{{ $zone->area_name }}"</span>? Properti yang berada di dalam lingkup wilayah ini tidak akan lagi ditandai rawan banjir.
                                        </div>
                                        
                                        <div class="mt-5 flex gap-3">
                                            <button
                                                type="button"
                                                onclick="closeModal('del-zone-{{ $zone->id }}')"
                                                class="btn btn-outline flex-1 cursor-pointer"
                                            >Batal</button>
                                            <form method="POST" action="{{ route('admin.flood-zones.destroy', $zone->id) }}" class="flex-1">
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
                            <td colspan="3" class="px-6 py-12 text-center text-sm font-semibold text-slate-400">
                                Belum ada zona rawan banjir yang digambar. Klik tombol di atas untuk membuat polygon baru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
