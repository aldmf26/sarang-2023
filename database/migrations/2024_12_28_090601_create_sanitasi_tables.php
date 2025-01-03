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
        // Tabel tbl_item_pembersihan
        Schema::create('item_pembersihan', function (Blueprint $table) {
            $table->id('id_item');
            $table->string('nama_item');
            $table->timestamps();
        });

        // Tabel tbl_sanitasi
        Schema::create('sanitasi', function (Blueprint $table) {
            $table->id('id_sanitasi');
            $table->integer('id_lokasi');
            $table->integer('id_item');
            $table->date('tgl')->nullable();
            $table->string('paraf_petugas')->nullable();
            $table->string('verifikator')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanitasi');
        Schema::dropIfExists('item_pembersihan');
    }
};
