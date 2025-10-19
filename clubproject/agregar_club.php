<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
  redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Recoger y sanitizar los datos del formulario
  $club_name    = trim($_POST['club_name']);
  $club_id      = trim($_POST['club_id']);
  $password     = trim($_POST['password']);
  $creator_name = trim($_POST['creator_name']);
  $club_type    = trim($_POST['club_type']);

  // Validación básica
  if ($club_name === '' || $club_id === '' || $password === '' || $creator_name === '' || $club_type === '') {
    header('Location: teacher_dashboard.php?error=campos');
    exit;
  }

  // Verificar si el ID del club ya existe
  $check = $pdo->prepare("SELECT COUNT(*) FROM clubs WHERE club_id = ?");
  $check->execute([$club_id]);
  if ($check->fetchColumn() > 0) {
    header('Location: teacher_dashboard.php?error=duplicado');
    exit;
  }

  // Hashear la contraseña
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);

  // Insertar el nuevo club en la base de datos
  $stmt = $pdo->prepare("INSERT INTO clubs (club_id, club_name, password, creator_name, club_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
  $stmt->execute([$club_id, $club_name, $hashed_password, $creator_name, $club_type]);

  // Redirigir al panel con mensaje de éxito
  header('Location: teacher_dashboard.php?success=1');
  exit;
} else {
  // Si se accede directamente sin POST, redirigir al panel
  header('Location: teacher_dashboard.php');
  exit;
}
