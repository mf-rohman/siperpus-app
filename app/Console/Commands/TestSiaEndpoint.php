<?php
// app/Console/Commands/TestSiaEndpoint.php

namespace App\Console\Commands;

use App\Services\SiakadService;
use Illuminate\Console\Command;

class TestSiaEndpoint extends Command
{
    protected $signature = 'sia:test-endpoint {angkatan? : Tahun angkatan}';
    protected $description = 'Test all_mhs endpoint di SIA';

    public function handle(SiakadService $sia)
    {
        $angkatan = $this->argument('angkatan') ?: 2021;
        
        $this->info("Menguji endpoint all_mhs untuk angkatan {$angkatan}...");
        
        $results = $sia->testAllMhsEndpoint($angkatan);
        
        $this->table(
            ['Endpoint', 'Status', 'Keterangan'],
            collect($results)->map(function ($result, $endpoint) {
                return [
                    $endpoint,
                    $result['status'] ?? 'error',
                    is_array($result) ? json_encode($result['data'] ?? $result) : $result,
                ];
            })->toArray()
        );
        
        return self::SUCCESS;
    }
}