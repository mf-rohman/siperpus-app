<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->dropUnique('mahasiswa_email_unique');
            // Email juga ubah jadi nullable karena data SIA kadang kosong
            $table->string('email', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('mahasiswa', function (Blueprint $table) {
            $table->unique('email');
        });
    }
};