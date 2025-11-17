<?php
require '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

$materia = $_POST['club_name'] ?? '';
$maestro = $_POST['creator_name'] ?? '';

if (empty($materia) || empty($maestro)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

try {
    $sql = "UPDATE tutoring_registrations SET maestro = ? WHERE materia = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$maestro, $materia])) {
        echo json_encode(['success' => true, 'message' => 'Maestro asignado correctamente a la materia.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al asignar el maestro.']);
    }
} catch (PDOException $e) {
    error_log("Error en asignar_maestro.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Ocurrió un error en la base de datos.']);
}
?>
