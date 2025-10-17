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
<style>
  body{font-family:Arial;margin:0;display:flex;height:100vh;align-items:center;justify-content:center;background:#f5f7fb;}
  .box{width:380px;padding:30px;background:white;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.08);text-align:center;}
  .btn{display:block;margin:12px auto;padding:12px 20px;border-radius:8px;border:0;cursor:pointer;font-weight:600;}
  .btn.primary{background:#2b6ef6;color:white;}
  .btn.ghost{background:transparent;border:2px solid #2b6ef6;color:#2b6ef6;}
  a.small{display:block;margin-top:8px;color:#666;text-decoration:none;}
</style>
</head>
<body>
  <div class="box">
    <h2>Bienvenido — Clubes</h2>
    <p>Elige una opción</p>
    <a href="register.php" class="btn primary">Crear cuenta</a>
    <a href="login.php" class="btn ghost">Iniciar sesión</a>
  </div>
</body>
</html>
