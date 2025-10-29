<?php
require 'includes/config.php';
if (!isset($_SESSION['user'])) redirect('auth/index.php');
$type = $_GET['type'] ?? '';
$user = $_SESSION['user'];

$lists = [
  'cultural' => ['Fotografía/Video','Danza y baile','Música/Rondalla','Música grupo norteño','Arte manual','Oratoria y declamación','Pintura/Dibujo','Teatro','Creación literaria'],
  'deportivo' => ['Ajedrez','Atletismo','Basquetbol','Defensa personal','Fútbol femenil','Fútbol varonil','Voleibol femenil','Voleibol varonil'],
  'civil' => ['Banda de guerra','Escolta'],
  'asesoria' => ['Matemáticas 1','Matemáticas 2','Matemáticas 3','Inglés']
];

if (!isset($lists[$type])) {
    echo "Tipo inválido. <a href='student/dashboard.php'>Volver</a>";
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
    <div class="logo">
        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258" style="height: 50px; margin-right: 15px;">
<img src="admin/assets/img/logo1.png" alt="Logo EduClubs" style="height: 80px; margin-right: 15px;">
    </div>
    <h1>Clubs de tipo <?=htmlspecialchars(ucfirst($type))?></h1>
    <nav>
      <a href="student/dashboard.php">Volver al Panel</a>
      <a href="auth/logout.php">Cerrar Sesión</a>
    </nav>
  </header>

  <div class="main-container fade-in-content">
    <h2>Selecciona un club para registrarte</h2>
    <div class="club-list-grid">
      <?php foreach($lists[$type] as $club): ?>
        <div class="club-list-item">
          <span><?=htmlspecialchars($club)?></span>
          <a class="btn" href="actions/register_club.php?type=<?=urlencode($type)?>&club=<?=urlencode($club)?>">Registrarme</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</body>
</html>
