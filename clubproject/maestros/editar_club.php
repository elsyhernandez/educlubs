<?php
require '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    echo json_encode(['success' => false, 'message' => 'Acceso no autorizado.']);
    exit;
}

$club_id = $_POST['club_id'] ?? '';
$club_name = trim($_POST['club_name'] ?? '');
$creator_name = trim($_POST['creator_name'] ?? '');
$club_type = trim($_POST['club_type'] ?? '');

if (empty($club_id) || empty($club_name) || empty($creator_name) || empty($club_type)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE clubs SET club_name = ?, creator_name = ?, club_type = ? WHERE club_id = ?");
    $stmt->execute([$club_name, $creator_name, $club_type, $club_id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Club actualizado correctamente.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se realizaron cambios o el club no fue encontrado.']);
    }
} catch (PDOException $e) {
    // Log error here if needed
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos al actualizar el club.']);
}
