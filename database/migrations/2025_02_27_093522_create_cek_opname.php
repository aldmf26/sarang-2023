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
        Schema::create('cek_opname', function (Blueprint $table) {
            $table->id();
            $table->date('tgl');
            $table->string('kategori');  // Grading, Wip, Qc, Pengiriman, dll
            $table->double('pcs')->default(0);
            $table->double('gr')->default(0);
            $table->string('status')->default('current');  // Untuk membedakan data bulan lalu dan sekarang
            $table->timestamps();

            // Indeks untuk mempercepat pencarian
            $table->index(['tgl', 'kategori', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cek_opname');
    }
};
