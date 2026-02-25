<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
