<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    public function __invoke()
    {
        return inertia('Ventas/Venta');
    }
    
    /**
     * Generar un comprobante de venta en formato PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function generarComprobante(Request $request)
    {
        try {
            // Validar los datos recibidos - Hacemos la validación menos estricta para adaptarnos a la solicitud AJAX
            $request->validate([
                'numero_venta' => 'required|string',
                'detalles' => 'required|array',
                'subtotal' => 'required|numeric',
                'descuentos' => 'required|numeric',
                'total' => 'required|numeric',
                'fecha' => 'required',
                'cajero' => 'required|string',
            ]);
            
            // En una implementación real, aquí se obtendría la venta de la base de datos
            // $venta = Venta::findOrFail($request->venta_id);
            
            // Para esta demostración, usamos los datos del request
            $datosVenta = $request->all();
            
            // Generar el PDF
            $pdf = PDF::loadView('comprobantes.venta', [
                'venta' => $datosVenta,
                'fecha_formateada' => now()->format('d/m/Y H:i:s'),
            ]);
            
            // Nombre del archivo
            $nombreArchivo = 'comprobante_' . $datosVenta['numero_venta'] . '.pdf';
            
            // Retornar el PDF como un stream binario
            return $pdf->stream($nombreArchivo);
        } catch (\Exception $e) {
            // Log el error
            \Log::error('Error al generar comprobante: ' . $e->getMessage());
            
            // Retornar el error
            return response()->json(['error' => 'Error al generar comprobante: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Enviar comprobante por correo electrónico
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarComprobantePorEmail(Request $request)
    {
        // Validar los datos recibidos - Hacemos la validación menos estricta
        $request->validate([
            'email' => 'required|email',
            'numero_venta' => 'required|string',
            'detalles' => 'required|array',
            'subtotal' => 'required|numeric',
            'descuentos' => 'required|numeric',
            'total' => 'required|numeric',
            'fecha' => 'required',
            'cajero' => 'required|string',
        ]);
        
        try {
            // Generar el PDF
            $pdf = PDF::loadView('comprobantes.venta', [
                'venta' => $request->all(),
                'fecha_formateada' => now()->format('d/m/Y H:i:s'),
            ]);
            
            // Nombre del archivo
            $nombreArchivo = 'comprobante_' . $request->numero_venta . '.pdf';
            
            // Crear directorio si no existe
            $pdfPath = storage_path('app/public/comprobantes/' . $nombreArchivo);
            $dir = dirname($pdfPath);
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $pdf->save($pdfPath);
            
            // Enviar el correo con el PDF adjunto
            Mail::send('emails.comprobante', ['venta' => $request->all()], function ($message) use ($request, $pdfPath, $nombreArchivo) {
                $message->to($request->email)
                    ->subject('Comprobante de compra - VentasPlus')
                    ->attach($pdfPath, [
                        'as' => $nombreArchivo,
                        'mime' => 'application/pdf',
                    ]);
            });
            
            // Eliminar el archivo temporal
            @unlink($pdfPath);
            
            return response()->json(['message' => 'Comprobante enviado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al enviar el comprobante: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Reimprimir un comprobante de venta existente
     *
     * @param string $ventaId
     * @return \Illuminate\Http\Response
     */
    public function reimprimirComprobante($ventaId)
    {
        // En una implementación real, aquí se obtendría la venta de la base de datos
        // $venta = Venta::findOrFail($ventaId);
        
        // Para esta demostración, usamos datos de ejemplo
        $datosVenta = [
            'venta_id' => $ventaId,
            'fecha' => now()->toISOString(),
            'cajero' => 'Usuario Ejemplo',
            'detalles' => [],
            'subtotal' => 0,
            'descuentos' => 0,
            'total' => 0,
        ];
        
        // Generar el PDF
        $pdf = PDF::loadView('comprobantes.venta', [
            'venta' => $datosVenta,
            'fecha_formateada' => now()->format('d/m/Y H:i:s'),
            'es_reimpresion' => true,
        ]);
        
        // Nombre del archivo
        $nombreArchivo = 'comprobante_reimpresion_' . $ventaId . '.pdf';
        
        // Retornar el PDF para descarga
        return $pdf->download($nombreArchivo);
    }
}
