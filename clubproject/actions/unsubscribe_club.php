<?php
// =================================================================
// --- EduClubs: Unsubscribe from Club Handler ---
// =================================================================

// --- Environment Setup ---
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// --- Utility Function for JSON Response ---
function send_json_response($success, $message) {
    http_response_code($success ? 200 : 400);
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

try {
    // --- Initialization and Configuration ---
    require_once '../includes/config.php';

    if (!isset($pdo)) {
        throw new RuntimeException("Database connection is not available.");
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Session and Input Validation ---
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
        send_json_response(false, 'Your session has expired. Please log in again.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json_response(false, 'Invalid request method.');
    }

    $user_id = $_SESSION['user']['user_id'];
    $club_name = isset($_POST['club_name']) ? trim($_POST['club_name']) : null;
    $club_type = isset($_POST['club_type']) ? trim($_POST['club_type']) : null;

    if (empty($club_name) || empty($club_type)) {
        send_json_response(false, 'Club name and type are required.');
    }

    // --- Business Logic: Delete Registration ---
    $pdo->beginTransaction();

    if ($club_type === 'asesoria') {
        $table = 'tutoring_registrations';
        $column = 'materia';
    } else {
        $table = 'club_registrations';
        $column = 'club_name';
    }

    $sql = "DELETE FROM {$table} WHERE user_id = :user_id AND {$column} = :club_name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':club_name' => $club_name
    ]);

    $rowCount = $stmt->rowCount();
    $pdo->commit();

    // --- Response ---
    if ($rowCount > 0) {
        send_json_response(true, 'Has sido dado de baja del club correctamente.');
    } else {
        send_json_response(false, 'No se encontró tu registro en este club. Es posible que ya hayas sido dado de baja.');
    }

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Database Error in unsubscribe_club.php: ' . $e->getMessage());
    send_json_response(false, 'Ocurrió un error al procesar tu solicitud.');
} catch (Exception $e) {
    error_log('General Error in unsubscribe_club.php: ' . $e->getMessage());
    send_json_response(false, 'Ocurrió un error inesperado en el servidor.');
}
