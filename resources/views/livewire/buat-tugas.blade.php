<div class="grid gap-5 lg:grid-cols-2">
    <form wire:submit="simpanTugas" class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <label for="teksTugas" class="mb-2 block font-semibold text-[#1b2a4a]">Tugas Kuliah Baru</label>
        <textarea id="teksTugas" wire:model="teksTugas" rows="5"
                  wire:loading.attr="readonly" wire:target="simpanTugas"
                  placeholder="Contoh: Kerjakan Soal 1–5 Livewire, kumpulkan sebelum Pertemuan 13"
                  class="w-full rounded-lg border border-gray-300 p-3 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"></textarea>
        @error('teksTugas') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

        <button type="submit"
                wire:loading.attr="disabled"
                wire:target="simpanTugas"
                class="mt-3 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-50">
            {{-- Teks normal, disembunyikan saat request berjalan --}}
            <span wire:loading.remove wire:target="simpanTugas">Simpan Tugas Kuliah</span>

            {{-- Hanya tampil ketika server sedang menyimpan --}}
            <span wire:loading wire:target="simpanTugas" class="inline-flex items-center gap-2">
                <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                    <path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path>
                </svg>
                Sedang Mengirim ke Database...
            </span>
        </button>

        @if (session('status'))
            <p class="mt-3 text-sm text-green-700">{{ session('status') }}</p>
        @endif

        <p class="mt-4 text-xs text-gray-500">
            Server sengaja ditunda 2 detik. Coba klik tombol berkali-kali: hanya satu tugas yang tersimpan.
        </p>
    </form>

    <div class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
        <h2 class="mb-3 font-semibold text-[#1b2a4a]">Tersimpan di database ({{ $jumlah }})</h2>
        <ul class="space-y-2 text-sm">
            @forelse ($daftarTugas as $tugas)
                <li wire:key="tugas-{{ $tugas->id }}" class="rounded-lg bg-gray-50 px-3 py-2">
                    <span class="text-xs text-gray-400">#{{ $tugas->id }} · {{ $tugas->created_at->format('H:i:s') }}</span>
                    <p>{{ $tugas->teks }}</p>
                </li>
            @empty
                <li class="text-gray-500">Belum ada tugas.</li>
            @endforelse
        </ul>
    </div>
</div>
