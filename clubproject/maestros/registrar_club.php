<?php
$conn = new mysqli("localhost", "root", "", "usuarios");
$conn->set_charset("utf8");

$tipo = $_POST['tipo_club'];
$nombre = $_POST['nombre_club'];
$responsable = $_POST['nombre'] . " " . $_POST['apellidos'];
$password = $_POST['password'];

$stmt = $conn->prepare("INSERT INTO clubs (tipo_club, nombre_club, responsable, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $tipo, $nombre, $responsable, $password);
$stmt->execute();

echo "<script>alert('Club registrado exitosamente'); window.location.href='maestros.html';</script>";

$stmt->close();
$conn->close();
?>