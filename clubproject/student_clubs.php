<?php
require 'includes/config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') redirect('auth/index.php');
$user = $_SESSION['user'];

// Fetch clubs the student is registered in
$stmt = $pdo->prepare("
    SELECT cr.club_name, cr.club_type, cr.created_at AS registration_date
    FROM club_registrations cr
    WHERE cr.user_id = ?
    UNION ALL
    SELECT tr.materia AS club_name, 'asesoria' AS club_type, tr.created_at AS registration_date
    FROM tutoring_registrations tr
    WHERE tr.user_id = ?
    ORDER BY registration_date DESC
");
$stmt->execute([$user['user_id'], $user['user_id']]);
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Mis Clubs</title>
  <link rel="stylesheet" href="css/main-modern.css">
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
        <span>EduClubs - Mis Clubs</span>
    </div>
    <div class="header-actions">
        <a href="student_dashboard.php" class="btn transparent">Volver al Panel</a>
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
    <h2>Clubs en los que estoy registrado</h2>
    <?php if (empty($clubs)): ?>
      <p>Aún no te has registrado en ningún club.</p>
    <?php else: ?>
      <div class="table-container">
        <table class="styled-table">
          <thead>
            <tr>
              <th>Nombre del Club</th>
              <th>Tipo</th>
              <th>Fecha de Registro</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clubs as $club): ?>
              <tr>
                <td><?= htmlspecialchars($club['club_name']) ?></td>
                <td><?= htmlspecialchars(ucfirst($club['club_type'])) ?></td>
                <td><?= htmlspecialchars($club['registration_date']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
