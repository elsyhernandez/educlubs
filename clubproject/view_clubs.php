<?php
require 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') redirect('login.php');

// Pagination logic
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Get total count
$countStmt = $pdo->query("SELECT COUNT(*) FROM clubs");
$total = (int) $countStmt->fetchColumn();
$totalPages = $total > 0 ? ceil($total / $limit) : 1;

// Get paginated results
$stmt = $pdo->prepare("SELECT club_id, club_name, creator_name, club_type, created_at FROM clubs ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Base de Datos de Clubs</title>
  <style>
    :root{
      --bg1: #e0f7fa;
      --bg2: #fce4ec;
      --card-bg: rgba(255,255,255,0.9);
      --primary: #007bff;
      --muted: #888;
      --glass: rgba(255,255,255,0.6);
      --radius: 12px;
    }
    body { margin: 0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: linear-gradient(135deg, var(--bg1), var(--bg2)); color: #333; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    header { background: var(--glass); backdrop-filter: blur(6px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px 24px; position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; }
    header h2 { margin: 0; font-size: 22px; color:#143; }
    .container { max-width: 1160px; margin: 0 auto; padding: 28px; }
    .card { background: var(--card-bg); border-radius: var(--radius); box-shadow: 0 8px 24px rgba(0,0,0,0.06); padding: 20px; margin-bottom: 28px; }
    .table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    .table th, .table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
    .table th { background: #fafafa; color: #555; }
    .btn { padding: 10px 16px; background: var(--primary); color: #fff; border: none; border-radius: 8px; cursor: pointer; font-weight:600; text-decoration: none; display: inline-block; }
    .btn.alt { background:#fff;color:#333;border:1px solid #e6e9ef; box-shadow:none; }
    .pagination { margin-top: 16px; display:flex; flex-wrap:wrap; gap:8px; }
    .pagination a { padding: 8px 12px; border-radius: 8px; background: #fff; border: 1px solid #e0e7ef; color: var(--primary); text-decoration: none; font-weight:600; }
    .pagination a.active { background-color: var(--primary); color: white; border-color: var(--primary); }
  </style>
</head>
<body>
 <header>
  <h2>Base de Datos de Clubs</h2>
  <a href="teacher_dashboard.php" class="btn">&laquo; Volver al Panel</a>
 </header>

  <div class="container">
    <div class="card">
      <h3>Clubs Registrados</h3>
      <?php if (empty($clubs)): ?>
        <p>No hay clubs registrados.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th>ID del Club</th>
              <th>Nombre del Club</th>
              <th>Creador</th>
              <th>Tipo</th>
              <th>Fecha de Creación</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clubs as $club): ?>
              <tr>
                <td><?= htmlspecialchars($club['club_id']) ?></td>
                <td><?= htmlspecialchars($club['club_name']) ?></td>
                <td><?= htmlspecialchars($club['creator_name']) ?></td>
                <td><?= htmlspecialchars($club['club_type']) ?></td>
                <td><?= htmlspecialchars($club['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

      <?php if ($totalPages > 1): ?>
        <div class="pagination">
          <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&laquo; Anterior</a>
          <?php endif; ?>
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">Siguiente &raquo;</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</body>
</html>
