<div class="mx-auto flex h-[calc(100vh-12rem)] max-w-3xl flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-200">
    <div class="flex items-center justify-between border-b border-gray-200 px-5 py-3">
        <div>
            <p class="font-semibold text-[#1b2a4a]">Asisten Pintar</p>
            <p class="text-xs text-gray-500">{{ $totalDiDb }} pesan tersimpan di tabel <code>chat_messages</code></p>
        </div>
        <button wire:click="hapusRiwayat" wire:confirm="Hapus semua riwayat chat dari database?"
                wire:loading.attr="disabled"
                class="text-xs text-red-600 hover:underline disabled:opacity-50">
            Hapus riwayat
        </button>
    </div>

    {{-- x-init + $watch: auto-scroll ke bawah setiap ada balon baru --}}
    <div class="flex-1 space-y-3 overflow-y-auto bg-gray-50 px-5 py-4"
         x-data
         x-init="$el.scrollTop = $el.scrollHeight; $wire.$watch('riwayat', () => $nextTick(() => $el.scrollTop = $el.scrollHeight))">
        @forelse ($riwayat as $pesan)
            <div wire:key="pesan-{{ $pesan['id'] }}" @class(['flex', 'justify-end' => $pesan['role'] === 'user'])>
                <div @class([
                    'max-w-[80%] rounded-2xl px-4 py-2 text-sm shadow-sm',
                    'rounded-br-sm bg-teal-600 text-white' => $pesan['role'] === 'user',
                    'rounded-bl-sm bg-white text-gray-800 ring-1 ring-gray-200' => $pesan['role'] !== 'user',
                ])>
                    <p class="whitespace-pre-wrap">{{ $pesan['content'] }}</p>
                    <p @class(['mt-1 text-[10px]', 'text-teal-100' => $pesan['role'] === 'user', 'text-gray-400' => $pesan['role'] !== 'user'])>
                        {{ $pesan['role'] === 'user' ? 'Anda' : 'AI' }} · {{ $pesan['jam'] }}
                    </p>
                </div>
            </div>
        @empty
            <p class="pt-10 text-center text-sm text-gray-400">Halo ITS! Tulis pertanyaan pertama Anda di bawah.</p>
        @endforelse

        @if ($menunggu)
            <div class="flex">
                <div class="rounded-2xl rounded-bl-sm bg-white px-4 py-3 text-sm text-gray-500 ring-1 ring-gray-200">
                    <span class="inline-flex gap-1">
                        <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:150ms]"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-gray-400 [animation-delay:300ms]"></span>
                    </span>
                    <span class="ml-2">AI sedang memikirkan jawaban…</span>
                </div>
            </div>
        @endif
    </div>

    <form wire:submit="kirim" class="border-t border-gray-200 p-4">
        <div class="flex gap-2">
            <input type="text" wire:model="prompt"
                   placeholder="Tulis instruksi Anda..."
                   autofocus
                   class="flex-1 rounded-lg border border-gray-300 px-4 py-2 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/30">
            <button type="submit"
                    wire:loading.attr="disabled"
                    @disabled($menunggu)
                    class="rounded-lg bg-teal-600 px-5 py-2 font-medium text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
                <span wire:loading.remove>{{ $menunggu ? 'Menunggu…' : 'Kirim' }}</span>
                <span wire:loading>Memproses…</span>
            </button>
        </div>
        @error('prompt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </form>
</div>
