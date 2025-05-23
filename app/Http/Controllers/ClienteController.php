<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClienteController extends Controller
{
    /**
     * Muestra la lista de clientes
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $clientes = Cliente::where('estado', 'activo')->orderBy('nombre')->get();
        
        return Inertia::render('Clientes/Index', [
            'clientes' => $clientes
        ]);
    }

    /**
     * Busca clientes por nombre, teléfono o documento
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buscar(Request $request)
    {
        $busqueda = $request->busqueda;
        
        if (empty($busqueda)) {
            return response()->json([
                'clientes' => []
            ]);
        }
        
        $clientes = Cliente::where('estado', 'activo')
            ->where(function($query) use ($busqueda) {
                $query->where('nombre', 'LIKE', "%{$busqueda}%")
                    ->orWhere('telefono', 'LIKE', "%{$busqueda}%")
                    ->orWhere('documento', 'LIKE', "%{$busqueda}%");
            })
            ->orderBy('nombre')
            ->limit(10)
            ->get();
            
        return response()->json([
            'clientes' => $clientes
        ]);
    }

    /**
     * Guarda un nuevo cliente rápidamente desde la pantalla de ventas
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function guardarRapido(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:20',
            'tipo_documento' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
        ]);
        
        // Verificar si ya existe un cliente con el mismo documento
        if ($request->documento) {
            $clienteExistente = Cliente::where('documento', $request->documento)
                ->where('estado', 'activo')
                ->first();
                
            if ($clienteExistente) {
                return response()->json([
                    'success' => false,
                    'cliente' => $clienteExistente,
                    'message' => 'Ya existe un cliente con este documento',
                    'tipo' => 'existente'
                ]);
            }
        }
        
        // Verificar si ya existe un cliente con el mismo correo electrónico
        if ($request->email) {
            $clienteExistente = Cliente::where('email', $request->email)
                ->where('estado', 'activo')
                ->first();
                
            if ($clienteExistente) {
                return response()->json([
                    'success' => false,
                    'cliente' => $clienteExistente,
                    'message' => 'Ya existe un cliente con este correo electrónico',
                    'tipo' => 'existente'
                ]);
            }
        }
        
        $cliente = Cliente::create([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'tipo_documento' => $request->tipo_documento,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'estado' => 'activo'
        ]);
        
        return response()->json([
            'success' => true,
            'cliente' => $cliente,
            'message' => 'Cliente creado correctamente'
        ]);
    }

    /**
     * Obtiene los detalles de un cliente específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function obtenerDetalles($id)
    {
        $cliente = Cliente::with('ventas')->findOrFail($id);
        
        return response()->json([
            'cliente' => $cliente
        ]);
    }
    
    /**
     * Actualiza la información de un cliente existente
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'documento' => 'nullable|string|max:20',
            'tipo_documento' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string|max:255',
        ]);
        
        $cliente = Cliente::findOrFail($id);
        
        // Verificar si ya existe otro cliente con el mismo documento
        if ($request->documento && $request->documento !== $cliente->documento) {
            $clienteExistente = Cliente::where('documento', $request->documento)
                ->where('id', '!=', $id)
                ->where('estado', 'activo')
                ->first();
                
            if ($clienteExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otro cliente con este documento',
                    'errors' => ['documento' => ['Ya existe otro cliente con este documento']]
                ], 422);
            }
        }
        
        // Verificar si ya existe otro cliente con el mismo correo electrónico
        if ($request->email && $request->email !== $cliente->email) {
            $clienteExistente = Cliente::where('email', $request->email)
                ->where('id', '!=', $id)
                ->where('estado', 'activo')
                ->first();
                
            if ($clienteExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe otro cliente con este correo electrónico',
                    'errors' => ['email' => ['Ya existe otro cliente con este correo electrónico']]
                ], 422);
            }
        }
        
        $cliente->update([
            'nombre' => $request->nombre,
            'documento' => $request->documento,
            'tipo_documento' => $request->tipo_documento,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
        ]);
        
        return response()->json([
            'success' => true,
            'cliente' => $cliente,
            'message' => 'Cliente actualizado correctamente'
        ]);
    }
}
