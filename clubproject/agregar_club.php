<?php
require 'config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_name    = trim($_POST['club_name']);
    $creator_name = trim($_POST['creator_name']);
    $club_type    = trim($_POST['club_type']);
    $errors = [];

    // 1. Validaciones
    if (empty($club_name)) {
        $errors[] = "El nombre del club es obligatorio.";
    } elseif (!preg_match('/[a-zA-Z]/', $club_name)) {
        $errors[] = "El nombre del club debe contener al menos una letra.";
    } elseif (is_numeric($club_name)) {
        $errors[] = "El nombre del club no puede ser solo números.";
    }

    if (empty($creator_name)) {
        $errors[] = "El nombre del creador es obligatorio.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $creator_name)) {
        $errors[] = "El nombre del creador solo debe contener letras y espacios.";
    }

    if (empty($club_type)) {
        $errors[] = "Debe seleccionar un tipo de club.";
    }

    // 2. Si hay errores, redirigir con ellos
    if (!empty($errors)) {
        $_SESSION['flash'] = ['type' => 'error', 'messages' => $errors];
        header('Location: teacher_dashboard.php');
        exit;
    }

    // 3. Generar un ID de club único
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

    // 4. Insertar en la base de datos (sin contraseña)
    try {
        $stmt = $pdo->prepare("INSERT INTO clubs (club_id, club_name, creator_name, club_type, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$club_id, $club_name, $creator_name, $club_type]);

        $_SESSION['flash'] = ['type' => 'success', 'msg' => '¡Club "' . htmlspecialchars($club_name) . '" registrado con éxito!'];
        header('Location: teacher_dashboard.php');
        exit;

    } catch (PDOException $e) {
        $_SESSION['flash'] = ['type' => 'error', 'messages' => ['Error al guardar en la base de datos.']];
        header('Location: teacher_dashboard.php');
        exit;
    }

} else {
    header('Location: teacher_dashboard.php');
    exit;
}
