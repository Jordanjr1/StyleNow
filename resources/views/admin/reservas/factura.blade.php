<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura de Venta - StyleNow</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; font-size: 14px; margin: 0; padding: 20px; background: #555; display: flex; justify-content: center; }
        .factura-container { background: #fff; width: 100%; max-width: 800px; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #fad370; padding-bottom: 20px; margin-bottom: 20px; }
        .logo-box { width: 45%; }
        .logo-box h1 { font-family: 'Times New Roman', serif; color: #000; font-size: 32px; margin: 0 0 10px 0; }
        .info-empresa { font-size: 12px; line-height: 1.5; color: #555; }
        .datos-factura { width: 45%; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
        .datos-factura h2 { margin: 0 0 10px 0; font-size: 18px; color: #000; }
        .fila-dato { display: flex; justify-content: space-between; margin-bottom: 5px; font-size: 13px; }
        .cliente-box { margin-bottom: 30px; border: 1px solid #eee; padding: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #1a1a1a; color: #fad370; padding: 12px; text-align: left; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .badge-iva { font-size: 10px; background: #e8f5e9; color: #2e7d32; padding: 2px 6px; border-radius: 10px; margin-left: 6px; font-weight: bold; }
        .badge-exento { font-size: 10px; background: #fff3e0; color: #e65100; padding: 2px 6px; border-radius: 10px; margin-left: 6px; font-weight: bold; }
        .totales-box { width: 320px; margin-left: auto; border-top: 2px solid #1a1a1a; padding-top: 15px; }
        .totales-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 14px; }
        .totales-row.descuento { color: #e53935; }
        .totales-row.abono { color: #2e7d32; font-style: italic; }
        .totales-row.total-bruto { font-weight: bold; border-top: 1px solid #eee; padding-top: 8px; margin-top: 4px; }
        .totales-row.gran-total { font-size: 18px; font-weight: bold; color: #fff; background: #1a1a1a; padding: 10px 8px; border-radius: 6px; margin-top: 8px; }
        .puntos-badge { display: inline-block; background: #fad370; color: #000; font-weight: bold; padding: 4px 12px; border-radius: 20px; font-size: 13px; }
        .footer { text-align: center; margin-top: 40px; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 20px; }
        .btn-imprimir { background: #fad370; color: #000; border: none; padding: 10px 20px; font-weight: bold; border-radius: 5px; cursor: pointer; position: fixed; top: 20px; right: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); z-index: 1000; }

        @media print {
            body { background: #fff; padding: 0; }
            .factura-container { box-shadow: none; max-width: 100%; padding: 0; }
            .btn-imprimir { display: none; }
        }
    </style>
</head>
<body>

    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Factura</button>

    <div class="factura-container">

        {{-- ── CABECERA ── --}}
        <div class="header">
            <div class="logo-box">
                <h1>StyleNow</h1>
                <div class="info-empresa">
                    <strong>RUC:</strong> 1792145874001<br>
                    <strong>Dirección:</strong> {{ $sucursal->suc_direccion ?? 'Quito - Ecuador' }}<br>
                    <strong>Teléfono:</strong> {{ $sucursal->suc_telefono ?? '0999999999' }}<br>
                    <strong>Obligado a llevar contabilidad:</strong> SÍ
                </div>
            </div>
            <div class="datos-factura">
                <h2>FACTURA</h2>
                <div class="fila-dato"><strong>Nº:</strong> <span>{{ $venta->vnt_numeroFactura }}</span></div>
                <div class="fila-dato"><strong>Fecha Emisión:</strong> <span>{{ \Carbon\Carbon::parse($venta->vnt_fechaVenta)->format('d/m/Y H:i') }}</span></div>
                <div class="fila-dato"><strong>Método de Pago:</strong> <span>{{ $venta->vnt_metodoPago }}</span></div>
                <div class="fila-dato"><strong>Atendido por:</strong> <span>{{ $estilista->usr_nombre }} {{ $estilista->usr_apellido }}</span></div>
            </div>
        </div>

        {{-- ── DATOS DEL CLIENTE ── --}}
        <div class="cliente-box">
            <div class="fila-dato" style="margin-bottom: 8px;"><strong>Cliente / Razón Social:</strong> <span>{{ $cliente->usr_nombre }} {{ $cliente->usr_apellido }}</span></div>
            <div class="fila-dato" style="margin-bottom: 8px;"><strong>RUC / CI:</strong> <span>{{ $cliente->usr_cedula ?? '9999999999' }}</span></div>
            <div class="fila-dato" style="margin-bottom: 8px;"><strong>Correo Electrónico:</strong> <span>{{ $cliente->usr_email }}</span></div>
        </div>

        {{-- ── TABLA DE ÍTEMS ── --}}
        <table>
            <thead>
                <tr>
                    <th>CANT</th>
                    <th>DESCRIPCIÓN</th>
                    <th style="text-align: right;">V. UNITARIO</th>
                    <th style="text-align: right;">V. TOTAL</th>
                </tr>
            </thead>
            <tbody>

                {{-- Servicios: una fila por cada servicio --}}
                @php
                    // Intentamos usar el desglose detallado si el controlador lo pasó.
                    // Si no existe (facturas antiguas), caemos al método legacy de una sola fila.
                    $lineasServicios = $lineasServicios ?? [];
                    $lineasProductos = $lineasProductos ?? [];
                @endphp

                @if(!empty($lineasServicios))
                    @foreach($lineasServicios as $linea)
                    <tr>
                        <td>{{ $linea['cantidad'] }}</td>
                        <td>
                            {{ $linea['nombre'] }}
                            <span class="badge-iva">IVA 15%</span>
                        </td>
                        <td style="text-align: right;">${{ number_format($linea['precio'], 2) }}</td>
                        <td style="text-align: right;">${{ number_format($linea['subtotal'], 2) }}</td>
                    </tr>
                    @endforeach
                @elseif($cita->cit_nombres_servicios)
                    {{-- Fallback para facturas generadas antes de la actualización --}}
                    <tr>
                        <td>1</td>
                        <td>Servicio(s): {{ $cita->cit_nombres_servicios }} <span class="badge-iva">IVA 15%</span></td>
                        <td style="text-align: right;">${{ number_format($cita->cit_precio, 2) }}</td>
                        <td style="text-align: right;">${{ number_format($cita->cit_precio, 2) }}</td>
                    </tr>
                @endif

                {{-- Productos: una fila por cada producto --}}
                @if(!empty($lineasProductos))
                    @foreach($lineasProductos as $prod)
                    <tr>
                        <td>{{ $prod['cantidad'] }}</td>
                        <td>
                            {{ $prod['nombre'] }}
                            @if($prod['graba_iva'] ?? true)
                                <span class="badge-iva">IVA 15%</span>
                            @else
                                <span class="badge-exento">EXENTO</span>
                            @endif
                        </td>
                        <td style="text-align: right;">${{ number_format($prod['precio'], 2) }}</td>
                        <td style="text-align: right;">${{ number_format($prod['subtotal'], 2) }}</td>
                    </tr>
                    @endforeach
                @elseif($venta->vnt_productos_desc)
                    {{-- Fallback para facturas antiguas --}}
                    @php $productos = json_decode($venta->vnt_productos_desc, true); @endphp
                    @if(is_array($productos))
                        @foreach($productos as $prod)
                        <tr>
                            <td>{{ $prod['cantidad'] }}</td>
                            <td>{{ $prod['nombre'] }}</td>
                            <td style="text-align: right;">${{ number_format($prod['precio_unitario'], 2) }}</td>
                            <td style="text-align: right;">${{ number_format($prod['subtotal'], 2) }}</td>
                        </tr>
                        @endforeach
                    @endif
                @endif

            </tbody>
        </table>

        {{-- ── TOTALES (formato Gabriel) ── --}}
        <div class="totales-box">

            <div class="totales-row">
    <span>Subtotal:</span>
    <span>${{ number_format($venta->vnt_subtotal, 2) }}</span>
</div>

            <div class="totales-row">
                <span>IVA (15%):</span>
                <span>${{ number_format($venta->vnt_iva, 2) }}</span>
            </div>

            {{-- Descuento por promoción (si aplica) --}}
            @if($venta->vnt_descuento > 0)
            <div class="totales-row descuento">
                <span>(-) Descuento / Promoción:</span>
                <span>-${{ number_format($venta->vnt_descuento, 2) }}</span>
            </div>
            @endif

<div class="totales-row total-bruto">
    <span>Total:</span>
    <span>${{ number_format($venta->vnt_subtotal + $venta->vnt_iva, 2) }}</span>
</div>

            {{-- Abono previo de reserva (si aplica) --}}
            @if($cita->cit_abono > 0)
            <div class="totales-row abono">
                <span>(-) Abono Previo (Reserva):</span>
                <span>-${{ number_format($cita->cit_abono, 2) }}</span>
            </div>
            @endif

            <div class="totales-row gran-total">
                <span>SALDO PAGADO:</span>
                <span>${{ number_format($venta->vnt_total, 2) }}</span>
            </div>

        </div>

        {{-- ── PIE DE PÁGINA ── --}}
        <div class="footer">
            ¡Gracias por preferir StyleNow! 💛<br><br>
            <span class="puntos-badge">
                ★ Has ganado {{ floor($venta->vnt_subtotal + $venta->vnt_iva) }} puntos de fidelización
            </span><br><br>
            Documento generado electrónicamente.
        </div>

    </div>

</body>
</html>