<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'productos';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio_venta',
        'precio_compra',
        'stock',
        'stock_minimo',
        'categoria_id',
        'imagen',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'precio_venta' => 'decimal:2',
        'precio_compra' => 'decimal:2',
        'stock' => 'integer',
        'stock_minimo' => 'integer',
        'estado' => 'boolean',
    ];

    /**
     * Get the categoria that owns the producto.
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Get the detalles_venta for the producto.
     */
    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    /**
     * Get the detalles_cotizacion for the producto.
     */
    public function detallesCotizacion()
    {
        return $this->hasMany(DetalleCotizacion::class);
    }
}
