<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Reserva de Cita - StyleNow</title>
</head>
<body style='margin: 0; padding: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f7fa;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f5f7fa; padding: 20px 0;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 100%;'>
                    
                    <tr>
                        <td style='background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%); padding: 30px 20px; text-align: center;'>
                            <div style='width: 80px; height: 80px; background-color: #f2e19f2b; border-radius: 12px; margin: 0 auto 15px; display: inline-block; text-align: center; line-height: 80px;'>
                                <img src="{{ $message->embed(public_path('images/SN_icon.png')) }}" alt='Logo StyleNow' style='width: 50px; height: 50px; vertical-align: middle;' />
                            </div>
                            <h1 style='color: #F2E19F; margin: 0; font-size: 28px; font-weight: 600;'>StyleNow</h1>
                            <p style='color: #F2E19F; margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;'>Gestión la belleza. Inspira el estilo.</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style='padding: 40px 30px;'>
                            <h2 style='color: #0A0A0A; text-align: center; margin: 0 0 10px 0; font-size: 24px;'>¡Hemos recibido tu reserva!</h2>
                            
                            <p style='color: #333333; font-size: 16px; line-height: 1.6; margin: 20px 0; text-align: center;'>
                                Hola <strong style='color: #0A0A0A;'>{{ $nombre_cliente }}</strong>, gracias por preferirnos. <br>Aquí están los detalles de tu cita:
                            </p>
                            
                            <div style='background-color: #f8f9fa; border: 1px solid #e9ecef; border-left: 4px solid #F2E19F; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                <p style='margin: 0 0 10px 0; color: #333;'><strong style='color:#0A0A0A;'>Servicio(s):</strong> {{ $servicios }}</p>
                                <p style='margin: 0 0 10px 0; color: #333;'><strong style='color:#0A0A0A;'>Profesional:</strong> {{ $estilista }}</p>
                                <p style='margin: 0 0 10px 0; color: #333;'><strong style='color:#0A0A0A;'>Fecha:</strong> {{ $fecha }}</p>
                                <p style='margin: 0; color: #333;'><strong style='color:#0A0A0A;'>Hora:</strong> {{ $hora }}</p>
                            </div>

                            <p style='color: #666; font-size: 14px; text-align: center; margin-bottom: 25px;'>
                                Para asegurar tu espacio, por favor confirma tu asistencia haciendo clic en el siguiente botón:
                            </p>
                            
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{{ $confirmUrl }}' style='display: inline-block; background-color: #4ade80; color: #000000; text-decoration: none; padding: 16px 45px; border-radius: 10px; font-size: 16px; font-weight: bold; box-shadow: 0 4px 15px rgba(74, 222, 128, 0.3);'>
                                    ✓ Confirmar mi Asistencia
                                </a>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td style='background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%); padding: 25px 30px; text-align: center;'>
                            <p style='color: #F2E19F; margin: 0; font-size: 12px; opacity: 0.7;'>
                                © {{ date('Y') }} StyleNow. Todos los derechos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>