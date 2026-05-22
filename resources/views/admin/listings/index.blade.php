<x-layouts.admin>
    <div class="card p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="text-sm font-extrabold text-slate-900">Semua Listing Properti</div>
                <div class="mt-1 text-sm text-slate-500">Moderasi dan kelola semua iklan dari seluruh penjual.</div>
            </div>
            <div class="text-xs font-semibold text-slate-400">Total: {{ $properties->total() }} listing</div>
        </div>

        {{-- Filter bar --}}
        <form method="GET" action="{{ route('admin.listings.index') }}" class="mb-5 grid gap-2 sm:grid-cols-[1fr_auto_auto_auto]">
            <input
                name="search" type="text" value="{{ request('search') }}"
                class="input" placeholder="Cari judul properti..."
            />
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
                <button type="submit" class="btn btn-primary">Filter</button>
                @if (request()->hasAny(['search', 'type', 'status']))
                    <a href="{{ route('admin.listings.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </div>
        </form>

        <div class="mt-6 overflow-x-auto rounded-2xl ring-1 ring-slate-200/70">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Properti</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Penjual</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Harga</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-extrabold uppercase tracking-wide text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($properties as $property)
                        <tr class="hover:bg-slate-50/60">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($property->images->isNotEmpty())
                                        <img
                                            src="{{ Storage::url($property->images->first()->path) }}"
                                            alt="{{ $property->title }}"
                                            style="width:64px;height:64px;object-fit:cover;flex-shrink:0"
                                            class="rounded-xl ring-1 ring-slate-200/70"
                                        />
                                    @else
                                        <div style="width:64px;height:64px;flex-shrink:0"
                                             class="grid place-items-center rounded-xl bg-indigo-100 text-sm font-extrabold text-indigo-700">
                                            {{ strtoupper(substr($property->type, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('properties.show', $property->id) }}" target="_blank"
                                           class="block truncate font-extrabold text-slate-900 hover:text-indigo-600 hover:underline">
                                            {{ $property->title }}
                                        </a>
                                        <div class="mt-0.5 text-xs text-slate-400">{{ $property->type }} · {{ $property->land_area }} m²</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs font-semibold text-slate-700">{{ $property->user?->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $property->user?->email ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs font-semibold text-indigo-700 whitespace-nowrap">
                                Rp {{ number_format((float) $property->price, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3">
                                @if ($property->status === 'Terjual')
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Terjual</span>
                                @else
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <button
                                    type="button"
                                    onclick="openModal('del-listing-{{ $property->id }}')"
                                    class="btn btn-outline text-xs text-rose-600 hover:border-rose-300 hover:bg-rose-50"
                                >Hapus</button>
                            </td>
                        </tr>

                        {{-- Delete modal --}}
                        <div id="del-listing-{{ $property->id }}" class="modal-overlay">
                            <div class="modal-box">
                                <div class="text-sm font-extrabold text-slate-900">Hapus Listing?</div>
                                <div class="mt-2 text-sm text-slate-600">
                                    <span class="font-semibold">{{ $property->title }}</span>
                                    milik <span class="font-semibold">{{ $property->user?->name }}</span>
                                    akan dihapus permanen beserta semua fotonya.
                                </div>
                                <div class="mt-5 flex gap-3">
                                    <button
                                        type="button"
                                        onclick="closeModal('del-listing-{{ $property->id }}')"
                                        class="btn btn-outline flex-1"
                                    >Batal</button>
                                    <form method="POST" action="{{ route('admin.listings.destroy', $property->id) }}" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        @foreach (request()->only(['search', 'type', 'status', 'page']) as $key => $value)
                                            <input type="hidden" name="{{ $key }}" value="{{ $value }}" />
                                        @endforeach
                                        <button type="submit" class="btn w-full bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm font-semibold text-slate-400">
                                Tidak ada listing yang sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($properties->hasPages())
            <div class="mt-4">{{ $properties->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
