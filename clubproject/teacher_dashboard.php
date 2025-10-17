<?php
require 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') redirect('login.php');
$user = $_SESSION['user'];

// consultas por tipo
$sth_cultural = $pdo->prepare("SELECT * FROM club_registrations WHERE club_type = 'cultural' ORDER BY created_at DESC");
$sth_cultural->execute();
$cultural = $sth_cultural->fetchAll(PDO::FETCH_ASSOC);

$sth_deportivo = $pdo->prepare("SELECT * FROM club_registrations WHERE club_type = 'deportivo' ORDER BY created_at DESC");
$sth_deportivo->execute();
$deportivo = $sth_deportivo->fetchAll(PDO::FETCH_ASSOC);

$sth_civil = $pdo->prepare("SELECT * FROM club_registrations WHERE club_type = 'civil' ORDER BY created_at DESC");
$sth_civil->execute();
$civil = $sth_civil->fetchAll(PDO::FETCH_ASSOC);

$sth_ases = $pdo->prepare("SELECT * FROM tutoring_registrations ORDER BY created_at DESC");
$sth_ases->execute();
$asesorias = $sth_ases->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Panel Maestro</title>
<style>
body{font-family:Arial;background:#f5f7fb;padding:20px}
.container{max-width:1100px;margin:0 auto}
.card{background:#fff;padding:12px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06);margin-bottom:18px}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:8px;border-bottom:1px solid #eee;text-align:left}
small.gray{color:#666}
.logout{float:right}
</style>
</head>
<body>
<div class="container">
  <h2>Base de datos de registros <span class="logout"><a href="logout.php">Cerrar sesión</a></span></h2>

  <div class="card">
    <h3>Cultural (<?=count($cultural)?>)</h3>
    <table class="table"><thead><tr><th>Club</th><th>Nombre</th><th>Semestre</th><th>Correo</th><th>Turno</th><th>Registró</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php foreach($cultural as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['club_name'])?></td>
            <td><?=htmlspecialchars($r['paterno'].' '.$r['materno'].' '.$r['nombres'])?></td>
            <td><?=htmlspecialchars($r['semestre'])?></td>
            <td><?=htmlspecialchars($r['correo'])?></td>
            <td><?=htmlspecialchars($r['turno'])?></td>
            <td><?=htmlspecialchars($r['user_id'])?></td>
            <td><small class="gray"><?=htmlspecialchars($r['created_at'])?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Deportivo (<?=count($deportivo)?>)</h3>
    <table class="table"><thead><tr><th>Club</th><th>Nombre</th><th>Semestre</th><th>Correo</th><th>Turno</th><th>Registró</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php foreach($deportivo as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['club_name'])?></td>
            <td><?=htmlspecialchars($r['paterno'].' '.$r['materno'].' '.$r['nombres'])?></td>
            <td><?=htmlspecialchars($r['semestre'])?></td>
            <td><?=htmlspecialchars($r['correo'])?></td>
            <td><?=htmlspecialchars($r['turno'])?></td>
            <td><?=htmlspecialchars($r['user_id'])?></td>
            <td><small class="gray"><?=htmlspecialchars($r['created_at'])?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Civil (<?=count($civil)?>)</h3>
    <table class="table"><thead><tr><th>Club</th><th>Nombre</th><th>Semestre</th><th>Correo</th><th>Turno</th><th>Registró</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php foreach($civil as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['club_name'])?></td>
            <td><?=htmlspecialchars($r['paterno'].' '.$r['materno'].' '.$r['nombres'])?></td>
            <td><?=htmlspecialchars($r['semestre'])?></td>
            <td><?=htmlspecialchars($r['correo'])?></td>
            <td><?=htmlspecialchars($r['turno'])?></td>
            <td><?=htmlspecialchars($r['user_id'])?></td>
            <td><small class="gray"><?=htmlspecialchars($r['created_at'])?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card">
    <h3>Asesorías (<?=count($asesorias)?>)</h3>
    <table class="table"><thead><tr><th>Materia</th><th>Nombre</th><th>Carrera</th><th>Maestro</th><th>Teléfono</th><th>Registró</th><th>Fecha</th></tr></thead>
      <tbody>
        <?php foreach($asesorias as $r): ?>
          <tr>
            <td><?=htmlspecialchars($r['materia'])?></td>
            <td><?=htmlspecialchars($r['paterno'].' '.$r['materno'].' '.$r['nombres'])?></td>
            <td><?=htmlspecialchars($r['carrera'])?></td>
            <td><?=htmlspecialchars($r['maestro'])?></td>
            <td><?=htmlspecialchars($r['telefono'])?></td>
            <td><?=htmlspecialchars($r['user_id'])?></td>
            <td><small class="gray"><?=htmlspecialchars($r['created_at'])?></small></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>
</body>
</html>
