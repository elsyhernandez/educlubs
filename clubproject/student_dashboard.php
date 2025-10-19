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
    <h1>Panel de Alumno</h1>
    <nav>
      <a href="logout.php">Cerrar Sesión</a>
    </nav>
  </header>

  <div class="main-container">
    <h2>Selecciona un tipo de club</h2>
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
      <a href="club.php?type=cultural" style="text-decoration: none; color: inherit;">
        <div style="padding: 20px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; background: white;">
          <h3><i class="fas fa-palette"></i> Cultural</h3>
          <p>Fotografía, danza, música, teatro...</p>
        </div>
      </a>
      <a href="club.php?type=deportivo" style="text-decoration: none; color: inherit;">
        <div style="padding: 20px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; background: white;">
          <h3><i class="fas fa-futbol"></i> Deportivo</h3>
          <p>Fútbol, voleibol, atletismo, etc.</p>
        </div>
      </a>
      <a href="club.php?type=civil" style="text-decoration: none; color: inherit;">
        <div style="padding: 20px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; background: white;">
          <h3><i class="fas fa-flag"></i> Civil</h3>
          <p>Banda de guerra, escolta</p>
        </div>
      </a>
      <a href="club.php?type=asesoria" style="text-decoration: none; color: inherit;">
        <div style="padding: 20px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; background: white;">
          <h3><i class="fas fa-chalkboard-teacher"></i> Asesoría</h3>
          <p>Matemáticas, inglés, etc.</p>
        </div>
      </a>
    </div>
  </div>
</body>
</html>
