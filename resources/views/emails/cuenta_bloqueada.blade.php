<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f3f4f6; margin: 0; padding: 40px 20px; color: #333333; }
        .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { background-color: #0a0a0a; padding: 35px 20px; text-align: center; border-bottom: 5px solid #ef4444; }
        .header h1 { margin: 0; color: #fad370; font-family: 'Times New Roman', serif; font-size: 36px; letter-spacing: 2px; }
        .content { padding: 40px 35px; }
        .greeting { font-size: 22px; font-weight: bold; margin-bottom: 20px; color: #111111; }
        .alert-box { background-color: #fef2f2; border-left: 6px solid #ef4444; padding: 20px 25px; margin: 30px 0; border-radius: 0 8px 8px 0; }
        .alert-box h2 { margin: 0 0 10px 0; color: #991b1b; font-size: 18px; display: flex; align-items: center; }
        .alert-box p { margin: 0; color: #b91c1c; font-size: 15px; line-height: 1.5; }
        .info-section { margin-top: 35px; padding-top: 30px; border-top: 1px solid #e5e7eb; }
        .info-section h3 { font-size: 17px; color: #111111; margin-bottom: 12px; }
        .info-section p { font-size: 15px; line-height: 1.6; color: #4b5563; margin-bottom: 25px; }
        .contact-btn { display: inline-block; background-color: #0a0a0a; color: #fad370; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; font-size: 14px; transition: background-color 0.3s; }
        .contact-btn:hover { background-color: #1f1f1f; }
        .footer { background-color: #f9fafb; padding: 30px; text-align: center; font-size: 13px; color: #6b7280; border-top: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>StyleNow</h1>
        </div>
        
        <div class="content">
            <div class="greeting">Hola, {{ explode(' ', $clienteNombre)[0] }}</div>
            
            <p style="font-size: 16px; line-height: 1.6; color: #4b5563;">
                Te escribimos para notificarte sobre una actualización importante relacionada con la seguridad y el estado de tu cuenta en nuestra plataforma.
            </p>
            
            <div class="alert-box">
                <h2>⚠️ Cuenta Suspendida Temporalmente</h2>
                <p>Nuestro sistema automático ha detectado que has alcanzado el límite máximo permitido de <strong>3 citas canceladas</strong>.</p>
            </div>

            <div class="info-section">
                <h3>¿Por qué aplicamos esta medida?</h3>
                <p>En StyleNow valoramos profundamente el tiempo de nuestros profesionales y el de otros clientes que desean agendar espacios. Las cancelaciones recurrentes afectan directamente la disponibilidad de nuestra agenda.</p>
                
                <h3>¿Cómo puedo recuperar mi acceso?</h3>
                <p>Entendemos que pueden surgir imprevistos. Para reactivar tu cuenta y volver a disfrutar de nuestros servicios, por favor comunícate directamente con la recepción de nuestra sucursal para que podamos ayudarte.</p>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="mailto:soporte@stylenow.com" class="contact-btn">Contactar a Recepción</a>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 10px 0; font-size: 14px;">Atentamente,<br><strong style="color: #111;">El equipo de StyleNow</strong></p>
            <p style="margin: 0; font-size: 11px; color: #9ca3af;">Este es un mensaje automático generado por nuestro sistema de seguridad. Por favor no respondas a este correo.</p>
        </div>
    </div>
</body>
</html>