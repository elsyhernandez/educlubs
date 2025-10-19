<?php
require 'config.php';
header('Content-Type: application/json; charset=utf-8');

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) { echo json_encode(['success'=>false,'message'=>'ID inválido']); exit; }

$member_id = trim($_POST['member_id'] ?? '');
$paterno   = trim($_POST['paterno'] ?? '');
$materno   = trim($_POST['materno'] ?? '');
$nombres   = trim($_POST['nombres'] ?? '');
$correo    = trim($_POST['correo'] ?? '');
$semestre  = trim($_POST['semestre'] ?? '');
$turno     = trim($_POST['turno'] ?? '');

try {
    $stmt = $pdo->prepare("UPDATE club_registrations SET user_id = ?, paterno = ?, materno = ?, nombres = ?, correo = ?, semestre = ?, turno = ? WHERE id = ?");
    $stmt->execute([$member_id, $paterno, $materno, $nombres, $correo, $semestre, $turno, $id]);
    echo json_encode(['success' => true, 'rows' => $stmt->rowCount()]);
} catch (Throwable $e) {
    error_log($e->getMessage());
    echo json_encode(['success'=>false,'message'=>'Error al actualizar.']);
}
