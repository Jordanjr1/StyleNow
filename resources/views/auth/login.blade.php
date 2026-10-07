<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - StyleNow</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=David+Libre:wght@400;500;700&display=swap"
        rel="stylesheet">

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

        /* LADO IZQUIERDO - IMAGEN */
        .left-side {
            position: relative;
            overflow: hidden;
            background: url('https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=800') center/cover;
        }

        .left-side::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(250, 211, 112, 0.3));
            z-index: 1;
        }

        .image-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            text-align: center;
            padding: 2rem;
        }

        .barber-image {
            width: 100%;
            max-width: 350px;
            border: 3px solid var(--dorado);
            box-shadow: 0 10px 40px rgba(250, 211, 112, 0.4);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-15px);
            }
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
            font-size: 3rem;
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

        .form-group {
            margin-bottom: 1.5rem;
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
            top: 35%;
            transform: translateY(-50%);
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .eye-icon img {
            width: 20px;
            height: 20px;
            display: block;
        }

        .eye-icon:hover {
            opacity: 0.7;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--dorado);
        }

        .checkbox-wrapper label {
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            margin: 0;
        }

        .forgot-link {
            color: var(--dorado);
            text-decoration: none;
            transition: opacity 0.3s ease;
        }

        .forgot-link:hover {
            opacity: 0.7;
            text-decoration: underline;
        }

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

        .register-link {
            text-align: center;
            margin-top: 2rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .register-link a {
            color: var(--dorado);
            text-decoration: none;
            font-weight: 700;
            transition: opacity 0.3s ease;
        }

        .register-link a:hover {
            opacity: 0.7;
            text-decoration: underline;
        }

        /* RESPONSIVE */
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
                font-size: 2.5rem;
            }
        }

        @media (max-width: 480px) {
            .right-side {
                padding: 1.5rem;
            }

            .logo-header {
                top: 1rem;
                left: 1rem;
            }

            .form-title {
                font-size: 2rem;
            }
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
    </style>

    <style>
        /* Estilos para las alertas de intentos */
        .intento-alerta {
            animation: pulse-warning 2s infinite;
        }

        @keyframes pulse-warning {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }

        .intento-critico {
            animation: pulse-danger 1s infinite;
        }

        @keyframes pulse-danger {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        /* Estilos para SweetAlert personalizado */
        .swal2-popup .swal2-title {
            color: #333;
        }

        .swal2-popup .swal2-html-container {
            line-height: 1.6;
        }

        /* Estilos para el formulario cuando hay errores */
        .form-group.error .input-wrapper {
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Estilos para SweetAlert personalizado */
        .swal2-popup {
            border-radius: 15px !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
        }

        .swal2-title {
            color: #144987 !important;
            font-size: 1.5rem !important;
            font-weight: 600 !important;
        }

        .swal2-html-container {
            text-align: left !important;
            font-size: 1rem !important;
            color: #333 !important;
        }

        .swal2-confirm {
            border-radius: 8px !important;
            padding: 10px 30px !important;
            font-weight: 600 !important;
        }

        .swal2-error {
            border-color: #dc3545 !important;
        }

        .swal2-success {
            border-color: #28a745 !important;
        }

        /* Estilos para inputs con error */
        .error-input {
            border: 2px solid #dc3545 !important;
            animation: shake 0.5s;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        /* Mensajes de validación */
        .validation-message {
            font-size: 0.85rem;
            margin-top: 0.25rem;
            display: block;
            min-height: 20px;
        }

        .valid {
            color: #28a745;
        }

        .invalid {
            color: #dc3545;
        }
    </style>

    <style>
        /* Estilos para el modal */
        .modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #btnRecuperar:disabled {
            background: #cccccc !important;
            cursor: not-allowed;
        }

        /* Animaciones para mensajes */
        .success-message {
            animation: fadeIn 0.5s;
        }

        .error-message {
            animation: shake 0.5s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- LADO IZQUIERDO - IMAGEN -->
        <div class="left-side">
            <div class="image-content">
                <img src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=400" alt="Barbería"
                    class="barber-image">
            </div>
        </div>

        <!-- LADO DERECHO - FORMULARIO -->
        <div class="right-side">
            <a href="{{ route('inicio') }}" class="logo-header">
                <!-- Cambia según tu estructura -->
                <img src="{{ asset('images/logo_form.png') }}" alt="StyleNow Logo">
            </a>

            <div class="form-container">
                <h1 class="form-title">Inicio de Sesión</h1>
                <p class="form-subtitle">Por favor, ingrese sus credenciales</p>

                <!-- Alertas de Laravel -->
                @if ($errors->any())
                    <div class="alert error">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert success">
                        {{ session('success') }}
                    </div>
                @endif


                <form method="POST" action="{{ route('login') }}" id="loginForm">
                    @csrf

                    <div class="form-group">
                        <label for="email">Correo</label>
                        <input type="email" id="email" name="email" placeholder="Ingresa tu correo"
                            value="{{ old('email') }}" required>
                        <span id="msgEmail" class="validation-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" placeholder="• • • • •" required>
                            <span class="eye-icon" onclick="togglePassword('password')">
                                <img src="/images/icons/ojo-cerrado.png" alt="Mostrar contraseña" />
                            </span>
                            <span id="msgPassword" class="validation-message"></span>
                        </div>
                    </div>

                    <div class="remember-forgot">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Recordar contraseña</label>
                        </div>
                        <a href="javascript:void(0)" onclick="abrirModalRecuperacion()" class="forgot-link">¿Olvidaste
                            tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmit">Iniciar Sesión</button>
                </form>

                <div class="register-link">
                    ¿No tiene una cuenta? <a href="{{ route('registro') }}">Regístrese aquí</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Recuperación de Contraseña -->
    <div id="recuperacionModal" class="modal"
        style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
        <div class="modal-content"
            style="background-color: #fff; margin: 5% auto; padding: 0; width: 90%; max-width: 500px; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); overflow: hidden;">

            <!-- Header del Modal -->
            <div
                style="background: linear-gradient(135deg, #434447 0%, #060606 100%); padding: 25px 30px; color: #FAD370; align: center; position: relative;">
                <button onclick="cerrarModal()"
                    style="background: none; border: none; color: white; font-size: 20px; position: absolute; right: 20px; top: 20px; cursor: pointer;">&times;</button>
                <h2 style="margin: 0; font-size: 24px; font-weight: 600;">🔐 Recuperar Contraseña</h2>
                <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 14px;">Ingresa tu número de cédula para recibir
                    una contraseña temporal</p>
            </div>

            <!-- Contenido del Modal -->
            <div style="padding: 30px;">
                <div class="form-group">
                    <label for="cedula_recuperacion"
                        style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Número de
                        Cédula</label>
                    <input type="text" id="cedula_recuperacion" placeholder="Ingresa tu número de cédula"
                        maxlength="10"
                        style="width: 100%; color: black; padding: 12px 15px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px; transition: border-color 0.3s;">
                    <span id="msgCedulaRecuperacion"
                        style="display: block; margin-top: 5px; font-size: 14px; min-height: 20px;"></span>
                </div>

                <div id="infoSeguridad"
                    style="background-color: #f8f9fa; border-left: 4px solid #144987; padding: 15px; margin: 20px 0; border-radius: 8px; display: none;">
                    <p style="margin: 0 0 10px 0; color: #144987; font-weight: 600;">📋 Importante:</p>
                    <ul style="margin: 0; padding-left: 20px; color: #666; font-size: 14px;">
                        <li>Recibirás una contraseña temporal en tu correo registrado</li>
                        <li>Debes cambiar la contraseña en tu primer inicio de sesión</li>
                        <li>Tu cuenta será reactivada automáticamente</li>
                    </ul>
                </div>

                <button id="btnRecuperar" onclick="enviarRecuperacion()"
                    style="width: 100%; padding: 15px; background: linear-gradient(135deg, #e6a502 0%, #FAD370 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: transform 0.2s;">
                    Enviar Contraseña Temporal
                </button>

                <div id="resultadoRecuperacion" style="margin-top: 20px; display: none;"></div>
            </div>

            <!-- Footer del Modal -->
            <div
                style="background-color: #f8f9fa; padding: 20px 30px; border-top: 1px solid #e0e0e0; text-align: center;">
                <p style="margin: 0; color: #666; font-size: 14px;">¿No recibes el correo? Verifica tu carpeta de spam o
                    contacta al administrador.</p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = passwordInput.nextElementSibling.querySelector("img");

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.src = "/images/icons/ojo-abierto.png";
                eyeIcon.alt = "Ocultar contraseña";
            } else {
                passwordInput.type = 'password';
                eyeIcon.src = "/images/icons/ojo-cerrado.png";
                eyeIcon.alt = "Mostrar contraseña";
            }
        }

        function validarPasswordValor(valor) {
            if (valor.length === 0) return ""; // No mostrar error si está vacío

            if (valor.length < 6 || valor.length > 12)
                return "La contraseña debe tener entre 6 y 12 caracteres.";

            if (!/[a-z]/.test(valor))
                return "Debe contener al menos una minúscula.";

            if (!/[A-Z]/.test(valor))
                return "Debe contener al menos una mayúscula.";

            if (!/[0-9]/.test(valor))
                return "Debe contener al menos un número.";

            if (!/[^A-Za-z0-9]/.test(valor))
                return "Debe contener un carácter especial.";

            return ""; // contraseña válida
        }

        function validarEmail(valor) {
            if (valor.length === 0)
                return "El correo es obligatorio.";

            // Validación general
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!regex.test(valor))
                return "Formato de correo no válido.";

            // Dominios permitidos
            const dominios = [
                "gmail.com", "outlook.com", "hotmail.com",
                "yahoo.com", "live.com", "icloud.com"
            ];

            let dominio = valor.split("@")[1];
            if (!dominio) return "Formato de correo no válido.";

            dominio = dominio.toLowerCase();

            if (!dominios.includes(dominio))
                return "Dominio no permitido.";

            return ""; // Válido
        }

        const email = document.getElementById("email");
        const msgEmail = document.getElementById("msgEmail");

        const password = document.getElementById("password");
        const msgPassword = document.getElementById("msgPassword");

        const btnSubmit = document.getElementById("btnSubmit");

        // Validación de email
        email.addEventListener("input", function() {
            let error = validarEmail(this.value);
            msgEmail.textContent = error || "✓ Correo válido";
            msgEmail.style.color = error ? "#dc3545" : "#28a745";
        });

        // Validación de contraseña
        password.addEventListener("input", function() {
            let error = validarPasswordValor(this.value);
            msgPassword.textContent = error;
            msgPassword.style.color = error ? "#dc3545" : "#28a745";
            if (!error && this.value.length > 0) {
                msgPassword.textContent = "✓ Contraseña válida";
            }
        });

        if (typeof Swal === 'undefined') {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            document.head.appendChild(script);
        }

        // Asegurar que el meta tag CSRF existe
        function getCsrfToken() {
            // Buscar el meta tag CSRF
            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            if (csrfMeta) {
                return csrfMeta.content;
            }

            // Buscar input hidden CSRF
            const csrfInput = document.querySelector('input[name="_token"]');
            if (csrfInput) {
                return csrfInput.value;
            }

            console.error('CSRF token no encontrado');
            return '';
        }

        // Modifica el event listener del submit para usar AJAX
        document.getElementById("loginForm").addEventListener("submit", async function(e) {
            e.preventDefault(); // Prevenir envío normal

            // Validar campos básicos
            const errorEmail = validarEmail(email.value);
            const errorPassword = validarPasswordValor(password.value);

            // Solo mostrar error si el campo tiene contenido
            if (email.value.length > 0 && errorEmail) {
                Swal.fire({
                    icon: "error",
                    title: "Correo inválido",
                    text: errorEmail,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: "#dc3545"
                });
                return false;
            }

            if (password.value.length > 0 && errorPassword) {
                Swal.fire({
                    icon: "error",
                    title: "Contraseña inválida",
                    text: errorPassword,
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: "#dc3545"
                });
                return false;
            }

            // Validar que ambos campos no estén vacíos
            if (email.value.length === 0 || password.value.length === 0) {
                Swal.fire({
                    icon: "warning",
                    title: "Campos requeridos",
                    text: "Por favor complete todos los campos obligatorios.",
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: "#ffc107"
                });
                return false;
            }

            // Mostrar loading
            const btnSubmit = document.getElementById('btnSubmit');
            const originalText = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Iniciando sesión...';
            btnSubmit.disabled = true;

            try {
                // Obtener CSRF token
                const csrfToken = getCsrfToken();
                if (!csrfToken) {
                    throw new Error('Token de seguridad no encontrado');
                }

                // Preparar datos del formulario
                const formData = new FormData(this);

                // Agregar remember si existe
                const rememberCheckbox = document.getElementById('remember');
                if (rememberCheckbox) {
                    formData.append('remember', rememberCheckbox.checked ? '1' : '0');
                }

                // Enviar datos por AJAX
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
                    // ÉXITO: Login correcto
                    Swal.fire({
                        icon: "success",
                        title: "¡Bienvenido!",
                        text: data.message,
                        confirmButtonText: "Continuar",
                        confirmButtonColor: "#28a745",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        timer: 1500,
                        timerProgressBar: true,
                        showClass: {
                            popup: 'animate__animated animate__fadeInDown'
                        }
                    }).then(() => {
                        // Redirigir a la página correspondiente
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    });

                    const loginEvent = new CustomEvent('loginExitoso', {
                        detail: {
                            requiere_reset: data.requiere_reset || false,
                            usuario: data.usuario
                        }
                    });
                    document.dispatchEvent(loginEvent);

                    // Si no requiere reset, redirigir normalmente
                    if (!data.requiere_reset) {
                        Swal.fire({
                            icon: "success",
                            title: "¡Bienvenido!",
                            text: data.message,
                            confirmButtonText: "Continuar",
                            confirmButtonColor: "#28a745",
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            timer: 1500,
                            timerProgressBar: true,
                            showClass: {
                                popup: 'animate__animated animate__fadeInDown'
                            }
                        }).then(() => {
                            window.location.href = data.redirect;
                        });
                    } else {
                        // Si requiere reset, mostrar mensaje especial
                        Swal.fire({
                            icon: "info",
                            title: "¡Bienvenido!",
                            html: `
                <div style="text-align: center;">
                    <p>${data.message}</p>
                    <div style="margin: 20px 0; padding: 15px; background-color: #fff3cd; border-radius: 10px;">
                        <p style="margin: 0; color: #856404;">
                            <strong>🔒 Seguridad:</strong> Debes cambiar tu contraseña temporal para continuar.
                        </p>
                    </div>
                    <p>Redirigiendo al cambio de contraseña...</p>
                </div>
            `,
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        });
                    }

                } else {
                    // ERROR: Manejar diferentes tipos de error

                    // Usuario bloqueado
                    if (data.bloqueado) {
                        Swal.fire({
                            icon: "error",
                            title: "Usuario Bloqueado",
                            html: `
                                <div style="text-align: left;">
                                    <p><strong>${data.message}</strong></p>
                                    <hr>
                                    <p><strong>¿Qué puede hacer?</strong></p>
                                    <ol style="margin-left: 20px;">
                                        <li>Utilice la opción de recuperación de cuenta</li>
                                    </ol>
                                </div>
                            `,
                            confirmButtonText: "Entendido",
                            confirmButtonColor: "#dc3545",
                            showDenyButton: true,
                            denyButtonText: "Recuperar Cuenta",
                            denyButtonColor: "#6c757d"
                        }).then((result) => {
                            if (result.isDenied) {
                                abrirModalRecuperacion();
                            }
                        });

                    } else if (data.intentos_restantes !== undefined) {
                        // Contraseña incorrecta con intentos restantes
                        let iconoIntentos = "";
                        if (data.intentos_restantes >= 3) {
                            iconoIntentos = "⚠️";
                        } else if (data.intentos_restantes === 2) {
                            iconoIntentos = "🔶";
                        } else {
                            iconoIntentos = "🔴";
                        }

                        Swal.fire({
                            icon: "warning",
                            title: "Contraseña Incorrecta",
                            html: `
                                <div style="text-align: center;">
                                    <p>${data.message}</p>
                                    <div style="margin: 20px 0; font-size: 1.5rem;">
                                        ${iconoIntentos}
                                        <strong style="font-size: 2rem; margin: 0 10px;">
                                            ${data.intentos_restantes}
                                        </strong>
                                        ${iconoIntentos}
                                    </div>
                                    <p><strong>Intentos restantes: ${data.intentos_restantes}</strong></p>
                                    <p style="font-size: 0.9rem; color: #666;">
                                        Después de ${data.intentos_restantes} intento(s) más, su cuenta será bloqueada.
                                    </p>
                                </div>
                            `,
                            confirmButtonText: "Reintentar",
                            confirmButtonColor: "#ffc107",
                            showDenyButton: true,
                            denyButtonText: "¿Olvidó su contraseña?",
                            denyButtonColor: "#6c757d",
                            showClass: {
                                popup: 'animate__animated animate__headShake'
                            }
                        }).then((result) => {
                            if (result.isDenied) {
                                window.location.href = '/olvide';
                            } else {
                                // Enfocar campo de contraseña
                                password.focus();
                                password.select();
                            }
                        });

                    } else {
                        // Otros errores (usuario no encontrado, etc.)
                        Swal.fire({
                            icon: "error",
                            title: "Error de Autenticación",
                            text: data.message || 'Credenciales incorrectas',
                            confirmButtonText: "Aceptar",
                            confirmButtonColor: "#dc3545"
                        });
                    }

                    // Limpiar campo de contraseña
                    password.value = '';
                    msgPassword.textContent = '';

                }
            } catch (error) {
                console.error('Error en la solicitud:', error);
                Swal.fire({
                    icon: "error",
                    title: "Error de Conexión",
                    text: "No se pudo conectar con el servidor. Verifique su conexión a internet.",
                    confirmButtonText: "Reintentar",
                    confirmButtonColor: "#dc3545"
                });
            } finally {
                // Restaurar botón
                btnSubmit.innerHTML = originalText;
                btnSubmit.disabled = false;
            }
        });

        // Verificar si hay mensajes de sesión al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            // Si hay un mensaje de intentos en la sesión de Laravel (para fallback no-AJAX)
            const alertaError = document.querySelector('.alert.error');
            if (alertaError && alertaError.textContent.includes('Intentos restantes:')) {
                const match = alertaError.textContent.match(/Intentos restantes:\s*(\d+)/);
                if (match) {
                    const intentos = parseInt(match[1]);
                    if (intentos < 4) {
                        Swal.fire({
                            icon: "warning",
                            title: "Intentos Restantes",
                            html: `
                                <div style="text-align: center;">
                                    <p>Le quedan <strong>${intentos}</strong> intento(s) antes de que su cuenta sea bloqueada.</p>
                                    <p style="font-size: 0.9rem; color: #666;">
                                        Por seguridad, verifique sus credenciales cuidadosamente.
                                    </p>
                                </div>
                            `,
                            confirmButtonText: "Entendido",
                            confirmButtonColor: "#ffc107"
                        });
                    }
                }
            }

            // Si hay mensaje de éxito (login exitoso desde otro lugar)
            const alertaSuccess = document.querySelector('.alert.success');
            if (alertaSuccess && alertaSuccess.textContent.includes('Bienvenido')) {
                Swal.fire({
                    icon: "success",
                    title: "¡Bienvenido!",
                    text: alertaSuccess.textContent,
                    confirmButtonText: "Continuar",
                    confirmButtonColor: "#28a745",
                    timer: 2000,
                    timerProgressBar: true
                });
            }
        });
    </script>

    <script>
        // Funciones para manejar el modal
        function abrirModalRecuperacion() {
            document.getElementById('recuperacionModal').style.display = 'block';
            document.getElementById('cedula_recuperacion').focus();
        }

        function cerrarModal() {
            document.getElementById('recuperacionModal').style.display = 'none';
            limpiarFormularioRecuperacion();
        }

        function limpiarFormularioRecuperacion() {
            document.getElementById('cedula_recuperacion').value = '';
            document.getElementById('msgCedulaRecuperacion').textContent = '';
            document.getElementById('infoSeguridad').style.display = 'none';
            document.getElementById('resultadoRecuperacion').style.display = 'none';
            document.getElementById('resultadoRecuperacion').innerHTML = '';
            document.getElementById('btnRecuperar').disabled = false;
            document.getElementById('btnRecuperar').innerHTML = 'Enviar Contraseña Temporal';
        }

        // Validación de cédula en tiempo real
        document.getElementById('cedula_recuperacion').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');

            if (this.value.length === 10) {
                document.getElementById('infoSeguridad').style.display = 'block';
                document.getElementById('msgCedulaRecuperacion').textContent = '✓ Cédula válida';
                document.getElementById('msgCedulaRecuperacion').style.color = '#28a745';
            } else if (this.value.length > 0) {
                document.getElementById('infoSeguridad').style.display = 'none';
                document.getElementById('msgCedulaRecuperacion').textContent =
                    `Ingresa 10 dígitos (${this.value.length}/10)`;
                document.getElementById('msgCedulaRecuperacion').style.color = '#ffc107';
            } else {
                document.getElementById('infoSeguridad').style.display = 'none';
                document.getElementById('msgCedulaRecuperacion').textContent = '';
            }
        });

        // Función para enviar la solicitud de recuperación
        async function enviarRecuperacion() {
            const cedula = document.getElementById('cedula_recuperacion').value;
            const btnRecuperar = document.getElementById('btnRecuperar');
            const resultadoDiv = document.getElementById('resultadoRecuperacion');

            // Validar cédula
            if (cedula.length !== 10 || !/^\d{10}$/.test(cedula)) {
                resultadoDiv.innerHTML = `
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                    <strong>❌ Error:</strong> Debes ingresar un número de cédula válido (10 dígitos)
                </div>
            `;
                resultadoDiv.style.display = 'block';
                resultadoDiv.className = 'error-message';
                return;
            }

            // Deshabilitar botón y mostrar loading
            btnRecuperar.disabled = true;
            btnRecuperar.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
            resultadoDiv.style.display = 'none';

            try {
                // Obtener CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ||
                    document.querySelector('input[name="_token"]')?.value;

                // Enviar solicitud AJAX
                const response = await fetch('/recuperar-contrasena', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        cedula: cedula
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Éxito
                    resultadoDiv.innerHTML = `
                    <div style="background-color: #d4edda; color: #155724; padding: 20px; border-radius: 8px; border: 1px solid #c3e6cb; text-align: center;">
                        <div style="font-size: 40px; margin-bottom: 15px;">✅</div>
                        <h4 style="margin: 0 0 10px 0; color: #155724;">¡Correo Enviado!</h4>
                        <p style="margin: 0 0 15px 0;">${data.message}</p>
                        <p style="font-size: 14px; color: #0c5460;">
                            <strong>Revisa tu correo:</strong> ${data.email}<br>
                            <small>Si no lo encuentras, verifica tu carpeta de spam.</small>
                        </p>
                        <button onclick="cerrarModal()" style="margin-top: 15px; padding: 10px 25px; background-color: #155724; color: white; border: none; border-radius: 6px; cursor: pointer;">
                            Cerrar
                        </button>
                    </div>
                `;

                    // Cerrar automáticamente después de 5 segundos
                    setTimeout(() => {
                        cerrarModal();
                    }, 5000);

                } else {
                    // Error
                    resultadoDiv.innerHTML = `
                    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                        <strong>❌ Error:</strong> ${data.message}
                    </div>
                `;
                }

                resultadoDiv.style.display = 'block';
                resultadoDiv.className = data.success ? 'success-message' : 'error-message';

            } catch (error) {
                console.error('Error:', error);
                resultadoDiv.innerHTML = `
                <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;">
                    <strong>❌ Error de conexión:</strong> No se pudo conectar con el servidor. Intenta nuevamente.
                </div>
            `;
                resultadoDiv.style.display = 'block';
                resultadoDiv.className = 'error-message';
            } finally {
                // Restaurar botón
                btnRecuperar.disabled = false;
                btnRecuperar.innerHTML = 'Enviar Contraseña Temporal';
            }
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('recuperacionModal');
            if (event.target === modal) {
                cerrarModal();
            }
        }

        // Tecla Escape para cerrar
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                cerrarModal();
            }
        });
    </script>

</body>

</html>
