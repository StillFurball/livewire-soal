<?php

namespace Database\Factories;

use App\Models\Mahasiswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mahasiswa>
 */
class MahasiswaFactory extends Factory
{
    public function definition(): array
    {
        $jurusan = ['Teknik Informatika', 'Sistem Informasi', 'Teknologi Informasi', 'Teknik Komputer', 'Sains Data'];
        $dosen = ['Dr. Rina Kusuma', 'Prof. Ahmad Fauzi', 'Dr. Siti Rahma', 'Ir. Budi Santoso', 'Dr. Dewi Lestari'];

        return [
            // NRP ITS: 10 digit, diawali 5025 untuk Informatika
            'nrp' => '5025'.fake()->unique()->numerify('2#####'),
            'nama' => fake('id_ID')->name(),
            'jurusan' => fake()->randomElement($jurusan),
            'dosen_wali' => fake()->randomElement($dosen),
        ];
    }
}
