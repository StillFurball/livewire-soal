<?php

namespace App\Livewire;

use App\Models\Tugas;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Soal 2: tombol simpan dikunci selama request berjalan supaya tidak terjadi duplikasi data.
 */
class BuatTugas extends Component
{
    #[Validate('required|min:5|max:500', message: [
        'required' => 'Teks tugas wajib diisi.',
        'min' => 'Teks tugas minimal 5 karakter.',
        'max' => 'Teks tugas maksimal 500 karakter.',
    ])]
    public string $teksTugas = '';

    public function simpanTugas(): void
    {
        $this->validate();

        // Simulasi koneksi server lambat agar efek loading & tombol terkunci terlihat saat demo.
        sleep(2);

        Tugas::create(['teks' => $this->teksTugas]);

        $this->reset('teksTugas');
        session()->flash('status', 'Tugas tersimpan.');
    }

    public function render(): View
    {
        return view('livewire.buat-tugas', [
            'daftarTugas' => Tugas::latest()->limit(10)->get(),
            'jumlah' => Tugas::count(),
        ]);
    }
}
