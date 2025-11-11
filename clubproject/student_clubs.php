<?php
require_once 'includes/student_header.php';

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
<title>Mis Clubs</title>
<link rel="stylesheet" href="css/main-modern.css?v=<?php echo time(); ?>">

  <div class="main-container">
    <h2>Clubs en los que estoy registrado</h2>
    <?php if (empty($clubs)): ?>
      <p>Aún no te has registrado en ningún club.</p>
    <?php else: ?>
      <div class="club-grid">
        <?php foreach ($clubs as $club): ?>
          <div class="club-card registered-club-card">
            <h3><?= htmlspecialchars($club['club_name']) ?></h3>
            <p><strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($club['club_type'])) ?></p>
            <p><small>Fecha de Registro: <?= htmlspecialchars($club['registration_date']) ?></small></p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
