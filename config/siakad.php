<?php

// config/siakad.php

return [

    /*
    |--------------------------------------------------------------------------
    | Mode Integrasi SIAKAD
    |--------------------------------------------------------------------------
    | Pilihan:
    |   'dummy'  → data lokal hardcoded, tanpa API (default sementara)
    |   'open'   → simulasi pakai RandomUser API (untuk testing/demo)
    |   'siakad' → API SIAKAD kampus asli (aktifkan setelah dapat akses)
    |
    | Ganti via .env:  SIAKAD_MODE=siakad
    */
    'mode' => env('SIAKAD_MODE', 'dummy'),

    /*
    |--------------------------------------------------------------------------
    | Base URL API SIAKAD
    |--------------------------------------------------------------------------
    | Contoh: https://sia.unirow.ac.id/api2/sia
    | Isi setelah dapat akses dari IT kampus.
    */
    'base_url' => env('SIAKAD_BASE_URL', 'https://sia.unirow.ac.id/api2/sia'),

    /*
    |--------------------------------------------------------------------------
    | Autentikasi
    |--------------------------------------------------------------------------
    | Isi salah satu sesuai yang diberikan IT kampus.
    */
    'api_key' => env('SIAKAD_API_KEY', null),
    'token'   => env('SIAKAD_TOKEN',   null),

    /*
    |--------------------------------------------------------------------------
    | Timeout & Cache
    |--------------------------------------------------------------------------
    */
    'timeout'     => env('SIAKAD_TIMEOUT', 10),       // detik
    'cache_ttl'   => env('SIAKAD_CACHE_TTL', 600),    // detik (10 menit)

];
