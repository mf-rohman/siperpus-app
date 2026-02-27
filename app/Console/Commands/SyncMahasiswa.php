<?php

namespace App\Console\Commands;

use App\Models\Mahasiswa;
use App\Services\SiakadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncMahasiswa extends Command
{
    protected $signature = 'siakad:sync
                                {--angkatan=    : Tahun angkatan tertentu (contoh: --angkatan=2023)}
                                {--semua        : Sync semua angkatan dari SIA}
                                {--limit=500    : Jumlah data per request (max 1000)}
                                {--all-status   : Ambil SEMUA mahasiswa (aktif dan tidak aktif)}';

    protected $description = 'Sinkronisasi data mahasiswa dari API SIA Unirow';

    public function __construct(protected SiakadService $sia)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $mode = config('siakad.mode', 'dummy');
        $allStatus = $this->option('all-status');

        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  SiPerpus — Sync Mahasiswa dari SIA Unirow");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  Mode          : {$mode}");
        $this->info("  Base URL      : " . config('siakad.base_url'));
        $this->info("  Ambil semua   : " . ($allStatus ? 'Ya (aktif & tidak aktif)' : 'Tidak (hanya aktif)'));
        $this->newLine();

        if ($mode === 'dummy') {
            return $this->syncDummyMode();
        }

        if ($mode === 'open') {
            return $this->syncOpenMode();
        }

        // Cek koneksi
        $health = $this->sia->checkHealth();
        if (! $health['online']) {
            $this->error("❌ API SIA tidak dapat dijangkau.");
            return self::FAILURE;
        }
        $this->info("✅ API SIA online - Service: " . ($health['service'] ?? 'sia-api2'));

        // Tentukan angkatan
        if ($this->option('angkatan')) {
            $angkatanList = [(int) $this->option('angkatan')];
        } elseif ($this->option('semua')) {
            $angkatanList = $this->sia->getYears();
            if (empty($angkatanList)) {
                $this->error("Gagal ambil daftar angkatan dari SIA.");
                return self::FAILURE;
            }
            $this->info("📋 Ditemukan " . count($angkatanList) . " angkatan: " . implode(', ', $angkatanList));
        } else {
            $angkatanList = [(int) date('Y')];
            $this->comment("ℹ  Default sync angkatan " . date('Y') . ". Pakai --semua untuk semua angkatan.");
        }

        $grandProcessed = 0;
        $grandSaved = 0;
        $grandSkipped = 0;
        $grandTotal = 0;
        $grandExpected = 0;

        foreach ($angkatanList as $angkatan) {
            // Hitung total yang diharapkan
            if ($allStatus) {
                $expectedTotal = $this->sia->countAllMahasiswaByAngkatan($angkatan);
            } else {
                $expectedTotal = $this->sia->countMahasiswaByAngkatan($angkatan);
            }
            $grandExpected += $expectedTotal;
            
            // Sync angkatan
            [$processed, $saved, $skipped, $actualTotal] = $this->syncAngkatan(
                (int) $angkatan,
                min((int) $this->option('limit'), 1000),
                $allStatus
            );
            
            $grandTotal += $actualTotal;
            $grandProcessed += $processed;
            $grandSaved += $saved;
            $grandSkipped += $skipped;
        }

        $this->newLine();
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("  Status sync      : " . ($allStatus ? 'SEMUA (aktif & tidak aktif)' : 'Hanya AKTIF'));
        $this->info("  Total di SIA     : {$grandExpected}");
        $this->info("  Total diterima   : {$grandTotal}");
        $this->info("  Total diproses   : {$grandProcessed}");
        $this->info("  Total upsert     : {$grandSaved}");
        $this->info("  Total skip (err) : {$grandSkipped}");
        $this->info("  Total di DB      : " . Mahasiswa::count());
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

        return self::SUCCESS;
    }

    /**
     * Sync satu angkatan
     */
    private function syncAngkatan(int $angkatan, int $limit, bool $allStatus): array
    {
        $offset = 0;
        $totalProc = 0;
        $totalSave = 0;
        $totalSkip = 0;
        $hasMore = true;
        $pageData = [];

        // Cek total dari SIA
        if ($allStatus) {
            $totalSia = $this->sia->countAllMahasiswaByAngkatan($angkatan);
        } else {
            $totalSia = $this->sia->countMahasiswaByAngkatan($angkatan);
        }

        $statusText = $allStatus ? 'SEMUA STATUS' : 'AKTIF';
        $this->info("\n📅 Angkatan {$angkatan} ({$statusText}) — total di SIA: {$totalSia}");

        if ($totalSia === 0) {
            $this->warn("   Tidak ada data untuk angkatan {$angkatan}, skip.");
            return [0, 0, 0, 0];
        }

        while ($hasMore) {
            try {
                // Ambil data dari service
                if ($allStatus) {
                    $page = $this->sia->getAllMahasiswaByAngkatan($angkatan, $limit, $offset);
                } else {
                    $page = $this->sia->getMahasiswaByAngkatan($angkatan, $limit, $offset);
                }

                $rows = $page['data'] ?? [];
                $processed = count($rows);
                $hasMore = $page['has_more'] ?? false;
                $nextOffset = $page['next_offset'] ?? ($offset + $limit);

                if ($processed === 0) {
                    $this->warn("   Data kosong untuk offset={$offset}, berhenti.");
                    break;
                }

                // Tampilkan info status dari data pertama sebagai sampel
                if (!empty($rows) && isset($rows[0]['status'])) {
                    $statusSample = collect($rows)->pluck('status')->unique()->implode(', ');
                    $this->line("   Status dalam batch: {$statusSample}");
                }

                $bar = $this->output->createProgressBar($processed);
                $saved = 0;
                $skip = 0;

                DB::beginTransaction();
                try {
                    foreach ($rows as $r) {
                        $npm = trim((string) ($r['nim'] ?? ''));
                        if ($npm === '') {
                            $skip++;
                            $bar->advance();
                            continue;
                        }

                        $data = $this->prepareData($r);
                        $exists = Mahasiswa::where('nim', $npm)->first();

                        if ($exists) {
                            if (empty($data['jurusan']) || $data['jurusan'] === '—') {
                                unset($data['jurusan']);
                            }
                            $exists->update($data);
                        } else {
                            Mahasiswa::create($data);
                        }

                        $saved++;
                        $bar->advance();
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->newLine();
                    $this->error("   Batch error offset={$offset}: " . $e->getMessage());
                    $skip += $processed;
                    $saved = 0;
                }

                $bar->finish();
                $this->newLine();
                $this->line("   ✓ offset={$offset} → diproses: {$processed}, upsert: {$saved}, skip: {$skip}");

                $totalProc += $processed;
                $totalSave += $saved;
                $totalSkip += $skip;
                $offset = $nextOffset;
                $pageData[] = $processed;

            } catch (\Exception $e) {
                $this->error("   Error pada offset={$offset}: " . $e->getMessage());
                break;
            }
        }

        $actualTotal = array_sum($pageData);

        if ($actualTotal < $totalSia) {
            $this->warn("   ⚠ PERHATIAN: Hanya mendapatkan {$actualTotal} dari {$totalSia} data");
        }

        return [$totalProc, $totalSave, $totalSkip, $actualTotal];
    }

    /**
     * Prepare data untuk disimpan ke database
     */
    private function prepareData(array $r): array
    {
        return [
            'nim' => $r['nim'] ?? '',
            'nama' => $r['nama'] ?? '—',
            'jurusan' => $r['jurusan'] ?? '—',
            'angkatan' => $r['angkatan'] ?? null,
            'email' => $r['email'] ?? null,
            'no_hp' => $r['no_hp'] ?? null,
            'nik' => $r['nik'] ?? null,
            'tempat_lahir' => $r['tempat_lahir'] ?? null,
            'tanggal_lahir' => $r['tanggal_lahir'] ?? null,
            'status_mahasiswa' => $r['status'] ?? null,
        ];
    }

    /**
     * Mode dummy
     */
    private function syncDummyMode(): int
    {
        $this->info("📁 Mode dummy — menggunakan data contoh...");
        
        $dummyData = [
            [
                'nim' => '1101210001',
                'nama' => 'PUTRI NUR FARIHATUL JANNAH',
                'jurusan' => 'S1 P. PKN',
                'angkatan' => 2021,
                'email' => 'putrifariha030@gmail.com',
                'no_hp' => '083893331927',
                'status_mahasiswa' => 'N',
            ],
            [
                'nim' => '2021262010001',
                'nama' => 'Aditya Pratama',
                'jurusan' => 'S1 Teknik Industri',
                'angkatan' => 2021,
                'email' => 'aditya@mhs.unirow.ac.id',
                'no_hp' => '08123456701',
                'status_mahasiswa' => 'A',
            ],
        ];

        $bar = $this->output->createProgressBar(count($dummyData));
        $saved = 0;

        DB::beginTransaction();
        try {
            foreach ($dummyData as $data) {
                Mahasiswa::updateOrCreate(
                    ['nim' => $data['nim']],
                    $data
                );
                $saved++;
                $bar->advance();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nError: " . $e->getMessage());
            return self::FAILURE;
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Selesai! Upsert: {$saved} data");
        
        return self::SUCCESS;
    }

    /**
     * Mode open
     */
    private function syncOpenMode(): int
    {
        $this->info("🌐 Mode demo — RandomUser API...");
        // ... kode open mode ...
        return self::SUCCESS;
    }
}