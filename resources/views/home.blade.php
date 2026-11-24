<x-layout title="Beranda" heading="Bagian 5: Contoh Soal & Solusi Livewire"
          subheading="Lima skenario dari slide Pertemuan 12, masing-masing satu komponen Livewire.">
    @php
        $soal = [
            ['soal1', 'Pencarian Mahasiswa Real-Time', 'wire:model.live + query Eloquent di render(), dengan paginasi.', 'CariMahasiswa'],
            ['soal2', 'Menghindari Spam Double Click', 'wire:submit + wire:loading.attr="disabled" + spinner di tombol.', 'BuatTugas'],
            ['soal3', 'Validasi dengan Penghitung Karakter', 'Hook updatedPrompt() + validateOnly() untuk batas 500 karakter.', 'SecureInput'],
            ['soal4', 'Chat Box Tersimpan di Database', 'Setiap pesan user & AI disimpan ke tabel chat_messages.', 'DbChat'],
            ['soal5', 'Split-Panel Workspace Code Editor', 'Prompt di kiri (4 kolom), kode hasil AI di kanan (8 kolom).', 'AiWorkspace'],
        ];
    @endphp
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($soal as [$route, $judul, $desk, $kelas])
            <a href="{{ route($route) }}" class="block rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-200 transition hover:ring-teal-500">
                <p class="text-xs font-semibold uppercase tracking-wide text-[#d4605f]">Soal {{ $loop->iteration }}</p>
                <h2 class="mt-1 font-semibold text-[#1b2a4a]">{{ $judul }}</h2>
                <p class="mt-2 text-sm text-gray-600">{{ $desk }}</p>
                <p class="mt-3 font-mono text-xs text-teal-700">app/Livewire/{{ $kelas }}.php</p>
            </a>
        @endforeach
    </div>
</x-layout>
