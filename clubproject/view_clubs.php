<?php
require 'includes/config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') redirect('auth/index.php');
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="css/main-modern.css">
  <link rel="stylesheet" href="css/menu.css">
   <style>
    .main-header .logo {
        display: flex;
        align-items: center;
    }

    .main-header .logo img {
        height: 50px;
        margin-right: 15px;
    }

    .main-header .logo span {
        font-size: 24px;
        font-weight: 700;
        color: #fff;
    }
:root{
      --primary-color: #4D0011; /* Guinda más oscuro (para header) */
      --secondary-color: #62152d; /* Guinda oscuro (para sub-encabezados) */
      --accent-color: #952f57; /* Guinda medio (para tablas y botones) */
      --bg1: #f9e6e6; /* Fondo claro complementario */
      --bg2: #e8d1d1; /* Fondo claro complementario */
      --card-bg: rgba(255,255,255,0.9);
      --primary: var(--secondary-color);
      --button-bg: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
      --button-hover-bg: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
      --muted: #888;
      --glass: rgba(255,255,255,0.6); 
      --glass2: #4D0011d7; /* Guinda con transparencia */
      --radius: 12px;
      --edit-highlight: rgba(255, 244, 180, 0.7);
      --white-color: #FFFFFF;
    }
    body { margin: 0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #FFFF; color: #333; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    header { background: var(--glass2); backdrop-filter: blur(6px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px 24px; position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; }
    header h2 { margin: 0; font-size: 28px; color:#fff; }
    .header-actions { display:flex; align-items:center; gap:12px; }
    .header-actions .btn {
        margin-right: 1.5rem;
        background: transparent;
        border: 1px solid var(--white-color);
        color: var(--white-color);
        padding: 0.5rem 1rem;
        border-radius: 5px;
        text-decoration: none;
        box-shadow: none;
        transition: all 0.3s ease; /* Revert to original transition to include transform */
        font-size: 16px;
    }

    .header-actions .btn:hover {
        background: var(--white-color);
        color: var(--primary-color);
        /* transform: none; is removed to allow animation from .btn:hover */
        box-shadow: none; /* Keep box-shadow override */
    }
    .btn.alt { background:#fff;color:#333;border:1px solid #e6e9ef; box-shadow:none; }
    a.btn { text-decoration: none; }
    .container { max-width: 1160px; margin: 0 auto; padding: 28px; }
    .card { background: var(--card-bg); border-radius: var(--radius); box-shadow: 0 8px 24px rgba(0,0,0,0.06); padding: 20px; margin-bottom: 28px; transition: transform 0.18s ease; position: relative; }
    .card.editing { border: 2px dashed #ffb703; background: linear-gradient(180deg, var(--edit-highlight), rgba(255,255,255,0.9)); }
    .card h3 { margin-top: 0; font-size: 20px; color: #2b3a42; display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .filter-row { display:flex; gap:10px; align-items:center; margin-top:8px; margin-bottom:10px; }
    .filter-row select, .filter-row input[type="search"] { height:36px; padding:6px 10px; border-radius:8px; border:1px solid #e6e9ef; background:#fff; transition: all 0.2s ease-in-out; }
    .filter-row select:focus, .filter-row input[type="search"]:focus { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
    small.gray { color: var(--muted); }
    .pagination { margin-top: 16px; display:flex; flex-wrap:wrap; gap:8px; }
    .pagination a { padding: 8px 12px; border-radius: 8px; background: #fff; border: 1px solid #e0e7ef; color: var(--primary); text-decoration: none; font-weight:600; }
    .pagination a.active { background-color: var(--primary); color: white; border-color: var(--primary); }
    .btn { 
        padding: 10px 20px; 
        background: var(--button-bg); 
        color: #fff; 
        border: none; 
        border-radius: 10px; 
        cursor: pointer; 
        font-weight:600; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        text-decoration: none; 
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        background: var(--button-hover-bg);
    }
    .btn.small { padding:6px 10px; font-size:13px; }
    .btn-icon {
        padding: 6px 8px;
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 1;
    }
    .styled-table .btn.btn-icon {
        padding: 0 !important;
        font-size: 16px;
        line-height: 1;
    }
    #editToolbar { display:none; position: fixed; top: 120px; left: 50%; transform: translateX(-50%); width: 100%; max-width: 1160px; z-index: 105; margin-bottom:12px; background: #fff9e6; padding:10px 14px; border:1px solid #ffecb3; border-radius:10px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); box-sizing: border-box; }
    .edit-active { display:flex; align-items:center; gap:12px; }
    .edit-col, .edit-action {display:none; text-align: center;}
    .edit-col{width:36px;}
    tr.row-selected{background:#ffe488; font-weight: 600;}
    
    /* Mostrar columnas de edición cuando el modo está activo */
    body.edit-mode-active .edit-col,
    body.edit-mode-active .edit-action {
        display: table-cell;
    }

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
    .user-menu div { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; color: #333; }
    .user-menu a { padding: 8px 12px; text-decoration: none; display: block; color: #333; }
    .user-menu a:hover { background: #f5f5f5; }
    .user-menu a.logout { color: #d93025; font-weight: 500; }

    /* Modal styles */
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 200; backdrop-filter: blur(4px); }
    .modal.show { display: flex; align-items: center; justify-content: center; }
    .modal-panel { 
        background: #fff; 
        border-radius: var(--radius); 
        box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
        max-width: 500px; 
        width: 90%; 
        animation: modal-pop 0.25s ease;
    }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-bottom: 1px solid #e6e9ef; }
    .modal-header h3 { margin: 0; color: #333; }
    .close-btn { background: none; border: none; font-size: 20px; cursor: pointer; color: #888; }
    #confirmationModalMessage { color: #333; font-size: 16px; line-height: 1.5; }
    @keyframes modal-pop { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }

    /* Form styles */
    #modal form { background-color: transparent; }
    form { padding: 20px; }
    .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .field { flex: 1; display: flex; flex-direction: column; }
    .form-label { font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #444; }
    input[type="text"], input[type="password"], select { width: 100%; height: 40px; padding: 0 10px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; background: #fff; color: #333; }
    input[type="text"]::placeholder, input[type="password"]::placeholder { color: #aaa; opacity: 1; }
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF8,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='24'%20height='24'%20viewBox='0%200%2024%2024'%3E%3Cpath%20fill='%23888888'%20d='M7%2010l5%205%205-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
    }
    select option { color: #000; }
    .helper { font-size: 12px; color: #777; margin-top: 4px; }
    .actions { text-align: center; margin-top: 24px; }
    .actions .btn { width: 100%; }
    .form-error { color: #d93025; font-size: 12px; margin-top: 4px; }

    @media (max-width: 768px) {
      header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
      }
      header h2 {
        font-size: 22px;
      }
      .header-actions {
        flex-wrap: wrap;
        width: 100%;
        justify-content: flex-start;
      }
      .container {
        padding: 16px;
      }
    }

    @media (max-width:560px){ .modal-panel { padding:16px; border-radius:12px; } .form-row { flex-direction:column; } .filter-row { flex-direction:column; align-items:stretch; } }

    /* Animación de entrada */
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    .fade-in-content {
        animation: fadeIn 0.6s ease-out forwards;
    }
  </style>
  <link rel="stylesheet" href="css/table-styles.css?v=<?= time() ?>">
  <link rel="stylesheet" href="css/notification.css?v=<?= time() ?>">

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const addClubForm = document.getElementById('addClubForm');
      if (addClubForm) {
        addClubForm.addEventListener('submit', async (e) => {
          e.preventDefault();
          const formData = new FormData(addClubForm);
          const response = await fetch('actions/agregar_club.php', {
            method: 'POST',
            body: formData
          });
          const result = await response.json();
          if (result.success) {
            showNotification(result.message, 'success');
            addClubForm.reset();
            document.getElementById('modal').classList.remove('show');
            document.body.style.overflow = '';
            // Optionally, you can reload the page or update the club list dynamically
            location.reload();
          } else {
            showNotification(result.message, 'error');
          }
        });
      }
    });
  </script>
  <?php
    // Recuperar datos del formulario y errores si existen
    $form_data = $_SESSION['form_data'] ?? [];
    $form_errors = ($_SESSION['flash']['is_form_error'] ?? false) ? ($_SESSION['flash']['messages'] ?? []) : [];
    unset($_SESSION['form_data']);

    // Limpiar flash solo si no es un error de formulario, para que los errores se puedan mostrar
    if (!($_SESSION['flash']['is_form_error'] ?? false)) {
        unset($_SESSION['flash']);
    }
  ?>
</head>
<body class="fade-in-content">
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Si la URL tiene `modal=show`, abre el modal automáticamente.
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.get('modal') === 'show') {
        const modal = document.getElementById('modal');
        if (modal) {
          modal.classList.add('show');
          document.body.style.overflow = 'hidden';
        }
        // Limpia el parámetro de la URL para que no se vuelva a abrir al recargar
        urlParams.delete('modal');
        const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
        window.history.replaceState({}, document.title, newUrl);
      }
    });
  </script>
 <header class="main-header">
    <div class="logo">
        <img src="admin/assets/img/logo.png" alt="Logo EduClubs" style="height: 80px; margin-right: 10px;">
        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258" style="height: 60px; margin-right: 15px;">
        <span>EduClubs - Base de Clubs</span>
    </div>
  <div class="header-actions">
    <a href="teacher/dashboard.php" class="btn">Volver</a>
    <button id="openModalBtn" class="btn" type="button" aria-haspopup="dialog" onclick="document.getElementById('modal').classList.add('show');document.body.style.overflow='hidden'"> Agregar club</button>
    <button id="toggleEditBtn" class="btn" type="button">Editar</button>
    <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
        <?php include 'includes/teacher_menu.php'; ?>
        <div class="usericon">
          <div class="avatar"><?=strtoupper($user['username'][0] ?? 'U')?></div>
          <div class="user-menu">
            <div><?=htmlspecialchars($user['user_id'] ?? '')?></div>
            <a href="auth/logout.php?redirect=index.php" class="logout">Cerrar sesión</a>
          </div>
        </div>
    </div>
  </div>
 </header>

  <div class="container fade-in-content">
    <div class="card">
      <div id="editToolbar">
        <div class="edit-active">
          <strong>✎ Modo edición activo</strong>
          <button id="bulkDeleteBtn" class="btn small" title="Eliminar seleccionados">🗑️</button>
          <small style="margin-left:8px;color:#666;">Selecciona filas para eliminar o usa el icono de lápiz para editar.</small>
        </div>
      </div>
      <form id="filterForm" class="filter-row">
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
        <div class="table-container">
          <table class="styled-table">
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
                  <td data-label="ID del Club" data-col="club_id"><?= htmlspecialchars($club['club_id']) ?></td>
                  <td data-label="Nombre del Club" data-col="club_name"><?= htmlspecialchars($club['club_name']) ?></td>
                  <td data-label="Creador" data-col="creator_name"><?= htmlspecialchars($club['creator_name']) ?></td>
                  <td data-label="Tipo" data-col="club_type"><?= htmlspecialchars($club['club_type']) ?></td>
                  <td data-label="Fecha de Creación"><?= htmlspecialchars($club['created_at']) ?></td>
                  <td class="edit-action" data-label="Acciones">
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
        </div>
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

  <!-- Confirmation Modal -->
  <div id="confirmationModal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="confirmationModalTitle">
      <div class="modal-header">
        <h3 id="confirmationModalTitle">Confirmar acción</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>
      <div style="padding: 20px;">
        <p id="confirmationModalMessage">¿Estás seguro?</p>
        <div class="actions" style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
          <button type="button" class="btn alt btn-cancel">Cancelar</button>
          <button type="button" class="btn btn-confirm">Confirmar</button>
        </div>
      </div>
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

      // Filter on category change
      typeFilter.addEventListener('change', applyFilters);

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

      function showConfirmationModal(title, message, onConfirm) {
        const modal = document.getElementById('confirmationModal');
        if (!modal) return;

        modal.querySelector('#confirmationModalTitle').textContent = title;
        modal.querySelector('#confirmationModalMessage').textContent = message;

        const confirmBtn = modal.querySelector('.btn-confirm');
        const cancelBtn = modal.querySelector('.btn-cancel');

        const confirmHandler = () => {
          onConfirm();
          closeModal();
        };

        const closeModal = () => {
          modal.classList.remove('show');
          document.body.style.overflow = '';
          confirmBtn.removeEventListener('click', confirmHandler);
        };

        confirmBtn.addEventListener('click', confirmHandler);
        cancelBtn.addEventListener('click', closeModal, { once: true });
        
        modal.querySelectorAll('.close-modal').forEach(btn => {
            btn.addEventListener('click', closeModal, { once: true });
        });

        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
      }

      const editForm = document.getElementById('editClubForm');
      if (editForm) {
        editForm.addEventListener('submit', async (ev) => {
          ev.preventDefault();
          const form = new FormData(editForm);
          try {
            const res = await fetch('actions/editar_club.php', { method: 'POST', body: new URLSearchParams([...form]) });
            const data = await res.json();
            if (data.success) {
              showNotification('Club actualizado correctamente.');
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
        bulkDeleteBtn.addEventListener('click', () => {
          const selected = document.querySelectorAll('.row-checkbox:checked');
          if (selected.length === 0) {
            showNotification('Selecciona al menos un club para eliminar.', 'error');
            return;
          }
          
          const clubNames = Array.from(selected).map(cb => cb.dataset.name);
          
          showConfirmationModal(
            'Confirmar eliminación',
            `¿Estás seguro de que quieres eliminar ${selected.length} club(s)? Esto también eliminará a todos los miembros registrados.`,
            async () => {
              try {
                const res = await fetch('admin/bulk_delete_clubs.php', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ club_names: clubNames })
                });
                const data = await res.json();
                if (data.success) {
                  showNotification('Club(es) eliminado(s) correctamente.');
                  selected.forEach(cb => {
                    const tr = cb.closest('tr');
                    if (tr) tr.remove();
                  });
                } else {
                  showNotification(data.message || 'No se pudieron eliminar los clubes.', 'error');
                }
              } catch (err) {
                console.error(err);
                showNotification('Error al procesar la solicitud de eliminación.', 'error');
              }
            }
          );
        });
      }
    });
  </script>
  <script src="js/notification.js?v=<?= time() ?>"></script>
  <script src="js/menu.js"></script>
  <!-- Modal Agregar Club -->
  <div id="modal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <div class="modal-header">
        <h3 id="modal-title">Nuevo Club</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>

      <form id="addClubForm" method="post" action="actions/agregar_club.php" novalidate>
        <div class="form-row">
          <div class="field">
            <label class="form-label">Nombre del club</label>
            <input type="text" name="club_name" required placeholder="Ej. Club de Robótica" pattern=".*[a-zA-Z]+.*" title="El nombre del club debe contener al menos una letra." value="<?= htmlspecialchars($form_data['club_name'] ?? '') ?>">
            <?php if (isset($form_errors['club_name'])): ?><div class="form-error"><?= htmlspecialchars($form_errors['club_name']) ?></div><?php endif; ?>
          </div>
          <div class="field">
            <label class="form-label">Tipo de club</label>
            <select name="club_type" required>
              <option value="">Selecciona una opción</option>
              <option value="cultural" <?= ($form_data['club_type'] ?? '') === 'cultural' ? 'selected' : '' ?>>Cultural</option>
              <option value="deportivo" <?= ($form_data['club_type'] ?? '') === 'deportivo' ? 'selected' : '' ?>>Deportivo</option>
              <option value="asesoria" <?= ($form_data['club_type'] ?? '') === 'asesoria' ? 'selected' : '' ?>>Asesorías</option>
              <option value="civil" <?= ($form_data['club_type'] ?? '') === 'civil' ? 'selected' : '' ?>>Civil</option>
            </select>
            <?php if (isset($form_errors['club_type'])): ?><div class="form-error"><?= htmlspecialchars($form_errors['club_type']) ?></div><?php endif; ?>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="form-label">Nombre del creador</label>
            <input type="text" name="creator_name" required placeholder="Nombre completo" pattern="[a-zA-Z\s]+" title="El nombre del creador solo puede contener letras y espacios." value="<?= htmlspecialchars($form_data['creator_name'] ?? '') ?>">
            <?php if (isset($form_errors['creator_name'])): ?><div class="form-error"><?= htmlspecialchars($form_errors['creator_name']) ?></div><?php endif; ?>
          </div>
          <div class="field" style="flex:0 0 120px; align-self:flex-end;">
            <button type="button" class="btn alt close-modal" style="width:100%;">Cancelar</button>
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn">Guardar club</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
