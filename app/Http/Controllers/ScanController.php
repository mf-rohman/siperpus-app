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

        $kunjungan = Kunjungan::create([
            'nim'         => $mahasiswa->nim,
            'waktu_masuk' => now(),
        ]);

        return response()->json([
            'success'    => true,
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
