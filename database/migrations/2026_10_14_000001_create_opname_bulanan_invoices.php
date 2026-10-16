<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opname_bulanan_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('no_invoice', 50)->unique();
            $table->date('bulan')->index();
            $table->date('tanggal');
            $table->string('posisi', 20);
            $table->string('tujuan', 100);
            $table->unsignedBigInteger('pengawas_id')->index();
            $table->unsignedBigInteger('created_by')->index();
            $table->string('status', 20)->default('draft')->index();
            $table->text('catatan')->nullable();
            $table->text('alasan_kembali')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->string('no_pengiriman', 10)->nullable();
            $table->json('transaksi')->nullable();
            $table->timestamps();
        });
        Schema::create('opname_bulanan_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('opname_bulanan_invoices')->cascadeOnDelete();
            $table->string('baris', 20);
            $table->string('no_box', 50);
            $table->string('no_box_baru', 50)->nullable()->unique();
            $table->string('no_box_kirim', 50)->nullable()->index();
            $table->string('nm_partai', 100);
            $table->string('grade_sistem', 100)->nullable();
            $table->string('grade_real', 50);
            $table->decimal('pcs_sistem', 14, 2);
            $table->decimal('gr_sistem', 14, 2);
            $table->unsignedInteger('pcs_real');
            $table->decimal('gr_real', 14, 2);
            foreach (['cost_bk', 'cost_kerja', 'cost_op', 'cost_cu', 'modal_baru'] as $cost) {
                $table->decimal($cost, 18, 2)->default(0);
            }
            $table->decimal('harga_baru', 28, 8)->default(0);
            $table->json('snapshot');
            $table->timestamps();
            $table->unique(['invoice_id', 'baris']);
            $table->unique(['invoice_id', 'no_box']);
            $table->index('no_box');
        });
        Schema::create('opname_bulanan_sequences', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->unsignedBigInteger('value')->default(0);
        });
        DB::table('opname_bulanan_sequences')->insert(['name' => 'posting', 'value' => 0]);
        foreach (['cabut', 'eo', 'grading_partai', 'pengiriman'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->boolean('opname_cost_locked')->default(false)->index();
            });
        }
    }

    public function down(): void
    {
        foreach (['cabut', 'eo', 'grading_partai', 'pengiriman'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropColumn('opname_cost_locked'));
        }
        Schema::dropIfExists('opname_bulanan_lines');
        Schema::dropIfExists('opname_bulanan_invoices');
        Schema::dropIfExists('opname_bulanan_sequences');
    }
};
