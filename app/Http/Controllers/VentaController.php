<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function __invoke()
    {
        return inertia('Ventas/Venta');
    }
}
