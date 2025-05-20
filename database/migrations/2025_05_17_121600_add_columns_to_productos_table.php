<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'codigo')) {
                $table->string('codigo');
            }
            if (!Schema::hasColumn('productos', 'nombre')) {
                $table->string('nombre');
            }
            if (!Schema::hasColumn('productos', 'descripcion')) {
                $table->text('descripcion')->nullable();
            }
            if (!Schema::hasColumn('productos', 'precio_venta')) {
                $table->decimal('precio_venta', 10, 2);
            }
            if (!Schema::hasColumn('productos', 'precio_compra')) {
                $table->decimal('precio_compra', 10, 2);
            }
            if (!Schema::hasColumn('productos', 'stock')) {
                $table->integer('stock')->default(0);
            }
            if (!Schema::hasColumn('productos', 'stock_minimo')) {
                $table->integer('stock_minimo')->default(0);
            }
            if (!Schema::hasColumn('productos', 'categoria_id')) {
                $table->foreignId('categoria_id')->nullable()->constrained('categorias');
            }
            if (!Schema::hasColumn('productos', 'imagen')) {
                $table->string('imagen')->nullable();
            }
            if (!Schema::hasColumn('productos', 'estado')) {
                $table->boolean('estado')->default(true);
            }
        });
    }

    public function down()
    {
        if (Schema::hasTable('productos')) {
            Schema::table('productos', function (Blueprint $table) {
                $columns = [
                    'codigo', 'nombre', 'descripcion', 'precio_venta',
                    'precio_compra', 'stock', 'stock_minimo', 'categoria_id',
                    'imagen', 'estado'
                ];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('productos', $column)) {
                        if ($column === 'categoria_id') {
                            $table->dropForeign(['categoria_id']);
                        }
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};