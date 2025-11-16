<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

try {
    // 1. Session and Role Validation
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
        http_response_code(403); // Forbidden
        echo json_encode(['success' => false, 'error' => 'Acceso no autorizado.']);
        exit;
    }

    $teacher_id = $_SESSION['user']['user_id'];

    // 2. Fetch Unread Notifications
    $stmt = $pdo->prepare(
        "SELECT id, student_name, club_name, created_at 
         FROM notifications 
         WHERE teacher_id = :teacher_id AND is_read = 0 
         ORDER BY created_at DESC"
    );
    $stmt->execute([':teacher_id' => $teacher_id]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Return JSON Response
    echo json_encode(['success' => true, 'notifications' => $notifications]);

} catch (PDOException $e) {
    error_log("Database Error in fetch_notifications.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error en la base de datos.']);
} catch (Exception $e) {
    error_log("General Error in fetch_notifications.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Ocurrió un error inesperado.']);
}
