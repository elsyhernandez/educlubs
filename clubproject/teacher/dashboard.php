<?php
require '../includes/config.php';
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') redirect('../auth/index.php');
// $user = $_SESSION['user'];
$_SESSION['user'] = ['user_id' => 'test_teacher', 'username' => 'Test Teacher', 'role' => 'teacher'];
$user = $_SESSION['user'];

// Handle flash messages via session and Post-Redirect-Get
if (isset($_GET['error']) || isset($_GET['success'])) {
    $flash = null;
    if (isset($_GET['success'])) {
        $flash = ['type' => 'success', 'msg' => 'Se ha creado correctamente'];
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
        // Clean up URL after setting flash message
        $params = $_GET;
        unset($params['success'], $params['error']);
        $redirectUrl = $_SERVER['PHP_SELF'] . (count($params) > 0 ? '?' . http_build_query($params) : '');
        header('Location: ' . $redirectUrl);
        exit;
    }
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
 */
function getPaginated(PDO $pdo, string $table, string $whereBase, string $pageParam, ?string $filterValue = null, string $orderBy = 'ORDER BY reg.created_at DESC') {
    $page = max(1, intval($_GET[$pageParam] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $params = [];
    $where = str_replace(['club_type', 'club_name'], ['reg.club_type', 'reg.club_name'], $whereBase);
    if ($filterValue !== null && $filterValue !== '') {
        $where .= " AND reg.club_name = ?";
        $params[] = $filterValue;
    }

    $countSql = "SELECT COUNT(reg.id) FROM `$table` reg WHERE $where";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    if ($total === 0) {
        return ['rows' => [], 'total' => 0, 'page' => $page, 'limit' => $limit];
    }

    $selectSql = "SELECT 
                    reg.id, reg.club_name, reg.created_at,
                    u.user_id, u.nombres, u.paterno, u.materno, u.email AS correo, u.telefono, u.semestre, u.turno, u.carrera, u.profile_picture AS profile_pic_path
                  FROM `$table` reg
                  LEFT JOIN `users` u ON reg.user_id = u.user_id
                  WHERE $where $orderBy LIMIT ? OFFSET ?";
    
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

function getPaginatedWithColumnFilter(PDO $pdo, string $table, string $whereBase, string $pageParam, ?string $filterColumn = null, ?string $filterValue = null, string $orderBy = 'ORDER BY reg.created_at DESC') {
    $page = max(1, intval($_GET[$pageParam] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $params = [];
    $where = $whereBase;
    if ($filterColumn && $filterValue !== null && $filterValue !== '') {
        $where .= " AND reg.`$filterColumn` = ?";
        $params[] = $filterValue;
    }

    $countSql = "SELECT COUNT(reg.id) FROM `$table` reg WHERE $where";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    if ($total === 0) {
        return ['rows' => [], 'total' => 0, 'page' => $page, 'limit' => $limit];
    }

    $selectSql = "SELECT 
                    reg.id, reg.materia, reg.maestro, reg.created_at,
                    u.user_id, u.nombres, u.paterno, u.materno, u.email AS correo, u.telefono, u.semestre, u.carrera, u.turno, u.profile_picture AS profile_pic_path
                  FROM `$table` reg
                  LEFT JOIN `users` u ON reg.user_id = u.user_id
                  WHERE $where $orderBy LIMIT ? OFFSET ?";

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

function getIconForClub(string $clubName): string {
    $clubName = strtolower($clubName);
    $iconMap = [
        'danza' => 'fa-female', 'artes manuales' => 'fa-cut', 'literatura' => 'fa-book-open',
        'rondalla' => 'fa-music', 'norteño' => 'fa-hat-cowboy', 'teatro' => 'fa-theater-masks',
        'fotografía' => 'fa-camera', 'ajedrez' => 'fa-chess', 'basquetbol' => 'fa-basketball-ball',
        'defensa personal' => 'fa-fist-raised', 'futbol' => 'fa-futbol', 'voleibol' => 'fa-volleyball-ball',
        'banda de guerra' => 'fa-drum', 'escolta' => 'fa-flag', 'inglés' => 'fa-language',
        'matemáticas' => 'fa-calculator', 'default' => 'fa-users'
    ];
    foreach ($iconMap as $key => $icon) {
        if (strpos($clubName, $key) !== false) return $icon;
    }
    return $iconMap['default'];
}

function renderClubFilterButtons(array $clubOptions, string $filterParam) {
    $selectedValue = $_GET[$filterParam] ?? '';
    echo '<div class="club-filters">';
    echo '<button class="club-filter-btn' . ($selectedValue === '' ? ' active' : '') . '" data-filter-param="' . htmlspecialchars($filterParam) . '" data-filter-value="">Todos</button>';
    foreach ($clubOptions as $club) {
        $icon = getIconForClub($club);
        $activeClass = ($club === $selectedValue) ? ' active' : '';
        echo '<button class="club-filter-btn' . $activeClass . '" data-filter-param="' . htmlspecialchars($filterParam) . '" data-filter-value="' . htmlspecialchars($club) . '">';
        echo '<i class="fas ' . $icon . ' icon"></i> ' . htmlspecialchars($club);
        echo '</button>';
    }
    echo '</div>';
}

$culturales = getUniqueValues($pdo, 'clubs', 'club_name', "club_type = 'cultural'");
$deportivos = getUniqueValues($pdo, 'clubs', 'club_name', "club_type = 'deportivo'");
$civiles    = getUniqueValues($pdo, 'clubs', 'club_name', "club_type = 'civil'");
$asesorias_clubs = getUniqueValues($pdo, 'clubs', 'club_name', "club_type = 'asesoria'");
$materias_asesoria = getUniqueValues($pdo, 'tutoring_registrations', 'materia');

$filter_cultural = trim($_GET['filter_cultural'] ?? '');
$filter_deportivo = trim($_GET['filter_deportivo'] ?? '');
$filter_civil     = trim($_GET['filter_civil'] ?? '');
$filter_asesoria  = trim($_GET['filter_asesoria'] ?? '');

$orderBy = " ORDER BY u.nombres ASC, u.paterno ASC, u.materno ASC";

$culturalData = getPaginated($pdo, 'club_registrations', "club_type = 'cultural'", 'page_cultural', $filter_cultural, $orderBy);
$deportivoData = getPaginated($pdo, 'club_registrations', "club_type = 'deportivo'", 'page_deportivo', $filter_deportivo, $orderBy);
$civilData     = getPaginated($pdo, 'club_registrations', "club_type = 'civil'", 'page_civil', $filter_civil, $orderBy);
$asesoriasData = getPaginatedWithColumnFilter($pdo, 'tutoring_registrations', "1=1", 'page_asesoria', 'materia', $filter_asesoria, $orderBy);

if (isset($_GET['ajax']) && $_GET['ajax'] == '1' && !empty($_GET['type'])) {
    $type = $_GET['type'];
    
    $tablesConfig = [
        'cultural' => ['title' => 'Cultural', 'data' => $culturalData, 'columns' => ['club_name' => 'Club', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'telefono' => 'Teléfono', 'semestre' => 'Semestre', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'pageParam' => 'page_cultural', 'filterParam' => 'filter_cultural', 'filterOptions' => $culturales],
        'deportivo' => ['title' => 'Deportivo', 'data' => $deportivoData, 'columns' => ['club_name' => 'Club', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'telefono' => 'Teléfono', 'semestre' => 'Semestre', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'pageParam' => 'page_deportivo', 'filterParam' => 'filter_deportivo', 'filterOptions' => $deportivos],
        'civil' => ['title' => 'Civil', 'data' => $civilData, 'columns' => ['club_name' => 'Club', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'telefono' => 'Teléfono', 'semestre' => 'Semestre', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'pageParam' => 'page_civil', 'filterParam' => 'filter_civil', 'filterOptions' => $civiles],
        'asesorias' => ['title' => 'Asesorías', 'data' => $asesoriasData, 'columns' => ['materia' => 'Materia', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'carrera' => 'Carrera', 'maestro' => 'Maestro', 'telefono' => 'Teléfono', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'pageParam' => 'page_asesoria', 'filterParam' => 'filter_asesoria', 'filterOptions' => $materias_asesoria]
    ];

    if (isset($tablesConfig[$type])) {
        $config = $tablesConfig[$type];
        renderTable($config['title'], $config['data'], $config['columns'], $config['pageParam'], $config['filterParam'], $config['filterOptions']);
    }
    
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Panel Maestro</title>
  <script>
    const BASE_PATH = '<?php echo str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'], 2)); ?>';
  </script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="../css/menu.css">
  <link rel="stylesheet" href="../css/main-modern.css">
   <style>
    .card-section-title {
        font-size: 24px;
        font-weight: 700;
        color: var(--primary-color);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 3px solid var(--accent-color);
    }
    .club-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    .club-filter-btn {
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 8px 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
        font-size: 14px;
    }
    .club-filter-btn.active,
    .club-filter-btn:hover {
        background-color: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .club-filter-btn .icon {
        font-size: 1.1em;
    }
    .print-report-btn-container {
        text-align: center;
        margin-top: 15px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
    }
    .print-report-btn-container.visible {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border-top: 1px solid #eee;
    }
    .print-report-btn {
        padding: 12px 25px;
        background: var(--button-bg);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .print-report-btn:hover {
        background: var(--button-hover-bg);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-area, .printable-area * {
            visibility: visible;
        }
        .printable-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .header-actions, .edit-col, .edit-action, .pagination, .club-filters, .print-report-btn-container, .main-header, #editToolbar, .table-header h4 {
            display: none !important;
        }
        .card {
            box-shadow: none;
            border: 1px solid #ccc;
        }
        .table-container {
            margin-top: 0;
        }
        h2.printable-title {
            visibility: visible;
            display: block;
            text-align: center;
            margin-bottom: 20px;
        }
    }
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
      --primary-color: #4D0011;
      --secondary-color: #62152d;
      --accent-color: #952f57;
      --bg1: #f9e6e6;
      --bg2: #e8d1d1;
      --card-bg: rgba(255,255,255,0.9);
      --primary: var(--secondary-color);
      --button-bg: linear-gradient(90deg, var(--accent-color), var(--secondary-color));
      --button-hover-bg: linear-gradient(90deg, var(--secondary-color), var(--accent-color));
      --muted: #888;
      --glass: rgba(255,255,255,0.6); 
      --glass2: #4D0011d7;
          --radius: 12px;
          --edit-highlight: rgba(255, 244, 180, 0.7);
          --white-color: #FFFFFF;
    }
    body { margin: 0; font-family: 'Segoe UI', Roboto, Arial, sans-serif; background: #FFFF; color: #333; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale; }
    header { background: var(--primary-color); backdrop-filter: blur(6px); box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 16px 24px; position: sticky; top: 0; z-index: 100; display: flex; justify-content: space-between; align-items: center; }
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
        transition: all 0.3s ease;
        font-size: 16px;
    }

    .header-actions .btn:hover {
        background: var(--white-color);
        color: var(--primary-color);
        box-shadow: none;
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
    
    body.edit-mode-active .edit-col,
    body.edit-mode-active .edit-action {
        display: table-cell;
    }

    .profile-modal-content {
        display: flex;
        gap: 20px;
        padding: 25px;
    }
    .profile-pic-container {
        flex-shrink: 0;
        text-align: center;
    }
    .profile-pic {
        width: 150px;
        height: 200px;
        border-radius: 8px;
        object-fit: cover;
        border: 4px solid var(--primary-color);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #888;
        font-size: 14px;
        font-weight: 500;
    }
    .profile-pic img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }
    .profile-pic-container .student-name {
        font-weight: 700;
        font-size: 18px;
        margin-top: 10px;
        color: var(--primary-color);
    }
    .profile-details {
        flex-grow: 1;
    }
    .profile-details h4 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 22px;
        color: var(--secondary-color);
        border-bottom: 2px solid var(--accent-color);
        padding-bottom: 8px;
    }
    .detail-item {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        font-size: 15px;
    }
    .detail-item .icon {
        font-size: 16px;
        color: var(--accent-color);
        width: 25px;
        text-align: center;
        margin-right: 10px;
    }
    .detail-item .label {
        font-weight: 600;
        color: #555;
    }
    .detail-item .value {
        color: #333;
        margin-left: 8px;
    }
    .styled-table tbody tr {
        cursor: pointer;
    }

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

    #modal form { background-color: transparent; }
    form { padding: 20px; }
    .form-row { display: flex; gap: 16px; margin-bottom: 16px; }
    .field { flex: 1; display: flex; flex-direction: column; }
    .form-label { font-size: 14px; font-weight: 600; margin-bottom: 6px; color: #444; }
    input[type="text"], input[type="password"], input[type="email"], select { width: 100%; height: 40px; padding: 0 10px; border: 1px solid #ccc; border-radius: 8px; box-sizing: border-box; background: #fff; color: #333; }
    input[type="text"]::placeholder, input[type="password"]::placeholder, input[type="email"]::placeholder { color: #aaa; opacity: 1; }
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
      header { flex-direction: column; align-items: flex-start; gap: 12px; }
      header h2 { font-size: 22px; }
      .header-actions { flex-wrap: wrap; width: 100%; justify-content: flex-start; }
      .container { padding: 16px; }
    }

    @media (max-width:560px){ .modal-panel { padding:16px; border-radius:12px; } .form-row { flex-direction:column; } .filter-row { flex-direction:column; align-items:stretch; } }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .fade-in-content { animation: fadeIn 0.6s ease-out forwards; }
  </style>
  <link rel="stylesheet" href="../css/table-styles.css?v=<?= time() ?>">
  <link rel="stylesheet" href="../css/notification.css?v=<?= time() ?>">
</head>
<body class="fade-in-content">
 <header class="main-header">
    <div class="logo">
        <img src="../admin/assets/img/logo.png" alt="Logo EduClubs" style="height: 80px; margin-right: 10px;">
        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258" style="height: 60px; margin-right: 15px;">
        <span>EduClubs - Panel de Maestro</span>
    </div>
  <div class="header-actions">
    <a href="../view_clubs.php" class="btn">Ver base de clubs</a>
    <button id="openModalBtn" class="btn" type="button" aria-haspopup="dialog" onclick="document.getElementById('modal').classList.add('show');document.body.style.overflow='hidden'"> Agregar club</button>
    <button id="toggleEditBtn" class="btn" type="button">Seleccionar Alumnos</button>
    
    <div style="display: flex; align-items: center; gap: 12px; margin-left: auto;">
        <?php include '../includes/teacher_menu.php'; ?>
        <div class="usericon">
          <div class="avatar"><?=strtoupper($user['username'][0] ?? 'U')?></div>
          <div class="user-menu">
            <div><?=htmlspecialchars($user['user_id'] ?? '')?></div>
            <a href="../auth/logout.php?redirect=index.php" class="logout">Cerrar sesión</a>
          </div>
        </div>
    </div>
  </div>
 </header>

  <div class="container fade-in-content">
    <div id="editToolbar">
      <div class="edit-active">
        <strong>Modo edición activo</strong>
        <button id="bulkDeleteBtn" class="btn btn-icon" title="Eliminar" style="background-color: #c62828; color: white;"><i class="fas fa-trash"></i></button>
        <small style="margin-left:8px;color:#666;">Selecciona las filas de los alumnos que deseas dar de baja.</small>
      </div>
    </div>

    <?php 
    if (!empty($_SESSION['flash']) && empty($_SESSION['flash']['is_form_error'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $type = $flash['type'] ?? 'info';
        $message_html = !empty($flash['msg']) ? htmlspecialchars($flash['msg']) : '';
        if ($message_html) {
            echo "<script>document.addEventListener('DOMContentLoaded', () => { showNotification(" . json_encode($message_html) . ", '" . htmlspecialchars($type) . "'); });</script>";
        }
    }
    if (!empty($_SESSION['flash']['is_form_error'])) unset($_SESSION['flash']);
    ?>

    <?php
    function renderTable($title, $data, $columns, $pageParam, $filterParam = null, $filterOptions = []) {
      $currentParams = $_GET; unset($currentParams['success'], $currentParams['error'], $currentParams['ajax'], $currentParams['type']);
      $cardId = strtolower(str_replace(['í', ' '], ['i', '-'], $title));
      echo "<div class='card fade-in-content' id='card-" . htmlspecialchars($cardId) . "' data-card='" . htmlspecialchars($title) . "'>";
      echo "<h3 class='card-section-title'>" . htmlspecialchars($title) . "</h3>";

      if ($filterParam !== null) renderClubFilterButtons($filterOptions, $filterParam);

      echo "<div class='printable-area'>";
      echo "<h2 class='printable-title' style='display:none;'>" . htmlspecialchars($title) . " - Reporte de Miembros</h2>";

      if (empty($data['rows'])) {
        echo "<p>No hay registros disponibles.</p>";
      } else {
        echo "<div class='table-container'>";
        echo "<table class='styled-table'><thead><tr>";
        echo "<th class='edit-col'></th>";
        foreach ($columns as $label) echo "<th>" . htmlspecialchars($label) . "</th>";
        echo "<th class='edit-action'>Acciones</th>";
        echo "</tr></thead><tbody>";
        foreach ($data['rows'] as $r) {
          $id = htmlspecialchars($r['id'] ?? '');
          $nombres = htmlspecialchars($r['nombres'] ?? '');
          $tr_data_attrs = "data-id='$id' " . implode(' ', array_map(function($k, $v) { return "data-" . htmlspecialchars(str_replace('_', '-', $k)) . "='" . htmlspecialchars($v ?? '') . "'"; }, array_keys($r), $r));
          echo "<tr $tr_data_attrs>";
          echo "<td class='edit-col'><input type='checkbox' class='row-checkbox' data-id='$id'></td>";
          foreach (array_keys($columns) as $key) {
            $value = ($key === 'fecha') ? "<small class='gray'>" . htmlspecialchars($r['created_at'] ?? '') . "</small>" : htmlspecialchars($r[$key] ?? '');
            echo "<td data-label='" . htmlspecialchars($columns[$key]) . "'>$value</td>";
          }
          echo "<td class='edit-action'>";
          echo "<button class='btn small btn-icon btn-delete' data-id='$id' data-nombres='$nombres' title='Dar de baja'><i class='fas fa-user-slash'></i></button>";
          echo "</td>";
          echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</div>";

        $totalPages = ceil($data['total'] / $data['limit']);
        if ($totalPages > 1) {
          echo "<div class='pagination'>";
          for ($i = 1; $i <= $totalPages; $i++) {
            $currentParams[$pageParam] = $i;
            $url = $_SERVER['PHP_SELF'] . '?' . http_build_query($currentParams);
            $activeClass = ($i == $data['page']) ? 'active' : '';
            echo "<a href='$url' class='ajax-page-link $activeClass'>$i</a>";
          }
          echo "</div>";
        }
      }
      echo "</div>";
      $selectedValue = $_GET[$filterParam] ?? '';
      $visibleClass = (!empty($selectedValue) && !empty($data['rows'])) ? 'visible' : '';
      echo '<div class="print-report-btn-container ' . $visibleClass . '"><button class="print-report-btn" data-card-id="' . htmlspecialchars($cardId) . '"><i class="fas fa-print"></i> Imprimir Reporte</button></div>';
      echo "</div>";
    }

    renderTable('Cultural', $culturalData, ['club_name' => 'Club', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'telefono' => 'Teléfono', 'semestre' => 'Semestre', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'page_cultural', 'filter_cultural', $culturales);
    renderTable('Deportivo', $deportivoData, ['club_name' => 'Club', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'telefono' => 'Teléfono', 'semestre' => 'Semestre', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'page_deportivo', 'filter_deportivo', $deportivos);
    renderTable('Civil', $civilData, ['club_name' => 'Club', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'telefono' => 'Teléfono', 'semestre' => 'Semestre', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'page_civil', 'filter_civil', $civiles);
    renderTable('Asesorías', $asesoriasData, ['materia' => 'Materia', 'nombres' => 'Nombres', 'paterno' => 'Ap. Paterno', 'materno' => 'Ap. Materno', 'carrera' => 'Carrera', 'maestro' => 'Maestro', 'telefono' => 'Teléfono', 'correo' => 'Correo', 'user_id' => 'Registró', 'fecha' => 'Fecha'], 'page_asesoria', 'filter_asesoria', $materias_asesoria);
    ?>
  </div>

  <div id="modal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <div class="modal-header">
        <h3 id="modal-title">Nuevo Club</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>
      <form id="addClubForm" method="post" action="../actions/agregar_club.php" novalidate>
        <div class="form-row">
          <div class="field">
            <label class="form-label">Nombre del club</label>
            <input type="text" name="club_name" required placeholder="Ej. Club de Robótica">
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
            <input type="text" name="creator_name" required placeholder="Nombre completo">
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

  <div id="studentProfileModal" class="modal" aria-hidden="true">
    <div class="modal-panel" role="dialog" aria-modal="true" style="max-width: 650px;">
      <div class="modal-header">
        <h3 id="studentProfileModalTitle">Perfil del Alumno</h3>
        <button type="button" class="close-btn close-modal" aria-label="Cerrar">✕</button>
      </div>
      <div class="profile-modal-content">
        <div class="profile-pic-container">
          <div class="profile-pic"></div>
          <div class="student-name"></div>
        </div>
        <div class="profile-details">
          <h4 id="profile-student-name"></h4>
          <div class="detail-item"><i class="fas fa-university icon"></i><span class="label">Club/Materia:</span><span class="value" id="profile-club-name"></span></div>
          <div class="detail-item"><i class="fas fa-id-card icon"></i><span class="label">Matrícula:</span><span class="value" id="profile-user-id"></span></div>
          <div class="detail-item"><i class="fas fa-envelope icon"></i><span class="label">Correo:</span><span class="value" id="profile-email"></span></div>
          <div class="detail-item"><i class="fas fa-phone icon"></i><span class="label">Teléfono:</span><span class="value" id="profile-phone"></span></div>
          <div class="detail-item"><i class="fas fa-graduation-cap icon"></i><span class="label">Semestre:</span><span class="value" id="profile-semester"></span></div>
          <div class="detail-item"><i class="fas fa-briefcase icon"></i><span class="label">Carrera:</span><span class="value" id="profile-carrera"></span></div>
          <div class="detail-item"><i class="far fa-clock icon"></i><span class="label">Turno:</span><span class="value" id="profile-turno"></span></div>
        </div>
      </div>
    </div>
  </div>

<script src="../js/notification.js?v=<?= time() ?>"></script>
<script src="../js/menu.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.container');
    let editMode = false;

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }, 10);
    }

    function showConfirmationModal(title, message, onConfirm) {
        const modal = document.getElementById('confirmationModal');
        if (!modal) return;
        modal.querySelector('#confirmationModalTitle').textContent = title;
        modal.querySelector('#confirmationModalMessage').textContent = message;
        const confirmBtn = modal.querySelector('.btn-confirm');
        const closeModal = () => {
            modal.classList.remove('show');
            document.body.style.overflow = '';
            confirmBtn.replaceWith(confirmBtn.cloneNode(true)); // Remove listeners
        };
        confirmBtn.addEventListener('click', () => { onConfirm(); closeModal(); }, { once: true });
        modal.querySelectorAll('.close-modal, .btn-cancel').forEach(btn => btn.addEventListener('click', closeModal, { once: true }));
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function handleAjaxNavigation(url, card) {
        history.pushState(null, '', url.toString());
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';
        const fetchUrl = new URL(url.toString());
        fetchUrl.searchParams.set('ajax', '1');
        fetchUrl.searchParams.set('type', card.id.replace('card-', ''));
        fetch(fetchUrl.toString())
            .then(response => response.text())
            .then(html => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;
                const newCard = tempDiv.firstChild;
                if (newCard) card.parentNode.replaceChild(newCard, card);
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                showNotification('Error al cargar los datos.', 'error');
                if(card) { card.style.opacity = '1'; card.style.pointerEvents = 'auto'; }
            });
    }

    function setEditMode(on) {
        editMode = !!on;
        document.body.classList.toggle('edit-mode-active', on);
        document.querySelectorAll('.card').forEach(c => c.classList.toggle('editing', on));
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
        if (!on) document.querySelectorAll('tr.row-selected').forEach(tr => tr.classList.remove('row-selected'));
        document.getElementById('editToolbar').style.display = on ? 'flex' : 'none';
        document.getElementById('toggleEditBtn').textContent = on ? 'Cancelar Selección' : 'Seleccionar Alumnos';
    }

    container.addEventListener('click', e => {
        const ajaxLink = e.target.closest('.ajax-page-link');
        if (ajaxLink) {
            e.preventDefault();
            handleAjaxNavigation(new URL(ajaxLink.href), ajaxLink.closest('.card'));
            return;
        }

        const filterBtn = e.target.closest('.club-filter-btn');
        if (filterBtn) {
            e.preventDefault();
            const param = filterBtn.dataset.filterParam;
            const value = filterBtn.dataset.filterValue;
            filterBtn.closest('.club-filters').querySelectorAll('.club-filter-btn').forEach(b => b.classList.remove('active'));
            filterBtn.classList.add('active');
            const url = new URL(window.location.href);
            if (value) url.searchParams.set(param, value); else url.searchParams.delete(param);
            const pageParam = `page_${param.split('_')[1]}`;
            url.searchParams.set(pageParam, '1');
            handleAjaxNavigation(url, filterBtn.closest('.card'));
            return;
        }

        const printBtn = e.target.closest('.print-report-btn');
        if (printBtn) {
            const card = printBtn.closest('.card');
            const type = card.id.replace('card-', '');
            const filterValue = card.querySelector('.club-filter-btn.active')?.dataset.filterValue || '';
            if (type && filterValue) {
                window.open(`print_report.php?type=${encodeURIComponent(type)}&filter_value=${encodeURIComponent(filterValue)}`, '_blank');
            } else {
                showNotification('Por favor, selecciona un club para generar el reporte.', 'error');
            }
            return;
        }

        const tr = e.target.closest('tr[data-id]');
        if (tr && !e.target.closest('button, input, a, select, textarea')) {
            if (editMode) {
                const cb = tr.querySelector('.row-checkbox');
                if (cb) {
                    cb.checked = !cb.checked;
                    tr.classList.toggle('row-selected', cb.checked);
                }
            } else {
                const modal = document.getElementById('studentProfileModal');
                if (!modal) return;
                const studentData = tr.dataset;
                const getData = (key) => (studentData[key] || 'N/A').toUpperCase();
                
                modal.querySelector('.student-name').textContent = getData('nombres');
                modal.querySelector('#profile-student-name').textContent = `${getData('nombres')} ${getData('paterno')} ${getData('materno')}`.trim();
                
                const profilePicContainer = modal.querySelector('.profile-pic');
                profilePicContainer.innerHTML = '';
                const picPath = studentData.profilePicPath;
                if (picPath && picPath.trim() !== '') {
                    const img = document.createElement('img');
                    img.src = `${BASE_PATH}/${picPath}`;
                    img.alt = 'Foto de perfil';
                    img.onerror = () => { profilePicContainer.textContent = 'NO CUENTA CON FOTO DE PERFIL'; };
                    profilePicContainer.appendChild(img);
                } else {
                    profilePicContainer.textContent = 'NO CUENTA CON FOTO DE PERFIL';
                }

                modal.querySelector('#profile-club-name').textContent = getData('clubName');
                modal.querySelector('#profile-user-id').textContent = getData('userId');
                modal.querySelector('#profile-email').textContent = (studentData.correo || 'N/A').toUpperCase();
                modal.querySelector('#profile-phone').textContent = getData('telefono');
                modal.querySelector('#profile-semester').textContent = getData('semestre');
                modal.querySelector('#profile-carrera').textContent = getData('carrera');
                modal.querySelector('#profile-turno').textContent = getData('turno');

                modal.classList.add('show');
                document.body.style.overflow = 'hidden';
            }
            return;
        }

        const deleteBtn = e.target.closest('.btn-delete');
        if (deleteBtn) {
            const id = deleteBtn.dataset.id;
            const nombre = deleteBtn.dataset.nombres || 'este alumno';
            showConfirmationModal('Confirmar baja', `¿Estás seguro de que quieres dar de baja a ${nombre}?`, async () => {
                try {
                    const res = await fetch('../actions/bulk_delete_members.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ ids: [id] })
                    });
                    const data = await res.json();
                    if (data.success) {
                        showNotification('Alumno dado de baja.');
                        const tr = document.querySelector(`tr[data-id="${id}"]`);
                        if (tr) {
                            tr.style.opacity = '0';
                            setTimeout(() => tr.remove(), 300);
                        }
                    } else {
                        showNotification(data.message || 'Error al dar de baja.', 'error');
                    }
                } catch (err) {
                    showNotification('Error de conexión.', 'error');
                }
            });
        }
    });

    document.getElementById('toggleEditBtn')?.addEventListener('click', () => setEditMode(!editMode));
    document.getElementById('bulkDeleteBtn')?.addEventListener('click', () => {
        const ids = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.dataset.id);
        if (!ids.length) return showNotification('Selecciona al menos un alumno.', 'error');
        showConfirmationModal(`Confirmar baja`, `¿Dar de baja a ${ids.length} alumno(s)?`, async () => {
            try {
                const res = await fetch('../actions/bulk_delete_members.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ ids })
                });
                const data = await res.json();
                if (data.success) {
                    showNotification(`${ids.length} alumno(s) dado(s) de baja.`);
                    ids.forEach(id => {
                        const tr = document.querySelector(`tr[data-id="${id}"]`);
                        if (tr) tr.remove();
                    });
                } else {
                    showNotification(data.message || 'Error al dar de baja.', 'error');
                }
            } catch (err) {
                showNotification('Error de conexión.', 'error');
            }
        });
    });

    document.querySelectorAll('.close-modal, .close-btn').forEach(b => b.addEventListener('click', e => {
        const panel = e.target.closest('.modal');
        if(panel) {
            panel.classList.remove('show');
            document.body.style.overflow = '';
        }
    }));

    const addClubForm = document.getElementById('addClubForm');
    if (addClubForm) {
        addClubForm.addEventListener('submit', async e => {
            e.preventDefault();
            const formData = new FormData(addClubForm);
            const res = await fetch('../actions/agregar_club.php', { method: 'POST', body: formData });
            const result = await res.json();
            if (result.success) {
                showNotification(result.message, 'success');
                addClubForm.reset();
                document.getElementById('modal').classList.remove('show');
                document.body.style.overflow = '';
                location.reload();
            } else {
                showNotification(result.message, 'error');
            }
        });
    }
});
</script>
</body>
</html>
