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
        Schema::create('hrga3_hasil_evaluasi', function (Blueprint $table) {
            $table->id();
            $table->integer('karyawan_id');
            $table->string('kriteria')->nullable();
            $table->string('standar')->nullable();
            $table->string('hasil')->nullable();
            $table->string('admin');
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
