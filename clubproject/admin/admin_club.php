<?php
require '../includes/config.php'; // Use the central config for PDO connection

// Admin actions should be protected. Assuming a session check is needed.
// For now, focusing on fixing the provided logic.
// IMPORTANT: Credentials should be sent via POST, not GET.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // For simplicity, redirecting to a non-existent admin login page.
    // In a real app, this would be the actual admin login form.
    header('Location: admin_login.html'); 
    exit;
}

$id = $_POST['id'] ?? '';
$pass = $_POST['pass'] ?? '';

if (empty($id) || empty($pass)) {
    echo "<script>alert('ID y contraseña son obligatorios'); window.history.back();</script>";
    exit;
}

// 1. Fetch club data including the password hash
$stmt = $pdo->prepare("SELECT club_id, club_name, password FROM clubs WHERE club_id = ?");
$stmt->execute([$id]);
$club_data = $stmt->fetch(PDO::FETCH_ASSOC);

// 2. Verify password
if ($club_data && password_verify($pass, $club_data['password'])) {
    $club_name = htmlspecialchars($club_data['club_name']);
    $club_id = htmlspecialchars($club_data['club_id']);

    echo "<h2>Modo Administrador: $club_name (ID: $club_id)</h2>";
    
    // The following section refers to `usuarios_club` and `usuarios` tables
    // which are not defined in the provided `clubs_db.sql` schema.
    // This part of the code is likely broken or belongs to a different database schema.
    // I am commenting it out to prevent errors.
    /*
    echo "<form method='POST' action='editar_club.php' onsubmit='return confirmarBaja()'>";
    echo "<table border='1' cellpadding='5'><tr><th>Seleccionar</th><th>Nombre</th><th>Correo</th></tr>";

    // Obtener usuarios del club
    // NOTE: This query will fail as `usuarios_club` table is not in the schema.
    $usuarios_stmt = $conn->prepare("SELECT id_usuario, nombre_usuario, correo FROM usuarios_club WHERE id_club = ?");
    $usuarios_stmt->bind_param("s", $id);
    $usuarios_stmt->execute();
    $usuarios_result = $usuarios_stmt->get_result();

    while ($row = $usuarios_result->fetch_assoc()) {
        echo "<tr>
        <td><input type='checkbox' name='baja[]' value='{$row['id_usuario']}'></td>
        <td><input type='text' name='nombre[{$row['id_usuario']}]' value='{$row['nombre_usuario']}'></td>
        <td><input type='text' name='correo[{$row['id_usuario']}]' value='{$row['correo']}'></td>
        </tr>";
    }

    echo "</table><br><button type='submit'>Actualizar</button></form>";
    echo "<script>
        function confirmarBaja() {
        const seleccionados = document.querySelectorAll('input[type=\"checkbox\"]:checked').length;
        if (seleccionados > 0) {
            return confirm('¿Estás seguro de que deseas dar de baja a los usuarios seleccionados?');
        }
        return true;
        }
    </script>";
    */
    echo "<p>Funcionalidad de edición de miembros del club no disponible actualmente debido a un esquema de base de datos inconsistente.</p>";

} else {
    // Generic error message to prevent user enumeration
    echo "<script>alert('ID o contraseña incorrectos'); window.history.back();</script>";
}

?>
