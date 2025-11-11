<?php
require '../includes/config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'No autorizado.']);
    exit;
}

$user_id = $_SESSION['user']['id'];

// Recoger y limpiar datos
$nombres = trim($_POST['nombres'] ?? '');
$paterno = trim($_POST['paterno'] ?? '');
$materno = trim($_POST['materno'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$semestre = trim($_POST['semestre'] ?? '');

// Validación básica
if (empty($nombres) || empty($paterno) || empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Los campos Nombres, Apellido Paterno y Correo son obligatorios.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'El formato del correo no es válido.']);
    exit;
}

// Validación de correo duplicado (contra otros usuarios)
$stmt_email_check = $pdo->prepare("SELECT id FROM `users` WHERE email = ? AND id <> ?");
$stmt_email_check->execute([$email, $user_id]);
if ($stmt_email_check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'El correo ya está en uso por otro usuario.']);
    exit;
}

$profile_picture_path = null;
$profile_picture_url = null;

// Manejo de la subida de la foto de perfil
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    // Primero, obtener la ruta de la imagen de perfil anterior para borrarla después
    $stmt_old_pic = $pdo->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt_old_pic->execute([$user_id]);
    $old_pic = $stmt_old_pic->fetchColumn();

    $upload_dir = '../assets/profile_pics/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($_FILES['profile_picture']['type'], $allowed_types)) {
        $file_extension = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid('user_' . $user_id . '_', true) . '.' . $file_extension;
        $target_file = $upload_dir . $new_filename;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture_path = 'assets/profile_pics/' . $new_filename;
            $profile_picture_url = $profile_picture_path;
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al mover el archivo subido.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se aceptan JPG, PNG y GIF.']);
        exit;
    }
}

try {
    $sql_parts = [];
    $params = [];

    $sql_parts[] = "nombres = ?"; $params[] = $nombres;
    $sql_parts[] = "paterno = ?"; $params[] = $paterno;
    $sql_parts[] = "materno = ?"; $params[] = $materno;
    $sql_parts[] = "email = ?"; $params[] = $email;
    $sql_parts[] = "telefono = ?"; $params[] = $telefono;
    $sql_parts[] = "semestre = ?"; $params[] = $semestre;

    if ($profile_picture_path !== null) {
        $sql_parts[] = "profile_picture = ?";
        $params[] = $profile_picture_path;
    }

    $sql = "UPDATE users SET " . implode(', ', $sql_parts) . " WHERE id = ?";
    $params[] = $user_id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // Si se subió una nueva foto y había una anterior, borrar la anterior
    if ($profile_picture_path !== null && $old_pic) {
        $old_pic_path = '../' . $old_pic;
        if (file_exists($old_pic_path)) {
            unlink($old_pic_path);
        }
    }

    // Actualizar la sesión del usuario con los nuevos datos
    $stmt_user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt_user->execute([$user_id]);
    $updated_user = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if ($updated_user) {
        $_SESSION['user'] = $updated_user;
    }
    
    if ($profile_picture_url !== null) {
        $_SESSION['user']['profile_picture'] = $profile_picture_url;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Perfil actualizado correctamente.',
        'profile_picture_url' => $profile_picture_url
    ]);

} catch (PDOException $e) {
    error_log("Error al actualizar perfil: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos.']);
}
?>
