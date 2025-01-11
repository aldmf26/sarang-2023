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
        Schema::create('usulan_identifikasi', function (Blueprint $table) {
            $table->id();
            $table->integer('id_karyawan');
            $table->string('pengusul');
            $table->string('usulan');
            $table->date('waktu');
            $table->string('alasan');
            $table->integer('id_divisi');
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
