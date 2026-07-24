<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('balance_sheet_closings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('bulan');
            $table->unsignedSmallInteger('tahun');
            $table->decimal('cost_berjalan_sebelum_tutup', 20, 2)->default(0);
            $table->decimal('cumulative_cost_baseline', 20, 2)->default(0);
            $table->dateTime('closed_at');
            $table->string('admin');
            $table->timestamps();

            $table->unique(['bulan', 'tahun'], 'balance_closing_period_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_sheet_closings');
    }
};
