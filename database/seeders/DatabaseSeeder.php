<?php

namespace Database\Seeders;

use App\Models\Kunjungan;
use App\Models\Mahasiswa;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Daftar prodi resmi Unirow
     * Format NIM: ANGKATAN + KODE_PRODI + NOURUT (contoh: 2021262010001)
     * Kode prodi diambil dari 5 digit kode resmi Unirow
     */
    public function run(): void
    {
        // ── Prodi resmi Unirow ──
        // kode => nama lengkap
        $prodi = [
            '26201' => 'S1 Teknik Industri',
            '44201' => 'S1 Matematika',
            '46201' => 'S1 Biologi',
            '54241' => 'S1 Ilmu Kelautan',
            '54242' => 'S1 Ilmu Perikanan',
            '55201' => 'S1 Teknik Informatika',
            '67201' => 'S1 Ilmu Politik',
            '70201' => 'S1 Ilmu Komunikasi',
            '84105' => 'S1 P. Biologi',
            '84202' => 'S1 P. Matematika',
            '84205' => 'S2 Pendidikan Biologi',
            '86122' => 'S2 Pendidikan Dasar',
            '86206' => 'S1 PGSD',
            '86207' => 'S1 PGPAUD',
            '87203' => 'S1 P. Ekonomi',
            '87205' => 'S1 P. PKN',
            '88201' => 'S1 P. B. Indonesia',
            '88203' => 'S1 P. B. Inggris',
        ];

        $mahasiswaData = [
            // S1 Teknik Industri
            ['nim' => '2021262010001', 'nama' => 'Aditya Pratama',        'jurusan' => 'S1 Teknik Industri',     'angkatan' => 2021, 'email' => 'aditya.pratama@mhs.unirow.ac.id',       'no_hp' => '08123456701'],
            ['nim' => '2022262010001', 'nama' => 'Bagas Setiawan',         'jurusan' => 'S1 Teknik Industri',     'angkatan' => 2022, 'email' => 'bagas.setiawan@mhs.unirow.ac.id',       'no_hp' => '08123456702'],
            // S1 Matematika
            ['nim' => '2021442010001', 'nama' => 'Citra Dewi Lestari',     'jurusan' => 'S1 Matematika',          'angkatan' => 2021, 'email' => 'citra.dewi@mhs.unirow.ac.id',           'no_hp' => '08123456703'],
            ['nim' => '2023442010001', 'nama' => 'Dimas Ari Wicaksono',    'jurusan' => 'S1 Matematika',          'angkatan' => 2023, 'email' => 'dimas.ari@mhs.unirow.ac.id',            'no_hp' => '08123456704'],
            // S1 Biologi
            ['nim' => '2022462010001', 'nama' => 'Eka Putri Rahayu',       'jurusan' => 'S1 Biologi',             'angkatan' => 2022, 'email' => 'eka.putri@mhs.unirow.ac.id',            'no_hp' => '08123456705'],
            ['nim' => '2024462010001', 'nama' => 'Fauzan Hidayat',         'jurusan' => 'S1 Biologi',             'angkatan' => 2024, 'email' => 'fauzan.hidayat@mhs.unirow.ac.id',       'no_hp' => '08123456706'],
            // S1 Ilmu Kelautan
            ['nim' => '2021542410001', 'nama' => 'Gita Saraswati',         'jurusan' => 'S1 Ilmu Kelautan',       'angkatan' => 2021, 'email' => 'gita.saraswati@mhs.unirow.ac.id',       'no_hp' => '08123456707'],
            ['nim' => '2023542410001', 'nama' => 'Hendra Kusuma',          'jurusan' => 'S1 Ilmu Kelautan',       'angkatan' => 2023, 'email' => 'hendra.kusuma@mhs.unirow.ac.id',        'no_hp' => '08123456708'],
            // S1 Ilmu Perikanan
            ['nim' => '2022542420001', 'nama' => 'Indah Permatasari',      'jurusan' => 'S1 Ilmu Perikanan',      'angkatan' => 2022, 'email' => 'indah.permatasari@mhs.unirow.ac.id',    'no_hp' => '08123456709'],
            ['nim' => '2024542420001', 'nama' => 'Joko Santoso',           'jurusan' => 'S1 Ilmu Perikanan',      'angkatan' => 2024, 'email' => 'joko.santoso@mhs.unirow.ac.id',         'no_hp' => '08123456710'],
            // S1 Teknik Informatika
            ['nim' => '2021552010001', 'nama' => 'Kevin Anggara Putra',    'jurusan' => 'S1 Teknik Informatika',  'angkatan' => 2021, 'email' => 'kevin.anggara@mhs.unirow.ac.id',        'no_hp' => '08123456711'],
            ['nim' => '2022552010001', 'nama' => 'Layla Nur Fadilah',      'jurusan' => 'S1 Teknik Informatika',  'angkatan' => 2022, 'email' => 'layla.nur@mhs.unirow.ac.id',            'no_hp' => '08123456712'],
            ['nim' => '2023552010001', 'nama' => 'Muhammad Rizki Akbar',   'jurusan' => 'S1 Teknik Informatika',  'angkatan' => 2023, 'email' => 'rizki.akbar@mhs.unirow.ac.id',          'no_hp' => '08123456713'],
            ['nim' => '2024552010001', 'nama' => 'Nadia Putri Utami',      'jurusan' => 'S1 Teknik Informatika',  'angkatan' => 2024, 'email' => 'nadia.putri@mhs.unirow.ac.id',          'no_hp' => '08123456714'],
            // S1 Ilmu Politik
            ['nim' => '2022672010001', 'nama' => 'Omar Fauzi Ramadhan',    'jurusan' => 'S1 Ilmu Politik',        'angkatan' => 2022, 'email' => 'omar.fauzi@mhs.unirow.ac.id',           'no_hp' => '08123456715'],
            // S1 Ilmu Komunikasi
            ['nim' => '2022702010001', 'nama' => 'Putri Ayu Handayani',    'jurusan' => 'S1 Ilmu Komunikasi',     'angkatan' => 2022, 'email' => 'putri.ayu@mhs.unirow.ac.id',            'no_hp' => '08123456716'],
            ['nim' => '2023702010001', 'nama' => 'Qori Alfatih Nugraha',   'jurusan' => 'S1 Ilmu Komunikasi',     'angkatan' => 2023, 'email' => 'qori.alfatih@mhs.unirow.ac.id',         'no_hp' => '08123456717'],
            // S1 PGSD
            ['nim' => '2021862060001', 'nama' => 'Reza Pahlevi Susanto',   'jurusan' => 'S1 PGSD',                'angkatan' => 2021, 'email' => 'reza.pahlevi@mhs.unirow.ac.id',         'no_hp' => '08123456718'],
            ['nim' => '2023862060001', 'nama' => 'Siti Nur Khasanah',      'jurusan' => 'S1 PGSD',                'angkatan' => 2023, 'email' => 'siti.nur@mhs.unirow.ac.id',             'no_hp' => '08123456719'],
            // S1 P. B. Indonesia
            ['nim' => '2022882010001', 'nama' => 'Taufik Dwi Prasetyo',    'jurusan' => 'S1 P. B. Indonesia',     'angkatan' => 2022, 'email' => 'taufik.dwi@mhs.unirow.ac.id',           'no_hp' => '08123456720'],
            // S1 P. B. Inggris
            ['nim' => '2023882030001', 'nama' => 'Ulfa Rohmawati',         'jurusan' => 'S1 P. B. Inggris',       'angkatan' => 2023, 'email' => 'ulfa.rohmawati@mhs.unirow.ac.id',       'no_hp' => '08123456721'],
            // S1 P. Ekonomi
            ['nim' => '2022872030001', 'nama' => 'Vicky Nugroho Wibowo',   'jurusan' => 'S1 P. Ekonomi',          'angkatan' => 2022, 'email' => 'vicky.nugroho@mhs.unirow.ac.id',        'no_hp' => '08123456722'],
            // S1 P. PKN
            ['nim' => '2023872050001', 'nama' => 'Winda Kurnia Sari',      'jurusan' => 'S1 P. PKN',              'angkatan' => 2023, 'email' => 'winda.kurnia@mhs.unirow.ac.id',         'no_hp' => '08123456723'],
            // S1 P. Matematika
            ['nim' => '2021842020001', 'nama' => 'Xena Maulidia Putri',    'jurusan' => 'S1 P. Matematika',       'angkatan' => 2021, 'email' => 'xena.maulidia@mhs.unirow.ac.id',        'no_hp' => '08123456724'],
            // S1 P. Biologi
            ['nim' => '2022841050001', 'nama' => 'Yoga Pratama Atmaja',    'jurusan' => 'S1 P. Biologi',          'angkatan' => 2022, 'email' => 'yoga.pratama@mhs.unirow.ac.id',         'no_hp' => '08123456725'],
            // S1 PGPAUD
            ['nim' => '2023862070001', 'nama' => 'Zahra Aulia Fitri',      'jurusan' => 'S1 PGPAUD',              'angkatan' => 2023, 'email' => 'zahra.aulia@mhs.unirow.ac.id',          'no_hp' => '08123456726'],
            // S2 Pendidikan Biologi
            ['nim' => '2022842050001', 'nama' => 'Ahmad Fauzi Hakim',      'jurusan' => 'S2 Pendidikan Biologi',  'angkatan' => 2022, 'email' => 'ahmad.fauzi@mhs.unirow.ac.id',          'no_hp' => '08123456727'],
            // S2 Pendidikan Dasar
            ['nim' => '2023861220001', 'nama' => 'Bella Oktaviani',        'jurusan' => 'S2 Pendidikan Dasar',    'angkatan' => 2023, 'email' => 'bella.oktaviani@mhs.unirow.ac.id',      'no_hp' => '08123456728'],
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
