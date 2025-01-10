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
        Schema::create('tawaran_pelatihan', function (Blueprint $table) {
            $table->id('id_tawaran_pelatihan');
            $table->date('tgl_informasi');
            $table->integer('id_divisi');
            $table->string('jenis_pelatihan')->nullable();
            $table->string('sasaran_pelatihan')->nullable();
            $table->string('tema_pelatihan')->nullable();
            $table->string('sumber_informasi')->nullable();
            $table->string('personil_penghubung')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
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
