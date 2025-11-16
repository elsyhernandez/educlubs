<?php
session_start();
include '../includes/config.php'; // Asegúrate de que la ruta al archivo de configuración sea correcta

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

$teacher_id = $_SESSION['user_id'];

try {
    $pdo = get_db();
    $stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE teacher_id = :teacher_id AND is_read = 0");
    $stmt->execute(['teacher_id' => $teacher_id]);

    echo json_encode(['success' => true, 'message' => 'Notificaciones marcadas como leídas.']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar las notificaciones: ' . $e->getMessage()]);
}
?>
