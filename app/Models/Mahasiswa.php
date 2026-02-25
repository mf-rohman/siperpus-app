<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'nim',
        'nama',
        'jurusan',
        'angkatan',
        'email',
        'no_hp',
    ];

    public function kunjungan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'nim', 'nim');
    }

    public function kunjunganHariIni(): HasMany
    {
        return $this->kunjungan()->whereDate('waktu_masuk', today());
    }
}
