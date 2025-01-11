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
        Schema::create('ceklis_perawatan_mesin', function (Blueprint $table) {
            $table->id();
            $table->integer('id_perawatan');
            $table->date('tanggal');
            $table->string('kriteria_pemeriksaan');
            $table->string('metode');
            $table->enum('hasil_pemeriksaan', ['Ok', 'Tidak Ok']);
            $table->string('status');
            $table->string('ket');
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
