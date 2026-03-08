<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class RegistrasiController extends Controller
{

    public function index()
    {
        return view('registrasi.index');
    }

    public function search(Request $request)
    {
        $q = $request->input('q', '');
    
        $query = Mahasiswa::query()
            ->when($q, fn($q2) => $q2
                ->where('nim',     'like', "%{$q}%")
                ->orWhere('nama',  'like', "%{$q}%")
                ->orWhere('jurusan', 'like', "%{$q}%")  // ← ganti prodi → jurusan
            )
            ->orderBy('nama')
            ->limit(50)
            ->get(['nim','nama','jurusan','angkatan','registrasi','registrasi_at']); // ← ganti prodi → jurusan
    
        return response()->json([
            'mahasiswa' => $query,
            'stats' => [
                'total'     => Mahasiswa::count(),
                'terdaftar' => Mahasiswa::where('registrasi', true)->count(),
                'belum'     => Mahasiswa::where('registrasi', false)->count(),
            ]
        ]);
    }

    public function toggle(Request $request)
    {
        $mahasiswa = Mahasiswa::where('nim', $request->nim)->firstOrFail();
        
        $mahasiswa->registrasi    = !$mahasiswa->registrasi;
        $mahasiswa->registrasi_at = $mahasiswa->registrasi ? now() : null;
        $mahasiswa->save();

        return response()->json([
            'success'    => true,
            'registrasi' => $mahasiswa->registrasi,
            'nim'        => $mahasiswa->nim,
        ]);
    }
}