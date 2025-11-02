<?php
require 'includes/config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') redirect('auth/index.php');
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel - Alumno</title>
  <link rel="stylesheet" href="css/student-dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
   <style>
    .main-header .logo {
        display: flex;
        align-items: center;
    }

    .main-header .logo img {
        height: 50px;
        margin-right: 15px;
    }

    .main-header .logo span {
        font-size: 24px;
        font-weight: 700;
        color: #fff;
    }
:root{
      --primary-color: #4D0011;
      --secondary-color: #62152d;
      --accent-color: #952f57;
      --primary: var(--secondary-color);
      --button-bg: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
      --button-hover-bg: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
      --glass2: #4D0011d7;
    }
    header { background: var(--glass2); backdrop-filter: blur(6px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px 24px; position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; }
    .header-actions { display:flex; align-items:center; gap:12px; }
    .btn { 
        padding: 10px 20px; 
        background: var(--button-bg); 
        color: #fff; 
        border: none; 
        border-radius: 10px; 
        cursor: pointer; 
        font-weight:600; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        text-decoration: none; 
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        
    }
    .btn.transparent {
        background: transparent;
        box-shadow: none;
    }
    .btn.transparent:hover {
       
        transform: translateY(-4px);
        box-shadow: none;
    }
    .usericon { position: relative; }
    .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; cursor: pointer; }
    .user-menu {
      display: block;
      position: absolute;
      top: 42px;
      right: 0;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      padding: 8px;
      min-width: 160px;
      z-index: 110;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    .usericon:hover .user-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .user-menu div { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; color: #333; }
    .user-menu a { padding: 8px 12px; text-decoration: none; display: block; color: #333; }
    .user-menu a:hover { background: #f5f5f5; }
    .user-menu a.logout { color: #d93025; font-weight: 500; }
    a.btn { text-decoration: none; }
    body {
        background-image: url('assets/images/fondo.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
    }
  </style>
</head>
<body>
  <header class="main-header">
    <div class="logo">
        <img src="admin/assets/img/logo.png" alt="Logo EduClubs" style="height: 70px; margin-right: 15px;">
        <img src="https://cbtis258.edu.mx/wp-content/uploads/2024/08/cbtis258-logo.png" alt="Logo CBTis 258" style="height: 70px; margin-right: 15px;">
        <span>EduClubs - Panel de Alumno</span>
    </div>
    <div class="header-actions">
        <a href="student_clubs.php" class="btn transparent">Mis Clubs</a>
        <div class="usericon">
            <div class="avatar"><?=strtoupper($user['username'][0] ?? 'U')?></div>
            <div class="user-menu">
                <div><?=htmlspecialchars($user['user_id'] ?? '')?></div>
                <a href="auth/logout.php" class="logout">Cerrar sesión</a>
            </div>
        </div>
    </div>
  </header>

  <div class="main-container">
    <h2>Selecciona un tipo de club</h2>
    <div class="club-grid">
      <a href="club.php?type=cultural" class="club-card">
        <h3><i class="fas fa-palette"></i>Cultural</h3>
        <p>Explora tu creatividad y talento.</p>
      </a>
      <a href="club.php?type=deportivo" class="club-card">
        <h3><i class="fas fa-futbol"></i>Deportivo</h3>
        <p>Participa en deportes y mantente activo.</p>
      </a>
      <a href="club.php?type=civil" class="club-card">
        <h3><i class="fas fa-flag"></i>Civil</h3>
        <p>Fomenta valores cívicos y patriotismo.</p>
      </a>
      <a href="club.php?type=asesoria" class="club-card">
        <h3><i class="fas fa-chalkboard-teacher"></i>Asesorías</h3>
        <p>Refuerza tus conocimientos académicos.</p>
      </a>
    </div>
  </div>
</body>
</html>
