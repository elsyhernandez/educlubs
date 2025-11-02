<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Club de Danza y Baile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(to bottom, #7a1c3a, #2c2c2c);
      color: white;
    }

    header {
      background: #7a1c3a;
      padding: 20px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo {
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
      font-size: 1.3rem;
      margin-bottom: 10px;
      position: relative;
      animation: typing 3s steps(30, end);
      white-space: nowrap;
      overflow: hidden;
      border-right: 3px solid white;
      width: 15ch;
      margin: auto;
    }

    @keyframes typing {
      from { width: 0; }
      to { width: 15ch; }
    }

    .espera {
      text-align: center;
      font-size: 2rem;
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
      padding: 10px;
      border-radius: 12px;
      font-size: 1rem;
      animation: slideUp 1s ease;
      position: relative;
    }

    @keyframes slideUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .cuadro {
      position: absolute;
      width: 100px;
      height: 30px;
      background: rgba(255,255,255,0.1);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.8rem;
      animation: flotar 4s infinite alternate ease-in-out;
    }

    .cuadro.estetico { top: -40px; left: -60px; }
    .cuadro.artistico { top: -40px; right: -60px; }
    .cuadro.cultural { bottom: -40px; left: -60px; }
    .cuadro.expresivo { bottom: -40px; right: -60px; }

    @keyframes flotar {
      0% { transform: translate(0, 0); }
      50% { transform: translate(10px, -10px); }
      100% { transform: translate(-10px, 10px); }
    }

    /* Carrusel */
    .carrusel-wrapper {
      position: relative;
      overflow: hidden;
      margin: 70px auto;
      max-width: 1000px;
    }

    .carrusel {
      display: flex;
      transition: transform 0.5s ease;
    }

    .card {
      width: 400px;
      margin:  15px;
      background: rgba(255,255,255,0.1);
      border-radius: 12px;
      padding: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.3);
      transform: rotate(-3deg);
      transition: transform 0.3s ease;
      flex-shrink: 0;
    }

    .card:hover {
      transform: rotate(0deg) scale(1.05);
    }

    .card img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 8px;
    }

    .card .info {
      font-size: 0.85rem;
    }

    /* Botones invisibles */
    .btn-carrusel {
      position: absolute;
      top: 0;
      bottom: 0;
      width: 50px;
      cursor: pointer;
      z-index: 2;
    }

    .btn-prev {
      left: 0;
    }

    .btn-next {
      right: 0;
    }

    /* Botón de registro */
    .registro-container {
      text-align: center;
      margin: 40px 0;
    }

    .btn-registrar {
      background: white;
      color: #7a1c3a;
      padding: 12px 24px;
      border-radius: 30px;
      text-decoration: none;
      font-weight: bold;
      font-size: 1rem;
      box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .info-club {
  max-width: 1000px;
  margin: 60px auto;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.info-box {
  display: flex;
  align-items: center;
  gap: 30px;
  flex-wrap: wrap;
}

.info-box img {
  width: 300px;
  height: 200px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.info-box .texto {
  flex: 1;
  min-width: 250px;
}

.info-box h3 {
  margin-top: 0;
  font-size: 1.4rem;
  color: #ffd1dc;
}

.info-box p {
  font-size: 1rem;
  line-height: 1.6;
}

.info-box.invertido {
  flex-direction: row-reverse;
}

.info-box.vertical {
  flex-direction: column;
  text-align: center;
}
.divisor {
  border: none;
  height: 2px;
  background: rgba(255,255,255,0.3);
  margin: 60px auto;
  max-width: 80%;
}

.info-club {
  max-width: 1000px;
  margin: 60px auto;
  display: flex;
  flex-direction: column;
  gap: 40px;
}

.info-box {
  display: flex;
  align-items: center;
  gap: 30px;
  flex-wrap: wrap;
}

.info-box img {
  width: 400px;
  height: 250px;
  object-fit: cover;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
}

.info-box .texto {
  flex: 1;
  min-width: 250px;
}

.info-box h3 {
  margin-top: 0;
  font-size: 1.4rem;
  color: #ffd1dc;
}

.info-box p {
  font-size: 1rem;
  line-height: 2.6;
}

.info-box.invertido {
  flex-direction: row-reverse;
}
.logo-container {
  display: flex;
  align-items: center;
  gap: 12px;
}

.logo-container img {
  height: 80px;
  width: auto;
  border-radius: 6px;
}

.logo-text {
  font-weight: bold;
  font-size: 1.2rem;
}

  </style>
</head>
<body>

  <header>
  <div class="logo-container">
    <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo EduClubs">
    <span class="logo-text">Edu clubs</span>
  </div>
  <a href="../club.php?type=cultural" class="btn-volver">Volver a clubes culturales</a>
</header>


  <h1 class="titulo">Bien Venidos  al Club de Fotografia y Video</h1>

  <div class="pregunta">¿Qué es Fotografia?</div>
  <div class="espera">...</div>

  <div class="respuesta">
     son formas de expresión visual que capturan momentos, emociones e historias a través de
      imágenes fijas o en movimiento. Aunque comparten herramientas y principios similares, 
      cada una tiene su enfoque y propósito.
    <div class="cuadro estetico">Composición</div>
    <div class="cuadro artistico">Luz</div>
    <div class="cuadro cultural">Enfoque</div>
    <div class="cuadro expresivo">Estilos</div>
  </div>

  <!-- Carrusel con botones -->
  <div class="carrusel-wrapper">
    <div class="btn-carrusel btn-prev" onclick="moverCarrusel(-1)"></div>
    <div class="btn-carrusel btn-next" onclick="moverCarrusel(1)"></div>
    <div class="carrusel" id="carrusel">
      <div class="card"><img src="../assets/images/danza1.jpg"><div class="info">Clase abierta de danza moderna</div></div>
      <div class="card"><img src="../assets/images/danza2.jpg"><div class="info">Festival cultural 2024</div></div>
      <div class="card"><img src="../assets/images/danza4.jpg"><div class="info">Ganadores del concurso estatal</div></div>
      <div class="card"><img src="../assets/images/danza5.jpg"><div class="info">Coreografía para evento de primavera</div></div>
      <div class="card"><img src="../assets/images/danza6.jpg"><div class="info">Taller de expresión corporal</div></div>
      <div class="card"><img src="../assets/images/danza7.jpg"><div class="info">Grupo de danza contemporánea</div></div>
      <div class="card"><img src="../assets/images/danza8.jpg"><div class="info">Clase de ritmos latinos</div></div>
      <div class="card"><img src="../assets/images/danza9.jpg"><div class="info">Presentación en el auditorio</div></div>
    </div>
  </div>
<
</div>
<!-- Línea divisoria -->
<hr class="divisor">

<!-- Información del Club -->
<h2 class="titulo">Información del Club</h2>
<div class="info-club">

  <!-- Historia del Club: imagen izquierda -->
  <div class="info-box">
    <img src="../assets/images/danza15.jpg" alt="Historia del club">
    <div class="texto">
      <h3>Horarios de ensayo </h3>
      <p>Normalmente  sabados de 9:30am a 12:00pm en el aula 18</p>
    </div>
  </div>

  <!-- Actividades: imagen derecha -->
  <div class="info-box invertido">
    <img src="../assets/images/danza13.jpg" alt="Actividades del club">
    <div class="texto">
      <h3>Materiales</h3>
      <p>Uso de cámaras (fotográficas o de video)

Dominio de la luz y la composición

Capacidad de transmitir emociones e ideas

Herramientas digitales para edición (Photoshop, Premiere, etc.)
      </p>
    </div>
  </div>

  <!-- Valores: imagen izquierda -->
  <div class="info-box">
    <img src="../assets/images/danza9.jpg" alt="Valores del club">
    <div class="texto">
      <h3>Instructor</h3>
      <p>JJ coello</p>
    </div>
  </div>

</div>

  <!-- Botón de registro -->
  <div class="registro-container">
     <a class="btn-registrar" href="../actions/register_club.php?type=cultural&club=Fotografia y Video">Registrarme</a>
  </div>

 <script>
  const carrusel = document.getElementById('carrusel');
  const totalCards = 8;
  const visibleCards = 3;
  const cardWidth = 320; // ancho de cada tarjeta + margen
  let index = 0;

  function moverCarrusel(direccion) {
    index += direccion;
    if (index < 0) index = 0;
    if (index > totalCards - visibleCards) index = totalCards - visibleCards;
    carrusel.style.transform = `translateX(-${index * cardWidth}px)`;
  }

  // Desplazamiento automático cada 5 segundos
  setInterval(() => {
    index++;
    if (index > totalCards - visibleCards) index = 0;
    carrusel.style.transform = `translateX(-${index * cardWidth}px)`;
  }, 5000);
</script>
