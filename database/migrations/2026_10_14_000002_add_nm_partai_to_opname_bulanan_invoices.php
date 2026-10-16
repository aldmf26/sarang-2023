<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opname_bulanan_invoices', function (Blueprint $table) {
            $table->string('nm_partai', 100)->nullable()->after('no_invoice')->index();
        });
        DB::table('opname_bulanan_invoices')->orderBy('id')->chunkById(100, function ($invoices) {
            foreach ($invoices as $invoice) {
                $partai = DB::table('opname_bulanan_lines')->where('invoice_id', $invoice->id)->value('nm_partai');
                if ($partai) {
                    DB::table('opname_bulanan_invoices')->where('id', $invoice->id)->update(['nm_partai' => $partai]);
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('opname_bulanan_invoices', function (Blueprint $table) {
            $table->dropColumn('nm_partai');
        });
    }
};
