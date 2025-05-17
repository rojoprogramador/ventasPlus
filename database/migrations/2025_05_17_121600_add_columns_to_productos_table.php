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
        Schema::table('productos', function (Blueprint $table) {
            $table->string('codigo')->unique()->after('id');
            $table->string('nombre')->after('codigo');
            $table->text('descripcion')->nullable()->after('nombre');
            $table->decimal('precio_venta', 10, 2)->after('descripcion');
            $table->decimal('precio_compra', 10, 2)->after('precio_venta');
            $table->integer('stock')->default(0)->after('precio_compra');
            $table->integer('stock_minimo')->default(0)->after('stock');
            $table->foreignId('categoria_id')->nullable()->after('stock_minimo')->constrained('categorias')->nullOnDelete();
            $table->string('imagen')->nullable()->after('categoria_id');
            $table->boolean('estado')->default(true)->after('imagen');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn([
                'codigo', 'nombre', 'descripcion', 'precio_venta', 'precio_compra',
                'stock', 'stock_minimo', 'categoria_id', 'imagen', 'estado'
            ]);
        });
    }
};
