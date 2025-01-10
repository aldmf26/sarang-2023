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
        Schema::create('detail_perbaikan_mesin', function (Blueprint $table) {
            $table->id();
            $table->integer('id_permintaan');
            $table->text('detail_perbaikan');
            $table->text('verifikasi_user');
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
