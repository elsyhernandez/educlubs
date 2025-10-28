<?php
$nombre = $_GET['nombre'] ?? '';
$conn = new mysqli("localhost", "root", "", "usuarios");
$conn->set_charset("utf8");

$stmt = $conn->prepare("SELECT nombre_usuario, correo FROM usuarios_club WHERE nombre_club LIKE ?");
$like = "%$nombre%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

echo "<table border='1'><tr><th>Nombre</th><th>Correo</th></tr>";
while ($row = $result->fetch_assoc()) {
  echo "<tr><td>{$row['nombre_usuario']}</td><td>{$row['correo']}</td></tr>";
}
echo "</table>";

$stmt->close();
$conn->close();
?>