<?php
require '../includes/config.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'teacher') {
    redirect('../auth/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_name    = trim($_POST['club_name']);
    $creator_name = trim($_POST['creator_name']);
    $club_type    = trim($_POST['club_type']);
    $errors = [];

    // 1. Validaciones
    if (empty($club_name)) {
        $errors['club_name'] = "El nombre del club es obligatorio.";
    } elseif (!preg_match('/[a-zA-Z]/', $club_name)) {
        $errors['club_name'] = "El nombre del club debe contener al menos una letra, no puede ser solo números o símbolos.";
    } elseif (is_numeric($club_name)) {
        $errors['club_name'] = "El nombre del club no puede ser solo números.";
    }

    if (empty($creator_name)) {
        $errors['creator_name'] = "El nombre del creador es obligatorio.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $creator_name)) {
        $errors['creator_name'] = "El nombre del creador solo debe contener letras y espacios, sin números o símbolos.";
    }

    if (empty($club_type)) {
        $errors['club_type'] = "Debe seleccionar un tipo de club.";
    }

    // 2. Si hay errores, devolverlos como JSON
    if (!empty($errors)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => implode('<br>', $errors)]);
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

    // 4. Insertar en la base de datos
    try {
        // La contraseña se omite aquí; se establecerá a través del panel de administración si es necesario.
        $stmt = $pdo->prepare("INSERT INTO clubs (club_id, club_name, creator_name, club_type, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$club_id, $club_name, $creator_name, $club_type]);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Se ha creado correctamente']);
        exit;

    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Error al guardar en la base de datos.']);
        exit;
    }

} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}
