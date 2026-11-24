<?php

namespace Tests\Feature;

use App\Livewire\AiWorkspace;
use App\Livewire\BuatTugas;
use App\Livewire\CariMahasiswa;
use App\Livewire\DbChat;
use App\Livewire\SecureInput;
use App\Models\ChatMessage;
use App\Models\Mahasiswa;
use App\Models\Tugas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Tests\TestCase;

class SoalLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_semua_halaman_bisa_dibuka(): void
    {
        foreach (['/', '/soal-1', '/soal-2', '/soal-3', '/soal-4', '/soal-5'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_soal1_menyaring_berdasarkan_nama_dan_nrp(): void
    {
        Mahasiswa::factory()->create(['nama' => 'Budi Hartono', 'nrp' => '5025211001']);
        Mahasiswa::factory()->create(['nama' => 'Siti Aminah', 'nrp' => '5025211002']);

        Livewire::test(CariMahasiswa::class)
            ->set('cari', 'budi')
            ->assertSee('Budi Hartono')
            ->assertDontSee('Siti Aminah')
            ->set('cari', '1002')
            ->assertSee('Siti Aminah')
            ->assertDontSee('Budi Hartono')
            ->set('cari', 'tidak-ada')
            ->assertSee('Tidak ada mahasiswa');
    }

    public function test_soal2_menyimpan_tugas_dan_validasi(): void
    {
        Livewire::test(BuatTugas::class)
            ->call('simpanTugas')
            ->assertHasErrors(['teksTugas' => 'required'])
            ->set('teksTugas', 'Kerjakan soal Livewire')
            ->call('simpanTugas')
            ->assertHasNoErrors()
            ->assertSet('teksTugas', '');

        $this->assertSame(1, Tugas::count());
    }

    public function test_soal3_penghitung_dan_batas_karakter(): void
    {
        Livewire::test(SecureInput::class)
            ->set('prompt', 'halo')
            ->assertSet('sisaKarakter', 496)
            ->assertHasNoErrors()
            ->set('prompt', str_repeat('a', 501))
            ->assertSet('sisaKarakter', -1)
            ->assertHasErrors(['prompt' => 'max'])
            ->set('prompt', 'é') // 1 karakter multibyte tetap dihitung 1
            ->assertSet('sisaKarakter', 499);
    }

    public function test_soal4_pesan_user_dan_ai_tersimpan_di_database(): void
    {
        Http::fake(['*/api/chat' => Http::response(['message' => ['content' => 'Jawaban dari AI']])]);

        Livewire::test(DbChat::class)
            ->set('prompt', 'Apa itu Livewire?')
            ->call('kirim')
            ->assertSet('prompt', '')
            ->assertSet('menunggu', true)
            ->assertDispatched('minta-jawaban')
            ->call('jawab')
            ->assertSet('menunggu', false)
            ->assertSee('Jawaban dari AI');

        $this->assertSame(['user', 'assistant'], ChatMessage::orderBy('id')->pluck('role')->all());

        // Riwayat dipulihkan dari DB saat komponen dimuat ulang (refresh browser).
        Livewire::test(DbChat::class)->assertSee('Apa itu Livewire?')->assertSee('Jawaban dari AI');
    }

    public function test_soal4_ollama_mati_tidak_membuat_crash(): void
    {
        Http::fake(['*/api/chat' => Http::response('down', 500)]);

        Livewire::test(DbChat::class)
            ->set('prompt', 'Halo AI')
            ->call('kirim')
            ->call('jawab')
            ->assertSee('Koneksi AI terputus');
    }

    public function test_soal5_blok_kode_dipisah_dan_nama_file_menyesuaikan(): void
    {
        Http::fake(['*/api/chat' => Http::response(['message' => [
            'content' => "```python\nprint('halo ITS')\n```\nKode ini mencetak salam.",
        ]])]);

        Livewire::test(AiWorkspace::class)
            ->set('prompt', 'cetak halo ITS dengan python')
            ->call('prosesKode')
            ->assertSet('hasilKode', "print('halo ITS')")
            ->assertSet('namaFile', 'output_file.py')
            ->assertSet('catatan', 'Kode ini mencetak salam.')
            ->assertSet('prompt', '');
    }
}
