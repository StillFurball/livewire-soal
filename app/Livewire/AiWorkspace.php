<?php

namespace App\Livewire;

use App\Services\OllamaClient;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;

/**
 * Soal 5: workspace split-panel. Prompt di kiri, kode hasil AI di panel editor kanan.
 */
class AiWorkspace extends Component
{
    public string $prompt = '';

    public string $hasilKode = "<?php\n\n// Kode hasil AI akan muncul di sini.\n";

    public string $namaFile = 'output_file.php';

    public ?string $catatan = null;

    public ?string $galat = null;

    /** @var array<int, string> prompt yang pernah dikirim, terbaru di atas */
    public array $riwayatPrompt = [];

    protected $rules = ['prompt' => 'required|min:3|max:1000'];

    protected $messages = [
        'prompt.required' => 'Tulis dulu kode apa yang ingin dibuat.',
        'prompt.min' => 'Instruksi minimal 3 karakter.',
        'prompt.max' => 'Instruksi maksimal 1000 karakter.',
    ];

    public function prosesKode(OllamaClient $llm): void
    {
        $this->validate();
        $this->galat = null;

        try {
            $jawaban = $llm->chat([
                ['role' => 'system', 'content' => 'Anda asisten pemrograman. Balas dengan SATU blok kode markdown '
                    .'(```bahasa ... ```) berisi kode lengkap, lalu maksimal dua kalimat penjelasan dalam bahasa Indonesia.'],
                ['role' => 'user', 'content' => $this->prompt],
            ]);

            [$bahasa, $kode, $sisa] = $this->pisahkanBlokKode($jawaban);

            $this->hasilKode = $kode;
            $this->namaFile = 'output_file.'.$this->ekstensi($bahasa);
            $this->catatan = $sisa !== '' ? $sisa : null;

            array_unshift($this->riwayatPrompt, $this->prompt);
            $this->riwayatPrompt = array_slice($this->riwayatPrompt, 0, 5);
            $this->prompt = '';
        } catch (Throwable $e) {
            Log::warning('Ollama gagal dihubungi', ['error' => $e->getMessage()]);
            // Prompt sengaja tidak dikosongkan supaya user bisa mencoba lagi.
            $this->galat = 'Koneksi AI terputus. Pastikan Ollama berjalan di '.config('services.ollama.url').'.';
        }
    }

    public function render(): View
    {
        return view('livewire.ai-workspace');
    }

    /**
     * Ambil blok ```bahasa ... ``` pertama dari jawaban AI.
     *
     * @return array{0: string, 1: string, 2: string} [bahasa, kode, teks di luar blok kode]
     */
    private function pisahkanBlokKode(string $jawaban): array
    {
        if (preg_match('/```([\w+#.-]*)[^\n]*\n(.*?)```/s', $jawaban, $m)) {
            $sisa = trim(str_replace($m[0], '', $jawaban));

            return [strtolower($m[1]), rtrim($m[2]), $sisa];
        }

        // Tidak ada blok kode: anggap seluruh jawaban adalah kode.
        return ['', $jawaban, ''];
    }

    private function ekstensi(string $bahasa): string
    {
        return [
            'php' => 'php', 'blade' => 'blade.php', 'html' => 'html', 'css' => 'css',
            'javascript' => 'js', 'js' => 'js', 'typescript' => 'ts', 'ts' => 'ts',
            'python' => 'py', 'py' => 'py', 'java' => 'java', 'c' => 'c', 'cpp' => 'cpp', 'c++' => 'cpp',
            'go' => 'go', 'rust' => 'rs', 'sql' => 'sql', 'bash' => 'sh', 'sh' => 'sh', 'json' => 'json',
        ][$bahasa] ?? 'txt';
    }
}
