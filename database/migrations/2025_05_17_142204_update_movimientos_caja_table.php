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
        // Verificar si la tabla existe, y si no, créala con todas las columnas necesarias
        if (!Schema::hasTable('movimientos_caja')) {
            Schema::create('movimientos_caja', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('caja_id');
                $table->unsignedBigInteger('usuario_id');
                $table->string('tipo'); // apertura, cierre, entrada, salida, etc.
                $table->decimal('monto', 10, 2);
                $table->text('descripcion')->nullable();
                $table->timestamp('fecha');
                $table->timestamps();
                
                // Claves foráneas
                $table->foreign('caja_id')->references('id')->on('cajas')->onDelete('cascade');
                $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            });
            return;
        }
        
        // Si la tabla ya existe, añadir las columnas faltantes
        Schema::table('movimientos_caja', function (Blueprint $table) {
            if (!Schema::hasColumn('movimientos_caja', 'usuario_id')) {
                $table->unsignedBigInteger('usuario_id')->after('caja_id');
                $table->foreign('usuario_id')->references('id')->on('users')->onDelete('restrict');
            }
            
            if (!Schema::hasColumn('movimientos_caja', 'tipo')) {
                $table->string('tipo')->after('usuario_id'); // apertura, cierre, entrada, salida, etc.
            }
            
            if (!Schema::hasColumn('movimientos_caja', 'monto')) {
                $table->decimal('monto', 10, 2)->after('tipo');
            }
            
            if (!Schema::hasColumn('movimientos_caja', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('monto');
            }
            
            if (!Schema::hasColumn('movimientos_caja', 'fecha')) {
                $table->timestamp('fecha')->after('descripcion');
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
        if (Schema::hasTable('movimientos_caja')) {
            Schema::table('movimientos_caja', function (Blueprint $table) {
                // Eliminar las columnas añadidas en orden inverso
                if (Schema::hasColumn('movimientos_caja', 'fecha')) {
                    $table->dropColumn('fecha');
                }
                
                if (Schema::hasColumn('movimientos_caja', 'descripcion')) {
                    $table->dropColumn('descripcion');
                }
                
                if (Schema::hasColumn('movimientos_caja', 'monto')) {
                    $table->dropColumn('monto');
                }
                
                if (Schema::hasColumn('movimientos_caja', 'tipo')) {
                    $table->dropColumn('tipo');
                }
            });
        }
    }
};
