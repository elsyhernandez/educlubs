<?php
require '../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['ids']) || !is_array($input['ids']) || empty($input['ids'])) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron IDs.']);
    exit;
}

// sanitize integer ids
$ids = array_values(array_filter(array_map('intval', $input['ids']), function($v){ return $v > 0; }));
if (empty($ids)) {
    echo json_encode(['success' => false, 'message' => 'IDs inválidos.']);
    exit;
}

// Get context for pagination recalculation
$clubType = $input['club_type'] ?? null;
$filterValue = $input['filter_value'] ?? null;
$currentPage = isset($input['current_page']) ? intval($input['current_page']) : 1;
$limit = 10;

// Determine table and columns based on club type
$isAsesoria = $clubType === 'asesorias';
$tableName = $isAsesoria ? 'tutoring_registrations' : 'club_registrations';
$nameColumn = $isAsesoria ? 'nombre' : "CONCAT(nombres, ' ', paterno)";

try {
    // First, get the names before deleting
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sqlSelect = "SELECT $nameColumn as nombre_completo FROM $tableName WHERE id IN ($placeholders)";
    $stmtSelect = $pdo->prepare($sqlSelect);
    $stmtSelect->execute($ids);
    $nombres = $stmtSelect->fetchAll(PDO::FETCH_COLUMN);
    
    // Now, delete the records
    $sql = "DELETE FROM $tableName WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $deletedCount = $stmt->execute($ids) ? $stmt->rowCount() : 0;

    // Recalculate pagination
    $params = [];
    if ($isAsesoria) {
        $where = '1=1';
        if ($filterValue) {
            $where = "materia = ?";
            $params[] = $filterValue;
        }
    } else {
        $where = "club_type = ?";
        $params[] = $clubType;
        if ($filterValue) {
            $where .= " AND club_name = ?";
            $params[] = $filterValue;
        }
    }

    $countSql = "SELECT COUNT(*) FROM $tableName WHERE $where";
    $stmt = $pdo->prepare($countSql);
    $stmt->execute($params);
    $total = (int) $stmt->fetchColumn();

    $totalPages = max(1, ceil($total / $limit));
    $newPage = min($currentPage, $totalPages);

    echo json_encode([
        'success' => true, 
        'deleted' => $deletedCount, 
        'nombres' => $nombres,
        'newPage' => $newPage,
        'needsReload' => $currentPage != $newPage || ($total > 0 && $deletedCount > 0 && $total % $limit === 0)
    ]);

} catch (Throwable $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al eliminar registros.']);
}
