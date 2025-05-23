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
        Schema::table('ventas', function (Blueprint $table) {
            // Si la columna cliente_id no existe, la añadimos
            if (!Schema::hasColumn('ventas', 'cliente_id')) {
                $table->unsignedBigInteger('cliente_id')->nullable()->after('id');
                
                // Añadir la clave foránea
                $table->foreign('cliente_id')
                      ->references('id')
                      ->on('clientes')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['cliente_id']);
            
            // Luego eliminar la columna
            $table->dropColumn('cliente_id');
        });
    }
};
