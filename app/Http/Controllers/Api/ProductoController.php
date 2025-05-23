<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\Producto;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Get all active products with all necessary fields
            $productos = Producto::select(
                'id', 
                'nombre', 
                'descripcion',
                'precio_venta', 
                'precio_compra',
                'stock', 
                'codigo',
                'imagen',
                'precio_promocional',
                'fecha_inicio_promocion',
                'fecha_fin_promocion'
            )->get();
            
            // Transform data to match frontend expectations
            $transformed = $productos->map(function ($producto) {
                return [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio_venta, // Alias for frontend
                    'precio_venta' => $producto->precio_venta,
                    'precio_compra' => $producto->precio_compra,
                    'stock' => $producto->stock,
                    'codigo_barras' => $producto->codigo, // Alias for frontend
                    'codigo' => $producto->codigo,
                    'imagen' => $producto->imagen,
                    'tiene_promocion' => $producto->tienePromocionActiva(),
                    'precio_actual' => $producto->getPrecioActual()
                ];
            });
            
            return response()->json($transformed);        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error in ProductoController@index: ' . $e->getMessage());
            
            // Return an empty array with 200 status to prevent frontend errors
            return response()->json([], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
