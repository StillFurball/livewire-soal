<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Soal 3: prompt dibatasi 500 karakter, sisa karakter dihitung real-time di hook updatedPrompt().
 */
class SecureInput extends Component
{
    public const BATAS = 500;

    public string $prompt = '';

    public int $sisaKarakter = self::BATAS;

    public ?string $terkirim = null;

    protected function rules(): array
    {
        return ['prompt' => 'required|min:3|max:'.self::BATAS];
    }

    protected function messages(): array
    {
        return [
            'prompt.required' => 'Kotak prompt tidak boleh dikosongkan!',
            'prompt.min' => 'Instruksi minimal terdiri atas 3 karakter.',
            'prompt.max' => 'Instruksi melebihi batas '.self::BATAS.' karakter.',
        ];
    }

    // Dipanggil otomatis setiap $prompt berubah (updated + NamaProperti).
    public function updatedPrompt(): void
    {
        // mb_strlen, bukan strlen: huruf beraksen/emoji dihitung 1 karakter, bukan beberapa byte.
        $this->sisaKarakter = self::BATAS - mb_strlen($this->prompt);

        // Saat mengetik hanya batas atas yang dicek; "required" & "min" baru dicek ketika dikirim.
        $this->validateOnly('prompt', ['prompt' => 'max:'.self::BATAS]);
    }

    public function kirim(): void
    {
        $this->validate();

        $this->terkirim = $this->prompt;
        $this->reset('prompt');
        $this->sisaKarakter = self::BATAS;
    }

    public function render(): View
    {
        return view('livewire.secure-input');
    }
}
