<?php
require 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') redirect('login.php');
$user = $_SESSION['user'];

// Filter and Search logic
$search = trim($_GET['search'] ?? '');
$type = trim($_GET['type'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$whereClauses = [];
$params = [];

if ($search !== '') {
    $whereClauses[] = "club_name LIKE ?";
    $params[] = "%$search%";
}
if ($type !== '') {
    $whereClauses[] = "club_type = ?";
    $params[] = $type;
}

$whereSql = '';
if (!empty($whereClauses)) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get total count with filters
$countSql = "SELECT COUNT(*) FROM clubs $whereSql";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$total = (int) $countStmt->fetchColumn();
$totalPages = $total > 0 ? ceil($total / $limit) : 1;

// Get paginated and filtered results
$sql = "SELECT club_id, club_name, creator_name, club_type, created_at FROM clubs $whereSql ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);

// Bind WHERE params
$paramIndex = 1;
foreach ($params as $param) {
    $stmt->bindValue($paramIndex++, $param);
}
// Bind LIMIT and OFFSET params
$stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
$stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);

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
    .btn { padding: 10px 16px; background: var(--primary); color: #fff !important; border: none; border-radius: 8px; cursor: pointer; font-weight:600; text-decoration: none; display: inline-block; text-shadow: 1px 1px 1px rgba(0,0,0,0.1); }
    .btn.alt { background:#007bff;color:#fafafa !important;border:1px solid #e6e9ef; box-shadow:none; }
    .pagination { margin-top: 16px; display:flex; flex-wrap:wrap; gap:8px; }
    .pagination a { padding: 8px 12px; border-radius: 8px; background: #fff; border: 1px solid #e0e7ef; color: var(--primary); text-decoration: none; font-weight:600; }
    .pagination a.active { background-color: var(--primary); color: white; border-color: var(--primary); }
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; }
    .modal.show { display: flex; }
    .modal-panel { background: #fff; border-radius: var(--radius); box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; width: 90%; animation: modal-pop 0.25s ease; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #e6e9ef; }
    .modal-header h3 { margin: 0; }
    .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #888; }
    @keyframes modal-pop { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    form { padding: 20px; }
    .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .field { flex: 1; display: flex; flex-direction: column; }
    .form-label { font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #444; }
    input[type="text"], input[type="password"], select { width: 100%; height: 40px; padding: 0 10px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; }
    .actions { text-align: center; margin-top: 24px; }
    .actions .btn { width: 100%; }
    .btn.small { padding: 6px 10px; font-size: 13px; }
    .filter-controls { display: flex; gap: 10px; margin-bottom: 16px; align-items: center; }
    .filter-controls input[type="search"], .filter-controls select { height: 38px; padding: 0 10px; border-radius: 8px; border: 1px solid #ccc; }
    .filter-controls input[type="search"] { flex: 1; }
    .filter-controls .reset-btn { font-size: 24px; text-decoration: none; color: #888; }
    #editToolbar { display:none; position: sticky; top:72px; z-index: 105; margin-bottom:12px; background: #fff9e6; padding:10px 14px; border:1px solid #ffecb3; border-radius:10px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
    .edit-active { display:flex; align-items:center; gap:12px; }
    .edit-col, .edit-action {display:none;}
    .edit-col{width:36px;}
    tr.row-selected{background:#ffe488; font-weight: 600;}
    .header-actions { display:flex; align-items:center; gap:12px; }
    /* User Icon */
    .usericon { position: relative; }
    .avatar { width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 600; cursor: pointer; }
    .user-menu {
      display: block;
      position: absolute;
      top: 42px;
      right: 0;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
      padding: 8px;
      min-width: 160px;
      z-index: 110;
      opacity: 0;
      visibility: hidden;
      transform: translateY(-10px);
      transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
    }
    .usericon:hover .user-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }
    .user-menu div { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; }
    .user-menu a { padding: 8px 12px; text-decoration: none; display: block; }
    .user-menu a:hover { background: #f5f5f5; }
  </style>
</head>
<body>
 <header>
  <h2>Base de Datos de Clubs</h2>
  <div class="header-actions">
    <button id="toggleEditBtn" class="btn" type="button">Editar</button>
    <a href="teacher_dashboard.php" class="btn alt">&laquo; Volver al Panel</a>
    <div class="usericon">
      <div class="avatar"><?=strtoupper($user['username'][0] ?? 'U')?></div>
      <div class="user-menu">
        <div><?=htmlspecialchars($user['user_id'] ?? '')?></div>
        <a href="logout.php" style="color:#b00; text-decoration: none;">Cerrar sesión</a>
      </div>
    </div>
  </div>
 </header>

  <div class="container">
    <div id="editToolbar">
      <div class="edit-active">
        <strong>Modo edición activo</strong>
        <button id="bulkDeleteBtn" class="btn small">Eliminar seleccionados</button>
        <small style="margin-left:8px;color:#666;">Selecciona filas para eliminar o usa el icono de lápiz para editar.</small>
      </div>
    </div>
    <div class="card">
      <h3>Clubs Registrados</h3>
      <form id="filterForm" class="filter-controls">
        <input type="search" id="searchInput" name="search" placeholder="Buscar por nombre de club..." value="<?= htmlspecialchars($search) ?>">
        <select id="typeFilter" name="type">
          <option value="">Todas las categorías</option>
          <option value="cultural" <?= $type === 'cultural' ? 'selected' : '' ?>>Cultural</option>
          <option value="deportivo" <?= $type === 'deportivo' ? 'selected' : '' ?>>Deportivo</option>
          <option value="civil" <?= $type === 'civil' ? 'selected' : '' ?>>Civil</option>
          <option value="asesoria" <?= $type === 'asesoria' ? 'selected' : '' ?>>Asesorías</option>
        </select>
        <button type="submit" class="btn">Buscar</button>
        <a href="view_clubs.php" class="reset-btn" title="Restablecer filtros">↻</a>
      </form>
      <?php if (empty($clubs)): ?>
        <p>No se encontraron clubs con los filtros seleccionados.</p>
      <?php else: ?>
        <table class="table">
          <thead>
            <tr>
              <th class="edit-col"></th>
              <th>ID del Club</th>
              <th>Nombre del Club</th>
              <th>Creador</th>
              <th>Tipo</th>
              <th>Fecha de Creación</th>
              <th class="edit-action">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($clubs as $club): ?>
              <tr data-id="<?= htmlspecialchars($club['club_id']) ?>" data-name="<?= htmlspecialchars($club['club_name']) ?>">
                <td class="edit-col"><input type="checkbox" class="row-checkbox" data-id="<?= htmlspecialchars($club['club_id']) ?>" data-name="<?= htmlspecialchars($club['club_name']) ?>"></td>
                <td data-col="club_id"><?= htmlspecialchars($club['club_id']) ?></td>
                <td data-col="club_name"><?= htmlspecialchars($club['club_name']) ?></td>
                <td data-col="creator_name"><?= htmlspecialchars($club['creator_name']) ?></td>
                <td data-col="club_type"><?= htmlspecialchars($club['club_type']) ?></td>
                <td><?= htmlspecialchars($club['created_at']) ?></td>
                <td class="edit-action">
                  <button class="btn btn-edit" 
                          style="padding:6px 8px;"
                          data-id="<?= htmlspecialchars($club['club_id']) ?>"
                          data-name="<?= htmlspecialchars($club['club_name']) ?>"
                          data-creator="<?= htmlspecialchars($club['creator_name']) ?>"
                          data-type="<?= htmlspecialchars($club['club_type']) ?>">
                    ✎
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

      <?php
      // Pagination links
      if ($totalPages > 1) {
          echo '<div class="pagination">';
          $queryParams = ['search' => $search, 'type' => $type];
          if ($page > 1) {
              echo '<a href="?' . http_build_query(array_merge($queryParams, ['page' => $page - 1])) . '">&laquo; Anterior</a>';
          }
          for ($i = 1; $i <= $totalPages; $i++) {
              $activeClass = ($i == $page) ? 'active' : '';
              echo '<a href="?' . http_build_query(array_merge($queryParams, ['page' => $i])) . '" class="' . $activeClass . '">' . $i . '</a>';
          }
          if ($page < $totalPages) {
              echo '<a href="?' . http_build_query(array_merge($queryParams, ['page' => $page + 1])) . '">Siguiente &raquo;</a>';
          }
          echo '</div>';
      }
      ?>
    </div>
  </div>

  <!-- Edit Club Modal -->
  <div id="editClubModal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="editTitle">
      <div class="modal-header">
        <h3 id="editTitle">Editar Club</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>
      <form id="editClubForm" novalidate>
        <input type="hidden" id="edit_club_id" name="club_id" value="">
        <div class="form-row">
          <div class="field">
            <label class="form-label">Nombre del Club</label>
            <input id="edit_club_name" name="club_name" type="text" required>
          </div>
        </div>
        <div class="form-row">
          <div class="field">
            <label class="form-label">Creador</label>
            <input id="edit_creator_name" name="creator_name" type="text" required>
          </div>
        </div>
        <div class="form-row">
          <div class="field">
            <label class="form-label">Tipo de Club</label>
            <select id="edit_club_type" name="club_type" required>
              <option value="cultural">Cultural</option>
              <option value="deportivo">Deportivo</option>
              <option value="asesoria">Asesorías</option>
              <option value="civil">Civil</option>
            </select>
          </div>
        </div>
        <div class="actions">
          <button type="submit" class="btn">Guardar Cambios</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const filterForm = document.getElementById('filterForm');
      const searchInput = document.getElementById('searchInput');
      const typeFilter = document.getElementById('typeFilter');

      function applyFilters() {
        const search = searchInput.value.trim();
        const type = typeFilter.value;
        const url = new URL(window.location.href);
        url.searchParams.set('search', search);
        url.searchParams.set('type', type);
        url.searchParams.set('page', '1'); // Reset to first page on new filter
        window.location.href = url.toString();
      }

      // Search on form submit (Enter key or button click)
      filterForm.addEventListener('submit', (e) => {
        e.preventDefault();
        applyFilters();
      });

      // Filter by type immediately on change
      typeFilter.addEventListener('change', () => {
        // We can submit the form which will trigger our submit listener
        filterForm.dispatchEvent(new Event('submit', { cancelable: true }));
      });

      let editMode = false;
      const toggleEditBtn = document.getElementById('toggleEditBtn');
      const editToolbar = document.getElementById('editToolbar');

      function setEditMode(on) {
        editMode = !!on;
        document.querySelectorAll('.edit-col, .edit-action').forEach(c => {
          c.style.display = on ? 'table-cell' : 'none';
        });
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        if (!on) {
          document.querySelectorAll('tr.row-selected').forEach(tr => tr.classList.remove('row-selected'));
        }
        editToolbar.style.display = on ? 'block' : 'none';
        toggleEditBtn.textContent = on ? 'Salir de Edición' : 'Editar';
      }

      if (toggleEditBtn) {
        toggleEditBtn.addEventListener('click', () => setEditMode(!editMode));
      }

      document.addEventListener('change', (e) => {
        const cb = e.target.closest('.row-checkbox');
        if (!cb) return;
        const tr = cb.closest('tr');
        if (tr) tr.classList.toggle('row-selected', cb.checked);
      });

      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;
        document.getElementById('edit_club_id').value = btn.dataset.id || '';
        document.getElementById('edit_club_name').value = btn.dataset.name || '';
        document.getElementById('edit_creator_name').value = btn.dataset.creator || '';
        document.getElementById('edit_club_type').value = btn.dataset.type || '';
        document.getElementById('editClubModal').classList.add('show');
        document.body.style.overflow = 'hidden';
      });

      document.querySelectorAll('.close-modal, .close-btn').forEach(b => b.addEventListener('click', (e) => {
        const panel = e.target.closest('.modal');
        if(panel) panel.classList.remove('show');
        document.body.style.overflow = '';
      }));

      const editForm = document.getElementById('editClubForm');
      if (editForm) {
        editForm.addEventListener('submit', async (ev) => {
          ev.preventDefault();
          const form = new FormData(editForm);
          try {
            const res = await fetch('maestros/editar_club.php', { method: 'POST', body: new URLSearchParams([...form]) });
            const data = await res.json();
            if (data.success) {
              alert('Club actualizado correctamente.');
              const id = form.get('club_id');
              const tr = document.querySelector(`tr[data-id="${id}"]`);
              if (tr) {
                tr.querySelector('td[data-col="club_name"]').textContent = form.get('club_name') || '';
                tr.querySelector('td[data-col="creator_name"]').textContent = form.get('creator_name') || '';
                tr.querySelector('td[data-col="club_type"]').textContent = form.get('club_type') || '';
                const editBtn = tr.querySelector('.btn-edit');
                if (editBtn) {
                  editBtn.dataset.name = form.get('club_name') || '';
                  editBtn.dataset.creator = form.get('creator_name') || '';
                  editBtn.dataset.type = form.get('club_type') || '';
                }
              }
              document.getElementById('editClubModal').classList.remove('show');
              document.body.style.overflow = '';
            } else {
              alert(data.message || 'No se pudo actualizar el club.');
            }
          } catch (err) { console.error(err); alert('Error al guardar los cambios.'); }
        });
      }

      const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
      if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', async () => {
          const selected = document.querySelectorAll('.row-checkbox:checked');
          if (selected.length === 0) {
            return alert('Selecciona al menos un club para eliminar.');
          }
          
          const clubNames = Array.from(selected).map(cb => cb.dataset.name);
          if (!confirm(`¿Estás seguro de que quieres eliminar ${selected.length} club(s)? Esto también eliminará a todos los miembros registrados en este(os) club(es).`)) {
            return;
          }

          try {
            const res = await fetch('maestros/bulk_delete_clubs.php', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ club_names: clubNames })
            });
            const data = await res.json();
            if (data.success) {
              alert('Club(es) eliminado(s) correctamente.');
              selected.forEach(cb => {
                const tr = cb.closest('tr');
                if (tr) tr.remove();
              });
            } else {
              alert(data.message || 'No se pudieron eliminar los clubes.');
            }
          } catch (err) {
            console.error(err);
            alert('Error al procesar la solicitud de eliminación.');
          }
        });
      }
    });
  </script>
</body>
</html>
