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
        Schema::create('perawatan', function (Blueprint $table) {
            $table->id('id_perawatan');
            $table->integer('item_id');
            $table->date('tgl');
            $table->integer('lokasi_id');
            $table->text('kesimpulan');
            $table->string('fungsi');
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
