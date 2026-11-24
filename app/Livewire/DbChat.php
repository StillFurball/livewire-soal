<?php

namespace App\Livewire;

use App\Models\ChatMessage;
use App\Services\OllamaClient;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

/**
 * Soal 4: setiap balon chat (user dan AI) disimpan ke tabel chat_messages sebelum dirender.
 */
class DbChat extends Component
{
    // Hanya 10 pesan terakhir di state publik agar payload AJAX tetap kecil (slide 25, kesalahan umum #1).
    private const MAKS_RIWAYAT = 10;

    public string $prompt = '';

    /** @var array<int, array{id: int, role: string, content: string, jam: string}> */
    public array $riwayat = [];

    public bool $menunggu = false;

    protected $rules = ['prompt' => 'required|min:3|max:1000'];

    protected $messages = [
        'prompt.required' => 'Kotak prompt tidak boleh dikosongkan!',
        'prompt.min' => 'Instruksi minimal terdiri atas 3 karakter.',
        'prompt.max' => 'Instruksi maksimal 1000 karakter.',
    ];

    public function mount(): void
    {
        // Pulihkan riwayat dari database, jadi percakapan tidak hilang saat halaman di-refresh.
        $this->riwayat = $this->milikSaya()
            ->latest('id')
            ->limit(self::MAKS_RIWAYAT)
            ->get()
            ->reverse()
            ->map(fn (ChatMessage $m) => $this->keArray($m))
            ->values()
            ->all();
    }

    public function kirim(): void
    {
        if ($this->menunggu) {
            return; // lapisan kedua anti double-submit, selain wire:loading di tombol
        }

        $this->validate();

        // 1. Simpan pesan user ke DB, lalu tampilkan.
        $this->tambah(ChatMessage::create([
            'user_id' => auth()->id(),
            'role' => 'user',
            'content' => $this->prompt,
        ]));

        $this->prompt = ''; // Kosongkan box input instan
        $this->menunggu = true;

        // 2. Minta jawaban AI di request berikutnya, supaya balon user langsung muncul
        //    tanpa menunggu LLM selesai berpikir.
        $this->dispatch('minta-jawaban')->self();
    }

    #[On('minta-jawaban')]
    public function jawab(OllamaClient $llm): void
    {
        if (! $this->menunggu) {
            return;
        }

        // Kirim riwayat sebagai konteks agar AI ingat percakapan sebelumnya.
        $konteks = array_map(
            fn (array $m) => ['role' => $m['role'], 'content' => $m['content']],
            $this->riwayat,
        );

        try {
            $balasan = $llm->chat($konteks);
        } catch (Throwable $e) {
            Log::warning('Ollama gagal dihubungi', ['error' => $e->getMessage()]);
            $balasan = 'Koneksi AI terputus. Pastikan Ollama berjalan di '.config('services.ollama.url')
                .' dan model '.config('services.ollama.model').' sudah di-pull.';
        }

        // 3. Simpan balasan AI ke DB, lalu tampilkan.
        $this->tambah(ChatMessage::create([
            'user_id' => auth()->id(),
            'role' => 'assistant',
            'content' => $balasan,
        ]));

        $this->menunggu = false;
    }

    public function hapusRiwayat(): void
    {
        $this->milikSaya()->delete();
        $this->reset('riwayat', 'menunggu');
    }

    public function render(): View
    {
        return view('livewire.db-chat', [
            'totalDiDb' => $this->milikSaya()->count(),
        ]);
    }

    private function tambah(ChatMessage $pesan): void
    {
        $this->riwayat[] = $this->keArray($pesan);
        $this->riwayat = array_slice($this->riwayat, -self::MAKS_RIWAYAT);
    }

    /** Pesan milik user yang login; tamu (belum login) memakai user_id NULL. */
    private function milikSaya(): Builder
    {
        return ChatMessage::query()->where('user_id', auth()->id());
    }

    /** @return array{id: int, role: string, content: string, jam: string} */
    private function keArray(ChatMessage $m): array
    {
        return [
            'id' => $m->id,
            'role' => $m->role,
            'content' => $m->content,
            'jam' => $m->created_at->format('H:i'),
        ];
    }
}
