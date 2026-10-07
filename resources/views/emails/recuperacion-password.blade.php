<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Recuperación de Contraseña - StyleNow</title>
</head>
<body style='margin: 0; padding: 0; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; background-color: #f5f7fa;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f5f7fa; padding: 20px 0;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); max-width: 100%;'>
                    
                    <!-- Header con logo StyleNow -->
                    <tr>
                        <td style='background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%); padding: 30px 20px; text-align: center;'>
                            <div style='width: 80px; height: 80px; background-color: #f2e19f2b; border-radius: 12px; margin: 0 auto 15px; display: inline-block; text-align: center; line-height: 80px;'>
                                <img src="{{ $message->embed(public_path('images/SN_icon.png')) }}" alt='Logo StyleNow' style='width: 50px; height: 50px; vertical-align: middle;' />
                            </div>
                            <h1 style='color: #F2E19F; margin: 0; font-size: 28px; font-weight: 600;'>StyleNow</h1>
                            <p style='color: #F2E19F; margin: 5px 0 0 0; font-size: 14px; opacity: 0.9;'>Gestión la belleza. Inspira el estilo.</p>
                        </td>
                    </tr>
                    
                    <!-- Contenido principal -->
                    <tr>
                        <td style='padding: 40px 30px;'>
                            <div style='text-align: center; margin-bottom: 20px;'>
                                <div style='display: inline-block; width: 80px; height: 80px; background: linear-gradient(135deg, #0A0A0A 0%, #333333 100%); border-radius: 50%; line-height: 80px; font-size: 40px;'>
                                    🔐
                                </div>
                            </div>
                            
                            <h2 style='color: #0A0A0A; text-align: center; margin: 0 0 10px 0; font-size: 24px;'>Recuperación de Contraseña</h2>
                            
                            <p style='color: #333333; font-size: 16px; line-height: 1.6; margin: 20px 0;'>
                                Hola <strong style='color: #0A0A0A;'>{{ $nombre_usuario }}</strong>,
                            </p>
                            
                            <p style='color: #333333; font-size: 16px; line-height: 1.6; margin: 20px 0;'>
                                Hemos recibido una solicitud para recuperar tu contraseña en <strong>StyleNow</strong>. A continuación te proporcionamos una contraseña temporal:
                            </p>
                            
                            <!-- Caja de contraseña temporal -->
                            <div style='background: linear-gradient(135deg, #f5f7fa 0%, #e8eef5 100%); border: 2px dashed #F2E19F; padding: 25px; margin: 30px 0; border-radius: 12px; text-align: center;'>
                                <p style='color: #666; margin: 0 0 10px 0; font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;'>Contraseña Temporal</p>
                                <p style='background-color: #0A0A0A; color: #F2E19F; font-size: 32px; font-weight: bold; margin: 0; padding: 15px; border-radius: 8px; letter-spacing: 2px; font-family: "Courier New", monospace; display: inline-block; min-width: 200px;'>
                                    {{ $passwordTemporal }}
                                </p>
                            </div>
                            
                            <!-- Instrucciones importantes -->
                            <div style='background-color: #FFF3CD; border-left: 4px solid #FFC107; padding: 20px; margin: 25px 0; border-radius: 8px;'>
                                <p style='color: #856404; margin: 0 0 10px 0; font-size: 15px; line-height: 1.6;'>
                                    <strong>⚠️ Importante:</strong> Por seguridad, deberás cambiar esta contraseña temporal al iniciar sesión.
                                </p>
                                <p style='color: #856404; margin: 0; font-size: 14px;'>
                                    <strong>Estado de cuenta:</strong> Reactivada ✓ | <strong>Intentos:</strong> Restablecidos a 4 ✓
                                </p>
                            </div>
                            
                            @if($cedula)
                            <p style='color: #333333; font-size: 16px; line-height: 1.6; margin: 20px 0;'>
                                Para acceder al sistema, utiliza tu cédula (<strong>{{ $cedula }}</strong>) y esta contraseña temporal.
                            </p>
                            @endif
                            
                            <!-- Botón de acción -->
                            <div style='text-align: center; margin: 30px 0;'>
                                <a href='{{ $loginUrl }}' style='display: inline-block; background: linear-gradient(135deg, #0A0A0A 0%, #333333 100%); color: #F2E19F; text-decoration: none; padding: 16px 45px; border-radius: 10px; font-size: 16px; font-weight: 600; box-shadow: 0 4px 15px rgba(10, 10, 10, 0.3);'>
                                    Iniciar Sesión en StyleNow
                                </a>
                            </div>
                            
                            <!-- Requisitos de contraseña -->
                            <div style='background-color: #F8F9FA; padding: 20px; margin: 25px 0; border-radius: 10px; border: 1px solid #E9ECEF;'>
                                <p style='color: #0A0A0A; margin: 0 0 15px 0; font-size: 15px; font-weight: 600;'>
                                    📋 Requisitos para tu nueva contraseña:
                                </p>
                                <table width='100%' cellpadding='5' cellspacing='0'>
                                    <tr>
                                        <td width='50%' style='padding: 5px;'>
                                            <div style='background: white; padding: 10px; border-radius: 6px; border: 1px solid #E9ECEF;'>
                                                <span style='color: #28a745;'>✓</span> 6-12 caracteres
                                            </div>
                                        </td>
                                        <td width='50%' style='padding: 5px;'>
                                            <div style='background: white; padding: 10px; border-radius: 6px; border: 1px solid #E9ECEF;'>
                                                <span style='color: #28a745;'>✓</span> 1 mayúscula
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width='50%' style='padding: 5px;'>
                                            <div style='background: white; padding: 10px; border-radius: 6px; border: 1px solid #E9ECEF;'>
                                                <span style='color: #28a745;'>✓</span> 1 minúscula
                                            </div>
                                        </td>
                                        <td width='50%' style='padding: 5px;'>
                                            <div style='background: white; padding: 10px; border-radius: 6px; border: 1px solid #E9ECEF;'>
                                                <span style='color: #28a745;'>✓</span> 1 número
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' style='padding: 5px;'>
                                            <div style='background: white; padding: 10px; border-radius: 6px; border: 1px solid #E9ECEF; text-align: center;'>
                                                <span style='color: #28a745;'>✓</span> 1 carácter especial (@#$%^&+=!.,;:)
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            
                            <p style='color: #666; font-size: 14px; line-height: 1.6; margin: 25px 0 0 0;'>
                                <strong>Nota:</strong> Si no solicitaste esta recuperación, por favor contacta inmediatamente al administrador de StyleNow.
                            </p>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style='background: linear-gradient(135deg, #0A0A0A 0%, #1A1A1A 100%); padding: 25px 30px; text-align: center;'>
                            <p style='color: #F2E19F; margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;'>
                                StyleNow - Sistema de Gestión de Belleza
                            </p>
                            <p style='color: #F2E19F; margin: 0; font-size: 12px; opacity: 0.7;'>
                                © {{ date('Y') }} StyleNow. Todos los derechos reservados.
                            </p>
                            <p style='color: #F2E19F; margin: 10px 0 0 0; font-size: 12px; opacity: 0.7;'>
                                Este es un correo automático, por favor no responder.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>