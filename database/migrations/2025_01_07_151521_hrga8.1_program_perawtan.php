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
        Schema::create('program_perawatan', function (Blueprint $table) {
            $table->id();
            $table->integer('id_item');
            $table->integer('frekuensi_perawatan');
            $table->string('penanggung_jawab');
            $table->date('tgl_mulai');

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
