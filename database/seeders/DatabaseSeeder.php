<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $mahasiswaData = [
            ['nim' => '2021010001', 'nama' => 'Aditya Pratama',       'jurusan' => 'Teknik Informatika',    'angkatan' => 2021, 'email' => 'aditya@student.ac.id',   'no_hp' => '08123456701'],
            ['nim' => '2021010002', 'nama' => 'Bunga Citra Lestari',  'jurusan' => 'Sistem Informasi',      'angkatan' => 2021, 'email' => 'bunga@student.ac.id',    'no_hp' => '08123456702'],
            ['nim' => '2021010003', 'nama' => 'Cahyo Nugroho',        'jurusan' => 'Teknik Elektro',        'angkatan' => 2021, 'email' => 'cahyo@student.ac.id',    'no_hp' => '08123456703'],
            ['nim' => '2021020001', 'nama' => 'Dewi Rahayu',          'jurusan' => 'Manajemen',             'angkatan' => 2021, 'email' => 'dewi@student.ac.id',     'no_hp' => '08123456704'],
            ['nim' => '2021020002', 'nama' => 'Eko Susanto',          'jurusan' => 'Akuntansi',             'angkatan' => 2021, 'email' => 'eko@student.ac.id',      'no_hp' => '08123456705'],
            ['nim' => '2022010001', 'nama' => 'Fajar Ramadhan',       'jurusan' => 'Teknik Informatika',    'angkatan' => 2022, 'email' => 'fajar@student.ac.id',    'no_hp' => '08123456706'],
            ['nim' => '2022010002', 'nama' => 'Gita Savitri',         'jurusan' => 'Sistem Informasi',      'angkatan' => 2022, 'email' => 'gita@student.ac.id',     'no_hp' => '08123456707'],
            ['nim' => '2022010003', 'nama' => 'Hendra Wijaya',        'jurusan' => 'Teknik Mesin',          'angkatan' => 2022, 'email' => 'hendra@student.ac.id',   'no_hp' => '08123456708'],
            ['nim' => '2022020001', 'nama' => 'Indah Permatasari',    'jurusan' => 'Psikologi',             'angkatan' => 2022, 'email' => 'indah@student.ac.id',    'no_hp' => '08123456709'],
            ['nim' => '2022020002', 'nama' => 'Joko Widodo',          'jurusan' => 'Hukum',                 'angkatan' => 2022, 'email' => 'joko@student.ac.id',     'no_hp' => '08123456710'],
            ['nim' => '2023010001', 'nama' => 'Kevin Anggara',        'jurusan' => 'Teknik Informatika',    'angkatan' => 2023, 'email' => 'kevin@student.ac.id',    'no_hp' => '08123456711'],
            ['nim' => '2023010002', 'nama' => 'Layla Sari',           'jurusan' => 'Sistem Informasi',      'angkatan' => 2023, 'email' => 'layla@student.ac.id',    'no_hp' => '08123456712'],
            ['nim' => '2023010003', 'nama' => 'Muhammad Rizki',       'jurusan' => 'Teknik Sipil',          'angkatan' => 2023, 'email' => 'rizki@student.ac.id',    'no_hp' => '08123456713'],
            ['nim' => '2023020001', 'nama' => 'Nadia Kusuma',         'jurusan' => 'Kedokteran',            'angkatan' => 2023, 'email' => 'nadia@student.ac.id',    'no_hp' => '08123456714'],
            ['nim' => '2023020002', 'nama' => 'Omar Fauzi',           'jurusan' => 'Farmasi',               'angkatan' => 2023, 'email' => 'omar@student.ac.id',     'no_hp' => '08123456715'],
            ['nim' => '2024010001', 'nama' => 'Putri Ayu Handayani',  'jurusan' => 'Teknik Informatika',    'angkatan' => 2024, 'email' => 'putri@student.ac.id',    'no_hp' => '08123456716'],
            ['nim' => '2024010002', 'nama' => 'Qori Alfatih',         'jurusan' => 'Desain Komunikasi Visual', 'angkatan' => 2024, 'email' => 'qori@student.ac.id', 'no_hp' => '08123456717'],
            ['nim' => '2024010003', 'nama' => 'Reza Fahlefi',         'jurusan' => 'Ilmu Komunikasi',       'angkatan' => 2024, 'email' => 'reza@student.ac.id',     'no_hp' => '08123456718'],
            ['nim' => '2024020001', 'nama' => 'Siti Nurhaliza',       'jurusan' => 'Pendidikan Bahasa',     'angkatan' => 2024, 'email' => 'siti@student.ac.id',     'no_hp' => '08123456719'],
            ['nim' => '2024020002', 'nama' => 'Taufik Hidayat',       'jurusan' => 'Olahraga',              'angkatan' => 2024, 'email' => 'taufik@student.ac.id',   'no_hp' => '08123456720'],
        ];

        foreach ($mahasiswaData as $data) {
            Mahasiswa::create($data);
        }

        // Generate kunjungan dummy untuk 30 hari terakhir
        $nims = array_column($mahasiswaData, 'nim');

        for ($day = 30; $day >= 0; $day--) {
            $date = Carbon::now()->subDays($day);
            $visitCount = rand(3, 12);

            for ($i = 0; $i < $visitCount; $i++) {
                $nim = $nims[array_rand($nims)];
                $masuk = $date->copy()->setHour(rand(8, 18))->setMinute(rand(0, 59));
                $keluar = (rand(0, 3) > 0)
                    ? $masuk->copy()->addMinutes(rand(30, 180))
                    : null;

                Kunjungan::create([
                    'nim'          => $nim,
                    'waktu_masuk'  => $masuk,
                    'waktu_keluar' => $keluar,
                ]);
            }
        }
    }
}
