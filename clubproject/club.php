<?php
require_once 'includes/student_header.php';

$type = $_GET['type'] ?? '';

$lists = [
  'cultural' => ['Fotografía/Video','Danza y baile','Música/Rondalla','Música grupo norteño','Arte manual','Oratoria y declamación','Pintura/Dibujo','Teatro','Creación literaria'],
  'deportivo' => ['Ajedrez','Atletismo','Basquetbol','Defensa personal','Fútbol femenil','Fútbol varonil','Voleibol femenil','Voleibol varonil'],
  'civil' => ['Banda de guerra','Escolta'],
  'asesoria' => ['Matemáticas 1','Matemáticas 2','Matemáticas 3','Matemáticas 4','Inglés']
];

function getClubIcon($club) {
    $map = [
        'Fotografía/Video' => 'fas fa-camera',
        'Danza y baile' => 'fas fa-female',
        'Música/Rondalla' => 'fas fa-music',
        'Música grupo norteño' => 'fas fa-hat-cowboy',
        'Arte manual' => 'fas fa-paint-brush',
        'Oratoria y declamación' => 'fas fa-microphone',
        'Pintura/Dibujo' => 'fas fa-palette',
        'Teatro' => 'fas fa-theater-masks',
        'Creación literaria' => 'fas fa-book-open',
        'Ajedrez' => 'fas fa-chess',
        'Atletismo' => 'fas fa-running',
        'Basquetbol' => 'fas fa-basketball-ball',
        'Defensa personal' => 'fas fa-user-shield',
        'Fútbol femenil' => 'fas fa-futbol',
        'Fútbol varonil' => 'fas fa-futbol',
        'Voleibol femenil' => 'fas fa-volleyball-ball',
        'Voleibol varonil' => 'fas fa-volleyball-ball',
        'Banda de guerra' => 'fas fa-drum',
        'Escolta' => 'fas fa-flag',
        'Matemáticas 1' => 'fas fa-calculator',
        'Matemáticas 2' => 'fas fa-calculator',
        'Matemáticas 3' => 'fas fa-calculator',
        'Matemáticas 4' => 'fas fa-calculator',
        'Inglés' => 'fas fa-language'
    ];
    return $map[$club] ?? 'fas fa-star'; // Default icon
}

function clubToFile($club) {
  $map = [
    'Fotografía/Video' => 'foto',
    'Danza y baile' => 'danza',
    'Música/Rondalla' => 'rondalla',
    'Música grupo norteño' => 'norteno',
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
<title>Clubes - <?=htmlspecialchars(ucfirst($type))?></title>
<link rel="stylesheet" href="css/main-modern.css?v=<?php echo time(); ?>">

<div class="main-container club-page-container">
    <h2>Clubes de <?=htmlspecialchars(ucfirst($type))?></h2>
    <p class="subtitle">Haz clic en un club para ver más detalles o inscribirte.</p>
    <div class="club-grid<?php if ($type === 'civil') echo ' civil-grid'; ?>">
        <?php foreach($lists[$type] as $club): 
            $id = clubToFile($club); 
            $icon = getClubIcon($club);
            ?>
            <div class="club-card" onclick="abrirModal('<?=$id?>')">
                <i class="<?=$icon?>"></i>
                <h3><?=htmlspecialchars($club)?></h3>
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
