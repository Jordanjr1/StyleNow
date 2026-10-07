<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { max-width: 600px; background-color: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background-color: #1a1a1a; color: #fad370; padding: 20px; text-align: center; }
        .content { padding: 30px; color: #333; line-height: 1.6; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px; }
        .table th { background-color: #f9f9f9; padding: 10px; border-bottom: 2px solid #ddd; text-align: left; }
        .table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .total-box { background-color: #f9f9f9; padding: 15px; text-align: right; font-weight: bold; font-size: 18px; border-radius: 5px; }
        .btn { display: inline-block; padding: 12px 25px; background-color: #fad370; color: #000; text-decoration: none; font-weight: bold; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin:0;">StyleNow</h1>
            <p style="margin:5px 0 0 0; font-size:14px; color:#ccc;">Comprobante de Pago Electrónico</p>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $clienteNombre }}</strong>,</p>
            <p>Agradecemos tu visita a nuestra sucursal. Adjuntamos el detalle de tu factura <b>{{ $numeroFactura }}</b>:</p>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th style="text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong style="font-size: 15px;">Servicio: {{ $servicioNombre }}</strong><br>
                            
                            @if(isset($productosJson) && $productosJson)
                                @php $prods = json_decode($productosJson, true); @endphp
                                @if(is_array($prods) && count($prods) > 0)
                                    <div style="margin-top: 8px; margin-bottom: 8px; color: #555; font-size: 13px;">
                                        <strong>Productos añadidos:</strong><br>
                                        @foreach($prods as $p)
                                            • {{ $p['cantidad'] }}x {{ str_replace('📦 ', '', $p['nombre']) }} (${{ number_format($p['subtotal'], 2) }})<br>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                            
                            <small style="color:#888;">Atendido por: {{ $estilistaNombre }}</small>
                        </td>
                        <td style="text-align: right; vertical-align: top; font-size: 16px;">
                            ${{ number_format($total, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="total-box">
                Total Pagado: ${{ number_format($total, 2) }}
            </div>

            <p style="text-align: center;">
                <a href="{{ $urlFactura }}" class="btn">Ver e Imprimir Factura Completa</a>
            </p>
        </div>
    </div>
</body>
</html>