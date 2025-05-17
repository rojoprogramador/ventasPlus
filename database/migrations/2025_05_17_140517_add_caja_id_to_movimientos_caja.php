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
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->unsignedBigInteger('caja_id')->after('id');
            $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('movimientos_caja', function (Blueprint $table) {
            $table->dropForeign(['caja_id']);
            $table->dropColumn('caja_id');
        });
    }
};
