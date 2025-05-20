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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->foreignId('categoria_id')->constrained('categorias');
            $table->string('imagen')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->decimal('precio_promocional', 10, 2)->nullable()->after('precio_venta');
            $table->timestamp('fecha_inicio_promocion')->nullable()->after('precio_promocional');
            $table->timestamp('fecha_fin_promocion')->nullable()->after('fecha_inicio_promocion');
            $table->boolean('permite_descuentos')->default(true)->after('fecha_fin_promocion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos');
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['precio_promocional', 'fecha_inicio_promocion', 'fecha_fin_promocion', 'permite_descuentos']);
            $table->dropForeign(['categoria_id']);
            $table->dropColumn([
                'codigo', 'nombre', 'descripcion', 'precio_venta', 'precio_compra',
                'stock', 'stock_minimo', 'categoria_id', 'imagen', 'estado'
            ]);
        });
    }
};
