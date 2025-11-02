<?php
require 'includes/config.php';
if (!isset($_SESSION['user'])) redirect('auth/index.php');

$type = $_GET['type'] ?? '';
$user = $_SESSION['user'];

$lists = [
  'cultural' => ['Fotografía/Video','Danza y baile','Música/Rondalla','Música grupo norteño','Arte manual','Oratoria y declamación','Pintura/Dibujo','Teatro','Creación literaria'],
  'deportivo' => ['Ajedrez','Atletismo','Basquetbol','Defensa personal','Fútbol femenil','Fútbol varonil','Voleibol femenil','Voleibol varonil'],
  'civil' => ['Banda de guerra','Escolta'],
  'asesoria' => ['Matemáticas 1','Matemáticas 2','Matemáticas 3','Matemáticas 4','Inglés']
];

function clubToFile($club) {
  $map = [
    'Fotografía/Video' => 'foto',
    'Danza y baile' => 'danza',
    'Música/Rondalla' => 'rondalla',
    'Música grupo norteño' => 'norteño',
    'Arte manual' => 'artemanual',
    'Oratoria y declamación' => 'oratoria',
    'Pintura/Dibujo' => 'pintura',
    'Teatro' => 'teatro',
    'Creación literaria' => 'literaria',
    'Ajedrez' => 'ajedrez',
    'Atletismo' => 'atletismo',
    'Basquetbol' => 'basquetbol',
    'Defensa personal' => 'defensa',
    'Fútbol femenil' => 'futbol',
    'Fútbol varonil' => 'futvaronil',
    'Voleibol femenil' => 'volifemenil',
    'Voleibol varonil' => 'volivaronil',
    'Banda de guerra' => 'banda',
    'Escolta' => 'escolta',
    'Matemáticas 1' => 'mate1',
    'Matemáticas 2' => 'mate2',
    'Matemáticas 3' => 'mate3',
    'Matemáticas 4' => 'mate4',
    'Inglés' => 'ingles1'
  ];
  return $map[$club] ?? 'club';
}

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
  <title>Clubes - <?=htmlspecialchars(ucfirst($type))?></title>
  <link rel="stylesheet" href="css/main-modern.css">
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f5f7fa, #d7e1ec);
      margin: 0;
      padding: 0;
    }

    .main-header {
      padding: 20px;
      background: #7a1c3a;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .main-header a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
    }

    .main-container {
      padding: 30px;
      text-align: center;
      position: relative;
    }

    .main-container h2 {
      color: #7a1c3a;
      margin-bottom: 30px;
      font-size: 1.8rem;
    }

    .club-list-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 20px;
    }

    .club-list-item {
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      padding: 20px;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .club-list-item:hover {
      transform: scale(1.05);
    }

    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0; top: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      border-radius: 20px;
      padding: 30px;
      width: 90%;
      max-width: 600px;
      position: relative;
      animation: fadeIn 0.3s ease-in-out;
      text-align: center;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .close-btn {
      position: absolute;
      top: 10px; right: 15px;
      font-size: 22px;
      cursor: pointer;
      color: #7a1c3a;
    }

    .btn-vermas {
      background: #7a1c3a;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 20px;
      font-size: 16px;
      text-decoration: none;
    }

    .btn-vermas:hover {
      background: #5e142c;
    }

    /* Cuadros flotantes motivacionales */
    .floating-label {
  position: fixed;
  background: white;
  color: #7a1c3a;
  font-weight: bold;
  padding: 10px 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  animation: floatUp 4s ease-in-out infinite alternate;
  z-index: 100;
  font-size: 0.95rem;
  white-space: nowrap;
}


@keyframes floatUp {
  0%   { transform: translateY(0);     opacity: 0.8; }
  50%  { transform: translateY(-10px); opacity: 1; }
  100% { transform: translateY(0);     opacity: 0.8; }
}


    .floating-label.disciplina {
      top: 60%;
      left: -30px;
      animation-delay: 0s;
    }

    .floating-label.constancia {
      top: 30%;
      right: -30px;
      animation-delay: 1s;
    }

    .floating-label.esfuerzo {
      bottom: 10%;
      left: 10%;
      animation-delay: 2s;
    }

    @keyframes floatUp {
      0% { transform: translateY(0) rotate(-5deg); opacity: 0.8; }
      50% { transform: translateY(-20px) rotate(5deg); opacity: 1; }
      100% { transform: translateY(0) rotate(-5deg); opacity: 0.8; }
    }
  </style>
</head>
<body>
  <header class="main-header">
    <!-- Cuadros motivacionales flotantes -->


    <h1>Clubs de tipo <?=htmlspecialchars(ucfirst($type))?></h1>
    <nav>
      <a href="student_dashboard.php">Volver al Panel</a>
      <a href="auth/logout.php">Cerrar Sesión</a>
    </nav>
  </header>

  <div class="main-container">
    <h2>Selecciona un club para ver detalles</h2>

    
    <div class="club-list-grid">
      <?php foreach($lists[$type] as $club): 
        $id = clubToFile($club); ?>
        <div class="club-list-item" onclick="abrirModal('<?=$id?>')">
          <span><?=htmlspecialchars($club)?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <?php foreach($lists[$type] as $club): 
    $id = clubToFile($club); 
    @include "{$type}/modals/{$id}-modal.php";
  endforeach; ?>

  <script>
    function abrirModal(id) {
      document.getElementById('modal-' + id).style.display = 'flex';
    }

    function cerrarModal(id) {
      document.getElementById('modal-' + id).style.display = 'none';
    }

    window.onclick = function(e) {
      document.querySelectorAll('.modal').forEach(modal => {
        if (e.target === modal) {
          modal.style.display = 'none';
        }
      });
    };
  </script>
</body>
</html>
