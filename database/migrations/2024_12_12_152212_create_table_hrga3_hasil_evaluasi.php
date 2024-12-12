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
        Schema::create('table_hrga3_hasil_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->string('status');
            $table->string('periode');
            $table->enum('periode', ['1', '3', '6'])->default('1');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_hrga3_hasil_evaluasi');
    }
};
