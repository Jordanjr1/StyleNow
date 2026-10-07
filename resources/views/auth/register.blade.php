<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - StyleNow</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=David+Libre:wght@400;500;700&display=swap" rel="stylesheet">
    
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
            padding: 2rem 0;
            overflow-x: hidden;
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
            max-width: 1300px;
            min-height: 650px;
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

        /* LADO IZQUIERDO - IMÁGENES */
        .left-side {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            padding: 3rem;
            background: linear-gradient(135deg, rgba(0,0,0,0.9), rgba(20,20,20,0.95));
        }

        .image-box {
            flex: 1;
            position: relative;
            border: 3px solid var(--dorado);
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(250, 211, 112, 0.3);
            transition: transform 0.3s ease;
        }

        .image-box:hover {
            transform: scale(1.02);
            box-shadow: 0 15px 40px rgba(250, 211, 112, 0.5);
        }

        .image-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-box:nth-child(1) {
            animation: float 4s ease-in-out infinite;
        }

        .image-box:nth-child(2) {
            animation: float 4s ease-in-out 2s infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* LADO DERECHO - FORMULARIO */
        .right-side {
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow-y: auto;
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

        .logo-header span {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.5rem;
            color: var(--blanco);
            text-decoration: underline;
            text-decoration-color: var(--dorado);
            text-underline-offset: 5px;
        }

        .form-container {
            max-width: 450px;
            margin: 0 auto;
            width: 100%;
        }

        .form-title {
            font-family: 'Abril Fatface', cursive;
            font-size: 3rem;
            color: var(--blanco);
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .form-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        .form-group label {
            display: block;
            color: var(--blanco);
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-group label .optional {
            color: rgba(255, 255, 255, 0.5);
            font-weight: 400;
            font-size: 0.85rem;
        }

        .input-wrapper {
            position: relative;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(250, 211, 112, 0.3);
            color: var(--blanco);
            font-family: 'David Libre', serif;
            font-size: 0.95rem;
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

        .help-icon {
            position: absolute;
            right: 1rem;
            top: -1.8rem;
            color: var(--dorado);
            cursor: help;
            font-size: 1rem;
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
            margin-top: 1rem;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(250, 211, 112, 0.6);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
        }

        .login-link a {
            color: var(--dorado);
            text-decoration: none;
            font-weight: 700;
            transition: opacity 0.3s ease;
        }

        .login-link a:hover {
            opacity: 0.7;
            text-decoration: underline;
        }

        /* RESPONSIVE */
        @media (max-width: 1100px) {
            .container {
                grid-template-columns: 1fr;
            }

            .left-side {
                display: none;
            }
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .right-side {
                padding: 2rem 1.5rem;
            }

            .form-title {
                font-size: 2.5rem;
            }

            .logo-header {
                top: 1rem;
                left: 1rem;
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
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
    20%, 40%, 60%, 80% { transform: translateX(5px); }
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
</head>
<body>

    <div class="container">
        <!-- LADO IZQUIERDO - IMÁGENES -->
        <div class="left-side">
            <div class="image-box">
                <img src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=600" alt="Barbería 1">
            </div>
            <div class="image-box">
                <img src="https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=600" alt="Barbería 2">
            </div>
        </div>

        <!-- LADO DERECHO - FORMULARIO -->
        <div class="right-side">
            <a href="/" class="logo-header">
                <img src="{{ asset('images/logo_form.png') }}" alt="StyleNow Logo">
            </a>

            <div class="form-container">
                <h1 class="form-title">Registro</h1>
                <p class="form-subtitle">Por favor, ingrese sus datos</p>

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

                @if(session('success'))
                    <div class="alert success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('registro') }}" id="formRegistro" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nombre">Nombres</label>
                            <input 
                                type="text" 
                                id="nombre" 
                                name="nombre" 
                                placeholder="Ingrese sus nombres"
                                value="{{ old('nombre') }}"
                                required
                            >
                            <span id="msgNombres" class="validation-message"></span>
                        </div>

                        <div class="form-group">
                            <label for="apellido">Apellidos</label>
                            <input 
                                type="text" 
                                id="apellido" 
                                name="apellido" 
                                placeholder="Ingrese sus apellidos"
                                value="{{ old('apellido') }}"
                                required
                            >
                            <span id="msgApellidos" class="validation-message"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="cedula">
                            Cédula
                            <span class="help-icon" title="Ingrese su número de cédula">ⓘ</span>
                        </label>
                        <input 
                            type="text" 
                            id="cedula" 
                            name="cedula" 
                            placeholder="Ingrese su número de cédula"
                            value="{{ old('cedula') }}"
                            maxlength="10"
                            required
                        >
                        <span id="msgCedula" class="validation-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="telefono">
                            Teléfono
                            <span class="help-icon" title="Ingrese su número de teléfono">ⓘ</span>
                        </label>
                        <input 
                            type="tel" 
                            id="telefono" 
                            name="telefono" 
                            placeholder="Ingrese su número de teléfono"
                            value="{{ old('telefono') }}"
                            maxlength="10"
                            required
                        >
                         <span id="msgTelefono" class="validation-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="Ingrese su correo electrónico"
                            value="{{ old('email') }}"
                            required
                        >
                        <span id="msgEmail" class="validation-message"></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                placeholder="• • • • •"
                                required
                            >
                            <span class="eye-icon" onclick="togglePassword('password')">
                                <img src="/images/icons/ojo-cerrado.png" alt="Mostrar contraseña" />
                            </span>
                            <span id="msgPassword" class="validation-message"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirmar Contraseña</label>
                        <div class="input-wrapper">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                placeholder="• • • • •"
                                required
                            >
                            <span class="eye-icon" onclick="togglePassword('password_confirmation')">
                                <img src="/images/icons/ojo-cerrado.png" alt="Mostrar contraseña" />
                            </span>
                            <span id="msgPasswordConfirm" class="validation-message"></span>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="btnSubmit">Registrarme</button>
                </form>

                <div class="login-link">
                    ¿Ya tiene una cuenta? <a href="{{ route('login') }}">Inicie sesión</a>
                </div>
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

        function validarTexto(valor) {

            if (valor.length === 0)
                return "Este campo es obligatorio.";

            if (valor.length < 3)
                return "Debe contener al menos 3 caracteres.";

            if (valor.length > 50)
                return "No debe superar 50 caracteres.";

            if (!/^[a-zA-ZÁÉÍÓÚáéíóúñÑ\s]+$/.test(valor))
                return "Solo se permiten letras y espacios.";

            return ""; // Válido
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

            let dominio = valor.split("@")[1].toLowerCase();

            if (!dominios.includes(dominio))
                return "Dominio no permitido.";

            return ""; // Válido
        }

        function validarCelular(valor) {
            // Requerido
            if (valor.length === 0)
                return "El número de celular es requerido.";

            // Exactamente 10 o 11 dígitos
            if (!/^\d{10}$/.test(valor))
                return "El número de celular debe contener 10 dígitos.";

            // Debe comenzar con 09
            if (!/^09/.test(valor))
                return "El número debe comenzar con 09.";

            // Tercer dígito válido (4–9)
            let t = parseInt(valor[2]);
            if (t < 4 || t > 9)
                return "El tercer dígito no es válido para un celular ecuatoriano.";

            // No permitir todos los dígitos iguales
            if (/^(.)\1+$/.test(valor))
                return "El número no puede tener todos los dígitos iguales.";

            // No permitir que después de los 3 primeros, todos sean iguales (ej. 0999999999)
            if (/^\d{3}(\d)\1+$/.test(valor))
                return "El número no puede tener tantos dígitos repetidos.";

            // No permitir que termine en 4 dígitos iguales (ej. 0991111, 0989999)
            if (/(\d)\1{3}$/.test(valor))
                return "El número no puede terminar con cuatro dígitos iguales.";

            return ""; // número válido
        }

        // Validación de cédula ecuatoriana
        function validarDigitoVerificador(cedula) {
            let coef = [2,1,2,1,2,1,2,1,2];
            let suma = 0;

            for (let i = 0; i < coef.length; i++) {
                let mult = parseInt(cedula[i]) * coef[i];
                suma += mult > 9 ? mult - 9 : mult;
            }

            let verificador = 10 - (suma % 10);
            if (verificador === 10) verificador = 0;

            return verificador === parseInt(cedula[9]);
        }

        function validarCedulaValor(valor) {
            // Requerido
            if (valor.length === 0)
                return "La cédula es requerida.";

            // Exactamente 10 dígitos
            if (!/^\d{10}$/.test(valor))
                return "La cédula debe contener exactamente 10 dígitos.";

            // Provincia válida
            let prov = parseInt(valor.substring(0, 2));
            if (prov < 1 || (prov > 24 && prov !== 30))
                return "Los dos primeros dígitos no son válidos.";

            // Tercer dígito 0-6
            let t = parseInt(valor[2]);
            if (t < 0 || t > 6)
                return "El tercer dígito no es válido.";

            // Dígitos repetidos
            if (/^(.)\1+$/.test(valor))
                return "La cédula no puede tener todos los dígitos iguales.";

            if (/(\d)\1{3}$/.test(valor) && (valor.endsWith("1111") || valor.endsWith("9999")))
                return "La cédula no puede terminar con cuatro dígitos iguales.";

            if (/^\d{3}([6-9])\1{6}$/.test(valor)) 
                return "La cédula no puede tener tantos dígitos repetidos.";

            // Patrones inválidos
            if (/^1[02468]1[02468]1[02468]1[02468]1[02468]$/.test(valor))
                return "La cédula no es válida.";

            if (valor === "1700000001" || valor === "2020202020")
                return "La cédula no es válida.";

            // Dígito verificador
            if (!validarDigitoVerificador(valor))
                return "La cédula no es válida.";

            return ""; // cédula válida
        }

        // Validación de contraseña
        function validarPasswordValor(valor) {
            if (valor.length === 0) return "";

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

            return "";
        }

        function mostrarErrores(errores) {
    let mensaje = '<div style="text-align: left; padding-left: 20px;">';
    
    if (errores.nombre_completo) {
        mensaje += `<div>• <strong>Nombres y Apellidos:</strong> ${errores.nombre_completo}</div>`;
    }
    if (errores.cedula) {
        mensaje += `<div>• <strong>Cédula:</strong> ${errores.cedula}</div>`;
    }
    if (errores.email) {
        mensaje += `<div>• <strong>Email:</strong> ${errores.email}</div>`;
    }
    if (errores.telefono) {
        mensaje += `<div>• <strong>Teléfono:</strong> ${errores.telefono}</div>`;
    }
    if (errores.password) {
        mensaje += `<div>• <strong>Contraseña:</strong> ${errores.password}</div>`;
    }
    
    mensaje += '</div>';
    return mensaje;
}

// Función para validar todo antes de enviar
function validarFormularioCompleto() {
    const errores = {
        nombres: validarTexto(nombres.value),
        apellidos: validarTexto(apellidos.value),
        cedula: validarCedulaValor(cedula.value),
        email: validarEmail(email.value),
        telefono: validarCelular(telefono.value),
        password: password.value ? validarPasswordValor(password.value) : "",
        confirmacion: passwordConfirm.value !== password.value ? "Las contraseñas no coinciden" : ""
    };
    
    return errores;
}

        const nombres = document.getElementById("nombre");
        const msgNom = document.getElementById("msgNombres");

        const apellidos = document.getElementById("apellido");
        const msgApe = document.getElementById("msgApellidos");

        const cedula = document.getElementById("cedula");
        const msgCedula = document.getElementById("msgCedula");

        const telefono = document.getElementById("telefono");
        const msgTelefono = document.getElementById("msgTelefono");

        const email = document.getElementById("email");
        const msgEmail = document.getElementById("msgEmail");

        const password = document.getElementById("password");
        const msgPassword = document.getElementById("msgPassword");

        const passwordConfirm = document.getElementById("password_confirmation");
        const msgPasswordConfirm = document.getElementById("msgPasswordConfirm");

        const btnSubmit = document.getElementById("btnSubmit");


        // Validación nombres
        nombres.addEventListener("input", function() {
            let error = validarTexto(this.value);
            msgNom.textContent = error || "✓ Válido";
            msgNom.style.color = error ? "#dc3545" : "#28a745";
        });


        // Validación apellidos
        apellidos.addEventListener("input", function() {
            let error = validarTexto(this.value);
            msgApe.textContent = error || "✓ Válido";
            msgApe.style.color = error ? "#dc3545" : "#28a745";
        });

        // Solo números en cédula
        cedula.addEventListener("input", function() {
            this.value = this.value.replace(/[^0-9]/g, "");
            let error = validarCedulaValor(this.value);
            msgCedula.textContent = error;
            msgCedula.style.color = error ? "#dc3545" : "#28a745";
            if (!error && this.value.length === 10) {
                msgCedula.textContent = "✓ Cédula válida";
            }
        });

        // Validación teléfono
        telefono.addEventListener("input", function() {
            this.value = this.value.replace(/[^0-9]/g, "");
            let error = validarCelular(this.value);
            msgTelefono.textContent = error;
            msgTelefono.style.color = error ? "#dc3545" : "#28a745";
            if (!error && (this.value.length === 10)) {
                msgTelefono.textContent = "✓ Teléfono válido";
            }
        });

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
            validarConfirmacion();
        });

        // Validar confirmación
        passwordConfirm.addEventListener("input", validarConfirmacion);

        function validarConfirmacion() {
            if (passwordConfirm.value.length === 0) {
                msgPasswordConfirm.textContent = "";
                return;
            }

            if (passwordConfirm.value === password.value) {
                msgPasswordConfirm.textContent = "✓ Las contraseñas coinciden";
                msgPasswordConfirm.style.color = "#28a745";
            } else {
                msgPasswordConfirm.textContent = "✗ Las contraseñas no coinciden";
                msgPasswordConfirm.style.color = "#dc3545";
            }
        }

        document.getElementById("formRegistro").addEventListener("submit", async function(e) {
    e.preventDefault(); // Prevenir envío normal
    
    // Validar campos obligatorios
    const erroresValidacion = validarFormularioCompleto();
    const hayErroresValidacion = Object.values(erroresValidacion).some(err => err !== "");
    
    if (hayErroresValidacion) {
        Swal.fire({
            icon: "error",
            title: "Datos incorrectos",
            html: "Por favor corrija los campos marcados en rojo antes de continuar.",
            confirmButtonText: "Aceptar",
            confirmButtonColor: "#144987"
        });
        return false;
    }
    
    // Mostrar loading
    const btnSubmit = document.getElementById('btnSubmit');
    const originalText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
    btnSubmit.disabled = true;
    
    try {
        // Preparar datos del formulario
        const formData = new FormData(this);
        
        // Enviar datos por AJAX
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Éxito
            Swal.fire({
                icon: "success",
                title: "¡Registro Exitoso!",
                html: data.message,
                confirmButtonText: "Iniciar Sesión",
                confirmButtonColor: "#28a745",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = data.redirect;
                }
            });
        } else {
            // Error de validación del servidor
            if (data.errors) {
                let mensajeError = '<div style="text-align: left;">';
                
                // Mostrar errores específicos
                if (data.errors.nombre_completo) {
                    mensajeError += `<div><strong>Nombres y Apellidos:</strong> ${data.errors.nombre_completo}</div>`;
                    nombres.classList.add('error-input');
                    apellidos.classList.add('error-input');
                }
                if (data.errors.cedula) {
                    mensajeError += `<div><strong>Cédula:</strong> ${data.errors.cedula}</div>`;
                    cedula.classList.add('error-input');
                }
                if (data.errors.email) {
                    mensajeError += `<div><strong>Email:</strong> ${data.errors.email}</div>`;
                    email.classList.add('error-input');
                }
                if (data.errors.telefono) {
                    mensajeError += `<div><strong>Teléfono:</strong> ${data.errors.telefono}</div>`;
                    telefono.classList.add('error-input');
                }
                if (data.errors.password) {
                    mensajeError += `<div><strong>Contraseña:</strong> ${data.errors.password}</div>`;
                    password.classList.add('error-input');
                }
                
                mensajeError += '</div>';
                
                Swal.fire({
                    icon: "error",
                    title: "Datos Duplicados",
                    html: mensajeError,
                    confirmButtonText: "Corregir",
                    confirmButtonColor: "#dc3545"
                }).then(() => {
                    // Remover clases de error después de cerrar
                    [nombres, apellidos, cedula, email, telefono].forEach(input => {
                        input.classList.remove('error-input');
                    });
                });
            } else {
                // Error general
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: data.message || 'Hubo un problema al registrar. Intente nuevamente.',
                    confirmButtonText: "Aceptar",
                    confirmButtonColor: "#dc3545"
                });
            }
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

// Agrega este CSS para resaltar errores
const style = document.createElement('style');
style.textContent = `
    .error-input {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
`;
document.head.appendChild(style);

// Funciones para validar en tiempo real y hacer pre-validación AJAX
function verificarDisponibilidad(campo, valor, tipo) {
    if (!valor || valor.length === 0) return;
    
    // Solo verificar después de una pausa
    clearTimeout(window[tipo + 'Timeout']);
    window[tipo + 'Timeout'] = setTimeout(async () => {
        try {
            const response = await fetch('/verificar-disponibilidad', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ tipo, valor })
            });
            
            const data = await response.json();
            
            if (!data.disponible) {
                const msgElement = document.getElementById(`msg${tipo.charAt(0).toUpperCase() + tipo.slice(1)}`);
                if (msgElement) {
                    msgElement.textContent = `✗ ${data.mensaje || 'Ya está registrado'}`;
                    msgElement.style.color = '#dc3545';
                    campo.classList.add('error-input');
                }
            }
        } catch (error) {
            console.error('Error verificando disponibilidad:', error);
        }
    }, 500);
}

// Agregar event listeners para verificación en tiempo real
cedula.addEventListener('blur', () => verificarDisponibilidad(cedula, cedula.value, 'cedula'));
email.addEventListener('blur', () => verificarDisponibilidad(email, email.value, 'email'));
telefono.addEventListener('blur', () => verificarDisponibilidad(telefono, telefono.value, 'telefono'));
    
    </script>

</body>
</html>