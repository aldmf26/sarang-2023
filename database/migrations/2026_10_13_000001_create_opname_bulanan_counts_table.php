<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_bulanan_counts', function (Blueprint $table) {
            $table->id();
            $table->date('bulan');
            $table->string('no_box', 50);
            $table->decimal('pcs_real', 12, 2)->nullable();
            $table->decimal('gr_real', 12, 2)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->unique(['bulan', 'no_box']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_bulanan_counts');
    }
};
