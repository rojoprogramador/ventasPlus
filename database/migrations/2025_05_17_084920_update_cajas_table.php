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
        Schema::table('cajas', function (Blueprint $table) {
            if (!Schema::hasColumn('cajas', 'usuario_id')) {
                $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('cajas', 'fecha_apertura')) {
                $table->dateTime('fecha_apertura');
            }
            if (!Schema::hasColumn('cajas', 'fecha_cierre')) {
                $table->dateTime('fecha_cierre')->nullable();
            }
            if (!Schema::hasColumn('cajas', 'monto_inicial')) {
                $table->decimal('monto_inicial', 10, 2);
            }
            if (!Schema::hasColumn('cajas', 'monto_final')) {
                $table->decimal('monto_final', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('cajas', 'total_ventas')) {
                $table->decimal('total_ventas', 10, 2)->default(0);
            }
            if (!Schema::hasColumn('cajas', 'estado')) {
                $table->string('estado')->default('abierta');
            }
            if (!Schema::hasColumn('cajas', 'observaciones')) {
                $table->text('observaciones')->nullable();
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
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn([
                'usuario_id',
                'fecha_apertura',
                'fecha_cierre',
                'monto_inicial',
                'monto_final',
                'total_ventas',
                'estado',
                'observaciones'
            ]);
        });
    }
};
