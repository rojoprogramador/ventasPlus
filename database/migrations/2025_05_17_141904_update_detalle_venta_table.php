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
        // Verificar si la tabla existe, si no existe, créala
        if (!Schema::hasTable('detalle_venta')) {
            Schema::create('detalle_venta', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('venta_id');
                $table->unsignedBigInteger('producto_id');
                $table->integer('cantidad')->default(1);
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('descuento', 10, 2)->default(0);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();
                
                $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
                $table->foreign('producto_id')->references('id')->on('productos')->onDelete('restrict');
            });
            return; // Si creamos la tabla, no necesitamos añadir columnas
        }
        
        // Si la tabla ya existe, añadir las columnas que faltan
        Schema::table('detalle_venta', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_venta', 'venta_id')) {
                $table->unsignedBigInteger('venta_id')->after('id');
                $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('detalle_venta', 'producto_id')) {
                $table->unsignedBigInteger('producto_id')->after('venta_id');
                $table->foreign('producto_id')->references('id')->on('productos')->onDelete('restrict');
            }
            
            if (!Schema::hasColumn('detalle_venta', 'cantidad')) {
                $table->integer('cantidad')->after('producto_id')->default(1);
            }
            
            if (!Schema::hasColumn('detalle_venta', 'precio_unitario')) {
                $table->decimal('precio_unitario', 10, 2)->after('cantidad');
            }
            
            if (!Schema::hasColumn('detalle_venta', 'descuento')) {
                $table->decimal('descuento', 10, 2)->after('precio_unitario')->default(0);
            }
            
            if (!Schema::hasColumn('detalle_venta', 'subtotal')) {
                $table->decimal('subtotal', 10, 2)->after('descuento');
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
        if (Schema::hasTable('detalle_venta')) {
            Schema::table('detalle_venta', function (Blueprint $table) {
                // Eliminar las claves foráneas primero
                if (Schema::hasColumn('detalle_venta', 'venta_id')) {
                    $table->dropForeign(['venta_id']);
                }
                
                if (Schema::hasColumn('detalle_venta', 'producto_id')) {
                    $table->dropForeign(['producto_id']);
                }
                
                // Luego eliminar las columnas
                if (Schema::hasColumn('detalle_venta', 'subtotal')) {
                    $table->dropColumn('subtotal');
                }
                
                if (Schema::hasColumn('detalle_venta', 'descuento')) {
                    $table->dropColumn('descuento');
                }
                
                if (Schema::hasColumn('detalle_venta', 'precio_unitario')) {
                    $table->dropColumn('precio_unitario');
                }
                
                if (Schema::hasColumn('detalle_venta', 'cantidad')) {
                    $table->dropColumn('cantidad');
                }
                
                if (Schema::hasColumn('detalle_venta', 'producto_id')) {
                    $table->dropColumn('producto_id');
                }
                
                if (Schema::hasColumn('detalle_venta', 'venta_id')) {
                    $table->dropColumn('venta_id');
                }
            });
        }
    }
};
