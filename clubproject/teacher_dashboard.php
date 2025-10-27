<?php
require 'config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') redirect('login.php');
$user = $_SESSION['user'];

// Handle flash messages via session and Post-Redirect-Get
if (isset($_GET['error']) || isset($_GET['success'])) {
    $flash = null;
    if (isset($_GET['success'])) {
        $flash = ['type' => 'success', 'msg' => '¡Club registrado con éxito!'];
    } elseif (isset($_GET['error'])) {
        if ($_GET['error'] === 'duplicado') {
            $flash = ['type' => 'error', 'msg' => '⚠️ El ID del club ya está registrado. Usa uno diferente.'];
        } elseif ($_GET['error'] === 'campos') {
            $flash = ['type' => 'error', 'msg' => '⚠️ Todos los campos son obligatorios.'];
        } else {
            $flash = ['type' => 'error', 'msg' => '⚠️ Ocurrió un error al registrar el club.'];
        }
    }
    
    if ($flash) {
        $_SESSION['flash'] = $flash;
    }

    // Clean up URL
    $params = $_GET;
    unset($params['success'], $params['error']);
    $redirectUrl = $_SERVER['PHP_SELF'] . (count($params) > 0 ? '?' . http_build_query($params) : '');
    header('Location: ' . $redirectUrl);
    exit;
}

/**
 * Devuelve valores únicos de una columna (para listas dinámicas)
 */
function getUniqueValues(PDO $pdo, string $table, string $column, string $where = null, array $whereParams = []) {
    $sql = "SELECT DISTINCT `$column` FROM `$table`";
    if ($where) $sql .= " WHERE $where";
    $sql .= " ORDER BY `$column` ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($whereParams);
    return array_map(function($r){ return $r[0]; }, $stmt->fetchAll(PDO::FETCH_NUM));
}

/**
 * Paginación y filtro (usa parámetros preparados)
 * $whereBase: texto de condición inicial (por ejemplo "club_type = 'cultural'")
 * $filterValue: valor de filtro extra (se añade AND club_name = ? si está presente)
 *
 * IMPORTANTE: bindValue se usa para LIMIT/OFFSET como enteros para evitar que PDO los ponga entre comillas.
 */
