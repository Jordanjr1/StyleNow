<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Cliente; // <--- IMPORTANTE: Importar el modelo Cliente
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Helpers\PasswordHelper;
use App\Mail\RecuperacionPassword;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuario
        $usuario = Usuario::where('usr_email', $request->email)->first();

        // Si el usuario no existe
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 401);
        }

        // Verificar si el usuario está bloqueado (estado = 'I')
        if ($usuario->usr_estado === 'I') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario bloqueado por intentos fallidos.',
                'bloqueado' => true
            ], 401);
        }

        // MÉTODO EXACTO: Mismo cálculo que en registro
        $password_encoded = base64_encode(hash('sha256', $request->password, true));
        
        // Comparación EXACTA
        if ($password_encoded === $usuario->usr_password) {
            // Login exitoso
            
            // RESETEAR intentos a 4 si no estaban ya en 4
            if ($usuario->usr_intentos < 4) {
                $usuario->update([
                    'usr_intentos' => 4
                ]);
            }
            
            Auth::login($usuario, $request->has('remember'));
            $request->session()->regenerate();
            
            if ($usuario->requiere_reset == 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Debes cambiar tu contraseña temporal antes de continuar',
                    'redirect' => route('cambio.contrasena')
                ]);
            }
    
            // Redirigir según rol
            return response()->json([
                'success' => true,
                'message' => '¡Bienvenido ' . $usuario->usr_nombre . '!',
                'redirect' => $this->getRedirectUrlByRole($usuario->usr_rolId)
            ]);
        }
        
        // CONTRASEÑA INCORRECTA - Manejar intentos fallidos
        $intentosRestantes = $usuario->usr_intentos - 1;
        
        // Actualizar intentos
        $usuario->update([
            'usr_intentos' => $intentosRestantes
        ]);
        
        // Verificar si se bloqueará el usuario
        if ($intentosRestantes <= 0) {
            $usuario->update([
                'usr_estado' => 'I',
                'usr_intentos' => 0
            ]);
            
            // Para AJAX response
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario bloqueado por intentos fallidos. Contacte al administrador.',
                    'bloqueado' => true,
                    'intentos_restantes' => 0
                ], 401);
            }
            
            return back()->withErrors([
                'email' => 'Usuario bloqueado por intentos fallidos. Contacte al administrador.',
            ]);
        }
        
        // Para AJAX response (intentos restantes > 0)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Contraseña incorrecta.',
                'intentos_restantes' => $intentosRestantes,
                'bloqueado' => false
            ], 401);
        }
        
        return back()->withErrors([
            'email' => 'Contraseña incorrecta. Intentos restantes: ' . $intentosRestantes,
        ]);
    }

    public function mostrarCambio()
    {
        // Verificar si el usuario requiere cambio de contraseña
        if (!Auth::check() || Auth::user()->requiere_reset != 1) {
            return redirect('/');
        }
        
        return view('auth.cambio-contrasena');
    }
    
    // Procesar cambio de contraseña
    public function cambiar(Request $request)
    {
        // Validar que el usuario esté autenticado y requiera reset
        if (!Auth::check() || Auth::user()->requiere_reset != 1) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción'
            ], 403);
        }
        
        // Validar datos
        $validator = Validator::make($request->all(), [
            'new_password' => [
                'required',
                'string',
                'min:6',
                'max:12',
                'regex:/[a-z]/',      // al menos una minúscula
                'regex:/[A-Z]/',      // al menos una mayúscula
                'regex:/[0-9]/',      // al menos un número
                'regex:/[@#$%^&+=!.,;:]/', // al menos un carácter especial
                'confirmed'
            ]
        ], [
            'new_password.required' => 'La nueva contraseña es obligatoria',
            'new_password.min' => 'La contraseña debe tener al menos 6 caracteres',
            'new_password.max' => 'La contraseña no debe exceder 12 caracteres',
            'new_password.regex' => 'La contraseña debe contener al menos una letra minúscula, una mayúscula, un número y un carácter especial',
            'new_password.confirmed' => 'Las contraseñas no coinciden'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }
        
        try {
            $usuario = Auth::user();
            
            // Encriptar nueva contraseña (mismo método que registro)
            $password_encoded = base64_encode(hash('sha256', $request->new_password, true));
            
            // Actualizar usuario
            DB::table('tbl_usuario')
                ->where('usr_id', $usuario->usr_id)
                ->update([
                    'usr_password' => $password_encoded,
                    'requiere_reset' => 0,
                    'usr_intentos' => 4,
                    'updated_at' => now()
                ]);
            
            // Obtener URL de redirección según rol
            $redirectUrl = $this->getRedirectUrlByRole($usuario->usr_rolId);
            
            return response()->json([
                'success' => true,
                'message' => '¡Contraseña cambiada exitosamente! Ahora puedes acceder al sistema.',
                'redirect' => $redirectUrl
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar la contraseña: ' . $e->getMessage()
            ], 500);
        }
    }

    
    // Método auxiliar para obtener URL de redirección por rol (para AJAX)
    private function getRedirectUrlByRole($rolId)
    {
        switch ($rolId) {
            case 1: // Admin
                return '/admin/dashboard';
            case 2: // Empleado
                return '/empleado/dashboard';
            case 3: // Cliente
                return '/cliente/dashboard';
            default:
                return '/';
        }
    }

    // Método para redirección normal (mantener tu método existente)
    private function redirectByRole($rolId)
    {
        switch ($rolId) {
            case 1: // Admin
                return redirect('/admin/dashboard');
            case 2: // Empleado
                return redirect('/empleado/dashboard');
            case 3: // Cliente
                return redirect('/cliente/dashboard');
            default:
                return redirect('/');
        }
    }

    private function generarPasswordTemporal()
    {
        $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%^&+=!.,;:';
        
        do {
            // Generar password aleatorio de 10 caracteres
            $password = '';
            for ($i = 0; $i < 10; $i++) {
                $password .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }
            
            // Verificar que cumpla todas las reglas
            $valida = preg_match('/[A-Z]/', $password) &&  // Al menos una mayúscula
                      preg_match('/[a-z]/', $password) &&  // Al menos una minúscula
                      preg_match('/[0-9]/', $password) &&  // Al menos un número
                      preg_match('/[@#$%^&+=!.,;:]/', $password) && // Al menos un carácter especial
                      strlen($password) >= 6 && strlen($password) <= 12;
            
        } while (!$valida);
        
        return $password;
    }
    
    // Enviar correo de recuperación
    public function enviarRecuperacion(Request $request)
    {
        try {
            $request->validate([
                'cedula' => 'required|string|max:10'
            ]);
            
            // Buscar usuario por cédula
            $usuario = Usuario::where('usr_cedula', $request->cedula)->first();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún usuario con esta cédula.'
                ], 404);
            }
            
            // Verificar si el usuario tiene email
            if (empty($usuario->usr_email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'El usuario no tiene un correo electrónico registrado.'
                ], 400);
            }
            
            // Generar contraseña temporal
            $passwordTemporal = $this->generarPasswordTemporal();
            
            // Encriptar la contraseña temporal
            $password_encoded = base64_encode(hash('sha256', $passwordTemporal, true));
            
            // Actualizar usuario
            $usuario->update([
                'usr_password' => $password_encoded,
                'usr_estado' => 'A', // Activar cuenta
                'usr_intentos' => 4, // Resetear intentos
                'requiere_reset' => 1 // Forzar cambio de contraseña
            ]);
            
            // Enviar correo
            $this->enviarCorreoRecuperacion($usuario, $passwordTemporal);
            
            return response()->json([
                'success' => true,
                'message' => 'Se ha enviado una contraseña temporal a tu correo electrónico. Revisa tu bandeja de entrada.',
                'email' => $this->ocultarEmail($usuario->usr_email),
                'usuario' => [
                    'nombre' => $usuario->usr_nombre,
                    'apellido' => $usuario->usr_apellido
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Ocultar parte del email para privacidad
    private function ocultarEmail($email)
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;
        
        $username = $parts[0];
        $domain = $parts[1];
        
        if (strlen($username) <= 2) {
            $oculto = substr($username, 0, 1) . '***';
        } else {
            $oculto = substr($username, 0, 2) . '***' . substr($username, -1);
        }
        
        return $oculto . '@' . $domain;
    }
    
    // Enviar correo HTML
    private function enviarCorreoRecuperacion($usuario, $passwordTemporal)
    {
        $primer_nombre = explode(' ', $usuario->usr_nombre)[0] ?? '';
        $primer_apellido = explode(' ', $usuario->usr_apellido)[0] ?? '';
        $nombre_usuario = trim("$primer_nombre $primer_apellido") ?: 'Usuario';
        
        $appUrl = config('app.url', 'http://localhost');
        $loginUrl = $appUrl . '/login';

        try {
            Mail::to($usuario->usr_email)->send(
                new RecuperacionPassword($nombre_usuario, $passwordTemporal, $loginUrl, $usuario->usr_cedula)
            );
        } catch (\Exception $e) {
            Log::error('Error al enviar correo: ' . $e->getMessage());
            throw $e;
        }
    }

    // Mostrar formulario de registro
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Procesar registro CON SHA256 y Creación de Cliente Automática
    public function register(Request $request)
    {
        // Validaciones básicas
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'cedula' => 'required|string|max:10',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Iniciamos transacción para asegurar que se crean ambos (usuario y cliente) o ninguno
        DB::beginTransaction();

        try {
            // Validaciones de unicidad manuales para mensajes específicos
            $errores = [];
            
            // Validar combinación nombre + apellido
            $nombreCompletoExists = Usuario::where('usr_nombre', $request->nombre)
                ->where('usr_apellido', $request->apellido)
                ->exists();
                
            if ($nombreCompletoExists) {
                $errores['nombre_completo'] = 'La combinación de nombres y apellidos ya está registrada.';
            }
            
            // Validar cédula
            if (Usuario::where('usr_cedula', $request->cedula)->exists()) {
                $errores['cedula'] = 'La cédula ya está registrada.';
            }
            
            // Validar email
            if (Usuario::where('usr_email', $request->email)->exists()) {
                $errores['email'] = 'El correo electrónico ya está registrado.';
            }
            
            // Validar teléfono (si se proporciona)
            if ($request->telefono && Usuario::where('usr_telefono', $request->telefono)->exists()) {
                $errores['telefono'] = 'El número de teléfono ya está registrado.';
            }

            // Validar contraseña
            $password_encoded = base64_encode(hash('sha256', $request->password, true));
            if (Usuario::where('usr_password', $password_encoded)->exists()) {
                $errores['password'] = 'La contraseña ya está en uso. Por favor, elige otra.';
            }
            
            // Si hay errores de unicidad, devolverlos
            if (!empty($errores)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $errores
                ], 422);
            }
            
            // Crear el usuario
            $usuario = Usuario::create([
                'usr_nombre' => $request->nombre,
                'usr_apellido' => $request->apellido,
                'usr_cedula' => $request->cedula,
                'usr_email' => $request->email,
                'usr_telefono' => $request->telefono,
                'usr_password' => $password_encoded,
                'usr_rolId' => 3, // Cliente por defecto
                'usr_estado' => 'A',
                'usr_fechaRegistro' => now(),
            ]);

            // --- LÓGICA DE CREACIÓN AUTOMÁTICA DE CLIENTE ---
            // Si el rol es 3 (Cliente), creamos el registro en tbl_cliente
            if ($usuario->usr_rolId == 3) {
                Cliente::create([
                    'cli_usuarioId' => $usuario->usr_id,
                    'cli_puntosFidelizacion' => 0
                ]);
            }

            // Confirmar transacción
            DB::commit();

            // Éxito - redirigir con mensaje
            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso! Ahora puedes iniciar sesión.',
                'redirect' => route('login')
            ]);

        } catch (\Exception $e) {
            // Si algo falla, revertimos los cambios en la BD
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verificarDisponibilidad(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:cedula,email,telefono,nombre_completo',
            'valor' => 'required|string'
        ]);
        
        $tipo = $request->tipo;
        $valor = $request->valor;
        
        switch ($tipo) {
            case 'cedula':
                $existe = Usuario::where('usr_cedula', $valor)->exists();
                $mensaje = $existe ? 'La cédula ya está registrada' : 'Cédula disponible';
                break;
                
            case 'email':
                $existe = Usuario::where('usr_email', $valor)->exists();
                $mensaje = $existe ? 'El email ya está registrado' : 'Email disponible';
                break;
                
            case 'telefono':
                $existe = Usuario::where('usr_telefono', $valor)->exists();
                $mensaje = $existe ? 'El teléfono ya está registrado' : 'Teléfono disponible';
                break;
                
            case 'nombre_completo':
                $nombres = explode(' ', $valor);
                if (count($nombres) >= 2) {
                    $existe = Usuario::where('usr_nombre', $nombres[0])
                        ->where('usr_apellido', $nombres[1])
                        ->exists();
                    $mensaje = $existe ? 'Nombre y apellido ya registrados' : 'Nombre disponible';
                } else {
                    $existe = false;
                    $mensaje = 'Formato incorrecto';
                }
                break;

            // verificar contraseña
             case 'password':
                $existe = Usuario::where('usr_password', base64_encode(hash('sha256', $valor, true)))->exists();
                $mensaje = $existe ? 'La contraseña ya está en uso' : 'Contraseña disponible';
                break; 
                
            default:
                $existe = false;
                $mensaje = 'Tipo no válido';
        }
        
        return response()->json([
            'disponible' => !$existe,
            'mensaje' => $mensaje
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // Redirigir con headers anti-caché
        return redirect('/')
            ->with('success', 'Sesión cerrada correctamente')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    //Api para login
    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $usuario = Usuario::where('usr_email', $request->email)->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado.'
            ], 401);
        }

        // Mismo cálculo SHA256 + base64
        $password_encoded = base64_encode(hash('sha256', $request->password, true));
        
        if ($password_encoded === $usuario->usr_password) {
            // Crear token para API
            $token = $usuario->createToken('api-token')->plainTextToken;
            
            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'user' => [
                    'usr_id' => $usuario->usr_id,
                    'usr_nombre' => $usuario->usr_nombre,
                    'usr_apellido' => $usuario->usr_apellido,
                    'usr_cedula' => $usuario->usr_cedula,
                    'usr_email' => $usuario->usr_email,
                    'usr_telefono' => $usuario->usr_telefono,
                    'usr_rolId' => $usuario->usr_rolId,
                    'usr_estado' => $usuario->usr_estado
                ],
                'token' => $token
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Contraseña incorrecta.'
        ], 401);
    }

    // API para obtener usuario actual
    public function apiUser(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'user' => [
                'usr_id' => $user->usr_id,
                'usr_nombre' => $user->usr_nombre,
                'usr_apellido' => $user->usr_apellido,
                'usr_cedula' => $user->usr_cedula,
                'usr_email' => $user->usr_email,
                'usr_telefono' => $user->usr_telefono,
                'usr_rolId' => $user->usr_rolId,
                'usr_estado' => $user->usr_estado
            ]
        ]);
    }      
}