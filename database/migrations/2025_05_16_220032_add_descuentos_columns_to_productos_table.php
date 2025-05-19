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
            $table->decimal('precio_promocional', 10, 2)->nullable()->after('precio_venta');
            $table->timestamp('fecha_inicio_promocion')->nullable()->after('precio_promocional');
            $table->timestamp('fecha_fin_promocion')->nullable()->after('fecha_inicio_promocion');
            $table->boolean('permite_descuentos')->default(true)->after('fecha_fin_promocion');
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
            $table->dropColumn(['precio_promocional', 'fecha_inicio_promocion', 'fecha_fin_promocion', 'permite_descuentos']);
        });
    }
};
