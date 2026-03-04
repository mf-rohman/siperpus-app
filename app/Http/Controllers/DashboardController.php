<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index');
    }

    public function stats(Request $request)
    {
        $hariIni    = Kunjungan::whereDate('waktu_masuk', today())->count();
        $mingguIni  = Kunjungan::whereBetween('waktu_masuk', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ])->count();
        $bulanIni   = Kunjungan::whereMonth('waktu_masuk', now()->month)
                               ->whereYear('waktu_masuk', now()->year)
                               ->count();
        $totalAll   = Kunjungan::count();

        // Chart: 7 hari terakhir
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartData[] = [
                'tanggal' => $date->format('D, d M'),
                'total'   => Kunjungan::whereDate('waktu_masuk', $date->toDateString())->count(),
            ];
        }

        // Chart: 30 hari (untuk grafik bulanan)
        $chartMonth = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartMonth[] = [
                'tanggal' => $date->format('d/m'),
                'total'   => Kunjungan::whereDate('waktu_masuk', $date->toDateString())->count(),
            ];
        }

        // Jurusan terpopuler
        $jurusanPopuler = DB::table('kunjungan')
            ->join('mahasiswa', 'kunjungan.nim', '=', 'mahasiswa.nim')
            ->select('mahasiswa.jurusan', DB::raw('COUNT(*) as total'))
            ->whereMonth('kunjungan.waktu_masuk', now()->month)
            ->groupBy('mahasiswa.jurusan')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 10 kunjungan terbaru
        $kunjunganTerbaru = Kunjungan::with('mahasiswa')
            ->latest('waktu_masuk')
            ->limit(10)
            ->get()
            ->map(fn($k) => [
                'id'            => $k->id,
                'nim'           => $k->nim,
                'nama'          => $k->mahasiswa?->nama ?? '-',
                'jurusan'       => $k->mahasiswa?->jurusan ?? '-',
                'waktu_masuk'   => $k->waktu_masuk->format('d/m/Y H:i'),
                'waktu_keluar'  => $k->waktu_keluar?->format('d/m/Y H:i') ?? '—',
                'durasi'        => $k->waktu_keluar
                    ? $k->waktu_masuk->diffInMinutes($k->waktu_keluar) . ' mnt'
                    : 'Masih di dalam',
            ]);

        // Filter date range jika ada
        if ($request->filled(['start', 'end'])) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();

            $filtered = Kunjungan::with('mahasiswa')
                ->whereBetween('waktu_masuk', [$start, $end])
                ->latest('waktu_masuk')
                ->limit(50)
                ->get()
                ->map(fn($k) => [
                    'id'            => $k->id,
                    'nim'           => $k->nim,
                    'nama'          => $k->mahasiswa?->nama ?? '-',
                    'jurusan'       => $k->mahasiswa?->jurusan ?? '-',
                    'waktu_masuk'   => $k->waktu_masuk->format('d/m/Y H:i'),
                    'waktu_keluar'  => $k->waktu_keluar?->format('d/m/Y H:i') ?? '—',
                    'durasi'        => $k->waktu_keluar
                        ? $k->waktu_masuk->diffInMinutes($k->waktu_keluar) . ' mnt'
                        : 'Masih di dalam',
                ]);

            return response()->json([
                'filtered' => $filtered,
            ]);
        }

        return response()->json([
            'stats' => [
                'hari_ini'   => $hariIni,
                'minggu_ini' => $mingguIni,
                'bulan_ini'  => $bulanIni,
                'total'      => $totalAll,
            ],
            'chart_week'      => $chartData,
            'chart_month'     => $chartMonth,
            'jurusan_populer' => $jurusanPopuler,
            'terbaru'         => $kunjunganTerbaru,
        ]);
    }

    public function export(Request $request)
    {
        $query = Kunjungan::with('mahasiswa')->orderBy('waktu_masuk', 'desc');

        // Gunakan parameter 'start' dan 'end' agar seragam dengan fungsi stats()
        if ($request->filled(['start', 'end'])) {
            $start = Carbon::parse($request->start)->startOfDay();
            $end   = Carbon::parse($request->end)->endOfDay();
            $query->whereBetween('waktu_masuk', [$start, $end]);
        }

        $fileName = 'Laporan_Kunjungan_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            
            // Header Kolom
            fputcsv($file, ['No', 'NIM', 'Nama Mahasiswa', 'Jurusan', 'Waktu Masuk', 'Waktu Keluar', 'Durasi (Menit)']);

            $no = 1;
            // Gunakan chunk agar server tidak ngelag saat data kunjungan sudah sangat banyak
            $query->chunk(500, function ($kunjungans) use ($file, &$no) {
                foreach ($kunjungans as $k) {
                    $durasi = $k->waktu_keluar 
                        ? $k->waktu_masuk->diffInMinutes($k->waktu_keluar)
                        : 'Belum Keluar';

                    fputcsv($file, [
                        $no++,
                        $k->nim,
                        $k->mahasiswa?->nama ?? '-',
                        $k->mahasiswa?->jurusan ?? '-',
                        $k->waktu_masuk->format('Y-m-d H:i:s'), // Format standar excel
                        $k->waktu_keluar?->format('Y-m-d H:i:s') ?? '-',
                        $durasi
                    ]);
                }
            });

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
