<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_import_histories', function (Blueprint $table) {
            $table->id();
            $table->string('tujuan', 20)->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('user_name');
            $table->string('file_name')->nullable();
            $table->unsignedInteger('jumlah_box');
            $table->double('total_pcs');
            $table->double('total_gr');
            $table->double('total_rp');
            $table->json('invoices');
            $table->json('rows');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opname_import_histories');
    }
};
