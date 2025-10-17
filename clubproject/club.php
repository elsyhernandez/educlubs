<?php
require 'config.php';
if (!isset($_SESSION['user'])) redirect('login.php');
$type = $_GET['type'] ?? '';
$user = $_SESSION['user'];

$lists = [
  'cultural' => ['Fotografía/Video','Danza y baile','Música/Rondalla','Música grupo norteño','Arte manual','Oratoria y declamación','Pintura/Dibujo','Teatro','Creación literaria'],
  'deportivo' => ['Ajedrez','Atletismo','Basquetbol','Defensa personal','Fútbol femenil','Fútbol varonil','Voleibol femenil','Voleibol varonil'],
  'civil' => ['Banda de guerra','Escolta'],
  'asesoria' => ['Matemáticas 1','Matemáticas 2','Matemáticas 3','Inglés']
];

if (!isset($lists[$type])) {
    echo "Tipo inválido. <a href='student_dashboard.php'>Volver</a>";
    exit;
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=htmlspecialchars(ucfirst($type))?></title>
<style>
body{font-family:Arial;background:#f6f8ff;padding:20px}
.container{max-width:900px;margin:30px auto;background:#fff;padding:20px;border-radius:10px}
.list{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}
.item{padding:12px;border-radius:8px;border:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
a.register{background:#2b6ef6;color:white;padding:8px 12px;border-radius:8px;text-decoration:none}
</style>
</head>
<body>
<div class="container">
  <h2><?=htmlspecialchars(ucfirst($type))?></h2>
  <div class="list">
    <?php foreach($lists[$type] as $club): ?>
      <div class="item">
        <div><?=htmlspecialchars($club)?></div>
        <div><a class="register" href="register_club.php?type=<?=urlencode($type)?>&club=<?=urlencode($club)?>">Registrarme</a></div>
      </div>
    <?php endforeach; ?>
  </div>
  <p><a href="student_dashboard.php">Volver</a></p>
</div>
</body>
</html>
