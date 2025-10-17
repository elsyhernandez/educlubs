<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Activar reporte de errores como excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuarios";

$guardado = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8");

        // Obtener datos del formulario
        $id_usuario = $_POST['id_usuario'] ?? '';
        $id_club = $_POST['id_club'] ?? '';
        $nombre_club = $_POST['nombre_club'] ?? '';
        $tipo_club = $_POST['tipo_club'] ?? '';

        // Validar que no estén vacíos
        if ($id_usuario && $id_club && $nombre_club && $tipo_club) {
            $stmt = $conn->prepare("INSERT INTO clubs (id_club, nombre_club, tipo_club, id_usuario) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $id_club, $nombre_club, $tipo_club, $id_usuario);

            $guardado = $stmt->execute();
            $stmt->close();
        } else {
            $error = "faltan_datos";
        }

        $conn->close();
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $error = "usuario_duplicado";
        } else {
            $error = "otro";
        }
    }
} else {
    $error = "no_post";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Resultado del Registro</title>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
<?php if ($guardado): ?>
Swal.fire({
    icon: 'success',
    title: '¡Registro exitoso!',
    text: 'Tu registro en el club ha sido completado 🎉',
    confirmButtonText: 'Aceptar',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'index.html';
});
<?php elseif ($error === "usuario_duplicado"): ?>
Swal.fire({
    icon: 'error',
    title: 'Usuario ya registrado',
    text: 'Este ID de usuario ya está registrado en un club. Usa otro ID o edita el existente.',
    confirmButtonText: 'Volver',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'index.html';
});
<?php elseif ($error === "faltan_datos"): ?>
Swal.fire({
    icon: 'warning',
    title: 'Campos incompletos',
    text: 'Por favor completa todos los campos antes de enviar.',
    confirmButtonText: 'Volver',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'index.html';
});
<?php elseif ($error === "no_post"): ?>
Swal.fire({
    icon: 'error',
    title: 'Acceso inválido',
    text: 'Este archivo solo acepta envíos desde el formulario.',
    confirmButtonText: 'Volver',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'index.html';
});
<?php elseif ($error === "otro"): ?>
Swal.fire({
    icon: 'error',
    title: 'Error inesperado',
    text: 'Ocurrió un error al guardar los datos.',
    confirmButtonText: 'Volver',
    confirmButtonColor: '#ff0000de'
}).then(() => {
    window.location.href = 'index.html';
});
<?php endif; ?>
</script>
</body>
</html>