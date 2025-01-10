<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hrga5_1pemeliharaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sarana');
            $table->string('merek');
            $table->string('no_identifikasi');
            $table->string('lokasi');
            $table->string('frekuensi_perawatan');
            $table->string('penanggung_jawab');
            $table->date('tanggal_mulai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
