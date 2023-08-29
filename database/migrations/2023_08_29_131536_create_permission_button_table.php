<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_button', function (Blueprint $table) {
            $table->integer('id_permission_button', true);
            $table->integer('permission_id');
            $table->string('nm_permission_button', 100);
            $table->string('jenis', 100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_button');
    }
};
