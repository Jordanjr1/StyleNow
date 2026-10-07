<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambio de Contraseña - StyleNow</title>
    
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=David+Libre:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --dorado: #FAD370;
            --negro: #000000;
            --blanco: #FFFFFF;
            --rojo: #dc3545;
            --verde: #28a745;
            --amarillo: #ffc107;
        }

        body {
            font-family: 'David Libre', serif;
            background-color: var(--negro);
            color: var(--blanco);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?w=1920') center/cover;
            filter: blur(8px) brightness(0.4);
            z-index: 0;
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            width: 90%;
            max-width: 1200px;
            min-height: 600px;
            background: rgba(0, 0, 0, 0.95);
            border: 2px solid var(--dorado);
            box-shadow: 0 20px 80px rgba(0, 0, 0, 0.8);
            position: relative;
            z-index: 10;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* LADO IZQUIERDO - INFORMACIÓN */
        .left-side {
            position: relative;
            overflow: hidden;
            background: url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=800') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8), rgba(250, 211, 112, 0.4));
            z-index: 1;
        }

        .info-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 400px;
        }

        .security-icon {
            font-size: 80px;
            margin-bottom: 2rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .info-title {
            font-family: 'Abril Fatface', cursive;
            font-size: 2.5rem;
            color: var(--dorado);
            margin-bottom: 1rem;
        }

        .info-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .requirements {
            text-align: left;
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: 10px;
            border-left: 4px solid var(--dorado);
        }

        .requirements-title {
            color: var(--dorado);
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.2rem;
        }

        .requirement-item {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .requirement-item.valid {
            color: var(--verde);
        }

        .requirement-item.invalid {
            color: rgba(255, 255, 255, 0.5);
        }

        /* LADO DERECHO - FORMULARIO */
        .right-side {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .logo-header {
            position: absolute;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 1rem;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .logo-header:hover {
            transform: translateX(-50%) translateY(-5px);
        }

        .logo-header img {
            height: 70px;
            width: auto;
        }

        .form-container {
            max-width: 400px;
            margin: 0 auto;
            width: 100%;
        }

        .form-title {
            font-family: 'Abril Fatface', cursive;
            font-size: 2.5rem;
            color: var(--blanco);
            margin-bottom: 0.5rem;
            text-align: center;
            margin-top: 4rem;
        }

        .form-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            text-align: center;
            margin-bottom: 3rem;
        }

        .user-info {
            background: rgba(250, 211, 112, 0.1);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid rgba(250, 211, 112, 0.3);
        }

        .user-info p {
            margin: 0.5rem 0;
            color: var(--dorado);
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            color: var(--blanco);
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(250, 211, 112, 0.3);
            color: var(--blanco);
            font-family: 'David Libre', serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--dorado);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 15px rgba(250, 211, 112, 0.2);
        }

        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .eye-icon {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            transition: opacity 0.3s ease;
            background: none;
            border: none;
            color: var(--dorado);
            font-size: 1.2rem;
            z-index: 2;
        }

        .validation-message {
            display: block;
            font-size: 0.85rem;
            margin-top: 0.5rem;
            min-height: 20px;
        }

        .valid {
            color: var(--verde);
        }

        .invalid {
            color: var(--rojo);
        }

        .warning {
            color: var(--amarillo);
        }

        .strength-meter {
            height: 5px;
            width: 0%;
            background: var(--rojo);
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }

        .strength-meter.weak { width: 25%; background: var(--rojo); }
        .strength-meter.medium { width: 50%; background: var(--amarillo); }
        .strength-meter.strong { width: 75%; background: #17a2b8; }
        .strength-meter.very-strong { width: 100%; background: var(--verde); }

        .btn-submit {
            width: 100%;
            padding: 1.2rem;
            background: linear-gradient(135deg, var(--dorado) 0%, #e5c864 100%);
            color: var(--negro);
            border: none;
            font-family: 'David Libre', serif;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(250, 211, 112, 0.4);
            position: relative;
            overflow: hidden;
            margin-top: 1rem;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(250, 211, 112, 0.6);
        }

        .btn-submit:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-submit:disabled:hover::before {
            left: -100%;
        }

        /* Alertas */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
            font-size: 0.95rem;
        }

        .alert.error {
            background: rgba(220, 38, 38, 0.15);
            border: 1px solid rgba(220, 38, 38, 0.4);
            color: #fca5a5;
        }

        .alert.success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.4);
            color: #86efac;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .container {
                grid-template-columns: 1fr;
                width: 95%;
            }

            .left-side {
                display: none;
            }

            .right-side {
                padding: 2rem;
            }

            .form-title {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .right-side {
                padding: 1.5rem;
            }

            .logo-header {
                top: 1rem;
                left: 1rem;
                transform: none;
            }

            .logo-header:hover {
                transform: translateY(-5px);
            }

            .form-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- LADO IZQUIERDO - INFORMACIÓN -->
        <div class="left-side">
            <div class="info-content">
                <div class="security-icon">🔐</div>
                <h2 class="info-title">Seguridad Primero</h2>
                <p class="info-text">Por tu seguridad, debes cambiar la contraseña temporal antes de continuar.</p>
                
                <div class="requirements">
                    <h3 class="requirements-title">Requisitos de la nueva contraseña:</h3>
                    <div class="requirement-item invalid" id="req-length">
                        <span>•</span> Entre 6 y 12 caracteres
                    </div>
                    <div class="requirement-item invalid" id="req-lowercase">
                        <span>•</span> Al menos una minúscula
                    </div>
                    <div class="requirement-item invalid" id="req-uppercase">
                        <span>•</span> Al menos una mayúscula
                    </div>
                    <div class="requirement-item invalid" id="req-number">
                        <span>•</span> Al menos un número
                    </div>
                    <div class="requirement-item invalid" id="req-special">
                        <span>•</span> Al menos un carácter especial
                    </div>
                </div>
            </div>
        </div>

        <!-- LADO DERECHO - FORMULARIO -->
        <div class="right-side">
            <a href="/" class="logo-header">
                <img src="{{ asset('images/logo_form.png') }}" alt="StyleNow Logo">
            </a>

            <div class="form-container">
                <h1 class="form-title">Cambio de Contraseña</h1>
                <p class="form-subtitle">Por seguridad, debes establecer una nueva contraseña</p>

                <!-- Información del usuario -->
                <div class="user-info">
                    <p><strong>Usuario:</strong> {{ Auth::user()->usr_nombre }} {{ Auth::user()->usr_apellido }}</p>
                    <p><strong>Cédula:</strong> {{ Auth::user()->usr_cedula }}</p>
                </div>

                <!-- Alertas de Laravel -->
                @if($errors->any())
                    <div class="alert error">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('cambiar.contrasena') }}" id="formCambioContrasena">
                    @csrf
                    
                    <!-- Contraseña actual (temporal) -->
                    <div class="form-group">
                        <label for="current_password">Contraseña Temporal Actual</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password" 
                                placeholder="Ingresa tu contraseña temporal"
                                required
                                readonly
                                value="********"
                            >
                        </div>
                    </div>

                    <!-- Nueva contraseña -->
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="new_password" 
                                name="new_password" 
                                placeholder="Crea tu nueva contraseña"
                                required
                            >
                            <button type="button" class="eye-icon" onclick="togglePassword('new_password', this)">
                                👁️
                            </button>
                        </div>
                        <div class="strength-meter" id="strength-meter"></div>
                        <span id="msgNewPassword" class="validation-message"></span>
                    </div>

                    <!-- Confirmar nueva contraseña -->
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="new_password_confirmation" 
                                name="new_password_confirmation" 
                                placeholder="Repite tu nueva contraseña"
                                required
                            >
                            <button type="button" class="eye-icon" onclick="togglePassword('new_password_confirmation', this)">
                                👁️
                            </button>
                        </div>
                        <span id="msgConfirmPassword" class="validation-message"></span>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmit" disabled>
                        Cambiar Contraseña
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Obtener elementos
        const newPassword = document.getElementById('new_password');
        const confirmPassword = document.getElementById('new_password_confirmation');
        const msgNewPassword = document.getElementById('msgNewPassword');
        const msgConfirmPassword = document.getElementById('msgConfirmPassword');
        const strengthMeter = document.getElementById('strength-meter');
        const btnSubmit = document.getElementById('btnSubmit');
        
        // Elementos de requisitos
        const reqLength = document.getElementById('req-length');
        const reqLowercase = document.getElementById('req-lowercase');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqNumber = document.getElementById('req-number');
        const reqSpecial = document.getElementById('req-special');

        // Función para mostrar/ocultar contraseña
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                button.textContent = '🙈';
            } else {
                input.type = 'password';
                button.textContent = '👁️';
            }
        }

        // Función para calcular fortaleza de contraseña
        function calcularFortaleza(password) {
            let score = 0;
            
            // Longitud
            if (password.length >= 6 && password.length <= 12) score += 1;
            
            // Diferentes tipos de caracteres
            if (/[a-z]/.test(password)) score += 1;
            if (/[A-Z]/.test(password)) score += 1;
            if (/[0-9]/.test(password)) score += 1;
            if (/[^A-Za-z0-9]/.test(password)) score += 1;
            
            // Bonus por longitud mayor a 8
            if (password.length > 8) score += 1;
            
            return Math.min(score, 5);
        }

        // Actualizar indicadores de requisitos
        function actualizarRequisitos(password) {
            // Longitud
            if (password.length >= 6 && password.length <= 12) {
                reqLength.classList.remove('invalid');
                reqLength.classList.add('valid');
            } else {
                reqLength.classList.remove('valid');
                reqLength.classList.add('invalid');
            }
            
            // Minúsculas
            if (/[a-z]/.test(password)) {
                reqLowercase.classList.remove('invalid');
                reqLowercase.classList.add('valid');
            } else {
                reqLowercase.classList.remove('valid');
                reqLowercase.classList.add('invalid');
            }
            
            // Mayúsculas
            if (/[A-Z]/.test(password)) {
                reqUppercase.classList.remove('invalid');
                reqUppercase.classList.add('valid');
            } else {
                reqUppercase.classList.remove('valid');
                reqUppercase.classList.add('invalid');
            }
            
            // Números
            if (/[0-9]/.test(password)) {
                reqNumber.classList.remove('invalid');
                reqNumber.classList.add('valid');
            } else {
                reqNumber.classList.remove('valid');
                reqNumber.classList.add('invalid');
            }
            
            // Caracteres especiales
            if (/[^A-Za-z0-9]/.test(password)) {
                reqSpecial.classList.remove('invalid');
                reqSpecial.classList.add('valid');
            } else {
                reqSpecial.classList.remove('valid');
                reqSpecial.classList.add('invalid');
            }
        }

        // Validación de contraseña
        function validarPasswordValor(valor) {
            const errores = [];
            
            if (valor.length < 6 || valor.length > 12) {
                errores.push("Debe tener entre 6 y 12 caracteres");
            }
            if (!/[a-z]/.test(valor)) {
                errores.push("Debe contener al menos una minúscula");
            }
            if (!/[A-Z]/.test(valor)) {
                errores.push("Debe contener al menos una mayúscula");
            }
            if (!/[0-9]/.test(valor)) {
                errores.push("Debe contener al menos un número");
            }
            if (!/[^A-Za-z0-9]/.test(valor)) {
                errores.push("Debe contener al menos un carácter especial (@#$%^&+=!.,;:)");
            }
            
            return errores;
        }

        // Validar confirmación de contraseña
        function validarConfirmacion() {
            const confirmValue = confirmPassword.value;
            const newValue = newPassword.value;
            
            if (confirmValue.length === 0) {
                msgConfirmPassword.textContent = '';
                return false;
            }
            
            if (confirmValue === newValue) {
                msgConfirmPassword.textContent = '✓ Las contraseñas coinciden';
                msgConfirmPassword.className = 'validation-message valid';
                return true;
            } else {
                msgConfirmPassword.textContent = '✗ Las contraseñas no coinciden';
                msgConfirmPassword.className = 'validation-message invalid';
                return false;
            }
        }

        // Habilitar/deshabilitar botón de envío
        function actualizarBoton() {
            const errores = validarPasswordValor(newPassword.value);
            const confirmOk = validarConfirmacion();
            
            if (errores.length === 0 && confirmOk && newPassword.value.length > 0) {
                btnSubmit.disabled = false;
                msgNewPassword.textContent = '✓ Contraseña válida';
                msgNewPassword.className = 'validation-message valid';
            } else {
                btnSubmit.disabled = true;
            }
        }

        // Event listener para nueva contraseña
        newPassword.addEventListener('input', function() {
            const valor = this.value;
            
            // Actualizar requisitos visuales
            actualizarRequisitos(valor);
            
            // Calcular y mostrar fortaleza
            const fortaleza = calcularFortaleza(valor);
            strengthMeter.className = 'strength-meter';
            
            if (valor.length > 0) {
                if (fortaleza <= 2) {
                    strengthMeter.classList.add('weak');
                    msgNewPassword.className = 'validation-message invalid';
                } else if (fortaleza <= 3) {
                    strengthMeter.classList.add('medium');
                    msgNewPassword.className = 'validation-message warning';
                } else if (fortaleza <= 4) {
                    strengthMeter.classList.add('strong');
                    msgNewPassword.className = 'validation-message warning';
                } else {
                    strengthMeter.classList.add('very-strong');
                }
            } else {
                strengthMeter.className = 'strength-meter';
            }
            
            // Mostrar errores si los hay
            const errores = validarPasswordValor(valor);
            if (errores.length > 0 && valor.length > 0) {
                msgNewPassword.textContent = errores[0];
                msgNewPassword.className = 'validation-message invalid';
            } else if (valor.length > 0) {
                msgNewPassword.textContent = '';
            } else {
                msgNewPassword.textContent = '';
            }
            
            // Actualizar confirmación
            validarConfirmacion();
            actualizarBoton();
        });

        // Event listener para confirmación
        confirmPassword.addEventListener('input', function() {
            validarConfirmacion();
            actualizarBoton();
        });

        // Manejar envío del formulario con AJAX
        document.getElementById('formCambioContrasena').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Validar final antes de enviar
            const errores = validarPasswordValor(newPassword.value);
            if (errores.length > 0 || !validarConfirmacion()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: 'Por favor corrige los errores en el formulario',
                    confirmButtonColor: '#FAD370'
                });
                return;
            }
            
            // Mostrar loading
            const btnSubmit = document.getElementById('btnSubmit');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Cambiando contraseña...';
            btnSubmit.disabled = true;
            
            try {
                // Obtener CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                
                // Preparar datos
                const formData = new FormData(this);
                
                // Enviar solicitud AJAX
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Éxito - mostrar mensaje y redirigir
                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña cambiada!',
                        html: `
                            <div style="text-align: center;">
                                <p>${data.message}</p>
                                <p style="font-size: 14px; color: #666; margin-top: 10px;">
                                    Serás redirigido automáticamente...
                                </p>
                            </div>
                        `,
                        confirmButtonColor: '#FAD370',
                        timer: 3000,
                        timerProgressBar: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    });
                    
                    // Redirección automática después de 3 segundos
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }, 3000);
                    
                } else {
                    // Error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Error al cambiar la contraseña',
                        confirmButtonColor: '#FAD370'
                    });
                    
                    // Restaurar botón
                    btnSubmit.innerHTML = originalText;
                    btnSubmit.disabled = false;
                }
                
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error de conexión',
                    text: 'No se pudo conectar con el servidor',
                    confirmButtonColor: '#FAD370'
                });
                
                // Restaurar botón
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        });

        // Prevenir navegación si no ha cambiado la contraseña
        window.addEventListener('beforeunload', function(e) {
            if (btnSubmit.disabled === false && newPassword.value.length > 0) {
                e.preventDefault();
                e.returnValue = '¿Estás seguro de que quieres salir? Los cambios no se guardarán.';
            }
        });

        // Bloquear tecla F5 y navegación atrás
        document.addEventListener('keydown', function(e) {
            if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Recarga bloqueada',
                    text: 'Debes completar el cambio de contraseña primero',
                    confirmButtonColor: '#FAD370'
                });
            }
        });

        // Bloquear navegación atrás
        history.pushState(null, null, location.href);
        window.onpopstate = function() {
            history.go(1);
            Swal.fire({
                icon: 'warning',
                title: 'Navegación bloqueada',
                text: 'Debes completar el cambio de contraseña primero',
                confirmButtonColor: '#FAD370'
            });
        };
    </script>
</body>
</html>