<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ProductoController extends Controller
{
    /**
     * Muestra la lista de productos
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        try {
            // Obtener todos los productos ordenados por nombre
            $productos = Producto::orderBy('nombre')->get();
            
            // Registrar en log para depuración
            Log::info('Cargando página de productos', [
                'cantidad' => $productos->count()
            ]);
            
            return Inertia::render('Productos/Index', [
                'productos' => $productos
            ]);
        } catch (\Exception $e) {
            Log::error('Error al cargar productos', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // En caso de error, mostrar una página con array vacío
            return Inertia::render('Productos/Index', [
                'productos' => []
            ]);
        }
    }
}
