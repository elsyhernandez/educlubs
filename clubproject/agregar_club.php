<?php
require 'config.php';

// Verifica que el usuario esté autenticado y sea maestro
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
  redirect('login.php');
}

// Procesa el formulario solo si es POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Sanitiza y recoge los datos del formulario
  $club_name = trim($_POST['club_name']);
  $club_id = trim($_POST['club_id']);
  $password = trim($_POST['password']);
  $creator_name = trim($_POST['creator_name']);

  // Validación básica
  if ($club_name === '' || $club_id === '' || $password === '' || $creator_name === '') {
    die('Todos los campos son obligatorios.');
  }

  // Hashea la contraseña
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Inserta en la base de datos
  $stmt = $pdo->prepare("INSERT INTO clubs (club_id, club_name, password, creator_name, created_at) VALUES (?, ?, ?, ?, NOW())");
  $stmt->execute([$club_id, $club_name, $hashed_password, $creator_name]);

  // Redirige de vuelta al panel
  header('Location: teacher_dashboard.php');
  exit;
} else {
  // Si alguien accede directamente sin POST
  header('Location: teacher_dashboard.php');
  exit;
}
?>