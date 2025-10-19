<?php
// index.php
require 'config.php';
if (isset($_SESSION['user'])) {
    // redirige según rol
    if ($_SESSION['user']['role'] === 'teacher') redirect('teacher_dashboard.php');
    redirect('student_dashboard.php');
}
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Clubes - Inicio</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="css/welcome.css">
</head>
<body>
  <div class="welcome-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="welcome-container">
      <h2><i class="fas fa-graduation-cap"></i> EduClubs</h2>
      <p>Gestiona y explora clubes escolares de forma fácil y centralizada.</p>
      <div class="welcome-actions">
        <a href="login.php" class="welcome-btn">Iniciar sesión</a>
        <a href="register.php" class="welcome-btn primary">Crear cuenta</a>
      </div>
    </div>
  </div>
</body>
</html>
