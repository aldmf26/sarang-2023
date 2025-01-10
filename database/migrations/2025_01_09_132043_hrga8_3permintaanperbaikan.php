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
        Schema::create('permintaan_perbaikan_mesin', function (Blueprint $table) {
            $table->id();
            $table->integer('id_item');
            $table->date('tgl_mulai');
            $table->string('diajukan_oleh');
            $table->date('deadline');
            $table->text('deskripsi_masalah');
            $table->enum('selesai', ['Y', 'T'])->default('Y');
            $table->time('waktu_permintaan');

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
