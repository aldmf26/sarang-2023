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
        Schema::create('hasil_wawancara', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik');
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('P');
            $table->integer('id_divisi');
            $table->text('kesimpulan');
            $table->enum('keputusan', ['dilanjutkan', 'ditolak'])->nullable();
            $table->enum('periode_masa_percobaan', ['1', '3', '6'])->nullable();
            $table->string('status')->nullable();
            $table->string('keputusan_lulus')->nullable();
            $table->string('posisi2')->nullable();
            $table->integer('id_anak');
            $table->date('tgl_masuk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_wawancara');
    }
};
