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
        Schema::create('hrga5_3permintaan', function (Blueprint $table) {
            $table->id();
            $table->integer('lokasi_id');
            $table->integer('item_id');
            $table->date('tgl');
            $table->string('diajukan_oleh');
            $table->string('no_identifikasi');
            $table->text('deskripsi_masalah');
            $table->enum('selesai', ['Y', 'T'])->default('Y');
            $table->time('time');
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
