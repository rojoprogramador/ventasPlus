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
            // Añadir las columnas faltantes a la tabla ventas
            if (!Schema::hasColumn('ventas', 'usuario_id')) {
                $table->unsignedBigInteger('usuario_id')->after('id');
                $table->foreign('usuario_id')->references('id')->on('users');
            }
            
            if (!Schema::hasColumn('ventas', 'caja_id')) {
                $table->unsignedBigInteger('caja_id')->after('usuario_id')->nullable();
                $table->foreign('caja_id')->references('id')->on('cajas');
            }
            
            if (!Schema::hasColumn('ventas', 'codigo')) {
                $table->string('codigo')->after('caja_id')->nullable();
            }
            
            if (!Schema::hasColumn('ventas', 'fecha')) {
                $table->timestamp('fecha')->after('codigo')->nullable();
            }
            
            if (!Schema::hasColumn('ventas', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->after('fecha')->default(0);
            }
            
            if (!Schema::hasColumn('ventas', 'descuento')) {
                $table->decimal('descuento', 10, 2)->after('subtotal')->default(0);
            }
            
            if (!Schema::hasColumn('ventas', 'impuesto')) {
                $table->decimal('impuesto', 10, 2)->after('descuento')->default(0);
            }
            
            if (!Schema::hasColumn('ventas', 'total')) {
                $table->decimal('total', 10, 2)->after('impuesto')->default(0);
            }
            
            if (!Schema::hasColumn('ventas', 'estado')) {
                $table->string('estado')->after('total')->default('completada');
            }
            
            if (!Schema::hasColumn('ventas', 'tipo_pago')) {
                $table->string('tipo_pago')->after('estado')->default('efectivo');
            }
            
            if (!Schema::hasColumn('ventas', 'observaciones')) {
                $table->text('observaciones')->after('tipo_pago')->nullable();
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
            // Eliminar las columnas añadidas
            if (Schema::hasColumn('ventas', 'observaciones')) {
                $table->dropColumn('observaciones');
            }
            
            if (Schema::hasColumn('ventas', 'tipo_pago')) {
                $table->dropColumn('tipo_pago');
            }
            
            if (Schema::hasColumn('ventas', 'estado')) {
                $table->dropColumn('estado');
            }
            
            if (Schema::hasColumn('ventas', 'total')) {
                $table->dropColumn('total');
            }
            
            if (Schema::hasColumn('ventas', 'impuesto')) {
                $table->dropColumn('impuesto');
            }
            
            if (Schema::hasColumn('ventas', 'descuento')) {
                $table->dropColumn('descuento');
            }
            
            if (Schema::hasColumn('ventas', 'subtotal')) {
                $table->dropColumn('subtotal');
            }
            
            if (Schema::hasColumn('ventas', 'fecha')) {
                $table->dropColumn('fecha');
            }
            
            if (Schema::hasColumn('ventas', 'codigo')) {
                $table->dropColumn('codigo');
            }
            
            // Eliminar las claves foráneas antes de eliminar las columnas
            if (Schema::hasColumn('ventas', 'caja_id')) {
                $table->dropForeign(['caja_id']);
                $table->dropColumn('caja_id');
            }
            
            if (Schema::hasColumn('ventas', 'usuario_id')) {
                $table->dropForeign(['usuario_id']);
                $table->dropColumn('usuario_id');
            }
        });
    }
};