function getPaginated(PDO $pdo, string $table, string $whereBase, string $pageParam, ?string $filterValue = null) {
    $page = max(1, intval($_GET[$pageParam] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $params = [];
    $where = $whereBase;
    if ($filterValue !== null && $filterValue !== '') {
        $where .= " AND club_name = ?";
        $params[] = $filterValue;
    }

    // contar
    $countSql = "SELECT COUNT(*) FROM `$table` WHERE $where";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    if ($total === 0) {
        return ['rows' => [], 'total' => 0, 'page' => $page, 'limit' => $limit];
    }

    // seleccionar con límite, enlazando parámetros y luego bindValue para LIMIT/OFFSET
    // Nota: NO pasar $params a execute() aquí; usamos bindValue para parámetros dinámicos + LIMIT/OFFSET como enteros
    $selectSql = "SELECT * FROM `$table` WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($selectSql);

    // bind dynamic params (filters)
    $idx = 1;
    foreach ($params as $p) {
        $stmt->bindValue($idx++, $p);
    }
    // bind limit/offset como enteros
    $stmt->bindValue($idx++, (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue($idx++, (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return ['rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit];
}

/**
 * Paginación para tablas que no tienen club_name (ej. tutoring_registrations).
 * Permite filtrar por una columna arbitraria (por ejemplo materia).
 */
function getPaginatedWithColumnFilter(PDO $pdo, string $table, string $whereBase, string $pageParam, ?string $filterColumn = null, ?string $filterValue = null) {
    $page = max(1, intval($_GET[$pageParam] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $params = [];
    $where = $whereBase;
    if ($filterColumn && $filterValue !== null && $filterValue !== '') {
        $where .= " AND `$filterColumn` = ?";
        $params[] = $filterValue;
    }

    $countSql = "SELECT COUNT(*) FROM `$table` WHERE $where";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    if ($total === 0) {
        return ['rows' => [], 'total' => 0, 'page' => $page, 'limit' => $limit];
    }

    $selectSql = "SELECT * FROM `$table` WHERE $where ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $stmt = $pdo->prepare($selectSql);

    $idx = 1;
    foreach ($params as $p) {
        $stmt->bindValue($idx++, $p);
    }
    $stmt->bindValue($idx++, (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue($idx++, (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return ['rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit];
}

/* Obtener listas dinámicas para selects - arrays de TODOS los clubes disponibles por tipo,
   usando la tabla club_registrations (se actualizarán automáticamente cuando se añadan registros). */
$culturales = getUniqueValues($pdo, 'club_registrations', 'club_name', "club_type = 'cultural'");
$deportivos = getUniqueValues($pdo, 'club_registrations', 'club_name', "club_type = 'deportivo'");
$civiles    = getUniqueValues($pdo, 'club_registrations', 'club_name', "club_type = 'civil'");
$asesorias_clubs = getUniqueValues($pdo, 'club_registrations', 'club_name', "club_type = 'asesoria'");

/* Lista de materias para filtro de asesorías (tutoring_registrations) */
$materias_asesoria = getUniqueValues($pdo, 'tutoring_registrations', 'materia');

/* Leer filtros desde GET */
$filter_cultural = trim($_GET['filter_cultural'] ?? '');
$filter_deportivo = trim($_GET['filter_deportivo'] ?? '');
$filter_civil     = trim($_GET['filter_civil'] ?? '');
$filter_asesoria  = trim($_GET['filter_asesoria'] ?? '');

/* Obtener datos paginados aplicando filtros (si existen) */
$culturalData = getPaginated($pdo, 'club_registrations', "club_type = 'cultural'", 'page_cultural', $filter_cultural);
$deportivoData = getPaginated($pdo, 'club_registrations', "club_type = 'deportivo'", 'page_deportivo', $filter_deportivo);
$civilData     = getPaginated($pdo, 'club_registrations', "club_type = 'civil'", 'page_civil', $filter_civil);
$asesoriasData = getPaginatedWithColumnFilter($pdo, 'tutoring_registrations', "1=1", 'page_asesoria', 'materia', $filter_asesoria);
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel Maestro</title>
  <link rel="stylesheet" href="css/main-modern.css">
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
    }
    body { margin: 0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #FFFF; color: #333; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    header { background: var(--glass2); backdrop-filter: blur(6px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px 24px; position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; }
    header h2 { margin: 0; font-size: 28px; color:#fff; }
    .header-actions { display:flex; align-items:center; gap:12px; }
    .header-actions .btn {
        font-size: 16px;
        background: transparent;
        box-shadow: none;
    }
    .header-actions .btn:hover {
        background: transparent;
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
    .table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    .table th, .table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
    .table th { background: #fafafa; color: #555; }
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
    #editToolbar { display:none; position: sticky; top:72px; z-index: 105; margin-bottom:12px; background: #fff9e6; padding:10px 14px; border:1px solid #ffecb3; border-radius:10px; box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
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
    .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 200; align-items: center; justify-content: center; backdrop-filter: blur(4px); }
    .modal.show { display: flex; }
    .modal-panel { background: #fff; border-radius: var(--radius); box-shadow: 0 10px 30px rgba(0,0,0,0.1); max-width: 500px; width: 90%; animation: modal-pop 0.25s ease; }
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
  </style>
  <link rel="stylesheet" href="css/table-styles.css?v=<?= time() ?>">
  <link rel="stylesheet" href="css/notification.css">

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      (function removeFlashParams(){
        try {
          const u = new URL(window.location.href);
          if (u.searchParams.has('success') || u.searchParams.has('error')) {
            u.searchParams.delete('success');
            u.searchParams.delete('error');
            const newUrl = u.pathname + (u.searchParams.toString() ? '?' + u.searchParams.toString() : '');
            window.history.replaceState({}, document.title, newUrl);
            const flash = document.getElementById('flashMessage');
            if (flash) flash.remove();
          }
        } catch(e) { /* ignore */ }
      })();

      function setFilterParam(param, value) {
        const u = new URL(window.location.href);
        if (value === '' || value === null) {
          u.searchParams.delete(param);
        } else {
          u.searchParams.set(param, value);
        }
        const pageMap = {
          'filter_cultural': 'page_cultural',
          'filter_deportivo': 'page_deportivo',
          'filter_civil': 'page_civil',
          'filter_asesoria': 'page_asesoria'
        };
        if (pageMap[param]) u.searchParams.set(pageMap[param], '1');
        
        const cardMap = {
          'filter_cultural': 'card-cultural',
          'filter_deportivo': 'card-deportivo',
          'filter_civil': 'card-civil',
          'filter_asesoria': 'card-asesorias'
        };
        const hash = cardMap[param] ? '#' + cardMap[param] : '';
        
        // Update URL and reload
        history.pushState(null, '', u.pathname + u.search);
        location.reload();
      }

      document.querySelectorAll('.filter-select').forEach(sel => {
        sel.addEventListener('change', (e) => {
          const param = sel.dataset.param;
          const val = sel.value;
          setFilterParam(param, val === '__all__' ? '' : val);
        });
      });

      const toggleEditBtn = document.getElementById('toggleEditBtn');
      const editToolbar = document.getElementById('editToolbar');
      let editMode = false;
      function setEditMode(on) {
        editMode = !!on;
        document.body.classList.toggle('edit-mode-active', on);
        document.querySelectorAll('.card').forEach(card => {
          card.classList.toggle('editing', on);
        });
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        if (!on) {
          document.querySelectorAll('tr.row-selected').forEach(tr => tr.classList.remove('row-selected'));
        }
        editToolbar.style.display = on ? 'block' : 'none';
        toggleEditBtn.textContent = on ? 'Salir edición' : 'Editar';
      }
      if (toggleEditBtn) toggleEditBtn.addEventListener('click', () => setEditMode(!editMode));

      function getSelectedIds() {
        const ids = [];
        document.querySelectorAll('.row-checkbox:checked').forEach(cb => {
          const id = cb.getAttribute('data-id');
          if (id) ids.push(id);
        });
        return ids;
      }

      const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
      if (bulkDeleteBtn) bulkDeleteBtn.addEventListener('click', () => {
        const ids = getSelectedIds();
        if (!ids.length) {
          showNotification('Selecciona al menos un alumno.', 'error');
          return;
        }
        
        showConfirmationModal(
          'Confirmar eliminación',
          `¿Estás seguro de que quieres dar de baja a ${ids.length} alumno(s)? Esta acción no se puede deshacer.`,
          async () => {
            try {
              const res = await fetch('bulk_delete_members.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                body: JSON.stringify({ ids })
              });
              const data = await res.json();
              if (data.success) {
                ids.forEach(id => { const tr = document.querySelector('tr[data-id="'+id+'"]'); if (tr) tr.remove(); });
                
                // Crear mensaje con nombres
                let mensaje = '';
                if (data.nombres && data.nombres.length > 0) {
                  if (data.nombres.length === 1) {
                    mensaje = data.nombres[0] + ' eliminado';
                  } else if (data.nombres.length <= 3) {
                    mensaje = data.nombres.join(', ') + ' eliminados';
                  } else {
                    mensaje = data.nombres.slice(0, 3).join(', ') + '... eliminados';
                  }
                } else {
                  mensaje = 'Eliminado(s) correctamente';
                }
                
                showNotification(mensaje);
              } else {
                showNotification(data.message || 'No se pudieron eliminar los registros.', 'error');
              }
            } catch (err) {
              console.error(err);
              showNotification('Error al eliminar. Revisa la consola.', 'error');
            }
          }
        );
      });

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

      document.addEventListener('change', (e) => {
        const cb = e.target.closest('.row-checkbox');
        if (!cb) return;
        const tr = cb.closest('tr');
        if (!tr) return;
        tr.classList.toggle('row-selected', cb.checked);
      });
      document.addEventListener('click', (e) => {
        const tr = e.target.closest('tr[data-id]');
        if (!tr) return;
        if (e.target.closest('button, input, a, select, textarea')) return;
        if (!editMode) return;
        const cb = tr.querySelector('.row-checkbox');
        if (!cb) return;
        cb.checked = !cb.checked;
        tr.classList.toggle('row-selected', cb.checked);
      });

      document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-edit');
        if (!btn) return;
        document.getElementById('edit_id').value = btn.dataset.id || '';
        document.getElementById('edit_member_id').value = btn.dataset.memberId || '';
        document.getElementById('edit_paterno').value = btn.dataset.paterno || '';
        document.getElementById('edit_materno').value = btn.dataset.materno || '';
        document.getElementById('edit_nombres').value = btn.dataset.nombres || '';
        document.getElementById('edit_correo').value = btn.dataset.correo || '';
        document.getElementById('edit_semestre').value = btn.dataset.semestre || '';
        document.getElementById('edit_turno').value = btn.dataset.turno || '';
        document.getElementById('editMemberModal').classList.add('show');
        document.body.style.overflow = 'hidden';
      });

      document.querySelectorAll('.close-modal, .close-btn').forEach(b => b.addEventListener('click', (e) => {
        const panel = e.target.closest('.modal');
        if(panel) panel.classList.remove('show');
        document.body.style.overflow = '';
      }));

      const editForm = document.getElementById('editMemberForm');
      if (editForm) {
        editForm.addEventListener('submit', async (ev) => {
          ev.preventDefault();
          const form = new FormData(editForm);
          try {
            const res = await fetch('edit_member.php', { method: 'POST', body: new URLSearchParams([...form]) });
            const data = await res.json();
            if (data.success) {
              showNotification('Cambios guardados exitosamente.');
              const id = form.get('id');
              const tr = document.querySelector('tr[data-id="'+id+'"]');
              if (tr) {
                tr.querySelector('td[data-col="user_id"]').textContent = form.get('member_id') || '';
                tr.querySelector('td[data-col="nombre"]').textContent = ((form.get('paterno')||'') + ' ' + (form.get('materno')||'') + ' ' + (form.get('nombres')||'')).trim();
                tr.querySelector('td[data-col="correo"]').textContent = form.get('correo') || '';
                tr.querySelector('td[data-col="semestre"]').textContent = form.get('semestre') || '';
                tr.querySelector('td[data-col="turno"]').textContent = form.get('turno') || '';
                const editBtn = tr.querySelector('.btn-edit');
                if (editBtn) {
                  editBtn.dataset.memberId = form.get('member_id') || '';
                  editBtn.dataset.paterno = form.get('paterno') || '';
                  editBtn.dataset.materno = form.get('materno') || '';
                  editBtn.dataset.nombres = form.get('nombres') || '';
                  editBtn.dataset.correo = form.get('correo') || '';
                  editBtn.dataset.semestre = form.get('semestre') || '';
                  editBtn.dataset.turno = form.get('turno') || '';
                }
              } else {
                location.reload();
              }
              document.getElementById('editMemberModal').classList.remove('show');
              document.body.style.overflow = '';
            } else {
              showNotification(data.message || 'No se pudo guardar.', 'error');
            }
          } catch (err) { console.error(err); showNotification('Error al guardar.', 'error'); }
        });
      }

    });
  </script>
  <script src="js/notification.js"></script>
</head>
<body>
 <header class="main-header">
    <div class="logo">
        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
        <span>EduClubs - Panel de Maestro</span>
    </div>
  <div class="header-actions">
    <a href="view_clubs.php" class="btn">Ver base de clubs</a>
    <button id="openModalBtn" class="btn" type="button" aria-haspopup="dialog" onclick="document.getElementById('modal').classList.add('show');document.body.style.overflow='hidden'"> Agregar club</button>
    <button id="toggleEditBtn" class="btn" type="button">Editar</button>
    <div class="usericon">
      <div class="avatar"><?=strtoupper($user['username'][0] ?? 'U')?></div>
      <div class="user-menu">
        <div><?=htmlspecialchars($user['user_id'] ?? '')?></div>
        <a href="logout.php?redirect=index0.php" class="logout">Cerrar sesión</a>
      </div>
    </div>
  </div>
 </header>

  <div class="container">
    <div id="editToolbar">
      <div class="edit-active">
        <strong>Modo edición activo</strong>
        <button id="bulkDeleteBtn" class="btn small">Dar de baja</button>
        <button id="exitEditBtn" class="btn alt small" onclick="(function(){document.getElementById('toggleEditBtn').click();})();">Salir modo edición</button>
        <small style="margin-left:8px;color:#666;">Selecciona filas para dar de baja o usa el icono de lápiz para editar una fila.</small>
      </div>
    </div>

    <?php 
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $type = $flash['type'] ?? 'info';
        $message_html = '';

        if (!empty($flash['messages'])) {
            // Handle array of messages
            $message_html = '<ul>';
            foreach ($flash['messages'] as $msg) {
                $message_html .= '<li>' . htmlspecialchars($msg) . '</li>';
            }
            $message_html .= '</ul>';
        } elseif (!empty($flash['msg'])) {
            // Handle single message
            $message_html = htmlspecialchars($flash['msg']);
        }

        if ($message_html) {
            echo "<script>";
            echo "document.addEventListener('DOMContentLoaded', () => {";
            // Use JSON encoding to safely pass the HTML string to JavaScript
            echo "showNotification(" . json_encode($message_html) . ", '" . htmlspecialchars($type) . "');";
            echo "});";
            echo "</script>";
        }
    }
    ?>

    <?php
    // Render table with optional filter select
    function renderTable($title, $data, $columns, $pageParam, $filterParam = null, $filterOptions = []) {
      $currentParams = $_GET; unset($currentParams['success'], $currentParams['error']);
      $cardId = strtolower(str_replace(['í', ' '], ['i', '-'], $title));
      echo "<div class='card' id='card-" . htmlspecialchars($cardId) . "' data-card='" . htmlspecialchars($title) . "'>";
      
      if ($filterParam !== null) {
        $selected = $_GET[$filterParam] ?? '';
        echo "<div class='filter-row'>";
        echo "<label style='font-size:13px;color:#444;margin-right:6px;'>Filtrar por:</label>";
        echo "<select class='filter-select' data-param='".htmlspecialchars($filterParam)."'>";
        echo "<option value='__all__'>Todos</option>";
        foreach ($filterOptions as $opt) {
          $sel = ($opt === $selected) ? 'selected' : '';
          echo "<option value='".htmlspecialchars($opt)."' $sel>".htmlspecialchars($opt)."</option>";
        }
        echo "</select>";
        echo "<div style='margin-left:auto;color:#666;font-size:13px;'>Registros: " . intval($data['total'] ?? 0) . "</div>";
        echo "</div>";
      } else {
        echo "<div style='margin-top:6px;color:#666;font-size:13px;'>Registros: " . intval($data['total'] ?? 0) . "</div>";
      }

      if (empty($data['rows'])) {
        echo "<p>No hay registros disponibles.</p>";
      } else {
        echo "<div class='table-container'>";
        echo "<div class='table-header'><h4>" . htmlspecialchars($title) . "</h4></div>";
        echo "<table class='styled-table'><thead><tr>";
        echo "<th class='edit-col'></th>";
        foreach ($columns as $key => $label) echo "<th>" . htmlspecialchars($label) . "</th>";
        echo "<th class='edit-action'>Acciones</th>";
        echo "</tr></thead><tbody>";
        foreach ($data['rows'] as $r) {
          $id = htmlspecialchars($r['id'] ?? '');
          $nombre_full = trim(($r['paterno'] ?? '') . ' ' . ($r['materno'] ?? '') . ' ' . ($r['nombres'] ?? ''));
          echo "<tr data-id='$id'>";
          echo "<td class='edit-col'><input type='checkbox' class='row-checkbox' data-id='$id'></td>";
          foreach ($columns as $key => $label) {
            if ($key === 'nombre') {
              echo "<td data-label='" . htmlspecialchars($label) . "' data-col='nombre'>" . htmlspecialchars($nombre_full) . "</td>";
            } elseif ($key === 'fecha') {
              echo "<td data-label='" . htmlspecialchars($label) . "'><small class='gray'>" . htmlspecialchars($r['created_at'] ?? '') . "</small></td>";
            } else {
              echo "<td data-label='" . htmlspecialchars($label) . "' data-col='".htmlspecialchars($key)."'>" . htmlspecialchars($r[$key] ?? '') . "</td>";
            }
          }
          $btnAttrs = "";
          if ($id !== '') {
            $member_id = htmlspecialchars($r['user_id'] ?? '');
            $paterno = htmlspecialchars($r['paterno'] ?? '');
            $materno = htmlspecialchars($r['materno'] ?? '');
            $nombres = htmlspecialchars($r['nombres'] ?? '');
            $correo = htmlspecialchars($r['correo'] ?? '');
            $semestre = htmlspecialchars($r['semestre'] ?? '');
            $turno = htmlspecialchars($r['turno'] ?? '');
$btnAttrs = "class='btn-edit btn btn-icon' data-id='$id' data-member-id='$member_id' data-paterno='$paterno' data-materno='$materno' data-nombres='$nombres' data-correo='$correo' data-semestre='$semestre' data-turno='$turno'";
            echo "<td class='edit-action' data-label='Acciones'><button $btnAttrs type='button'>✎</button></td>";
          } else {
            echo "<td class='edit-action' data-label='Acciones'></td>";
          }
          echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";

        $totalPages = max(1, ceil(intval($data['total']) / max(1, intval($data['limit'] ?? 10))));
        $currentPage = max(1, intval($data['page'] ?? 1));
        if ($totalPages > 1) {
          echo "<div class='pagination'>";
          $params = $currentParams;
          if ($currentPage > 1) {
            $params[$pageParam] = $currentPage - 1;
            echo "<a href='?" . http_build_query($params) . "'>&laquo; Anterior</a>";
          }
          for ($i = 1; $i <= $totalPages; $i++) {
            $params[$pageParam] = $i;
            $active = ($currentPage == $i) ? 'active' : '';
            echo "<a href='?" . http_build_query($params) . "' class='$active'>$i</a>";
          }
          if ($currentPage < $totalPages) {
            $params[$pageParam] = $currentPage + 1;
            echo "<a href='?" . http_build_query($params) . "'>Siguiente &raquo;</a>";
          }
          echo "</div>";
        }
      }
      echo "</div>";
    }

    renderTable('Cultural', $culturalData, [
      'club_name' => 'Club',
      'nombre' => 'Nombre',
      'semestre' => 'Semestre',
      'correo' => 'Correo',
      'turno' => 'Turno',
      'user_id' => 'Registró',
      'fecha' => 'Fecha'
    ], 'page_cultural', 'filter_cultural', $culturales);

    renderTable('Deportivo', $deportivoData, [
      'club_name' => 'Club',
      'nombre' => 'Nombre',
      'semestre' => 'Semestre',
      'correo' => 'Correo',
      'turno' => 'Turno',
      'user_id' => 'Registró',
      'fecha' => 'Fecha'
    ], 'page_deportivo', 'filter_deportivo', $deportivos);

    renderTable('Civil', $civilData, [
      'club_name' => 'Club',
      'nombre' => 'Nombre',
      'semestre' => 'Semestre',
      'correo' => 'Correo',
      'turno' => 'Turno',
      'user_id' => 'Registró',
      'fecha' => 'Fecha'
    ], 'page_civil', 'filter_civil', $civiles);

    // Asesorías: registros de tutoría (filtro por materia). El select de clubes de tipo 'asesoria' está disponible en $asesorias_clubs si se necesita.
    renderTable('Asesorías', $asesoriasData, [
      'materia' => 'Materia',
      'nombre' => 'Nombre',
      'carrera' => 'Carrera',
      'maestro' => 'Maestro',
      'telefono' => 'Teléfono',
      'user_id' => 'Registró',
      'fecha' => 'Fecha'
    ], 'page_asesoria', 'filter_asesoria', $materias_asesoria);
    ?>
  </div>

  <!-- Modal Agregar Club -->
  <div id="modal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <div class="modal-header">
        <h3 id="modal-title">Nuevo Club</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>

      <form method="post" action="agregar_club.php" novalidate>
        <div class="form-row">
          <div class="field">
            <label class="form-label">Nombre del club</label>
            <input type="text" name="club_name" required placeholder="Ej. Club de Robótica" pattern=".*[a-zA-Z]+.*" title="El nombre del club debe contener al menos una letra.">
          </div>
          <div class="field">
            <label class="form-label">Tipo de club</label>
            <select name="club_type" required>
              <option value="">Selecciona una opción</option>
              <option value="cultural">Cultural</option>
              <option value="deportivo">Deportivo</option>
              <option value="asesoria">Asesorías</option>
              <option value="civil">Civil</option>
            </select>
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="form-label">Nombre del creador</label>
            <input type="text" name="creator_name" required placeholder="Nombre completo" pattern="[a-zA-Z\s]+" title="El nombre del creador solo puede contener letras y espacios.">
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

  <!-- Edit member modal -->
  <div id="editMemberModal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="editTitle">
      <div class="modal-header">
        <h3 id="editTitle">Editar miembro</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>

      <form id="editMemberForm" novalidate>
        <input type="hidden" id="edit_id" name="id" value="">
        <div class="form-row">
          <div class="field">
            <label class="form-label">ID socio</label>
            <input id="edit_member_id" name="member_id" type="text" required readonly>
          </div>
          <div class="field">
            <label class="form-label">Paterno</label>
            <input id="edit_paterno" name="paterno" type="text">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="form-label">Materno</label>
            <input id="edit_materno" name="materno" type="text">
          </div>
          <div class="field">
            <label class="form-label">Nombres</label>
            <input id="edit_nombres" name="nombres" type="text">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="form-label">Correo</label>
            <input id="edit_correo" name="correo" type="text">
          </div>
          <div class="field">
            <label class="form-label">Semestre</label>
            <input id="edit_semestre" name="semestre" type="text">
          </div>
        </div>

        <div class="form-row">
          <div class="field">
            <label class="form-label">Turno</label>
            <input id="edit_turno" name="turno" type="text">
          </div>
        </div>

        <div class="actions">
          <button type="submit" class="btn">Guardar</button>
        </div>
      </form>
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

</body>
</html>
