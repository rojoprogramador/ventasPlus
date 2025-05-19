<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Venta - {{ $venta['venta_id'] ?? $venta['numero_venta'] }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #2c5282;
        }
        .header h2 {
            margin: 5px 0;
        }
        .info-venta {
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            padding: 10px 0;
            margin-bottom: 20px;
        }
        .info-venta p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f3f4f6;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table th.text-right {
            text-align: right;
        }
        table th.text-center {
            text-align: center;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        table td.text-right {
            text-align: right;
        }
        table td.text-center {
            text-align: center;
        }
        .totales {
            margin-top: 20px;
            text-align: right;
        }
        .totales p {
            margin: 5px 0;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
        .reimpresion {
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 72px;
            color: rgba(255, 0, 0, 0.2);
            transform: rotate(-45deg);
            z-index: -1;
        }
    </style>
</head>
<body>
    @if(isset($es_reimpresion) && $es_reimpresion)
    <div class="reimpresion">REIMPRESIÓN</div>
    @endif

    <div class="header">
        <h1>VentasPlus</h1>
        <h2>Comprobante de Compra</h2>
    </div>

    <div class="info-venta">
        <p><strong>No. Venta:</strong> {{ $venta['venta_id'] ?? $venta['numero_venta'] }}</p>
        <p><strong>Fecha:</strong> {{ $fecha_formateada }}</p>
        <p><strong>Cajero:</strong> {{ $venta['cajero'] }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Producto</th>
                <th class="text-right">Precio Unit.</th>
                <th class="text-center">Cant.</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta['detalles'] as $detalle)
            <tr>
                <td>{{ $detalle['nombre'] }}</td>
                <td class="text-right">${{ number_format($detalle['precio_unitario'], 2) }}</td>
                <td class="text-center">{{ $detalle['cantidad'] }}</td>
                <td class="text-right">${{ number_format($detalle['precio_unitario'] * $detalle['cantidad'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totales">
        <p><strong>Subtotal:</strong> ${{ number_format($venta['subtotal'], 2) }}</p>
        <p><strong>Descuentos:</strong> ${{ number_format($venta['descuentos'], 2) }}</p>
        <p class="total"><strong>Total:</strong> ${{ number_format($venta['total'], 2) }}</p>
        
        @if(isset($venta['metodo_pago']) && $venta['metodo_pago'] === 'efectivo' && isset($venta['monto_entregado']))
        <p><strong>Monto entregado:</strong> ${{ number_format($venta['monto_entregado'], 2) }}</p>
        <p><strong>Cambio:</strong> ${{ number_format($venta['monto_entregado'] - $venta['total'], 2) }}</p>
        @endif
    </div>

    <div class="footer">
        <p>Gracias por su compra</p>
        <p>Para cualquier duda o aclaración, conserve este comprobante</p>
    </div>
</body>
</html>
