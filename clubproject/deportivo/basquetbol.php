<?php require_once '../includes/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Club de Basquetbol</title>
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../css/modal-styles.css">
   <style>
        /* -------------------- */
        /* Estilos Base */
        /* -------------------- */
        
        :root {
            /* Variables para el Carrusel 3D */
            --num-cards: 8;
            --angle-increment: calc(360deg / var(--num-cards));
            --card-width: 500px;
            --card-image-height: 350px;
            --card-total-height: calc(var(--card-image-height) + 60px);
            --translate-z: 700px;
            --rotation-speed: 0.6s;
            --scene-perspective: 1200px;
            /* Nuevo color guindo para el hover */
            --color-guindo: #7a1c3a;
        }
        
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            color: white;
            background: #701331; /* Fondo superior plano guinda */
            min-height: 100vh;
        }

        /* === CAMBIO SOLICITADO: Ajuste del margen para mover el fondo oscuro más abajo === */
        .dark-background {
            background-color: #1a1a1a; /* Un color oscuro plano y distinto al guinda */
            padding-bottom: 50px; /* Espacio al final de la página */
            margin-top: -50px; /* **Ajuste para bajar el fondo y que no choque con el carrusel** */
            position: relative; /* Necesario para que z-index funcione */
            z-index: 1; /* Asegura que este fondo esté debajo de los botones de navegación del carrusel 3D */
            width: 100%;
            box-sizing: border-box;
        }
        /* El divisor ahora debe tener un margen negativo para 'pegarse' */
        .dark-background .divisor {
            margin-top: 80px; /* Volvemos a un margen positivo para que no se pegue al fondo de golpe */
            margin-bottom: 80px;
        }

        header {
            background: #7a1c3a;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-container img {
            height: 40px;
            width: auto;
            border-radius: 6px;
        }

        .logo-text {
            font-weight: bold;
            font-size: 1.2rem;
        }

        .btn-volver {
            background: white;
            color: #7a1c3a;
            padding: 8px 16px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: bold;
        }

        .titulo {
            text-align: center;
            font-size: 2.5rem;
            margin: 40px 0;
            animation: fadeInUp 1s ease;
        }

        .pregunta {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 10px;
            position: relative;
            animation: typing 3s steps(30, end);
            white-space: nowrap;
            overflow: hidden;
            border-right: 3px solid white;
            width: 17ch;
            margin: auto;
        }

        @keyframes typing {
            from { width: 0; }
            to { width: 17ch; }
        }

        .espera {
            text-align: center;
            font-size: 1rem;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0; }
        }

        .respuesta {
            max-width: 800px;
            margin: 80px auto;
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 12px;
            font-size: 1.3rem;
            animation: slideUp 1s ease;
            position: relative;
            text-align: justify;
            line-height: 1.6;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .cuadro {
            position: absolute;
            width: 200px;
            height: 50px;
            background: rgba(255,255,255,0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.rem;
            animation: flotar 4s infinite alternate ease-in-out;
            font-weight: bold;
            color: #ffd1dc;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }

        .cuadro.estetico { top: -70px; left: -80px; }
        .cuadro.artistico { top: -70px; right: -80px; }
        .cuadro.cultural { bottom: -70px; left: -80px; }
        .cuadro.expresivo { bottom: -70px; right: -80px; }

        @keyframes flotar {
            0% { transform: translate(0, 0); }
            50% { transform: translate(10px, -10px); }
            100% { transform: translate(-10px, 10px); }
        }

        .divisor {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
            margin: 80px auto;
        }

        /* -------------------- */
        /* Carrusel 3D (Se mantiene, solo se ajusta la lógica en JS) */
        /* -------------------- */
        
        .carrusel-container {
            perspective: var(--scene-perspective);
            width: 100%; 
            height: calc(var(--card-total-height) + 80px);
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 40px;
        }

        .carrusel-wrapper {
            width: var(--card-width);
            height: var(--card-total-height);
            position: relative;
            transform-style: preserve-3d; 
            transform: translateZ(calc(var(--translate-z) * -1)) rotateY(0deg);
            transition: transform var(--rotation-speed) ease-in-out; 
        }

        .card {
            position: absolute;
            width: var(--card-width);
            height: var(--card-total-height);
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
            background-color: rgba(51, 51, 51, 0.8);
            overflow: hidden;
            
            top: 0;
            left: 0;
            
            transition: opacity var(--rotation-speed) ease-in-out, 
                        filter var(--rotation-speed) ease-in-out,
                        transform var(--rotation-speed) ease-in-out,
                        box-shadow var(--rotation-speed) ease-in-out;
            
            filter: grayscale(100%) brightness(50%); 
            opacity: 0.7;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            text-align: center;
            transform-origin: center;
            padding-bottom: 10px;
        }
        
        .card img {
            width: 100%;
            height: var(--card-image-height);
            object-fit: cover;
            border-radius: 12px 12px 0 0;
            z-index: 1;
        }
        
        .card .info {
            position: relative;
            z-index: 2;
            padding: 10px 15px;
            background: transparent;
            font-size: 0.95rem;
            font-weight: 500;
            color: #f0f0f0;
        }

        .card.active {
            filter: grayscale(0) brightness(100%);
            opacity: 1;
            transform: translateZ(calc(var(--translate-z) * 0.1)) scale(1.1); 
            z-index: 10;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.9);
            border-color: rgba(255, 255, 255, 0.3);
        }

        /* -------------------- */
        /* Posicionamiento 3D Inicial (8 elementos) */
        /* -------------------- */
        
        .card[data-index="0"] { transform: rotateY(0deg) translateZ(var(--translate-z)); } 
        .card[data-index="1"] { transform: rotateY(calc(1 * var(--angle-increment))) translateZ(var(--translate-z)); } 
        .card[data-index="2"] { transform: rotateY(calc(2 * var(--angle-increment))) translateZ(var(--translate-z)); } 
        .card[data-index="3"] { transform: rotateY(calc(3 * var(--angle-increment))) translateZ(var(--translate-z)); } 
        .card[data-index="4"] { transform: rotateY(calc(4 * var(--angle-increment))) translateZ(var(--translate-z)); } 
        .card[data-index="5"] { transform: rotateY(calc(5 * var(--angle-increment))) translateZ(var(--translate-z)); } 
        .card[data-index="6"] { transform: rotateY(calc(6 * var(--angle-increment))) translateZ(var(--translate-z)); } 
        .card[data-index="7"] { transform: rotateY(calc(7 * var(--angle-increment))) translateZ(var(--translate-z)); } 

        /* -------------------- */
        /* Flechas de Navegación */
        /* -------------------- */
        
        .btn-carrusel {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
            cursor: pointer;
            z-index: 200; /* Asegura que las flechas siempre estén por encima de todo */
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background 0.3s ease;
        }
        .btn-carrusel:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .btn-prev { left: 450px; }
        .btn-next { right: 450px; }

        /* -------------------- */
        /* Estilos del Carrusel de Features */
        /* -------------------- */
        
        .features-container {
            max-width: 1400px;
            width: 90%;
            margin: 60px auto;
            overflow: hidden;
            position: relative;
        }

        .features-wrapper {
            display: flex;
            gap: 20px; 
            padding-bottom: 40px;
            transition: transform 0.5s ease-in-out;
        }

        .feature-card {
            flex: 0 0 calc(50% - 10px);
            max-width: calc(50% - 10px); 
            height: 500px;
            border-radius: 5px;
            padding: 30px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white; 
            position: relative;
            overflow: hidden;
            background: rgba(255,255,255,0.1); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        }
        
        /* -------------------- */
        /* CLASES PARA ANIMACIÓN AL SCROLL (Inicialmente Ocultas) */
        /* -------------------- */

        /* Estado Inicial (Oculto) */
        .titulo.details-animation,
        .feature-card .feature-title,
        .feature-card .feature-description {
            opacity: 0;
            /* Se añaden las transiciones de las letras al cambiar con las flechas */
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        
        .titulo.details-animation {
            transform: scale(0.5) translateY(20px); 
        }

        .feature-card .feature-title {
            transform: translateX(-100%); 
        }

        .feature-card .feature-description {
            transform: translateX(100%); 
        }
        
        /* Keyframes - Se mantienen */
        @keyframes emergeAndGrow {
            0% { opacity: 0; transform: scale(0.5) translateY(20px); filter: blur(5px); }
            70% { opacity: 1; transform: scale(1.05) translateY(-5px); filter: blur(0); }
            100% { opacity: 1; transform: scale(1) translateY(0); filter: blur(0); }
        }

        @keyframes slideInFromLeft {
            from { transform: translateX(-100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideInFromRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }


        /* Estado Animado (Clase agregada por JS al scroll Y al cambio de slide) */
        .dark-background.animate .titulo.details-animation {
            animation: emergeAndGrow 1.5s forwards ease-out;
            animation-delay: 0.1s; 
        }
        
        /* CLASES PARA FORZAR LA ANIMACIÓN CADA VEZ QUE SE CAMBIE DE SLIDE EN FEATURES */
        .feature-card.animate-slide .feature-title {
            animation: slideInFromLeft 0.8s ease-out forwards;
            animation-delay: 0.1s;
        }

        .feature-card.animate-slide .feature-description {
            animation: slideInFromRight 0.8s ease-out forwards;
            animation-delay: 0.3s;
        }
        /* --- FIN: CLASES PARA ANIMACIÓN AL CAMBIO DE SLIDE --- */

        /* Estilos de Fondo Específicos para los 6 cuadros (sin cambios) */

        .card-horario { 
            background-color: #a07a68; 
            --card-index: 1; /* Variable CSS para el retraso */
        }
        .card-ubicacion { 
            background-color: #e0e0e0; 
            color: #333;
            --card-index: 2;
        }
        .card-ubicacion .feature-description { color: #555; }
        
        .card-materiales { 
            background-color: #2b2123; 
            --card-index: 3;
        }
        .card-temas { 
            background-color: #2e8b57;
            --card-index: 4;
        }
        
        .card-instructor { 
            background-color: #7a1c3a; 
            --card-index: 5;
        }
        
        .card-costos { 
            background-color: #4b4b4b;
            --card-index: 6;
        }

        /* Simulación de Contenido Visual (sin cambios) */
        .feature-visual {
            margin-top: auto;
            min-height: 100px; 
            display: flex;
            align-items: flex-end;
            font-size: 16px; 
            font-weight: bold;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .card-ubicacion .feature-visual { color: rgba(0, 0, 0, 0.5); }
        
        /* Controles de Navegación del Carrusel de Features (sin cambios) */

        .features-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            width: 100%;
            position: relative;
            padding: 0 10px; 
            box-sizing: border-box;
        }

        .features-pagination {
            display: flex;
            gap: 8px;
            margin: 0 auto; 
        }

        .feature-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #ccc;
            cursor: pointer;
            transition: all 0.3s;
        }

        .feature-dot.active {
            width: 24px;
            border-radius: 4px;
            background-color: #ffd1dc;
        }

        .features-arrows {
            display: flex;
            gap: 10px;
            margin-left: auto;
        }

        .feature-nav-arrow {
            background: none;
            border: 1px solid #ccc;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            font-size: 18px;
            cursor: pointer;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: background-color 0.3s, border-color 0.3s;
        }

        .feature-nav-arrow:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #ffd1dc;
        }

        /* -------------------- */
        /* Estilos del Botón "Registrarme" y Contenedor */
        /* -------------------- */
        .registro-container {
            /* **NUEVO: Contenedor para centrar el botón en una caja blanca** */
            margin: 60px auto 40px auto; /* Centrar y dar margen vertical */
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .btn-registrar {
            /* **NUEVO: Botón centrado dentro de la caja blanca** */
            background-color: #333; /* Color inicial (o el que prefieras, lo cambiará al guindo) */
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            transition: background-color 0.3s ease, transform 0.1s;
            margin: 60px auto 40px auto; /* Centrar y dar margen vertical */
            text-align: center;
        }

        .btn-registrar:hover,
        .btn-registrar:active {
            /* **NUEVO: Cambio a color guindo al pasar el mouse o picar** */
            background-color: var(--color-guindo); /* Color guindo */
            transform: scale(1.05); /* Pequeño efecto al hacer hover/click */
        }
        
        /* Estilos del Modal (sin cambios) */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.6); }
        .modal-content { background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 10px; position: relative; color: #333; }
        .close-button { color: #aaa; float: right; font-size: 28px; font-weight: bold; }
        .close-button:hover,
        .close-button:focus { color: black; text-decoration: none; cursor: pointer; }
        .modal-iframe { width: 100%; height: 400px; border: none; }

        /* -------------------- */
        /* Media Queries (Diseño Responsivo) */
        /* -------------------- */
        @media (max-width: 1200px) {
            :root {
                --card-width: 400px; /* Reducir tamaño de tarjeta */
                --translate-z: 550px; /* Reducir distancia de traducción */
                --card-image-height: 280px;
            }
            .btn-prev { left: 50px; } /* Ajustar flechas más cerca del borde */
            .btn-next { right: 50px; }
        }

        @media (max-width: 992px) {
             :root {
                --card-width: 350px; 
                --translate-z: 450px; 
                --card-image-height: 250px;
            }
            .btn-prev { left: 20px; }
            .btn-next { right: 20px; }

            .cuadro { width: 150px; height: 40px; font-size: 0.9rem; }
            .cuadro.estetico { top: -60px; left: -20px; }
            .cuadro.artistico { top: -60px; right: -20px; }
            .cuadro.cultural { bottom: -60px; left: -20px; }
            .cuadro.expresivo { bottom: -60px; right: -20px; }
        }

        @media (max-width: 768px) {
            .titulo { font-size: 2rem; }
            .pregunta { font-size: 1.5rem; }
            .respuesta { margin: 60px auto; font-size: 1.1rem; }

            /* Carrusel 3D */
            :root {
                --card-width: 80vw; /* Ocupa el 80% del ancho de la vista */
                --card-image-height: 50vw; /* Altura relativa al ancho para mantener proporción */
                --translate-z: 300px; 
                --scene-perspective: 800px;
            }
            .btn-prev { left: 10px; width: 40px; height: 40px; font-size: 20px;}
            .btn-next { right: 10px; width: 40px; height: 40px; font-size: 20px;}
            
            /* Carrusel de Features pasa a una sola columna */
            .features-wrapper {
                flex-wrap: nowrap; /* Asegura que no se rompan las tarjetas */
            }
            .feature-card {
                flex: 0 0 100%; /* Ocupa el 100% del contenedor */
                max-width: 100%;
                height: 400px;
            }
            /* Se ocultan las flechas para el carrusel de features en móvil (opcional, se puede dejar) */
            .features-arrows {
                display: none; 
            }
            .features-pagination {
                 margin: 0 auto; /* Centrar puntos */
            }
            .features-controls {
                 justify-content: center;
                 padding: 0;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo-container">
        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo EduClubs">
        <span class="logo-text">Edu clubs</span>
    </div>
    <a href="../club.php?type=deportivo" class="btn-volver">Volver a clubes deportivo</a>
</header>

<h1 class="titulo">Bienvenidos al Club de Basquetbol</h1>

<div class="pregunta">¿Qué es Basquetbol?</div>
<div class="espera">...</div>

<div class="respuesta">
    Es un **juego de estrategia** y concentración, considerado un deporte mental, que simula una batalla entre dos ejércitos. Desarrolla la **lógica**, la **planificación** y la **toma de decisiones** bajo presión.
    <div class="cuadro estetico">Táctica</div>
    <div class="cuadro artistico">Estrategia</div>
    <div class="cuadro cultural">Lógica</div>
    <div class="cuadro expresivo">Concentración</div>
</div>

<div class="carrusel-container">
    
    <button class="btn-carrusel btn-prev">
        <i class="fas fa-chevron-left"></i>
    </button>

    <div class="carrusel-wrapper" id="carrusel">
        <div class="card" data-index="0">
            <img src="../assets/images/danza1.jpg" alt="Ajedrez: Aperturas">
            <div class="info">Aperturas Clave</div>
        </div>
        <div class="card" data-index="1">
            <img src="../assets/images/danza2.jpg" alt="Torneo Local de Ajedrez">
            <div class="info">Torneo Local 2024</div>
        </div>
        <div class="card" data-index="2">
            <img src="../assets/images/danza4.jpg" alt="Estudio de Finales">
            <div class="info">Análisis de Finales</div>
        </div>
        <div class="card" data-index="3">
            <img src="../assets/images/danza5.jpg" alt="Sesión de Táctica">
            <div class="info">Resolución de Táctica</div>
        </div>
        <div class="card" data-index="4">
            <img src="../assets/images/danza6.jpg" alt="Ajedrez Rápido">
            <div class="info">Práctica de Blitz</div>
        </div>
        <div class="card" data-index="5">
            <img src="../assets/images/danza7.jpg" alt="Partida Simultánea">
            <div class="info">Partida Simultánea</div>
        </div>
        <div class="card" data-index="6">
            <img src="../assets/images/danza8.jpg" alt="Clase de Estrategia">
            <div class="info">Lecciones de Estrategia</div>
        </div>
        <div class="card" data-index="7">
            <img src="../assets/images/danza9.jpg" alt="Torneo de Clausura">
            <div class="info">Torneo de Clausura</div>
        </div>
    </div>

    <button class="btn-carrusel btn-next">
        <i class="fas fa-chevron-right"></i>
    </button>
</div>

<div class="dark-background" id="bottomSection">
    
    <hr class="divisor">

    <h2 class="titulo details-animation">Detalles y Horarios del Club</h2>

    <div class="features-container">
        <div class="features-wrapper" id="featuresWrapper">
            <div class="feature-card card-horario">
                <div class="content">
                    <h3 class="feature-title"><i class="far fa-clock"></i> Horario de Práctica</h3>
                    <p class="feature-description">Las sesiones regulares son los **sábados de 9:30 am a 12:00 pm**. Es esencial la puntualidad.</p>
                </div>
                <div class="feature-visual">
                    <p>9:30 AM | Sábados</p>
                </div>
            </div>

            <div class="feature-card card-ubicacion">
                <div class="content">
                    <h3 class="feature-title"><i class="fas fa-map-marker-alt"></i> Ubicación del Aula</h3>
                    <p class="feature-description">Nos reunimos en el **Aula 18**, un espacio tranquilo y adecuado para la concentración requerida.</p>
                </div>
                <div class="feature-visual">
                    <p>Aula 18 (Edificio Central)</p>
                </div>
            </div>

            <div class="feature-card card-materiales">
                <div class="content">
                    <h3 class="feature-title"><i class="fas fa-book-open"></i> Materiales Requeridos</h3>
                    <p class="feature-description">Solo se requiere un cuaderno para anotaciones (libreta de jugadas) y muchas ganas de aprender. **Los tableros son provistos por el club**.</p>
                </div>
                <div class="feature-visual">
                    <p>Cuaderno y Pluma</p>
                </div>
            </div>

            <div class="feature-card card-temas">
                <div class="content">
                    <h3 class="feature-title"><i class="fas fa-chess-pawn"></i> Temario Principal</h3>
                    <p class="feature-description">Cubrimos: **Aperturas** (Italiana, Siciliana), **Táctica**, **Estrategia** de medio juego y **Finales básicos**.</p>
                </div>
                <div class="feature-visual">
                    <p>Aperturas | Táctica | Finales</p>
                </div>
            </div>
            
            <div class="feature-card card-instructor">
                <div class="content">
                    <h3 class="feature-title"><i class="fas fa-user-tie"></i> Instructor del Club</h3>
                    <p class="feature-description">El club está a cargo del instructor **JJ Coello**, con experiencia en torneos y enseñanza de niveles inicial a avanzado.</p>
                </div>
                <div class="feature-visual">
                    <p>JJ Coello</p>
                </div>
            </div>

            <div class="feature-card card-costos">
                <div class="content">
                    <h3 class="feature-title"><i class="fas fa-trophy"></i> Nivel y Torneos</h3>
                    <p class="feature-description">Aceptamos todos los niveles. Los miembros avanzados pueden participar en torneos **inter-escolares** y **locales**.</p>
                </div>
                <div class="feature-visual">
                    <p>Principiante a Avanzado</p>
                </div>
            </div>
        </div>

        <div class="features-controls">
            <div class="features-pagination" id="featuresPaginationDots">
                <span class="feature-dot active" data-slide="0"></span>
                <span class="feature-dot" data-slide="1"></span>
                <span class="feature-dot" data-slide="2"></span> 
            </div>
            <div class="features-arrows">
                <button class="feature-nav-arrow left-arrow" id="featuresPrevBtn">❮</button>
                <button class="feature-nav-arrow right-arrow" id="featuresNextBtn">❯</button>
            </div>
        </div>
    </div>
    
    <div class="registro-container">
        <button class="btn-registrar" id="btn-registrar">Registrarme</button>
    </div>
</div>

<?php include '../includes/modals/registration_modal.php'; ?>

<script>
    // Definir variables globales para que el modal pueda acceder a ellas
    const clubType = 'deportivo';
    const clubName = 'Basquetbol';

document.addEventListener('DOMContentLoaded', function() {
    // Variables globales para el Carrusel 3D
    const carousel = document.getElementById('carrusel');
    const cards = Array.from(carousel.children);
    const numCards = cards.length;
    const angleIncrement = 360 / numCards; 
    let currentCardIndex = 0; 
    let currentRotation = 0; 
    let autoRotateInterval;

    function updateCarousel() {
        currentRotation = -(currentCardIndex * angleIncrement); 
        carousel.style.transform = `translateZ(calc(var(--translate-z) * -1)) rotateY(${currentRotation}deg)`;
        cards.forEach((card, index) => {
            card.classList.toggle('active', index === currentCardIndex);
        });
    }

    function moverCarrusel(dir) {
        resetAutoRotate(); 
        currentCardIndex = (currentCardIndex + dir + numCards) % numCards;
        updateCarousel();
    }
    
    document.querySelector('.btn-prev').addEventListener('click', () => moverCarrusel(-1));
    document.querySelector('.btn-next').addEventListener('click', () => moverCarrusel(1));

    cards.forEach((card, index) => {
        card.addEventListener('click', () => {
            resetAutoRotate();
            currentCardIndex = index;
            updateCarousel();
        });
    });

    function autoRotate() {
        currentCardIndex = (currentCardIndex + 1) % numCards;
        updateCarousel();
    }

    function resetAutoRotate() {
        clearInterval(autoRotateInterval);
        autoRotateInterval = setInterval(autoRotate, 5000); 
    }
    
    // Lógica del Carrusel de Features
    const featuresWrapper = document.getElementById('featuresWrapper');
    const prevBtnFeatures = document.getElementById('featuresPrevBtn');
    const nextBtnFeatures = document.getElementById('featuresNextBtn');
    const paginationDotsFeatures = document.getElementById('featuresPaginationDots');
    const featureCards = document.querySelectorAll('.feature-card');
    const cardsPerView = window.innerWidth <= 768 ? 1 : 2;
    let currentFeatureSlide = 0;
    const totalSlides = Math.ceil(featureCards.length / cardsPerView);

    function updateFeaturesCarousel() {
        const containerWidth = featuresWrapper.parentElement.offsetWidth;
        const gap = 20;
        const offset = -currentFeatureSlide * (containerWidth + gap) / cardsPerView;
        featuresWrapper.style.transform = `translateX(${offset}px)`;

        document.querySelectorAll('.feature-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentFeatureSlide);
        });

        featureCards.forEach(card => card.classList.remove('animate-slide'));
        const startCardIndex = currentFeatureSlide * cardsPerView;
        for (let i = 0; i < cardsPerView; i++) {
            if (featureCards[startCardIndex + i]) {
                featureCards[startCardIndex + i].classList.add('animate-slide');
            }
        }
    }

    const moveFeaturesCarousel = (dir) => {
        currentFeatureSlide = (currentFeatureSlide + dir + totalSlides) % totalSlides;
        updateFeaturesCarousel();
    };

    prevBtnFeatures.addEventListener('click', () => moveFeaturesCarousel(-1));
    nextBtnFeatures.addEventListener('click', () => moveFeaturesCarousel(1));

    paginationDotsFeatures.addEventListener('click', (event) => {
        if (event.target.classList.contains('feature-dot')) {
            currentFeatureSlide = parseInt(event.target.dataset.slide);
            updateFeaturesCarousel();
        }
    });

    // Lógica de Intersección para animación al scroll
    const bottomSection = document.getElementById('bottomSection');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                updateFeaturesCarousel(); 
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    observer.observe(bottomSection);

    // Inicialización
    updateCarousel();
    resetAutoRotate();
    window.addEventListener('resize', updateFeaturesCarousel);
});
</script>

</body>
</html>
