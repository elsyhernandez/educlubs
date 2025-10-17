<?php
require 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'student') redirect('login.php');
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Panel - Alumno</title>
<style>
body{font-family:Arial;background:#f2f6ff;padding:20px}
.topbar{display:flex;justify-content:space-between;align-items:center;max-width:1000px;margin:0 auto}
.usericon{cursor:pointer;position:relative}
.user-menu{display:none;position:absolute;right:0;top:36px;background:white;border:1px solid #ddd;padding:8px;border-radius:8px}
.usericon:hover .user-menu{display:block}
.container{max-width:1000px;margin:24px auto;text-align:center}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:18px;margin-top:24px}
.card{background:white;padding:18px;border-radius:10px;box-shadow:0 6px 18px rgba(0,0,0,0.06);cursor:pointer}
a.btn{display:inline-block;margin-top:12px;padding:8px 12px;border-radius:8px;border:0;background:#2b6ef6;color:white;text-decoration:none}
</style>
</head>
<body>
  <div class="topbar">
    <div></div>
    <div style="font-weight:700">Bienvenido, <?=htmlspecialchars($user['username'])?></div>
    <div class="usericon">
      <div style="width:40px;height:40px;border-radius:50%;background:#2b6ef6;color:white;display:flex;align-items:center;justify-content:center"><?=strtoupper($user['username'][0] ?? 'U')?></div>
      <div class="user-menu">
        <div style="padding:6px 10px"><?=htmlspecialchars($user['user_id'])?></div>
        <a href="logout.php" style="display:block;padding:6px 10px;color:#b00;text-decoration:none">Cerrar sesión</a>
      </div>
    </div>
  </div>

  <div class="container">
    <h2>Selecciona un tipo de club</h2>
    <div class="grid">
      <a class="card" href="club.php?type=cultural"><h3>Cultural</h3><p>Fotografía, danza, música, teatro...</p></a>
      <a class="card" href="club.php?type=deportivo"><h3>Deportivo</h3><p>Fútbol, voleibol, atletismo, etc.</p></a>
      <a class="card" href="club.php?type=civil"><h3>Civil</h3><p>Banda de guerra, escolta</p></a>
      <a class="card" href="club.php?type=asesoria"><h3>Asesoría</h3><p>Matemáticas, inglés, etc.</p></a>
    </div>
  </div>
</body>
</html>
