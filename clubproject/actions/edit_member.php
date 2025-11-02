<?php
require '../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

$id = intval($_POST['id'] ?? 0);
$type = trim($_POST['type'] ?? '');

if ($id <= 0 || empty($type)) {
    echo json_encode(['success' => false, 'message' => 'ID o tipo de registro inválido.']);
    exit;
}

$paterno = trim($_POST['paterno'] ?? '');
$materno = trim($_POST['materno'] ?? '');
$nombres = trim($_POST['nombres'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

// Enhanced validation
if (empty($paterno) || empty($nombres) || empty($correo)) {
    echo json_encode(['success' => false, 'message' => 'Los campos Paterno, Nombres y Correo son obligatorios.']);
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El formato del correo no es válido.']);
    exit;
}

$table = '';
$sql = '';
$params = [];

if ($type === 'asesoria') {
    $table = 'tutoring_registrations';
    $carrera = trim($_POST['carrera'] ?? '');
    $maestro = trim($_POST['maestro'] ?? '');
    
    $sql = "UPDATE `$table` SET paterno = ?, materno = ?, nombres = ?, correo = ?, telefono = ?, carrera = ?, maestro = ? WHERE id = ?";
    $params = [$paterno, $materno, $nombres, $correo, $telefono, $carrera, $maestro, $id];
} else {
    $table = 'club_registrations';
    $semestre = trim($_POST['semestre'] ?? '');

    $sql = "UPDATE `$table` SET paterno = ?, materno = ?, nombres = ?, correo = ?, semestre = ?, telefono = ? WHERE id = ?";
    $params = [$paterno, $materno, $nombres, $correo, $semestre, $telefono, $id];
}

try {
    // Check for duplicate email in the correct table
    $stmt = $pdo->prepare("SELECT id FROM `$table` WHERE correo = ? AND id <> ?");
    $stmt->execute([$correo, $id]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El correo ya está en uso por otro miembro.']);
        exit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Miembro actualizado correctamente.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'No se realizaron cambios.']);
    }

} catch (PDOException $e) {
    error_log("Error al actualizar miembro ($table): " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos al actualizar.']);
}
