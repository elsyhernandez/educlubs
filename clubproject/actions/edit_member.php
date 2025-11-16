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
    // 1. Get the user's control number (`user_id`) from the registration record.
    $stmt_user = $pdo->prepare("SELECT user_id FROM `$table` WHERE id = ?");
    $stmt_user->execute([$id]);
    $user_record = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user_record || empty($user_record['user_id'])) {
        // Fallback for older records without a user_id.
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode(['success' => true, 'message' => 'Miembro (registro antiguo) actualizado.']);
        exit;
    }
    $user_control_id = $user_record['user_id'];

    // 2. Check if the submitted email is used by any OTHER user.
    // We check against the unique control number (`user_id`).
    $stmt_email_check = $pdo->prepare("SELECT COUNT(*) FROM `users` WHERE email = ? AND user_id <> ?");
    $stmt_email_check->execute([$correo, $user_control_id]);
    $email_count = $stmt_email_check->fetchColumn();

    if ($email_count > 0) {
        echo json_encode(['success' => false, 'message' => 'El correo ya está en uso por otro usuario.']);
        exit;
    }

    $pdo->beginTransaction();

    // 3. Update the central 'users' table using the control number.
    $lastname = trim($paterno . ' ' . $materno);
    $semestre = ($type !== 'asesoria') ? trim($_POST['semestre'] ?? '') : null;

    $user_update_sql = "UPDATE `users` SET name = ?, lastname = ?, email = ?, phone = ?";
    $user_params = [$nombres, $lastname, $correo, $telefono];
    
    if ($semestre !== null) {
        $user_update_sql .= ", semester = ?";
        $user_params[] = $semestre;
    }
    
    $user_update_sql .= " WHERE user_id = ?";
    $user_params[] = $user_control_id;

    $pdo->prepare($user_update_sql)->execute($user_params);

    // 4. Propagate common fields to all registration records for that user.
    $common_update_club = "UPDATE `club_registrations` SET paterno = ?, materno = ?, nombres = ?, correo = ?, telefono = ?, semestre = ? WHERE user_id = ?";
    $pdo->prepare($common_update_club)->execute([$paterno, $materno, $nombres, $correo, $telefono, $semestre, $user_control_id]);

    $common_update_tutoring = "UPDATE `tutoring_registrations` SET paterno = ?, materno = ?, nombres = ?, correo = ?, telefono = ? WHERE user_id = ?";
    $pdo->prepare($common_update_tutoring)->execute([$paterno, $materno, $nombres, $correo, $telefono, $user_control_id]);

    // 5. Update specific fields for the single, edited registration record (if applicable).
    if ($type === 'asesoria') {
        $carrera = trim($_POST['carrera'] ?? '');
        $maestro = trim($_POST['maestro'] ?? '');
        $specific_sql = "UPDATE `tutoring_registrations` SET carrera = ?, maestro = ? WHERE id = ?";
        $pdo->prepare($specific_sql)->execute([$carrera, $maestro, $id]);
    }
    // For club registrations, all relevant fields are now propagated in step 4.

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Miembro actualizado correctamente en todos sus registros.']);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $error_user_ref = isset($user_control_id) ? "user_control_id: $user_control_id" : "registration_id: $id";
    error_log("Error al actualizar miembro ($error_user_ref): " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos al actualizar.']);
}
