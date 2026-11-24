<?php

namespace App\Livewire;

use App\Models\Mahasiswa;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Soal 1: kotak pencarian yang menyaring tabel setiap kali karakter diketik.
 */
class CariMahasiswa extends Component
{
    use WithPagination;

    // #[Url] menyimpan kata kunci di query string (?cari=...) supaya bisa di-refresh/dibagikan.
    #[Url(except: '')]
    public string $cari = '';

    // Kata kunci berubah → kembali ke halaman 1, agar tidak terjebak di halaman yang sudah tidak ada.
    public function updatedCari(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $kata = trim($this->cari);

        $data = Mahasiswa::query()
            ->when($kata !== '', function ($q) use ($kata) {
                // Dikelompokkan dalam closure supaya OR tidak "bocor" bila nanti ada where lain.
                $q->where(function ($q) use ($kata) {
                    $q->where('nama', 'like', "%{$kata}%")
                        ->orWhere('nrp', 'like', "%{$kata}%");
                });
            })
            ->orderBy('nama')
            ->paginate(5);

        return view('livewire.cari-mahasiswa', ['mahasiswas' => $data]);
    }
}
