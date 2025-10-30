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
</head>
<body>
  <header class="main-header">
    <h1>Mis Clubs</h1>
    <nav>
      <a href="student/dashboard.php">Volver al Panel</a>
      <a href="auth/logout.php">Cerrar Sesión</a>
    </nav>
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
