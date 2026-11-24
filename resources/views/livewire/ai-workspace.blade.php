<div class="grid h-[calc(100vh-12rem)] min-h-[480px] grid-cols-12 gap-4">
    {{-- PANEL KIRI: Kotak Obrolan AI (col-span-4) --}}
    <form wire:submit="prosesKode" class="col-span-12 flex flex-col rounded-lg bg-white p-4 shadow md:col-span-4">
        <h3 class="mb-2 font-bold text-gray-700">Asisten Pintar</h3>

        <textarea wire:model="prompt" rows="6"
                  wire:loading.attr="readonly" wire:target="prosesKode"
                  placeholder="Contoh: buat fungsi PHP untuk memvalidasi format NRP ITS 10 digit"
                  class="w-full rounded border border-gray-300 p-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30"></textarea>
        @error('prompt') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror

        <button type="submit"
                wire:loading.attr="disabled" wire:target="prosesKode"
                class="mt-2 w-full rounded bg-teal-600 py-2 font-medium text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
            <span wire:loading.remove wire:target="prosesKode">Koding!</span>
            <span wire:loading wire:target="prosesKode">Menulis kode…</span>
        </button>

        @if ($galat)
            <p class="mt-3 rounded bg-red-50 p-2 text-xs text-red-700">{{ $galat }}</p>
        @endif

        @if ($riwayatPrompt)
            <div class="mt-4 min-h-0 flex-1 overflow-y-auto">
                <p class="mb-1 text-xs font-semibold uppercase text-gray-400">Prompt sebelumnya</p>
                <ul class="space-y-1 text-xs text-gray-600">
                    @foreach ($riwayatPrompt as $i => $p)
                        <li wire:key="rp-{{ $i }}" class="rounded bg-gray-50 px-2 py-1">{{ $p }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>

    {{-- PANEL KANAN: Editor Output Monospaced (col-span-8) --}}
    <div class="col-span-12 flex min-h-0 flex-col overflow-hidden rounded-lg bg-gray-900 shadow-inner md:col-span-8">
        <div class="flex items-center gap-2 border-b border-gray-700 bg-gray-800 px-4 py-2">
            <span class="h-3 w-3 rounded-full bg-red-500"></span>
            <span class="h-3 w-3 rounded-full bg-yellow-500"></span>
            <span class="h-3 w-3 rounded-full bg-green-500"></span>
            <h3 class="ml-3 font-mono text-sm text-white">{{ $namaFile }}</h3>
            <button type="button"
                    x-data="{ ok: false }"
                    x-on:click="navigator.clipboard.writeText($refs.kode.innerText); ok = true; setTimeout(() => ok = false, 1500)"
                    class="ml-auto rounded px-2 py-0.5 text-xs text-gray-300 hover:bg-gray-700">
                <span x-text="ok ? 'Tersalin ✓' : 'Salin'">Salin</span>
            </button>
        </div>

        <div class="relative min-h-0 flex-1 overflow-auto p-6">
            {{-- {{ }} meng-escape HTML, jadi kode dari AI tampil sebagai teks dan tidak dieksekusi browser --}}
            <pre x-ref="kode" class="font-mono text-sm leading-relaxed text-green-400" wire:loading.class="opacity-40" wire:target="prosesKode">{{ $hasilKode }}</pre>

            <div wire:loading wire:target="prosesKode" class="mt-4 animate-pulse text-xs text-yellow-400">
                AI Sedang Menulis Baris Kode Baru...
            </div>
        </div>

        @if ($catatan)
            <div class="border-t border-gray-700 bg-gray-800 px-4 py-2 text-xs text-gray-300">{{ $catatan }}</div>
        @endif
    </div>
</div>
