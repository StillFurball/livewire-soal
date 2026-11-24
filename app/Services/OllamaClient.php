<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Pembungkus tipis untuk endpoint /api/chat milik Ollama (materi Pertemuan 11).
 * Dipakai bersama oleh Soal 4 (DbChat) dan Soal 5 (AiWorkspace).
 */
class OllamaClient
{
    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     *
     * @throws RuntimeException jika server tidak bisa dihubungi atau balasan kosong
     */
    public function chat(array $messages): string
    {
        $response = Http::timeout(config('services.ollama.timeout'))
            ->post(rtrim(config('services.ollama.url'), '/').'/api/chat', [
                'model' => config('services.ollama.model'),
                'messages' => $messages,
                'stream' => false,
                'think' => false, // qwen3: jangan sertakan blok <think> di jawaban
            ])
            ->throw();

        $content = trim((string) $response->json('message.content'));

        // Jaga-jaga bila model tetap mengirim blok <think>...</think>
        $content = trim(preg_replace('/<think>.*?<\/think>/s', '', $content));

        if ($content === '') {
            throw new RuntimeException('Balasan LLM kosong.');
        }

        return $content;
    }
}
