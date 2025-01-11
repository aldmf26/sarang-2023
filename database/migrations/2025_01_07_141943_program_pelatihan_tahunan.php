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
        Schema::create('program_pelatihan_tahunan', function (Blueprint $table) {
            $table->id('id_program_pelatihan');
            $table->string('materi_pelatihan');
            $table->enum('i', ['I', 'E'])->default('I');
            $table->string('narasumber');
            $table->string('sasaran_peserta');
            $table->date('tgl_rencana');
            $table->date('tgl_realisasi');
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
