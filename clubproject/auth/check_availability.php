<?php
require '../includes/config.php';

header('Content-Type: application/json');

$response = ['available' => false, 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field = $_POST['field'] ?? '';
    $value = trim($_POST['value'] ?? '');

    if (empty($field) || empty($value)) {
        $response['message'] = 'Field and value are required.';
        echo json_encode($response);
        exit();
    }

    // Whitelist of allowed fields to prevent SQL injection on column names
    $allowed_fields = ['email', 'telefono'];
    if (!in_array($field, $allowed_fields)) {
        $response['message'] = 'Invalid field specified.';
        echo json_encode($response);
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE {$field} = ?");
        $stmt->execute([$value]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $response['available'] = false;
            $response['message'] = 'Este dato ya se encuentra registrado.';
        } else {
            $response['available'] = true;
            $response['message'] = ucfirst($field) . ' is available.';
        }
    } catch (PDOException $e) {
        // Log the error, don't expose details to the user
        error_log($e->getMessage());
        $response['message'] = 'A database error occurred.';
    }
}

echo json_encode($response);
?>
