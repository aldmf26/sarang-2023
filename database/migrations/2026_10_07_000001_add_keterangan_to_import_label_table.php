<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('import_label')) {
            Schema::create('import_label', function (Blueprint $table) {
                $table->id();
                $table->string('partai', 100);
                $table->string('box', 100);
                $table->string('grade', 100);
                $table->double('pcs')->default(0);
                $table->double('gr')->default(0);
                $table->string('bagian', 100);
                $table->string('kelompok', 50)->nullable();
                $table->string('keterangan', 255)->nullable();
            });

            return;
        }

        Schema::table('import_label', function (Blueprint $table) {
            if (!Schema::hasColumn('import_label', 'keterangan')) {
                $table->string('keterangan', 255)->nullable()->after('kelompok');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('import_label', 'keterangan')) {
            Schema::table('import_label', function (Blueprint $table) {
                $table->dropColumn('keterangan');
            });
        }
    }
};
