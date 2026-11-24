@php
    $batas = \App\Livewire\SecureInput::BATAS;
    $persen = min(100, max(0, ($batas - $sisaKarakter) / $batas * 100));
    $warna = $sisaKarakter < 0 ? 'bg-red-500' : ($sisaKarakter <= 50 ? 'bg-amber-500' : 'bg-teal-500');
@endphp
<div class="max-w-3xl rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
    <form wire:submit="kirim">
        <label for="prompt" class="mb-2 block font-semibold text-[#1b2a4a]">Prompt instruksi asisten AI</label>
        <textarea id="prompt" wire:model.live="prompt" rows="6"
                  placeholder="Tulis instruksi untuk asisten AI..."
                  @class([
                      'w-full rounded-lg border p-3 focus:outline-none focus:ring-2',
                      'border-red-500 focus:ring-red-500/30' => $sisaKarakter < 0,
                      'border-gray-300 focus:border-teal-500 focus:ring-teal-500/30' => $sisaKarakter >= 0,
                  ])></textarea>

        <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200">
            <div class="h-full {{ $warna }} transition-all" style="width: {{ $persen }}%"></div>
        </div>

        <div class="mt-2 flex items-center justify-between text-sm">
            <span class="{{ $sisaKarakter < 0 ? 'font-semibold text-red-500' : 'text-gray-500' }}">
                Sisa Karakter: {{ $sisaKarakter }} karakter
            </span>
            <button type="submit"
                    wire:loading.attr="disabled" wire:target="kirim"
                    @disabled($sisaKarakter < 0)
                    class="rounded-lg bg-teal-600 px-4 py-2 font-medium text-white hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-50">
                Kirim
            </button>
        </div>

        @error('prompt') <p class="mt-2 text-sm text-red-500">{{ $message }}</p> @enderror
    </form>

    @if ($terkirim)
        <div class="mt-5 rounded-lg bg-teal-50 p-4 text-sm">
            <p class="mb-1 font-semibold text-teal-800">Prompt terakhir yang lolos validasi:</p>
            <p class="whitespace-pre-wrap text-gray-700">{{ $terkirim }}</p>
        </div>
    @endif
</div>
