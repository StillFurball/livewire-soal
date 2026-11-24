<div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
    <div class="relative mb-4">
        <input type="text"
               wire:model.live.debounce.300ms="cari"
               placeholder="Ketik nama/NRP..."
               autofocus
               class="w-full rounded-lg border border-gray-300 px-4 py-2.5 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
        <span wire:loading wire:target="cari" class="absolute right-3 top-3 text-xs text-gray-400">mencari…</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-teal-700 text-white">
                <tr>
                    <th class="px-3 py-2">NRP</th>
                    <th class="px-3 py-2">Nama</th>
                    <th class="px-3 py-2">Jurusan</th>
                    <th class="px-3 py-2">Dosen Wali</th>
                </tr>
            </thead>
            <tbody wire:loading.class="opacity-50" wire:target="cari">
                @forelse ($mahasiswas as $mhs)
                    <tr class="border-b border-gray-100 odd:bg-gray-50" wire:key="mhs-{{ $mhs->id }}">
                        <td class="px-3 py-2 font-mono">{{ $mhs->nrp }}</td>
                        <td class="px-3 py-2">{{ $mhs->nama }}</td>
                        <td class="px-3 py-2">{{ $mhs->jurusan }}</td>
                        <td class="px-3 py-2">{{ $mhs->dosen_wali }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-6 text-center text-gray-500">
                            Tidak ada mahasiswa yang cocok dengan "{{ $cari }}".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $mahasiswas->links() }}</div>
</div>
