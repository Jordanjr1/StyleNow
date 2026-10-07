<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 550px; background-color: #ffffff; margin: 0 auto; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .header { background-color: #0a0a0a; padding: 30px; text-align: center; border-bottom: 3px solid #fad370; }
        .header h1 { color: #fad370; margin: 0; font-family: 'Times New Roman', serif; font-size: 32px; letter-spacing: 1px; }
        .content { padding: 40px 30px; color: #333; line-height: 1.6; text-align: center; }
        .content h2 { color: #111; margin-top: 0; }
        .service-box { background-color: #fdfbf7; border: 1px dashed #fad370; padding: 20px; border-radius: 8px; margin: 25px 0; font-size: 16px; color: #555; }
        .btn { display: inline-block; padding: 14px 35px; background-color: #fad370; color: #000; text-decoration: none; font-weight: bold; border-radius: 30px; margin-top: 10px; font-size: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .footer { background-color: #f1f1f1; color: #888; text-align: center; padding: 20px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>StyleNow</h1>
        </div>
        <div class="content">
            <h2>¡Hola, {{ explode(' ', $clienteNombre)[0] }}! 👋</h2>
            <p>Han pasado algunos días desde tu última visita a nuestro salón y queremos asegurarnos de que sigas luciendo espectacular.</p>
            
            <div class="service-box">
                Ya es el momento ideal para darle un retoque a tu:<br>
                <strong style="color: #000; font-size: 18px; display: block; margin-top: 10px;">✨ {{ $servicio }} ✨</strong>
            </div>

            <p>Nuestros especialistas tienen horarios disponibles esta semana. ¿Te reservamos un espacio?</p>

            <a href="{{ $urlReserva }}" class="btn">Agendar mi Cita Ahora</a>
        </div>
        <div class="footer">
            No respondas a este correo. Si deseas dejar de recibir estos recordatorios, puedes actualizar tus preferencias en tu Perfil de StyleNow.
        </div>
    </div>
</body>
</html>