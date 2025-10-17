<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = $_GET['id'] ?? '';
$pass = $_GET['pass'] ?? '';

$conn = new mysqli("localhost", "root", "", "usuarios");
$conn->set_charset("utf8");

// Verificar credenciales del club
$stmt = $conn->prepare("SELECT nombre_club FROM clubs WHERE id_club = ? AND password = ?");
$stmt->bind_param("ss", $id, $pass);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $club = $result->fetch_assoc()['nombre_club'];
  echo "<h2>Modo Administrador: $club (ID: $id)</h2>";
  echo "<form method='POST' action='editar_club.php' onsubmit='return confirmarBaja()'>";
  echo "<table border='1' cellpadding='5'><tr><th>Seleccionar</th><th>Nombre</th><th>Correo</th></tr>";

  // Obtener usuarios del club
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

  $usuarios_stmt->close();
} else {
  echo "<script>alert('ID o contraseña incorrectos'); window.location.href='maestros.html';</script>";
}

$stmt->close();
$conn->close();
?>