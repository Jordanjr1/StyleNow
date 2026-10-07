<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StyleNow - Gestiona la belleza. Inspira el estilo.</title>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            overflow-x: hidden;
        }

        /* ============================================
           NAVBAR
        ============================================ */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(10px);
            z-index: 1000;
            border-bottom: 1px solid rgba(250, 211, 112, 0.1);
            animation: slideDown 0.8s ease-out;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .logo-nav {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-family: 'Abril Fatface', cursive;
            font-size: 1.5rem;
            color: var(--blanco);
            text-decoration: none;
        }

        .logo-nav img {
            width: 140px;
            height: auto;
       
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-5px) rotate(5deg); }
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            margin-left: 42rem;
        }

        .nav-links a {
            color: var(--blanco);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--dorado);
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--dorado);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn-reservas {
            padding: 0.8rem 2rem;
            background: transparent;
            color: var(--dorado);
            border: 2px solid var(--dorado);
            font-family: 'David Libre', serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-reservas:hover {
            background: var(--dorado);
            color: var(--negro);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(250, 211, 112, 0.4);
        }

        /* ============================================
           HERO SECTION
        ============================================ */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(0,0,0,0), rgba(0,0,0,0)), 
                        url('/images/fondo.png') center/cover;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 30%, rgba(250, 211, 112, 0.05) 70%);
            animation: heroShine 8s ease-in-out infinite;
        }

        @keyframes heroShine {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.6; }
        }

        .hero-content {
            text-align: center;
            z-index: 10;
            opacity: 0;
            animation: heroFadeIn 1.2s ease-out 0.5s forwards;
        }

        @keyframes heroFadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-logo {
            width: 500px;
            height: auto;
            margin: 0 auto 2rem;
            margin-top: 7rem;
            animation: logoGlow 3s ease-in-out infinite;
        }

        @keyframes logoGlow {
            0%, 100% {
                filter: drop-shadow(0 0 20px rgba(250, 211, 112, 0.5));
                transform: scale(1);
            }
            50% {
                filter: drop-shadow(0 0 40px rgba(250, 211, 112, 0.8));
                transform: scale(1.05);
            }
        }

        .hero h1 {
            font-family: 'Abril Fatface', cursive;
            font-size: 5rem;
            color: var(--blanco);
            margin-bottom: 1rem;
            text-shadow: 0 0 30px rgba(250, 211, 112, 0.3);
        }

        .hero p {
            font-size: 1.5rem;
            color: var(--dorado);
            margin-bottom: 0.5rem;
        }

        /* ============================================
           SECCIÓN NOSOTROS
        ============================================ */
        .nosotros {
            padding: 8rem 5%;
            background: var(--negro);
        }

        .section-title {
            font-family: 'Abril Fatface', cursive;
            font-size: 3rem;
            text-align: center;
            color: var(--blanco);
            margin-bottom: 4rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 100px;
            height: 3px;
            background: var(--dorado);
            margin: 1rem auto 0;
        }

        .experiencia-box {
            border: 3px solid var(--dorado);
            padding: 3rem;
            margin-bottom: 4rem;
            position: relative;
            opacity: 0;
            transform: translateX(-50px);
            transition: all 0.6s ease;
        }

        .experiencia-box.visible {
            opacity: 1;
            transform: translateX(0);
        }

        .experiencia-box h3 {
            font-family: 'Abril Fatface', cursive;
            font-size: 2rem;
            color: var(--dorado);
            margin-bottom: 1rem;
        }

        .experiencia-box p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--blanco);
        }

        .equipo-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 5rem;
        }

        .miembro {
            background: rgba(250, 211, 112, 0.05);
            border: 2px solid var(--dorado);
            padding: 0;
            text-align: center;
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.5s ease;
        }

        .miembro.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .miembro:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 40px rgba(250, 211, 112, 0.3);
        }

        .miembro img {
            width: 100%;
            height: 426px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .miembro:hover img {
            transform: scale(1.1);
        }

        .miembro-info {
            padding: 1.5rem;
            background: url('data:image/svg+xml,<svg width="4" height="4" xmlns="http://www.w3.org/2000/svg"><circle cx="2" cy="2" r="1" fill="%23FAD370" opacity="0.2"/></svg>');
        }

        .miembro h4 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.3rem;
            color: var(--blanco);
            margin-bottom: 0.5rem;
        }

        .miembro .cargo {
            color: var(--dorado);
            font-size: 0.95rem;
            margin-bottom: 0.8rem;
        }

        .miembro p {
            font-size: 0.9rem;
            line-height: 1.6;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Sección inferior con imágenes circulares */
        .consentir-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 5rem;
        }

        .consentir-box {
            display: flex;
            align-items: center;
            gap: 2rem;
            opacity: 0;
            transition: all 0.6s ease;
        }

        .consentir-box.visible {
            opacity: 1;
        }

        .consentir-box:nth-child(1) {
            transform: translateX(-50px);
        }

        .consentir-box.visible:nth-child(1) {
            transform: translateX(0);
        }

        .consentir-box:nth-child(2) {
            transform: translateX(50px);
        }

        .consentir-box.visible:nth-child(2) {
            transform: translateX(0);
        }

        .circular-img {
            width: 250px;
            height: 250px;
            border-radius: 50%;
            border: 5px solid var(--dorado);
            object-fit: cover;
            flex-shrink: 0;
            box-shadow: 0 0 30px rgba(250, 211, 112, 0.3);
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .text-box {
            border: 3px solid var(--dorado);
            padding: 2rem;
            flex: 1;
        }

        .text-box h3 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.8rem;
            color: var(--dorado);
            margin-bottom: 1rem;
        }

        .text-box p {
            line-height: 1.8;
            color: var(--blanco);
        }

        /* Iconos de características */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .feature-box {
            border: 3px solid var(--dorado);
            padding: 2.5rem 1.5rem;
            text-align: center;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.5s ease;
        }

        .feature-box.visible {
            opacity: 1;
            transform: scale(1);
        }

        .feature-box:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 40px rgba(250, 211, 112, 0.3);
        }

        .feature-icon {
            margin-bottom: 1rem;
        }

        .feature-icon img {
            width: 100px;       
            height: auto;      
            object-fit: contain; 
            display: inline-block;
        }


        .feature-box h4 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.2rem;
            color: var(--dorado);
            margin-bottom: 1rem;
        }

        .feature-box p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: var(--blanco);
        }

        .quote-section {
            text-align: center;
            padding: 3rem;
            border: 3px solid var(--dorado);
            background: rgba(250, 211, 112, 0.05);
        }

        .quote-section p {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.5rem;
            color: var(--dorado);
            line-height: 1.8;
        }

        /* ============================================
           SECCIÓN SERVICIOS
        ============================================ */
        .servicios {
            padding: 8rem 5%;
            background: linear-gradient(180deg, #000 0%, #1a1a1a 100%);
            position: relative;
            overflow: hidden;
        }

        .tabs {
            display: flex;
            justify-content: center;
            gap: 0;
            margin-bottom: 4rem;
        }

        .tab {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.5rem;
            padding: 1rem 3rem;
            background: transparent;
            color: rgba(255, 255, 255, 0.5);
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }

        .tab.active {
            color: var(--dorado);
            border-bottom-color: var(--dorado);
        }

        .tab:hover:not(.active) {
            color: rgba(250, 211, 112, 0.8);
        }

        /* Contenedores de servicios */
        .servicios-contenedor {
            transition: transform 0.5s ease;
            position: relative;
        }

        /* Navegación de categorías para damas */
        .categorias-navegacion {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 3rem;
            gap: 20px;
        }

        .nav-btn {
            background: transparent;
            border: 2px solid var(--dorado);
            color: var(--dorado);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .nav-btn:hover {
            background: var(--dorado);
            color: var(--negro);
            transform: scale(1.1);
        }

        .nav-btn:disabled {
            opacity: 0.3;
            cursor: not-allowed;
        }

        .nav-btn:disabled:hover {
            background: transparent;
            color: var(--dorado);
            transform: none;
        }

        .categorias-tabs {
            display: flex;
            gap: 10px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: none;
            max-width: 800px;
        }

        .categorias-tabs::-webkit-scrollbar {
            display: none;
        }

        .categoria-tab {
            font-family: 'David Libre', serif;
            font-size: 1.1rem;
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            border: 1px solid rgba(250, 211, 112, 0.3);
            border-radius: 30px;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .categoria-tab.active {
            background: var(--dorado);
            color: var(--negro);
            border-color: var(--dorado);
            transform: scale(1.05);
        }

        .categoria-tab:hover:not(.active) {
            background: rgba(250, 211, 112, 0.2);
            color: var(--dorado);
            border-color: var(--dorado);
        }

        /* Contenedor de categorías */
        .categorias-container {
            position: relative;
            min-height: 500px;
            overflow: hidden;
        }

        .categoria-contenedor {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.5s ease;
            pointer-events: none;
        }

        .categoria-contenedor.active {
            opacity: 1;
            transform: translateX(0);
            position: relative;
            pointer-events: all;
        }

        .categoria-contenedor.slide-left {
            transform: translateX(-100%);
        }

        .categoria-contenedor.slide-right {
            transform: translateX(100%);
        }

        .servicios-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .servicio-card {
            background: rgba(0, 0, 0, 0.8);
            border: 2px solid var(--dorado);
            padding: 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.5s ease;
            height: 350px;
            display: flex;
            flex-direction: column;
        }

        .servicio-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .servicio-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(250, 211, 112, 0.4);
        }

        .precio {
            position: absolute;
            top: 15px;
            left: 15px;
            background: var(--dorado);
            color: var(--negro);
            padding: 0.5rem 1rem;
            font-weight: 700;
            font-size: 1.1rem;
            z-index: 10;
            border-radius: 4px;
        }

        .servicio-icon {
            padding: 2rem 1rem 1rem; 
            text-align: center;
            height: 140px; 
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .servicio-icon img {
            height: 100px;
            width: auto;
            object-fit: contain;
            display: block; 
            margin: 0 auto;
            margin-top: 4rem;
            transition: transform 0.3s ease;
        }

        .img-retoque {
            height: 70px;   
            margin-top: 0;  
        }

        .servicio-icon img[src*="retoqueH.png"] {
            height: 55px;
            margin-top: 4rem;
        }

        .servicio-card:hover .servicio-icon img {
            transform: scale(1.1);
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .servicio-card h4 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.3rem;
            color: var(--blanco);
            margin: 3rem 0 0.5rem;
            padding: 0 1rem;
            flex-shrink: 0; /* Evita que se encoja */
            min-height: 60px; /* Altura mínima para títulos de 2 líneas */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .servicio-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            line-height: 1.5;
            padding: 0 1.5rem 1.5rem;
            flex-grow: 1; /* Ocupa el espacio restante */
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4; /* Limita a 4 líneas máximo */
            -webkit-box-orient: vertical;
            margin: 0;
        }

        /* Animación de cambio entre caballeros y damas */
        .servicios-contenedor.slide-out-left {
            animation: slideOutLeft 0.5s ease forwards;
        }

        .servicios-contenedor.slide-in-right {
            animation: slideInRight 0.5s ease forwards;
        }

        .servicios-contenedor.slide-out-right {
            animation: slideOutRight 0.5s ease forwards;
        }

        .servicios-contenedor.slide-in-left {
            animation: slideInLeft 0.5s ease forwards;
        }

        @keyframes slideOutLeft {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(-100%); opacity: 0; }
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        @keyframes slideInLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .servicios-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .categorias-tabs {
                max-width: 600px;
            }

            .servicio-card {
                height: 380px; 
            }
            
            .servicio-icon {
                height: 120px;
                padding: 1.5rem 1rem 1rem;
            }
            
            .servicio-icon img {
                width: 70px;
                height: 70px;
            }
            
            .servicio-card h4 {
                font-size: 1.2rem;
                min-height: 55px;
            }
        }

        @media (max-width: 768px) {
            .servicios-grid {
                grid-template-columns: 1fr;
            }
            
            .categorias-navegacion {
                flex-direction: column;
                gap: 15px;
            }
            
            .categorias-tabs {
                order: 1;
                width: 100%;
                justify-content: flex-start;
                padding-bottom: 15px;
            }
            
            .nav-btn.prev-btn {
                order: 2;
            }
            
            .nav-btn.next-btn {
                order: 3;
            }
            
            .tabs {
                flex-direction: column;
                align-items: center;
                gap: 10px;
            }
            
            .tab {
                width: 200px;
                text-align: center;
            }

            .servicio-card {
                height: 350px;
            }
            
            .servicio-icon {
                height: 100px;
                padding: 1.5rem 1rem 0.5rem;
            }
            
            .servicio-icon img {
                width: 60px;
                height: 60px;
            }
            
            .servicio-card h4 {
                font-size: 1.1rem;
                min-height: 50px;
            }
            
            .servicio-card p {
                font-size: 0.9rem;
                padding: 0 1rem 1rem;
                -webkit-line-clamp: 3; 
            }
        }

        @media (max-width: 480px) {
            .servicio-icon {
                padding: 2rem 1rem 1.5rem;
            }
            
            .servicio-card h4 {
                font-size: 1.1rem;
                margin: 1rem 0 0.5rem;
            }
            
            .servicio-card p {
                font-size: 0.9rem;
                padding: 0 1rem 1.5rem;
            }
            
            .categoria-tab {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
        }

        /* ============================================
           SECCIÓN CONTACTO
        ============================================ */
        .contacto {
            padding: 8rem 5%;
            background: var(--negro);
        }

        .contacto-container {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 4rem;
            align-items: center;
        }

        .contacto-img {
            width: 100%;
            height: 600px;
            border-radius: 20px;
            object-fit: cover;
            border: 3px solid var(--dorado);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .form-container {
            border: 3px solid var(--dorado);
            padding: 3rem;
            background: rgba(250, 211, 112, 0.03);
        }

        .form-container h3 {
            font-family: 'Abril Fatface', cursive;
            font-size: 2.5rem;
            color: var(--dorado);
            margin-bottom: 2rem;
            text-align: center;
        }

        .info-contacto {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid rgba(250, 211, 112, 0.3);
        }

        .info-contacto h4 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.2rem;
            color: var(--blanco);
            margin-bottom: 0.5rem;
        }

        .info-contacto p {
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .form-container label {
            display: block;
            color: var(--blanco);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.6);
            border: 2px solid rgba(250, 211, 112, 0.3);
            color: var(--blanco);
            font-family: 'David Libre', serif;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-container input:focus,
        .form-container textarea:focus {
            outline: none;
            border-color: var(--dorado);
            box-shadow: 0 0 10px rgba(250, 211, 112, 0.3);
        }

        .form-container textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn-enviar {
            width: 100%;
            padding: 1.2rem;
            background: var(--dorado);
            color: var(--negro);
            border: none;
            font-family: 'David Libre', serif;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-enviar:hover {
            background: #ffd740;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(250, 211, 112, 0.5);
        }

        /* ============================================
           FOOTER
        ============================================ */
        .footer {
            background: #0a0a0a;
            padding: 3rem 5%;
            border-top: 1px solid rgba(250, 211, 112, 0.2);
        }

        .footer-content {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-logo {
            display: flex;
            flex-direction: column; 
            align-items: center;    
            gap: 1rem;
        }

        .footer-logo img {
            height: 150px;
            width: auto; 
        }

        .footer-logo-text {
            text-align: center;     
        }


        .footer-logo-text h3 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.5rem;
            color: var(--blanco);
            margin-bottom: 0.5rem;
        }

        .footer-logo-text p {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .social-links a {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(250, 211, 112, 0.1);
            border: 1px solid var(--dorado);
            text-decoration: none;
            transition: all 0.3s ease;
            overflow: hidden; 
        }

        .social-links a img {
            width: 18px; 
            height: 18px;
            object-fit: contain;
        }

        .social-links a:hover {
            background: var(--dorado);
            transform: translateY(-3px);
        }

        .social-links a:hover img {
            filter: invert(1); 
        }

        .footer-section h4 {
            font-family: 'Abril Fatface', cursive;
            font-size: 1.2rem;
            color: var(--dorado);
            margin-bottom: 1rem;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.8rem;
        }

        .footer-section ul li a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--dorado);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(250, 211, 112, 0.1);
        }

        .footer-bottom p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.9rem;
        }

        .quote-text {
            font-style: italic;
            margin-top: 1rem;
            color: var(--dorado);
        }

        /* ============================================
           RESPONSIVE
        ============================================ */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero h1 {
                font-size: 3rem;
            }

            .equipo-grid,
            .servicios-grid,
            .features-grid {
                grid-template-columns: 1fr;
            }

            .consentir-section,
            .contacto-container {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="#" class="logo-nav">
            <img src="/images/logoHeader1-icon.png" alt="StyleNow Logo">
            
        </a>
        <ul class="nav-links">
            <li><a href="#inicio">Inicio</a></li>
            <li><a href="#nosotros">Nosotros</a></li>
            <li><a href="#servicios">Servicios</a></li>
            <li><a href="#contacto">Contacto</a></li>
        </ul>
        <button class="btn-reservas" onclick="window.location.href='/login'">Reservas</button>
    </nav>

    <!-- HERO SECTION -->
    <section id="inicio" class="hero">
        <div class="hero-content">
            <img src="/images/logo-icon.png" alt="Logo StyleNow" class="hero-logo">
        </div>
    </section>

    <!-- SECCIÓN NOSOTROS -->
    <section id="nosotros" class="nosotros">
        <h2 class="section-title">NOSOTROS</h2>

        <!-- Experiencia -->
        <div class="experiencia-box scroll-reveal">
            <h3>| Experiencia StlyeNow</h3>
            <p>En StyleNow ofrecemos una experiencia de belleza moderna y personalizada. Nuestro equipo está conformado por estilistas profesionales con años de experiencia en cortes, colorimetría, tratamientos y cuidado del cabello.</p>
        </div>

        <!-- Equipo -->
        <div class="equipo-grid">
            <div class="miembro scroll-reveal">
                <img src="/images/K2.png" alt="Keter Ruiz">
                <div class="miembro-info">
                    <h4>Keter Ruiz</h4>
                    <p class="cargo">Barbero Profesional</p>
                    <p>Experto en cortes clásicos, fades y estilos del día a día. Especializado en que cada corte mejore tu estilo personal.</p>
                </div>
            </div>

            <div class="miembro scroll-reveal">
                <img src="/images/Valeria.png" alt="Valeria Montenegro">
                <div class="miembro-info">
                    <h4>Valeria Montenegro</h4>
                    <p class="cargo">Estilista Profesional</p>
                    <p>Especialista en cortes clásicos y fades precisos. Atiende el detalle para que te veas impecable en todo momento.</p>
                </div>
            </div>

            <div class="miembro scroll-reveal">
                <img src="/images/Rodrigo.png" alt="Rodrigo Mendoza">
                <div class="miembro-info">
                    <h4>Rodrigo Mendoza</h4>
                    <p class="cargo">Barbero Moderno</p>
                    <p>Cortes modernos, skin urbanos, perfecta la barba algo diferente.</p>
                </div>
            </div>

            <div class="miembro scroll-reveal">
                <img src="/images/Camila.png" alt="Camila Herrera">
                <div class="miembro-info">
                    <h4>Camila Herrera</h4>
                    <p class="cargo">Colorista Experta</p>
                    <p>Experta en utilismo moderno y cuidado capilar. Siempre buscando realzar tu imagen con un acabado limpio y profesional.</p>
                </div>
            </div>
        </div>

        <!-- Consentir Section -->
        <div class="consentir-section">
            <div class="consentir-box scroll-reveal">
                <div class="text-box">
                    <h3>| Experiencia StlyeNow</h3>
                    <p>Tu visita no es solo un corte o un tratamiento: es un tiempo para ti, para sentirte cómodo, relajarte y confiar en que estás en manos que cuidan cada detalle.</p>
                </div>
                <img src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=400" alt="Experiencia" class="circular-img">
            </div>

            <div class="consentir-box scroll-reveal">
                <img src="https://images.unsplash.com/photo-1605497788044-5a32c7078486?w=400" alt="Consentir" class="circular-img">
                <div class="text-box">
                    <h3>| Ven a consentirte</h3>
                    <p>En nuestro espacio, cada servicio está pensado para darte una pausa del día a día, ayudarte a recuperar energía y hacerte salir con una versión de ti mismo.</p>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="features-grid">
            <div class="feature-box scroll-reveal">
                <div class="feature-icon">
                    <img src="images/ubicacion.png" alt="Ubicación" />
                </div>
                <h4>Ubicados en el Sector Shopping</h4>
                <p>La mejor ubicación en Cumbayá y los Valles</p>
            </div>

            <div class="feature-box scroll-reveal">
                <div class="feature-icon">
                    <img src="images/horario.png" alt="Ubicación" />
                </div>
                <h4>Lunes a Sábado</h4>
                <p>Agenda tu momento perfecto sin complicaciones de escoger</p>
            </div>

            <div class="feature-box scroll-reveal">
                <div class="feature-icon">
                    <img src="images/calidad.png" alt="Ubicación" />
                </div>
                <h4>Calidad y Experiencia</h4>
                <p>Técnicas profesionales que marcan la diferencia</p>
            </div>

            <div class="feature-box scroll-reveal">
                <div class="feature-icon">
                    <img src="images/satisfaccion.png" alt="Ubicación" />
                </div>
                <h4>Satisfacción Garantizada</h4>
                <p>Queremos que cada visita termine con una sonrisa</p>
            </div>
        </div>

        <!-- Quote -->
        <div class="quote-section scroll-reveal">
            <p>Entra, toma asiento y olvídate del ruido exterior.<br>Estilo, comodidad y calidad se encuentran</p>
        </div>
    </section>

    <!-- SECCIÓN SERVICIOS -->
    <section id="servicios" class="servicios">
    <h2 class="section-title">SERVICIOS</h2>

    <div class="tabs">
        <button class="tab active" data-tab="caballeros">Caballeros</button>
        <button class="tab" data-tab="damas">Damas</button>
    </div>

    <!-- Contenedor para servicios de caballeros -->
    <div class="servicios-contenedor" id="servicios-caballeros">
        <div class="servicios-grid">
            <!-- Corte de Pelo -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$15</div>
                <div class="servicio-icon">
                    <img src="images/corteH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>CORTE DE PELO</h4>
                <p>Corte, lavado y peinado. Afrontarlo será sencillo, estilo profesional y acabado moderno.</p>
            </div>

            <!-- Afeitado -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$7</div>
                <div class="servicio-icon">
                    <img src="images/afeitadoH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>AFEITADO</h4>
                <p>Método de cabeza o navajilla o con navaja, precisión y frescura. Tú eliges tu método.</p>
            </div>

            <!-- Corte de Barba -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$8</div>
                <div class="servicio-icon">
                    <img src="images/corte_barbaH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>CORTE DE BARBA</h4>
                <p>Corte y perfilado de barba, rasurado y línea. Adecúalo de manera impecable a tu estilo y rostro.</p>
            </div>

            <!-- Diseños -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$5</div>
                <div class="servicio-icon">
                    <img src="images/peinadoH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>DISEÑOS</h4>
                <p>Lavado y styling profesional. Aplicación de pomada, cera o gel para un estilo acabad esculpido.</p>
            </div>

            <!-- Retoque de Barba -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$4</div>
                <div class="servicio-icon">
                    <img src="images/retoqueH.png" alt="Corte de pelo" loading="lazy" class="img-retoque"/>
                </div>
                <h4>RETOQUE DE BARBA</h4>
                <p>Perfilado de barba. Afeitado de líneas y cuello y radeado o forma (siempre/alto) con énfasis sencillo.</p>
            </div>

            <!-- Limpieza Facial -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$12</div>
                <div class="servicio-icon">
                    <img src="images/limpiezaH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>LIMPIEZA FACIAL</h4>
                <p>Toalla caliente, limpiau y sales. Aplicación de crema hidratante o exfoliante y steam de calor.</p>
            </div>

            <!-- Coloración de Cabello -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$25</div>
                <div class="servicio-icon">
                    <img src="images/coloracionH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>COLORACIÓN DE CABELLO</h4>
                <p>Cobertura de canas, marca de color natural o aplicado de matiz cubres, cuidado productos importados para activar.</p>
            </div>

            <!-- Masaje Capilar -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$10</div>
                <div class="servicio-icon">
                    <img src="images/masajeH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>MASAJE CAPILAR</h4>
                <p>Tratamiento relajante que mejora la circulación y estimula el cuero cabelludo con aromas relajantes.</p>
            </div>

            <!-- Diseño y Tinte de Barba -->
            <div class="servicio-card scroll-reveal">
                <div class="precio">$18</div>
                <div class="servicio-icon">
                    <img src="images/diseño_tinteH.png" alt="Corte de pelo" loading="lazy"/>
                </div>
                <h4>DISEÑO Y TINTE DE BARBA</h4>
                <p>Diseñado, corte retro de barba con su respectivo al color o ratendir al color y crear canas.</p>
            </div>
        </div>
    </div>

    <!-- Contenedor para servicios de damas -->
    <div class="servicios-contenedor" id="servicios-damas" style="display: none;">
        <!-- Navegación de categorías para damas -->
        <div class="categorias-navegacion">
            <button class="nav-btn prev-btn">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z" fill="currentColor"/>
                </svg>
            </button>
            
            <div class="categorias-tabs">
                <button class="categoria-tab active" data-categoria="peluqueria">Peluquería y Estilismo</button>
                <button class="categoria-tab" data-categoria="coloracion">Coloración</button>
                <button class="categoria-tab" data-categoria="manicure">Manicure y Pedicure</button>
                <button class="categoria-tab" data-categoria="mirada">Mirada y Estética Facial</button>
            </div>
            
            <button class="nav-btn next-btn">
                <svg viewBox="0 0 24 24" width="24" height="24">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z" fill="currentColor"/>
                </svg>
            </button>
        </div>

        <!-- Contenedor de categorías -->
        <div class="categorias-container">
            <!-- Categoría 1: Peluquería y Estilismo -->
            <div class="categoria-contenedor active" data-categoria="peluqueria">
                <div class="servicios-grid">
                    <!-- Corte de Dama -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$25</div>
                        <div class="servicio-icon">
                            <img src="images/corteM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>CORTE DE DAMA</h4>
                        <p>Incluye lavado, masaje capilar, corte de puntas o cambio de look y secado con brushing profesional.</p>
                    </div>

                    <!-- Peinado para Eventos -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$45</div>
                        <div class="servicio-icon">
                            <img src="images/peinadoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>PEINADO PARA EVENTOS</h4>
                        <p>Peinados elaborados: recogidos, semi-recogidos, trenzas y ondas con plancha o tenaza para bodas o fiestas.</p>
                    </div>

                    <!-- Alisado Permanente -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$85</div>
                        <div class="servicio-icon">
                            <img src="images/alisadoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>ALISADO PERMANENTE</h4>
                        <p>Tratamiento de keratina, progresivo o japonés para eliminar el frizz y mantener el cabello liso por meses.</p>
                    </div>

                    <!-- Tratamiento Capilar Profundo -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$40</div>
                        <div class="servicio-icon">
                            <img src="images/tratamientoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>TRATAMIENTO CAPILAR PROFUNDO</h4>
                        <p>Hidratación intensiva, reparación de daños o nutrición profunda, incluye masaje y aplicación de calor.</p>
                    </div>

                    <!-- Corte con Técnica Japonesa -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$30</div>
                        <div class="servicio-icon">
                            <img src="images/corte_japonesM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>CORTE JAPONÉS</h4>
                        <p>Técnica de corte precisa que crea movimiento natural y eliminación de peso para mayor volumen.</p>
                    </div>

                    <!-- Brushing Profesional -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$20</div>
                        <div class="servicio-icon">
                            <img src="images/brushingM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>BRUSHING PROFESIONAL</h4>
                        <p>Secado y cepillado profesional para dar forma, volumen y brillo al cabello con productos específicos.</p>
                    </div>
                </div>
            </div>

            <!-- Categoría 2: Coloración -->
            <div class="categoria-contenedor" data-categoria="coloracion">
                <div class="servicios-grid">
                    <!-- Tinte Completo -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$55</div>
                        <div class="servicio-icon">
                            <img src="images/tinte_completoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>TINTE COMPLETO</h4>
                        <p>Coloración uniforme de raíz a puntas para cubrir canas o cambiar completamente el tono de cabello.</p>
                    </div>

                    <!-- Tinte Parcial -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$40</div>
                        <div class="servicio-icon">
                            <img src="images/tinte_parcialM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>TINTE PARCIAL</h4>
                        <p>Aplicación de tinta únicamente en el crecimiento. Técnica de coloración para aportar dimensión y luminosidad.</p>
                    </div>

                    <!-- Balayage -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$75</div>
                        <div class="servicio-icon">
                            <img src="images/balayageM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>BALAYAGE</h4>
                        <p>Técnica de coloración libre que crea reflejos naturales y degradados de color desde la mitad del cabello.</p>
                    </div>

                    <!-- Mechas -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$65</div>
                        <div class="servicio-icon">
                            <img src="images/mechasM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>MECHAS</h4>
                        <p>Aplicación de mechas californianas, españolas o rusas para iluminar el rostro con reflejos dorados o cenizos.</p>
                    </div>

                    <!-- Color Fantasía -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$70</div>
                        <div class="servicio-icon">
                            <img src="images/colorM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>COLOR FANTASÍA</h4>
                        <p>Aplicación de colores vibrantes como rosa, azul, morado o verde en mechas o todo el cabello.</p>
                    </div>
                </div>
            </div>

            <!-- Categoría 3: Manicure y Pedicure -->
            <div class="categoria-contenedor" data-categoria="manicure">
                <div class="servicios-grid">
                    <!-- Manicure Clásico -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$15</div>
                        <div class="servicio-icon">
                            <img src="images/manicure_clasicoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>MANICURE CLÁSICO</h4>
                        <p>Limpieza, arreglo de cutículas, masaje de manos y aplicación de esmalte tradicional.</p>
                    </div>

                    <!-- Manicure Semipermanente -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$25</div>
                        <div class="servicio-icon">
                            <img src="images/manicure_semiM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>MANICURE SEMIPERMANENTE</h4>
                        <p>Limpieza, limpieza profunda y aplicación de esmalte de gel en lámpara UV para mayor duración.</p>
                    </div>

                    <!-- Pedicure Spa -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$25</div>
                        <div class="servicio-icon">
                            <img src="images/pedicureM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>PEDICURE SPA</h4>
                        <p>Limpieza, exfoliación de pies, remoción de durezas, masaje relajante y pintado.</p>
                    </div>

                    <!-- Uñas Acrílicas / Gel -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$45</div>
                        <div class="servicio-icon">
                            <img src="images/uñasM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>UÑAS ACRÍLICAS / GEL</h4>
                        <p>Extensión y diseño personalizado de uñas con acrílico o gel para mayor longitud y resistencia.</p>
                    </div>

                    <!-- Pedicure Médico -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$35</div>
                        <div class="servicio-icon">
                            <img src="images/pedicure_medicoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>PEDICURE MÉDICO</h4>
                        <p>Tratamiento especializado para uñas encarnadas, hongos o problemas podológicos con instrumentos estériles.</p>
                    </div>
                </div>
            </div>

            <!-- Categoría 4: Mirada y Estética Facial -->
            <div class="categoria-contenedor" data-categoria="mirada">
                <div class="servicios-grid">
                    <!-- Depilación Facial con Cera -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$12</div>
                        <div class="servicio-icon">
                            <img src="images/depilacionM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>DEPILACIÓN FACIAL CON CERA</h4>
                        <p>Remoción de vello no deseado en cejas, bozo o rostro completo con cera de baja temperatura.</p>
                    </div>

                    <!-- Diseño de Cejas -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$10</div>
                        <div class="servicio-icon">
                            <img src="images/diseño_cejasM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>DISEÑO DE CEJAS</h4>
                        <p>Perfilado y diseño para crear la forma de ceja ideal según el rostro de la clienta.</p>
                    </div>

                    <!-- Laminado de Cejas -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$18</div>
                        <div class="servicio-icon">
                            <img src="images/laminadoM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>LAMINADO DE CEJAS</h4>
                        <p>Tratamiento para peinar y fijar el vello de la ceja, creando un efecto de mayor volumen y grosor.</p>
                    </div>

                    <!-- Lifting de Pestañas -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$35</div>
                        <div class="servicio-icon">
                            <img src="images/lifting.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>LIFTING DE PESTAÑAS</h4>
                        <p>Tratamiento que alza la pestaña natural desde la raíz, logrando un efecto de mayor longitud y apertura de la mirada.</p>
                    </div>

                    <!-- Tinte de Cejas y Pestañas -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$15</div>
                        <div class="servicio-icon">
                            <img src="images/tinte_cejasM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>TINTE DE CEJAS Y PESTAÑAS</h4>
                        <p>Coloración temporal que oscurece y define cejas y pestañas para un look más marcado y definido.</p>
                    </div>

                    <!-- Limpieza Facial Profunda -->
                    <div class="servicio-card scroll-reveal">
                        <div class="precio">$40</div>
                        <div class="servicio-icon">
                            <img src="images/limpiezaM.png" alt="Corte de pelo" loading="lazy"/>
                        </div>
                        <h4>LIMPIEZA FACIAL PROFUNDA</h4>
                        <p>Extracción de comedones, hidratación profunda y tratamiento con productos especializados para cada tipo de piel.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <!-- SECCIÓN CONTACTO -->
    <section id="contacto" class="contacto">
        <h2 class="section-title">CONTACTO</h2>

        <div class="contacto-container">
            <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=800" alt="Contacto StyleNow" class="contacto-img">

            <div class="form-container">
                <h3>Visítanos</h3>

                <div class="info-contacto">
                    <h4>Matriz</h4>
                    <p>Casanova y Portugal, Edif. RAIA,<br>Entrada posterior</p>
                </div>

                <div class="info-contacto">
                    <h4>Email</h4>
                    <p>contacto@stylenow.ec</p>
                </div>

                <div class="info-contacto">
                    <h4>Horario</h4>
                    <p>Lunes-Viernes 7am - 8pm<br>Sábado: 8am - 7pm</p>
                </div>

                <form>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Correo electrónico</label>
                            <input type="email" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mensaje</label>
                        <textarea required></textarea>
                    </div>

                    <button type="submit" class="btn-enviar">ENVIAR</button>
                </form>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="/images/logo_footer.png" alt="StyleNow">
                <div class="footer-logo-text">
                    <div class="social-links">
                        <a href="#">
                            <img src="/images/icons/instagram.png" alt="Instagram">
                        </a>
                        <a href="#">
                            <img src="/images/icons/x.png" alt="X (Twitter)">
                        </a>
                        <a href="#">
                            <img src="/images/icons/facebook.png" alt="Facebook">
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer-section">
                <h4>Inicio</h4>
                <ul>
                    <li><a href="#servicios">Servicios</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Nosotros</h4>
                <ul>
                    <li><a href="#nosotros">Equipo</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Reservas</h4>
                <ul>
                    <li><a href="#contacto">Contacto</a></li>
                </ul>
                <p class="quote-text">Quito, Ecuador<br>+593 99 999 9999<br>contacto@stylenow.com</p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>"Comprometidos con ofrecer el mejor servicio, puntualidad y resultados impecables."</p>
            <p style="margin-top: 1rem;">© 2025 StyleNow - Todos los derechos reservados</p>
        </div>
    </footer>

    <script>
        // Animación scroll reveal
        const scrollReveal = () => {
            const reveals = document.querySelectorAll('.scroll-reveal');
            
            reveals.forEach(element => {
                const windowHeight = window.innerHeight;
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        };

        window.addEventListener('scroll', scrollReveal);
        scrollReveal(); // Initial check

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // JavaScript para la funcionalidad de servicios
document.addEventListener('DOMContentLoaded', function() {
    // Tabs principales (Caballeros/Damas)
    const tabs = document.querySelectorAll('.tab');
    const serviciosCaballeros = document.getElementById('servicios-caballeros');
    const serviciosDamas = document.getElementById('servicios-damas');
    
    // Navegación de categorías para damas
    const categoriaTabs = document.querySelectorAll('.categoria-tab');
    const categoriaContenedores = document.querySelectorAll('.categoria-contenedor');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');
    const categoriasTabsContainer = document.querySelector('.categorias-tabs');
    
    let currentTab = 'caballeros';
    let currentCategoria = 'peluqueria';
    const categorias = ['peluqueria', 'coloracion', 'manicure', 'mirada'];
    let categoriaIndex = 0;
    
    // Configurar el scroll reveal para las tarjetas de servicio
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);
    
    // Observar todas las tarjetas de servicio
    document.querySelectorAll('.servicio-card').forEach(card => {
        observer.observe(card);
    });
    
    // Función para cambiar entre caballeros y damas
    function cambiarServicios(tab) {
        const isDamas = tab === 'damas';
        
        if (currentTab !== tab) {
            // Animación de salida
            if (currentTab === 'caballeros') {
                serviciosCaballeros.classList.add('slide-out-left');
                setTimeout(() => {
                    serviciosCaballeros.style.display = 'none';
                    serviciosCaballeros.classList.remove('slide-out-left');
                    
                    // Mostrar servicios de damas
                    serviciosDamas.style.display = 'block';
                    serviciosDamas.classList.add('slide-in-right');
                    
                    setTimeout(() => {
                        serviciosDamas.classList.remove('slide-in-right');
                    }, 500);
                }, 500);
            } else {
                serviciosDamas.classList.add('slide-out-right');
                setTimeout(() => {
                    serviciosDamas.style.display = 'none';
                    serviciosDamas.classList.remove('slide-out-right');
                    
                    // Mostrar servicios de caballeros
                    serviciosCaballeros.style.display = 'block';
                    serviciosCaballeros.classList.add('slide-in-left');
                    
                    setTimeout(() => {
                        serviciosCaballeros.classList.remove('slide-in-left');
                    }, 500);
                }, 500);
            }
            
            // Actualizar tabs
            tabs.forEach(t => t.classList.remove('active'));
            document.querySelector(`.tab[data-tab="${tab}"]`).classList.add('active');
            currentTab = tab;
            
            // Reiniciar animaciones para nuevas tarjetas
            setTimeout(() => {
                document.querySelectorAll('.servicio-card:not(.visible)').forEach(card => {
                    observer.observe(card);
                });
            }, 600);
        }
    }
    
    // Función para cambiar categorías en damas
    function cambiarCategoria(categoria) {
        if (currentCategoria !== categoria) {
            // Encontrar índices
            const oldIndex = categorias.indexOf(currentCategoria);
            const newIndex = categorias.indexOf(categoria);
            
            // Determinar dirección de la animación
            const direccion = newIndex > oldIndex ? 'right' : 'left';
            
            // Ocultar categoría actual
            const oldCategoria = document.querySelector(`.categoria-contenedor[data-categoria="${currentCategoria}"]`);
            oldCategoria.classList.remove('active');
            oldCategoria.classList.add(direccion === 'right' ? 'slide-left' : 'slide-right');
            
            // Mostrar nueva categoría
            const newCategoria = document.querySelector(`.categoria-contenedor[data-categoria="${categoria}"]`);
            newCategoria.classList.remove('slide-left', 'slide-right');
            newCategoria.classList.add('active');
            
            // Actualizar tabs de categoría
            categoriaTabs.forEach(tab => tab.classList.remove('active'));
            document.querySelector(`.categoria-tab[data-categoria="${categoria}"]`).classList.add('active');
            
            currentCategoria = categoria;
            categoriaIndex = newIndex;
            
            // Actualizar estado de botones de navegación
            actualizarBotonesNavegacion();
            
            // Scroll al tab activo
            const activeTab = document.querySelector(`.categoria-tab[data-categoria="${categoria}"]`);
            activeTab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            
            // Reiniciar animaciones para nuevas tarjetas
            setTimeout(() => {
                newCategoria.querySelectorAll('.servicio-card:not(.visible)').forEach(card => {
                    observer.observe(card);
                });
            }, 300);
        }
    }
    
    // Función para actualizar estado de botones de navegación
    function actualizarBotonesNavegacion() {
        prevBtn.disabled = categoriaIndex === 0;
        nextBtn.disabled = categoriaIndex === categorias.length - 1;
    }
    
    // Event listeners para tabs principales
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const tabType = tab.getAttribute('data-tab');
            cambiarServicios(tabType);
        });
    });
    
    // Event listeners para tabs de categorías
    categoriaTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const categoria = tab.getAttribute('data-categoria');
            cambiarCategoria(categoria);
        });
    });
    
    // Event listeners para botones de navegación
    prevBtn.addEventListener('click', () => {
        if (categoriaIndex > 0) {
            cambiarCategoria(categorias[categoriaIndex - 1]);
        }
    });
    
    nextBtn.addEventListener('click', () => {
        if (categoriaIndex < categorias.length - 1) {
            cambiarCategoria(categorias[categoriaIndex + 1]);
        }
    });
    
    // Inicializar botones de navegación
    actualizarBotonesNavegacion();
    
    // Scroll reveal para la sección completa
    const serviciosSection = document.querySelector('.servicios');
    const serviciosObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                serviciosSection.classList.add('animated');
            }
        });
    }, { threshold: 0.1 });
    
    serviciosObserver.observe(serviciosSection);
});
    </script>

</body>
</html>