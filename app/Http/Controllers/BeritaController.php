<?php
namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BeritaController extends Controller
{
    // Halaman admin
    public function index()
    {
        return view('berita.index');
    }

    // API: list berita untuk portal (publik)
    public function list(Request $request)
    {
        $offset = (int) $request->input('offset', 0);
        $limit  = (int) $request->input('limit', 6);

        $total = Berita::where('published', true)->count();
        $items = Berita::where('published', true)
            ->orderByDesc('created_at')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'data'     => $items,
            'total'    => $total,
            'has_more' => ($offset + $limit) < $total,
        ]);
    }

    // API: list untuk admin (semua termasuk unpublished)
    public function adminList()
    {
        return response()->json(
            Berita::orderByDesc('created_at')->get()
        );
    }

    // Simpan berita baru
    public function store(Request $request)
    {
        $request->validate([
            'judul'    => 'required|string|max:255',
            'narasi'   => 'required|string',
            'kategori' => 'required|string',
            'foto'     => 'nullable|image|max:2048',
        ]);

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('berita', 'public');
        }

        $berita = Berita::create([
            'judul'     => $request->judul,
            'narasi'    => $request->narasi,
            'kategori'  => $request->kategori,
            'foto'      => $fotoPath,
            'published' => $request->boolean('published', true),
        ]);

        return response()->json(['success' => true, 'data' => $berita]);
    }

    // Update
    public function update(Request $request, $id)
    {
        $berita = Berita::findOrFail($id);

        if ($request->hasFile('foto')) {
            if ($berita->foto) Storage::disk('public')->delete($berita->foto);
            $berita->foto = $request->file('foto')->store('berita', 'public');
        }

        $berita->judul     = $request->judul ?? $berita->judul;
        $berita->narasi    = $request->narasi ?? $berita->narasi;
        $berita->kategori  = $request->kategori ?? $berita->kategori;
        $berita->published = $request->boolean('published', $berita->published);
        $berita->save();

        return response()->json(['success' => true, 'data' => $berita]);
    }

    // Hapus
    public function destroy($id)
    {
        $berita = Berita::findOrFail($id);
        if ($berita->foto) Storage::disk('public')->delete($berita->foto);
        $berita->delete();
        return response()->json(['success' => true]);
    }

    // Toggle publish
    public function togglePublish($id)
    {
        $berita = Berita::findOrFail($id);
        $berita->published = !$berita->published;
        $berita->save();
        return response()->json(['success' => true, 'published' => $berita->published]);
    }
}