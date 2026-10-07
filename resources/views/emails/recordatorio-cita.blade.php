<!DOCTYPE html>
<html lang='es'>
<head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'></head>
<body style='margin: 0; padding: 0; font-family: "Segoe UI", sans-serif; background-color: #f5f7fa;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='padding: 20px 0;'>
        <tr><td align='center'>
            <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 100%;'>
                <tr>
                    <td style='background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%); padding: 30px 20px; text-align: center;'>
                        <h1 style='color: #F2E19F; margin: 0; font-size: 28px; font-weight: 600;'>StyleNow</h1>
                        <p style='color: #F2E19F; margin: 5px 0 0 0; font-size: 14px;'>Recordatorio de Cita</p>
                    </td>
                </tr>
                <tr>
                    <td style='padding: 40px 30px;'>
                        <h2 style='color: #0A0A0A; text-align: center; margin: 0 0 10px 0; font-size: 24px;'>¡Tu cita es MAÑANA! ⏰</h2>
                        <p style='color: #333333; font-size: 16px; line-height: 1.6; margin: 20px 0; text-align: center;'>
                            Hola <strong style='color: #0A0A0A;'>{{ $nombre_cliente }}</strong>. Te recordamos que mañana tienes una cita con nosotros para dejarte increíble.
                        </p>
                        <div style='background-color: #f8f9fa; border-left: 4px solid #F2E19F; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                            <p style='margin: 0 0 10px 0; color: #333;'><strong style='color:#0A0A0A;'>Servicio(s):</strong> {{ $servicios }}</p>
                            <p style='margin: 0 0 10px 0; color: #333;'><strong style='color:#0A0A0A;'>Profesional:</strong> {{ $estilista }}</p>
                            <p style='margin: 0 0 10px 0; color: #333;'><strong style='color:#0A0A0A;'>Día:</strong> {{ $fecha }}</p>
                            <p style='margin: 0; color: #333;'><strong style='color:#0A0A0A;'>Hora:</strong> {{ $hora }}</p>
                        </div>
                        <p style='color: #666; font-size: 14px; text-align: center;'>Si necesitas cancelar o reprogramar, por favor contáctanos lo antes posible.</p>
                    </td>
                </tr>
            </table>
        </td></tr>
    </table>
</body>
</html>