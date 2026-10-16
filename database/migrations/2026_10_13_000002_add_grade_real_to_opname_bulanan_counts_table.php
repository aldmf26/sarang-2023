<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opname_bulanan_counts', function (Blueprint $table) {
            $table->string('grade_real', 100)->nullable()->after('no_box');
        });
    }

    public function down(): void
    {
        Schema::table('opname_bulanan_counts', function (Blueprint $table) {
            $table->dropColumn('grade_real');
        });
    }
};
