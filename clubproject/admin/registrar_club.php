<?php
require '../includes/config.php'; // Use the central config for PDO connection

// Assuming this is an admin action that requires a session.
// For now, focusing on fixing the provided logic.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../auth/index.php'); // Redirect if not a POST request
    exit;
}

$club_type = $_POST['tipo_club'] ?? '';
$club_name = $_POST['nombre_club'] ?? '';
$creator_name = ($_POST['nombre'] ?? '') . " " . ($_POST['apellidos'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($club_type) || empty($club_name) || empty($creator_name) || empty($password)) {
    echo "<script>alert('Todos los campos son obligatorios.'); window.history.back();</script>";
    exit;
}

// Hash the password for secure storage
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Generate a unique club ID
$prefix = strtoupper(substr($club_type, 0, 3));
$is_unique = false;
$club_id = '';

while (!$is_unique) {
    $random_part = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
    $club_id = $prefix . '_' . $random_part;

    $check = $pdo->prepare("SELECT COUNT(*) FROM clubs WHERE club_id = ?");
    $check->execute([$club_id]);
    if ($check->fetchColumn() == 0) {
        $is_unique = true;
    }
}

try {
    // Use correct column names and insert the hashed password
    $stmt = $pdo->prepare(
        "INSERT INTO clubs (club_id, club_type, club_name, creator_name, password) 
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->execute([$club_id, $club_type, $club_name, trim($creator_name), $password_hash]);

    echo "<script>alert('Club registrado exitosamente'); window.location.href='maestros.html';</script>";

} catch (PDOException $e) {
    // In a real app, log this error instead of displaying it.
    // For now, a simple alert will suffice.
    echo "<script>alert('Error al registrar el club. Por favor, inténtelo de nuevo.'); window.history.back();</script>";
}

?>
