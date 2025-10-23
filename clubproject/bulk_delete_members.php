<?php
require 'config.php';
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

try {
    // prepare placeholders
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $sql = "DELETE FROM club_registrations WHERE id IN ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($ids);
    echo json_encode(['success' => true, 'deleted' => $stmt->rowCount()]);
} catch (Throwable $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al eliminar registros.']);
}
