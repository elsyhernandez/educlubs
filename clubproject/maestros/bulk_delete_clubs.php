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

$data = json_decode(file_get_contents('php://input'), true);
$club_names = $data['club_names'] ?? [];

if (empty($club_names)) {
    echo json_encode(['success' => false, 'message' => 'No se proporcionaron nombres de clubes para eliminar.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Delete members from club_registrations
    $placeholders = implode(',', array_fill(0, count($club_names), '?'));
    $stmt = $pdo->prepare("DELETE FROM club_registrations WHERE club_name IN ($placeholders)");
    $stmt->execute($club_names);

    // Delete clubs
    $stmt = $pdo->prepare("DELETE FROM clubs WHERE club_name IN ($placeholders)");
    $stmt->execute($club_names);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Club(es) y sus miembros han sido eliminados.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    // Log error here if needed
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos al eliminar los clubes.']);
}
