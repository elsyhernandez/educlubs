<?php
require 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') redirect('login.php');
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel - Alumno</title>
  <link rel="stylesheet" href="css/main-modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <header class="main-header">
    <div class="logo">
        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
        <span>EduClubs</span>
    </div>
    <nav>
      <a href="logout.php" class="btn">Cerrar Sesión</a>
    </nav>
  </header>

  <div class="main-container">
    <h2>Selecciona un tipo de club</h2>
    <div class="club-grid">
      <a href="club.php?type=cultural" class="club-card">
        <div>
          <h3><i class="fas fa-palette"></i> Cultural</h3>
          <p>Fotografía, danza, música, teatro...</p>
        </div>
      </a>
      <a href="club.php?type=deportivo" class="club-card">
        <div>
          <h3><i class="fas fa-futbol"></i> Deportivo</h3>
          <p>Fútbol, voleibol, atletismo, etc.</p>
        </div>
      </a>
      <a href="club.php?type=civil" class="club-card">
        <div>
          <h3><i class="fas fa-flag"></i> Civil</h3>
          <p>Banda de guerra, escolta</p>
        </div>
      </a>
      <a href="club.php?type=asesoria" class="club-card">
        <div>
          <h3><i class="fas fa-chalkboard-teacher"></i> Asesoría</h3>
          <p>Matemáticas, inglés, etc.</p>
        </div>
      </a>
    </div>
  </div>
</body>
</html>
