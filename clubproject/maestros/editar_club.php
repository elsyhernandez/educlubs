<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli("localhost", "root", "", "usuarios");
$conn->set_charset("utf8");

$bajas = $_POST['baja'] ?? [];
$nombres = $_POST['nombre'] ?? [];
$correos = $_POST['correo'] ?? [];

$exito = false;

// Dar de baja usuarios
if (!empty($bajas)) {
    foreach ($bajas as $id_usuario) {
        $stmt = $conn->prepare("DELETE FROM usuarios_club WHERE id_usuario = ?");
        $stmt->bind_param("s", $id_usuario);
        $stmt->execute();
        $stmt->close();
    }
    $exito = true;
}

// Editar información de usuarios
foreach ($nombres as $id => $nuevo_nombre) {
    $nuevo_correo = $correos[$id];
    $stmt = $conn->prepare("UPDATE usuarios_club SET nombre_usuario = ?, correo = ? WHERE id_usuario = ?");
    $stmt->bind_param("sss", $nuevo_nombre, $nuevo_correo, $id);
    $stmt->execute();
    $stmt->close();
    $exito = true;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Edición de Club</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php if ($exito): ?>
Swal.fire({
    icon: 'success',
    title: 'Cambios aplicados',
    text: 'Los datos han sido actualizados correctamente.',
    confirmButtonText: 'Volver',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'maestros.html';
});
<?php else: ?>
Swal.fire({
    icon: 'info',
    title: 'Sin cambios',
    text: 'No se realizaron modificaciones.',
    confirmButtonText: 'Volver',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'maestros.html';
});
<?php endif; ?>
</script>
</body>
</html>