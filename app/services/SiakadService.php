<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SiakadService
{
    protected string $mode;
    protected string $baseUrl;
    protected string $apiKey;
    protected int    $timeout;

    private array $prodiMap = [
        26201 => 'S1 Teknik Industri',
        44201 => 'S1 Matematika',
        46201 => 'S1 Biologi',
        54241 => 'S1 Ilmu Kelautan',
        54242 => 'S1 Ilmu Perikanan',
        55201 => 'S1 Teknik Informatika',
        67201 => 'S1 Ilmu Politik',
        70201 => 'S1 Ilmu Komunikasi',
        84105 => 'S1 P. Biologi',
        84202 => 'S1 P. Matematika',
        84205 => 'S2 Pendidikan Biologi',
        86122 => 'S2 Pendidikan Dasar',
        86206 => 'S1 PGSD',
        86207 => 'S1 PGPAUD',
        87203 => 'S1 P. Ekonomi',
        87205 => 'S1 P. PKN',
        88201 => 'S1 P. B. Indonesia',
        88203 => 'S1 P. B. Inggris',
    ];

    public function __construct()
    {
        $this->mode    = config('siakad.mode', 'dummy');
        $this->baseUrl = rtrim(config('siakad.base_url', ''), '/');
        $this->apiKey  = config('siakad.api_key', '');
        $this->timeout = (int) config('siakad.timeout', 60);
    }

    /**
     * Ambil SEMUA mahasiswa (aktif dan tidak aktif) menggunakan endpoint mhs-all
     */
    public function getAllMahasiswaByAngkatan(int $angkatan, int $limit = 500, int $offset = 0): array
    {
        if ($this->mode !== 'siakad') {
            return ['data' => [], 'total' => 0, 'has_more' => false];
        }

        try {
            // Gunakan endpoint mhs-all yang sudah terbukti bekerja
            $params = [
                'angk'   => $angkatan,
                'limit'  => $limit,
                'offset' => $offset,
                'status' => 'all', // all, aktif, nonaktif
            ];

            $response = $this->siaApi('mhs-all', $params);

            $rows      = $response['data'] ?? [];
            $total     = (int) ($response['total'] ?? 0);
            $hasMore   = (bool) ($response['has_more'] ?? false);
            $nextOffset = $response['next_offset'] ?? ($offset + $limit);

            Log::info("SIA getAllMahasiswaByAngkatan({$angkatan}): Mendapatkan " . count($rows) . " data dari total {$total}");

            return [
                'data'        => array_map(fn($r) => $this->mapSiaRow($r), $rows),
                'total'       => $total,
                'next_offset' => $nextOffset,
                'has_more'    => $hasMore,
                'processed'   => count($rows),
                'endpoint'    => 'mhs-all',
            ];
            
        } catch (\Exception $e) {
            Log::error("SIA getAllMahasiswaByAngkatan({$angkatan}): " . $e->getMessage());
            
            // Fallback ke endpoint mhs_all
            try {
                $params = [
                    'angk'   => $angkatan,
                    'limit'  => $limit,
                    'offset' => $offset,
                    'status' => 'all',
                ];
                $response = $this->siaApi('mhs_all', $params);
                
                $rows      = $response['data'] ?? [];
                $total     = (int) ($response['total'] ?? 0);
                $hasMore   = (bool) ($response['has_more'] ?? false);
                $nextOffset = $response['next_offset'] ?? ($offset + $limit);

                return [
                    'data'        => array_map(fn($r) => $this->mapSiaRow($r), $rows),
                    'total'       => $total,
                    'next_offset' => $nextOffset,
                    'has_more'    => $hasMore,
                    'processed'   => count($rows),
                    'endpoint'    => 'mhs_all',
                ];
            } catch (\Exception $e2) {
                Log::error("SIA fallback getAllMahasiswaByAngkatan({$angkatan}): " . $e2->getMessage());
                return ['data' => [], 'total' => 0, 'has_more' => false, 'error' => $e2->getMessage()];
            }
        }
    }

    /**
     * Ambil mahasiswa AKTIF per angkatan (endpoint lama)
     */
    public function getMahasiswaByAngkatan(int $angkatan, int $limit = 500, int $offset = 0): array
    {
        if ($this->mode !== 'siakad') {
            return ['data' => [], 'total' => 0, 'has_more' => false];
        }

        try {
            $params = [
                'angk'   => $angkatan,
                'limit'  => $limit,
                'offset' => $offset,
            ];

            $response = $this->siaApi('mhs', $params);

            $rows      = $response['data'] ?? [];
            $total     = (int) ($response['total'] ?? 0);
            $hasMore   = (bool) ($response['has_more'] ?? false);
            $nextOffset = $response['next_offset'] ?? ($offset + $limit);

            return [
                'data'        => array_map(fn($r) => $this->mapSiaRow($r), $rows),
                'total'       => $total,
                'next_offset' => $nextOffset,
                'has_more'    => $hasMore,
                'processed'   => count($rows),
            ];
        } catch (\Exception $e) {
            Log::error("SIA getMahasiswaByAngkatan({$angkatan}): " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'has_more' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Hitung total mahasiswa per angkatan (semua status)
     */
    public function countAllMahasiswaByAngkatan(int $angkatan): int
    {
        try {
            // Coba dapatkan dari endpoint mhs-all dengan limit 1
            $params = [
                'angk'   => $angkatan,
                'limit'  => 1,
                'offset' => 0,
                'status' => 'all',
            ];
            
            $response = $this->siaApi('mhs-all', $params);
            
            if (isset($response['total'])) {
                return (int) $response['total'];
            }
            
            // Fallback ke endpoint count
            $response = $this->siaApi('count', ['tahun' => $angkatan, 'all' => '1']);
            return (int) ($response['count'] ?? 0);
            
        } catch (\Exception $e) {
            Log::error("SIA countAllMahasiswaByAngkatan({$angkatan}): " . $e->getMessage());
            
            // Fallback ke count biasa
            try {
                $response = $this->siaApi('count', ['tahun' => $angkatan]);
                return (int) ($response['count'] ?? 0);
            } catch (\Exception $e) {
                return 0;
            }
        }
    }

    /**
     * Hitung total mahasiswa AKTIF per angkatan
     */
    public function countMahasiswaByAngkatan(int $angkatan): int
    {
        try {
            $response = $this->siaApi('count', ['tahun' => $angkatan]);
            return (int) ($response['count'] ?? 0);
        } catch (\Exception $e) {
            Log::error("SIA countMahasiswaByAngkatan({$angkatan}): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Ambil 1 mahasiswa by NPM
     */
    public function getMahasiswa(string $nim): ?array
    {
        return Cache::remember("mhs_{$nim}", (int) config('siakad.cache_ttl', 600), function () use ($nim) {
            return match ($this->mode) {
                'siakad' => $this->fetchFromSia($nim),
                'open'   => $this->fetchFromOpenApi($nim),
                default  => $this->fetchFromDummy($nim),
            };
        });
    }

    /**
     * Cek kesehatan API
     */
    public function checkHealth(): array
    {
        try {
            $response = $this->siaApi('health');
            return [
                'online' => true,
                'data' => $response,
                'service' => $response['service'] ?? 'unknown',
                'time' => $response['time'] ?? null,
            ];
        } catch (\Exception $e) {
            return ['online' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Ambil daftar tahun angkatan
     */
    public function getYears(): array
    {
        try {
            $response = $this->siaApi('years');
            return $response['years'] ?? [];
        } catch (\Exception $e) {
            Log::warning("SIA getYears: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil data dari endpoint tertentu (untuk testing)
     */
    public function getFromEndpoint(string $endpoint, array $params): array
    {
        try {
            return $this->siaApi($endpoint, $params);
        } catch (\Exception $e) {
            Log::error("SIA getFromEndpoint({$endpoint}): " . $e->getMessage());
            return ['data' => [], 'total' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * HTTP client ke API SIA
     */
    private function siaApi(string $path, array $query = []): array
    {
        if (! $this->baseUrl || ! $this->apiKey) {
            throw new \Exception('Config SIA belum lengkap. Set SIAKAD_BASE_URL dan SIAKAD_TOKEN di .env');
        }

        $url = $this->baseUrl . '/' . ltrim($path, '/');
        if ($query) {
            $url .= '?' . http_build_query($query);
        }

        Log::debug("SIA API Request: {$url}");

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->get($url);

        if ($response->failed()) {
            $msg = $response->json('message') ?? $response->body();
            throw new \Exception("SIA API {$response->status()}: {$msg}");
        }

        return $response->json() ?? [];
    }

    /**
     * Fetch 1 mahasiswa dari SIA by NPM
     */
    private function fetchFromSia(string $nim): ?array
    {
        try {
            $response = $this->siaApi('mhs/' . $nim);
            $data = $response['data'] ?? null;

            if (empty($data) || empty($data['npm'] ?? null)) {
                return null;
            }

            return $this->mapSiaRow($data);

        } catch (\Exception $e) {
            Log::warning("SIA fetchFromSia({$nim}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Map data dari API ke format lokal
     */
    private function mapSiaRow(array $r): array
    {
        // Resolve kode prodi
        $kodeProdi = isset($r['program_studi']) 
            ? (int) ltrim((string) $r['program_studi'], '0') 
            : null;

        if (! $kodeProdi && ! empty($r['kode_prodi'])) {
            $kodeProdi = (int) ltrim((string) $r['kode_prodi'], '0');
        }

        // Kode < 10000 tidak valid
        if ($kodeProdi && $kodeProdi < 10000) {
            $kodeProdi = null;
        }

        // Resolve nama prodi
        $jurusan = ($kodeProdi && isset($this->prodiMap[$kodeProdi]))
            ? $this->prodiMap[$kodeProdi]
            : ($r['nama_prodi'] ?? $r['prodi'] ?? '—');

        $npm = trim((string) ($r['npm'] ?? $r['NIM'] ?? ''));

        // Resolve angkatan
        $angkatan = isset($r['angkatan']) 
            ? (int) ltrim((string) $r['angkatan'], '0') 
            : (isset($r['tahun_masuk']) ? (int) $r['tahun_masuk'] : null);

        // Derive dari NPM jika kosong
        if (! $angkatan && strlen($npm) >= 4) {
            $yr = (int) substr($npm, 0, 4);
            if ($yr >= 2000 && $yr <= (int) date('Y')) {
                $angkatan = $yr;
            }
        }

        // Status mahasiswa
        $status = $r['status_mahasiswa'] ?? $r['status_aktif'] ?? null;
        
        // Mapping status
        $statusMap = [
            'A' => 'Aktif',
            'N' => 'Tidak Aktif',
            'C' => 'Cuti',
            'L' => 'Lulus',
            'DO' => 'Drop Out',
        ];
        
        $statusLabel = $statusMap[$status] ?? $status;

        $no_hp = $r['nomor_hp'] ?? $r['hp'] ?? $r['no_hp'] ?? null;
        $no_hp = $this->cleanPhoneNumber($no_hp);

        return [
            'nim'           => $npm,
            'nama'          => $r['nama'] ?? $r['nama_mahasiswa'] ?? '—',
            'jurusan'       => $jurusan,
            'angkatan'      => $angkatan,
            'email'         => $r['email'] ?? ($npm . '@mhs.unirow.ac.id'),
            'no_hp'         => $no_hp,
            'nik'           => $r['nik'] ?? null,
            'tempat_lahir'  => $r['tempat_lahir'] ?? null,
            'tanggal_lahir' => $r['tanggal_lahir'] ?? null,
            'jenis_kelamin' => $r['jenis_kelamin'] ?? null,
            'agama'         => $r['agama'] ?? null,
            'alamat'        => $r['alamat_lengkap'] ?? $r['alamat'] ?? null,
            'status'        => $status,
            'status_label'  => $statusLabel,
            'foto'          => $r['foto'] ?? null,
        ];
    }

    private function cleanPhoneNumber($phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Konversi ke string jika numerik dalam format scientific
        $phone = (string) $phone;

        // Hapus semua karakter kecuali angka dan tanda +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Jika diawali dengan 62 (kode Indonesia), ganti dengan 0
        if (strpos($phone, '62') === 0) {
            $phone = '0' . substr($phone, 2);
        }

        // Jika diawali dengan +62, ganti dengan 0
        if (strpos($phone, '+62') === 0) {
            $phone = '0' . substr($phone, 3);
        }

        // Hapus semua tanda + yang tersisa
        $phone = str_replace('+', '', $phone);

        // Batasi panjang maksimal 15 karakter (sesuai standar)
        if (strlen($phone) > 15) {
            $phone = substr($phone, 0, 15);
        }

        // Jika setelah dibersihkan tidak valid (kurang dari 10 digit), return null
        if (strlen($phone) < 10) {
            return null;
        }

        return $phone;
    }

    /**
     * Data dummy untuk mode dummy
     */
    private function fetchFromDummy(string $nim): ?array
    {
        $data = [
            '2021262010001' => [
                'nim' => '2021262010001',
                'nama' => 'Aditya Pratama',
                'jurusan' => 'S1 Teknik Industri',
                'angkatan' => 2021,
                'email' => 'aditya.pratama@mhs.unirow.ac.id',
                'no_hp' => '08123456701',
                'status' => 'A',
                'status_label' => 'Aktif',
            ],
            '1101210001' => [
                'nim' => '1101210001',
                'nama' => 'PUTRI NUR FARIHATUL JANNAH',
                'jurusan' => 'S1 P. PKN',
                'angkatan' => 2021,
                'email' => 'putrifariha030@gmail.com',
                'no_hp' => '083893331927',
                'status' => 'N',
                'status_label' => 'Tidak Aktif',
            ],
        ];

        return $data[$nim] ?? null;
    }

    /**
     * Mode open API (RandomUser)
     */
    private function fetchFromOpenApi(string $nim): ?array
    {
        try {
            $response = Http::timeout(8)->get('https://randomuser.me/api/', [
                'seed' => $nim,
                'nat'  => 'us',
                'inc'  => 'name,phone',
            ]);

            if (! $response->successful()) return null;
            $user = $response->json('results.0');
            if (! $user) return null;

            $prodiList    = array_values($this->prodiMap);
            $angkatanList = [2021, 2022, 2023, 2024];
            $statusList   = ['A', 'A', 'A', 'N', 'L', 'DO'];
            $hash         = crc32($nim);
            $prodi        = $prodiList[abs($hash) % count($prodiList)];
            $angkatan     = $angkatanList[abs($hash) % count($angkatanList)];
            $status       = $statusList[abs($hash) % count($statusList)];
            $nama         = ucwords(strtolower($user['name']['first'] . ' ' . $user['name']['last']));

            return [
                'nim'      => $nim,
                'nama'     => $nama,
                'jurusan'  => $prodi,
                'angkatan' => $angkatan,
                'email'    => $nim . '@mhs.unirow.ac.id',
                'no_hp'    => '08' . substr(preg_replace('/[^0-9]/', '', $user['phone']), 0, 10),
                'status'   => $status,
            ];

        } catch (\Exception $e) {
            Log::warning("OpenAPI fallback for NIM {$nim}: " . $e->getMessage());
            return $this->fetchFromDummy($nim);
        }
    }
}