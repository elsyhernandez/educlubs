<?php
require '../includes/config.php';

// --- AJAX Request Handler ---
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user'])) {
        echo json_encode(['success' => false, 'message' => 'No has iniciado sesión.']);
        exit;
    }

    $user_id = $_SESSION['user']['user_id'];
    $action = $_POST['action'] ?? $_GET['action'] ?? '';

    switch ($action) {
        case 'get_user_data':
            $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                $response_data = [
                    'nombres' => $user_data['nombres'] ?? '',
                    'paterno' => $user_data['paterno'] ?? '',
                    'materno' => $user_data['materno'] ?? '',
                    'email' => $user_data['email'] ?? '',
                    'phone' => $user_data['telefono'] ?? '',
                    'semester' => $user_data['semestre'] ?? '',
                    'blood_type' => $user_data['tipo_sangre'] ?? ''
                ];
                echo json_encode(['success' => true, 'data' => $response_data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No se pudieron obtener los datos del usuario.']);
            }
            break;

        case 'register':
            $club_name = $_POST['club'] ?? '';
            $club_type = $_POST['type'] ?? 'asesoria';

            // Check for duplicates
            $stmt_check = $pdo->prepare("SELECT 1 FROM club_registrations WHERE user_id = ? AND club_name = ?");
            $stmt_check->execute([$user_id, $club_name]);
            if ($stmt_check->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Ya estás registrado en este club.']);
                exit;
            }

            // Get data from form for user profile update
            $phone = $_POST['phone'] ?? '';
            $semester = $_POST['semester'] ?? '';
            $blood_type = $_POST['blood_type'] ?? '';

            // Validation
            $errors = [];
            if (!preg_match('/^\d{10}$/', $phone)) $errors[] = "El teléfono debe tener 10 dígitos.";
            if (empty($semester)) $errors[] = "El semestre es requerido.";
            if (empty($blood_type)) $errors[] = "El tipo de sangre es requerido.";
            
            if (!empty($errors)) {
                echo json_encode(['success' => false, 'message' => implode("\n", $errors)]);
                exit;
            }
            
            // Update user profile
            $stmt_update = $pdo->prepare("UPDATE users SET telefono = ?, semestre = ?, tipo_sangre = ? WHERE user_id = ?");
            if (!$stmt_update->execute([$phone, $semester, $blood_type, $user_id])) {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al actualizar tu perfil.']);
                exit;
            }

            // Insert registration
            $stmt_user = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt_user->execute([$user_id]);
            $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

            $stmt_insert = $pdo->prepare("INSERT INTO club_registrations (club_type, club_name, paterno, materno, nombres, semestre, correo, turno, user_id, telefono) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt_insert->execute([$club_type, $club_name, $user_data['paterno'], $user_data['materno'], $user_data['nombres'], $semester, $user_data['email'], $user_data['turno'], $user_id, $phone])) {
                echo json_encode(['success' => true, 'message' => '¡Registro completado con éxito!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al guardar tu registro.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
            break;
    }
    exit;
}
?>
