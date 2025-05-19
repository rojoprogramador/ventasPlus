<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['porcentaje', 'fijo']);
            $table->decimal('valor', 10, 2);
            $table->decimal('limite', 10, 2);
            $table->text('descripcion')->nullable();
            $table->foreignId('aplicado_por')->constrained('users');
            $table->foreignId('producto_id')->constrained('productos');
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('detalle_venta_id')->constrained('detalle_venta')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('descuentos');
    }
};
