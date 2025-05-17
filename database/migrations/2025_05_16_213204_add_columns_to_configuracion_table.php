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
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('nombre_empresa');
            $table->string('ruc', 20)->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('rfc', 20)->nullable();
            $table->string('sitio_web')->nullable();
            $table->string('logo')->nullable();
            $table->string('moneda', 10)->default('$');
            $table->decimal('porcentaje_impuesto', 5, 2)->default(0);
            $table->string('formato_factura', 50)->default('FACT-{serie}-{correlativo}');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn([
                'nombre_empresa',
                'ruc',
                'direccion',
                'telefono',
                'email',
                'rfc',
                'sitio_web',
                'logo',
                'moneda',
                'porcentaje_impuesto',
                'formato_factura'
            ]);
        });
    }
};
