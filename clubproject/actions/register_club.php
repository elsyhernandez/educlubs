 <?php
// =================================================================
// --- EduClubs: Club Registration Handler (Strict Profile Version) ---
// =================================================================

// --- Environment Setup ---

header('Content-Type: application/json');

// --- Utility Functions ---

/**
 * Sends a JSON response and terminates the script.
 *
 * @param bool $success Whether the operation was successful.
 * @param string $message The message to send.
 */
function send_json_response($success, $message) {
    if (!headers_sent()) {
        http_response_code($success ? 200 : 400);
    }
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

/**
 * Handles fatal errors, logs them, and sends a generic JSON error response.
 */
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        error_log("Fatal error in register_club.php: " . $error['message'] . " in " . $error['file'] . " on line " . $error['line']);
        if (!headers_sent()) {
            send_json_response(false, 'Ocurrió un error crítico en el servidor.');
        }
    }
});

// --- Application Logic ---

try {
    // --- Initialization and Configuration ---
    require_once '../includes/config.php';
    require_once '../includes/mail_config.php';

    if (!isset($pdo)) {
        throw new RuntimeException("La conexión a la base de datos no está disponible.");
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- Session and Input Validation ---
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id'])) {
        send_json_response(false, 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        send_json_response(false, 'Método de solicitud no válido.');
    }

    $user_id = $_SESSION['user']['user_id'];
    $club_name = $_POST['club'] ?? null;
    $club_type = $_POST['type'] ?? null;

    if (empty($club_name) || empty($club_type)) {
        send_json_response(false, 'El nombre y el tipo de club son obligatorios.');
    }

    // --- Business Logic: Fetch User Data for Validation ---
    $stmt_user = $pdo->prepare("SELECT paterno, materno, nombres, semestre, email, turno, telefono FROM users WHERE user_id = :user_id");
    $stmt_user->execute([':user_id' => $user_id]);
    $user_data = $stmt_user->fetch(PDO::FETCH_ASSOC);

    if (!$user_data) {
        send_json_response(false, 'No se pudo encontrar tu información de usuario.');
    }

    // **Strict Validation**: Ensure essential profile data exists.
    if (empty($user_data['paterno']) || empty($user_data['nombres']) || empty($user_data['semestre'])) {
        send_json_response(false, 'Tu perfil está incompleto. Por favor, actualiza tu información personal (nombre, apellido, semestre) en tu perfil antes de registrarte.');
    }

    // --- Branch Logic Based on Club Type ---
    if ($club_type === 'asesoria') {
        // --- Asesoría Registration Logic ---
        $carrera = $_POST['carrera'] ?? null;
        $maestro = $_POST['maestro'] ?? null;

        if (empty($carrera) || empty($maestro)) {
            send_json_response(false, 'La carrera y el nombre del maestro son obligatorios para las asesorías.');
        }

        // Check for duplicates in tutoring_registrations
        $stmt_check_tutoring = $pdo->prepare("SELECT 1 FROM tutoring_registrations WHERE user_id = :user_id AND materia = :materia");
        $stmt_check_tutoring->execute([':user_id' => $user_id, ':materia' => $club_name]);
        if ($stmt_check_tutoring->fetch()) {
            send_json_response(false, 'Ya te encuentras registrado en esta asesoría.');
        }

        // Insert into tutoring_registrations
        $sql_tutoring = "INSERT INTO tutoring_registrations 
                            (materia, paterno, materno, nombres, correo, turno, user_id, telefono, carrera, maestro) 
                         VALUES 
                            (:materia, :paterno, :materno, :nombres, :correo, :turno, :user_id, :telefono, :carrera, :maestro)";
        
        $stmt_insert_tutoring = $pdo->prepare($sql_tutoring);
        
        $params_tutoring = [
            ':materia'   => $club_name,
            ':paterno'   => $user_data['paterno'],
            ':materno'   => $user_data['materno'] ?? 'No especificado',
            ':nombres'   => $user_data['nombres'],
            ':correo'    => $user_data['email'] ?? 'No proporcionado',
            ':turno'     => $user_data['turno'] ?? 'No especificado',
            ':user_id'   => $user_id,
            ':telefono'  => $user_data['telefono'] ?? '',
            ':carrera'   => $carrera,
            ':maestro'   => $maestro
        ];
        
        $stmt_insert_tutoring->execute($params_tutoring);

        // --- Notify Teacher ---
        $stmt_teacher = $pdo->prepare("SELECT user_id, email FROM users WHERE username = :maestro AND role = 'teacher'");
        $stmt_teacher->execute([':maestro' => $maestro]);
        $teacher = $stmt_teacher->fetch(PDO::FETCH_ASSOC);

        if ($teacher) {
            $student_full_name = trim("{$user_data['nombres']} {$user_data['paterno']}");
            
            // Insert into new notifications table
            $stmt_notification = $pdo->prepare(
                "INSERT INTO notifications (teacher_id, student_name, club_name) VALUES (:teacher_id, :student_name, :club_name)"
            );
            $stmt_notification->execute([
                ':teacher_id'   => $teacher['user_id'],
                ':student_name' => $student_full_name,
                ':club_name'    => $club_name
            ]);

            // Email sending logic (optional, can be kept)
            if (isset($mailer) && !empty($teacher['email'])) {
                try {
                    $notification_message = "Un nuevo alumno ({$student_full_name}) se ha inscrito en tu asesoría '{$club_name}'.";
                    $mailer->setFrom('support@educlubs.com', 'EduClubs');
                    $mailer->addAddress($teacher['email']);
                    $mailer->Subject = 'Nuevo Alumno Inscrito en tu Asesoría';
                    $mailer->Body    = "Hola {$maestro},\n\n{$notification_message}\n\nGracias,\nEl equipo de EduClubs";
                    $mailer->send();
                } catch (Exception $e) {
                    error_log("Mailer Error: " . $mailer->ErrorInfo);
                }
            }
        }

    } else {
        // --- Standard Club Registration Logic ---
        $stmt_check = $pdo->prepare("SELECT 1 FROM club_registrations WHERE user_id = :user_id AND club_name = :club_name");
        $stmt_check->execute([':user_id' => $user_id, ':club_name' => $club_name]);
        if ($stmt_check->fetch()) {
            send_json_response(false, 'Ya te encuentras registrado en este club.');
        }

        $sql = "INSERT INTO club_registrations 
                    (club_type, club_name, paterno, materno, nombres, semestre, correo, turno, user_id, telefono) 
                VALUES 
                    (:club_type, :club_name, :paterno, :materno, :nombres, :semestre, :correo, :turno, :user_id, :telefono)";
        
        $stmt_insert = $pdo->prepare($sql);

        $params = [
            ':club_type' => $club_type,
            ':club_name' => $club_name,
            ':paterno'   => $user_data['paterno'],
            ':materno'   => $user_data['materno'] ?? 'No especificado',
            ':nombres'   => $user_data['nombres'],
            ':semestre'  => $user_data['semestre'],
            ':correo'    => $user_data['email'] ?? 'No proporcionado',
            ':turno'     => $user_data['turno'] ?? 'No especificado',
            ':user_id'   => $user_id,
            ':telefono'  => $user_data['telefono'] ?? ''
        ];

        $stmt_insert->execute($params);

        // --- Notify Teacher ---
        $stmt_creator = $pdo->prepare("SELECT creator_name FROM clubs WHERE club_name = :club_name");
        $stmt_creator->execute([':club_name' => $club_name]);
        $creator_name = $stmt_creator->fetchColumn();

        if ($creator_name) {
            $stmt_teacher = $pdo->prepare("SELECT user_id, email FROM users WHERE username = :creator_name AND role = 'teacher'");
            $stmt_teacher->execute([':creator_name' => $creator_name]);
            $teacher = $stmt_teacher->fetch(PDO::FETCH_ASSOC);

            if ($teacher) {
                $student_full_name = trim("{$user_data['nombres']} {$user_data['paterno']}");

                // Insert into new notifications table
                $stmt_notification = $pdo->prepare(
                    "INSERT INTO notifications (teacher_id, student_name, club_name) VALUES (:teacher_id, :student_name, :club_name)"
                );
                $stmt_notification->execute([
                    ':teacher_id'   => $teacher['user_id'],
                    ':student_name' => $student_full_name,
                    ':club_name'    => $club_name
                ]);

                // Email sending logic (optional, can be kept)
                if (isset($mailer) && !empty($teacher['email'])) {
                    try {
                        $notification_message = "Un nuevo alumno ({$student_full_name}) se ha inscrito en tu club '{$club_name}'.";
                        $mailer->setFrom('support@educlubs.com', 'EduClubs');
                        $mailer->addAddress($teacher['email']);
                        $mailer->Subject = "Nuevo Alumno Inscrito en tu Club '{$club_name}'";
                        $mailer->Body    = "Hola {$creator_name},\n\n{$notification_message}\n\nGracias,\nEl equipo de EduClubs";
                        $mailer->send();
                    } catch (Exception $e) {
                        error_log("Mailer Error: " . $mailer->ErrorInfo);
                    }
                }
            }
        }
    }

    // --- Success Response ---
    send_json_response(true, '¡Felicidades! Te has inscrito correctamente.');

} catch (PDOException $e) {
    error_log('Database Error in register_club.php: ' . $e->getMessage());
    send_json_response(false, 'Ocurrió un error al procesar tu registro.');
} catch (Exception $e) {
    error_log('General Error in register_club.php: ' . $e->getMessage());
    send_json_response(false, 'Ocurrió un error inesperado en el servidor.');
}
