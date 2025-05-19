<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Comprobante de Compra</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c5282;
        }
        .mensaje {
            margin-bottom: 20px;
        }
        .resumen {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>VentasPlus</h1>
        <h2>Comprobante de Compra</h2>
    </div>

    <div class="mensaje">
        <p>Estimado cliente,</p>
        <p>Gracias por su compra en VentasPlus. Adjunto encontrará el comprobante de su compra del día {{ date('d/m/Y', strtotime($venta['fecha'])) }}.</p>
    </div>

    <div class="resumen">
        <p><strong>Número de venta:</strong> {{ $venta['venta_id'] ?? $venta['numero_venta'] }}</p>
        <p><strong>Fecha:</strong> {{ date('d/m/Y H:i:s', strtotime($venta['fecha'])) }}</p>
        <p><strong>Total:</strong> ${{ number_format($venta['total'], 2) }}</p>
    </div>

    <p>Para más detalles, por favor revise el comprobante adjunto a este correo.</p>

    <div class="footer">
        <p>Este es un correo automático, por favor no responda a este mensaje.</p>
        <p>Si tiene alguna pregunta, comuníquese con nuestro servicio al cliente.</p>
        <p>&copy; {{ date('Y') }} VentasPlus - Todos los derechos reservados</p>
    </div>
</body>
</html>
