<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        return view('scan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:20',
        ]);
    
        $nim = trim($request->input('nim'));
        $mahasiswa = Mahasiswa::find($nim);
    
        if (! $mahasiswa) {
            return response()->json([
                'success' => false,
                'message' => "Mahasiswa dengan NIM <strong>{$nim}</strong> tidak ditemukan dalam sistem.",
            ], 404);
        }
    
        // Cek apakah ada kunjungan aktif hari ini (belum keluar)
        $kunjunganAktif = Kunjungan::where('nim', $nim)
            ->whereDate('waktu_masuk', today())
            ->whereNull('waktu_keluar')
            ->latest()
            ->first();
    
        // Kalau masih di dalam → catat waktu keluar
        if ($kunjunganAktif) {
            $kunjunganAktif->update([
                'waktu_keluar' => now(),
            ]);
        
            $durasi = $kunjunganAktif->waktu_masuk->diffInMinutes(now());
        
            return response()->json([
                'success'   => true,
                'tipe'      => 'keluar',
                'message'   => 'Sampai jumpa! Kunjungan selesai.',
                'mahasiswa' => [
                    'nim'      => $mahasiswa->nim,
                    'nama'     => $mahasiswa->nama,
                    'jurusan'  => $mahasiswa->jurusan,
                    'angkatan' => $mahasiswa->angkatan,
                    'email'    => $mahasiswa->email,
                    'no_hp'    => $mahasiswa->no_hp,
                ],
                'kunjungan' => [
                    'id'           => $kunjunganAktif->id,
                    'waktu_masuk'  => $kunjunganAktif->waktu_masuk->format('H:i:s'),
                    'waktu_keluar' => now()->format('H:i:s'),
                    'durasi'       => $durasi . ' menit',
                    'tanggal'      => $kunjunganAktif->waktu_masuk->translatedFormat('l, d F Y'),
                ],
            ]);
        }
    
        // Kalau belum masuk → catat waktu masuk
        $kunjungan = Kunjungan::create([
            'nim'         => $mahasiswa->nim,
            'waktu_masuk' => now(),
        ]);
    
        return response()->json([
            'success'    => true,
            'tipe'       => 'masuk',
            'message'    => 'Selamat datang! Kunjungan berhasil dicatat.',
            'mahasiswa'  => [
                'nim'      => $mahasiswa->nim,
                'nama'     => $mahasiswa->nama,
                'jurusan'  => $mahasiswa->jurusan,
                'angkatan' => $mahasiswa->angkatan,
                'email'    => $mahasiswa->email,
                'no_hp'    => $mahasiswa->no_hp,
            ],
            'kunjungan' => [
                'id'          => $kunjungan->id,
                'waktu_masuk' => $kunjungan->waktu_masuk->format('H:i:s'),
                'tanggal'     => $kunjungan->waktu_masuk->translatedFormat('l, d F Y'),
            ],
        ]);
    }
}
