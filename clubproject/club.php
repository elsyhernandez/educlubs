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
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?=htmlspecialchars(ucfirst($type))?></title>
  <link rel="stylesheet" href="css/main-modern.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
  <header class="main-header">
    <h1>Clubs de tipo <?=htmlspecialchars(ucfirst($type))?></h1>
    <nav>
      <a href="student_dashboard.php">Volver al Panel</a>
      <a href="logout.php">Cerrar Sesión</a>
    </nav>
  </header>

  <div class="main-container">
    <h2>Selecciona un club para registrarte</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
      <?php foreach($lists[$type] as $club): ?>
        <div style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center;">
          <span><?=htmlspecialchars($club)?></span>
          <a class="btn" href="register_club.php?type=<?=urlencode($type)?>&club=<?=urlencode($club)?>">Registrarme</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
