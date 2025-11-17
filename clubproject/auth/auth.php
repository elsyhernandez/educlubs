<?php
// --- Robust Error Handling ---

// Convert all warnings and notices to exceptions
function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler('exception_error_handler');

// Catch fatal errors for AJAX requests
function handle_shutdown() {
    $error = error_get_last();
    $is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if ($is_ajax && $error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (ob_get_length()) {
            ob_end_clean();
        }
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'errors' => ['Ocurrió un error crítico en el servidor.']
        ]);
        exit();
    }
}
register_shutdown_function('handle_shutdown');

ob_start();

require '../includes/config.php';
$errors = [];
$success_message = '';

// Check if the request is AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica de LOGIN
    if (isset($_POST['login'])) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Usuario no encontrado.";
        } else {
            if (!password_verify($password, $user['password_hash'])) {
                $errors[] = "Contraseña incorrecta.";
            }
        }

        if (empty($errors)) {
            $_SESSION['user'] = [
                'user_id' => $user['user_id'], 
                'username' => $user['username'],
                'email' => $user['email'], 
                'role' => $user['role'],
                'carrera' => $user['carrera']
            ];
            $redirect_url = ($user['role'] === 'teacher') ? '../teacher/dashboard.php' : '../student_dashboard.php';
            if ($is_ajax) {
                echo json_encode(['success' => true, 'redirect' => $redirect_url]);
                exit();
            }
            redirect($redirect_url);
        }
    }
    // Lógica de REGISTER
    elseif (isset($_POST['register'])) {
        try {
            $user_id = trim($_POST['user_id'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $nombres = trim($_POST['nombres'] ?? '');
            $paterno = trim($_POST['paterno'] ?? '');
            $materno = trim($_POST['materno'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $semestre = trim($_POST['semestre'] ?? '');
            $turno = trim($_POST['turno'] ?? '');
            $carrera = trim($_POST['carrera'] ?? '');
            $grupo = trim($_POST['grupo'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = isset($_POST['user_role']) ? $_POST['user_role'] : 'student';

            // Validar que el rol sea válido
            if (!in_array($role, ['student', 'teacher'])) {
                $errors[] = "Tipo de usuario inválido.";
            }

            if (empty($user_id)) {
                $errors[] = "El ID de usuario es requerido.";
            } elseif (!preg_match('/^@(al|ma)\d{8}$/', $user_id)) {
                $errors[] = "El formato del ID de usuario es inválido.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";
            else {
                $host = substr(strrchr($email, "@"), 1);
                if (strtolower($host) !== 'gmail.com' && substr(strtolower($host), -6) !== 'edu.mx') {
                    $errors[] = "Solo se permiten correos Gmail o institucionales .edu.mx.";
                }
            }

            if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres.";
            if (!preg_match('/[A-Z]/', $password)) $errors[] = "La contraseña debe contener al menos una letra mayúscula.";
            if (!preg_match('/[0-9]/', $password)) $errors[] = "La contraseña debe contener al menos un número.";

            if (!$nombres) $errors[] = "El nombre es requerido.";
            elseif (!preg_match('/^[a-zA-Z\s]+$/', $nombres)) $errors[] = "El nombre solo debe contener letras.";
            if (!$paterno) $errors[] = "El apellido paterno es requerido.";
            elseif (!preg_match('/^[a-zA-Z\s]+$/', $paterno)) $errors[] = "El apellido paterno solo debe contener letras.";
            if (!$materno) $errors[] = "El apellido materno es requerido.";
            elseif (!preg_match('/^[a-zA-Z\s]+$/', $materno)) $errors[] = "El apellido materno solo debe contener letras.";
            if (!$telefono) {
                $errors[] = "El número de teléfono es requerido.";
            } elseif (!preg_match('/^[0-9]{10}$/', $telefono)) {
                $errors[] = "El número de teléfono debe contener 10 dígitos.";
            } else {
                // Verificar si el número de teléfono ya existe
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE telefono = ?");
                $stmt->execute([$telefono]);
                if ($stmt->fetchColumn() > 0) {
                    $errors[] = "El número de teléfono ya está registrado.";
                }
            }
            if ($role === 'student') {
                if (empty($semestre)) {
                    $errors[] = "El semestre es requerido.";
                } elseif (!in_array($semestre, ['1', '2', '3', '4', '5', '6'])) {
                    $errors[] = "El semestre seleccionado no es válido.";
                }
                if (empty($turno)) {
                    $errors[] = "El turno es requerido.";
                } elseif (!in_array($turno, ['matutino', 'vespertino'])) {
                    $errors[] = "El turno seleccionado no es válido.";
                }
                if (empty($carrera)) {
                    $errors[] = "La carrera es requerida.";
                }
                if (empty($grupo)) {
                    $errors[] = "El grupo es requerido.";
                }
            } else {
                $semestre = '';
                $turno = '';
                $carrera = '';
                $grupo = '';
            }

            if (empty($errors)) {
                $stmt = $pdo->prepare("SELECT user_id, email FROM users WHERE user_id = ? OR email = ?");
                $stmt->execute([$user_id, $email]);
                $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($existing_user) {
                    if ($existing_user['user_id'] === $user_id) {
                        $errors[] = "El ID de usuario ya existe. Intenta registrarte de nuevo para generar uno diferente.";
                    }
                    if ($existing_user['email'] === $email) {
                        $errors[] = "El correo electrónico ya está registrado.";
                    }
                }

                if (empty($errors)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $username = trim($nombres . ' ' . $paterno);
                    $stmt = $pdo->prepare("INSERT INTO users (user_id, email, username, password_hash, role, nombres, paterno, materno, telefono, semestre, turno, carrera) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    if ($stmt->execute([$user_id, $email, $username, $hash, $role, $nombres, $paterno, $materno, $telefono, $semestre, $turno, $carrera])) {
                        $_SESSION['user'] = [
                            'user_id' => $user_id, 
                            'email' => $email, 
                            'username' => $username, 
                            'role' => $role,
                            'carrera' => $carrera
                        ];
                        $redirect_url = ($role === 'teacher') ? '../teacher/dashboard.php' : '../student_dashboard.php';

                        if ($is_ajax) {
                            echo json_encode(['success' => true, 'redirect' => $redirect_url]);
                            exit();
                        }
                        redirect($redirect_url);
                    } else {
                        $errors[] = "Error en el registro. Por favor, inténtalo de nuevo.";
                    }
                }
            }
        } catch (Exception $e) {
            // Catch any exception (including PDOException and ErrorException from our handler)
            // For security, don't expose detailed error messages to the user.
            // You might want to log the detailed error for debugging: error_log($e->getMessage());
            $errors[] = "Ocurrió un error inesperado. Por favor, inténtalo de nuevo. " . $e->getMessage();
        }

        if ($is_ajax && !empty($errors)) {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autenticación - EduClubs</title>
    <link rel="stylesheet" href="../css/auth-landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/notification.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .overlay-panel .logo-container {
        position: static;
        transform: none;
        margin-bottom: 15px;
    }
    .overlay-panel .logo-container span {
        color: white;
    }
    .form-container form .form-row {
        display: flex;
        gap: 10px;
        width: 100%;
    }
    .form-container form .form-row input,
    .form-container form .form-row select {
        flex: 1;
    }
    .scrollable-form {
        overflow-y: auto;
        scroll-behavior: smooth;
        transition: all 0.3s ease; /* Added for smooth animation */
        max-height: 100%;
    }
    .sign-up-container form {
        height: auto;
        justify-content: flex-start;
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    .scrollable-form::-webkit-scrollbar {
        width: 8px;
    }
    .scrollable-form::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .scrollable-form::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    .scrollable-form::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .scrollable-form form {
        padding-bottom: 20px; /* Extra space for the button */
    }
    .form-container input, .form-container select {
        background-color: #f0f0f0;
        border: none;
        padding: 12px 15px;
        margin: 8px 0;
        width: 100%;
        border-radius: 8px;
        outline: none;
        transition: all 0.3s ease;
        box-sizing: border-box; /* Added for consistent sizing */
    }
    .form-container input:focus, .form-container select:focus {
        border-bottom: 2px solid var(--primary-color);
        background-color: #e9e9e9;
    }
    .user-id-field-wrapper {
        position: relative;
        width: 100%;
        margin: 8px 0;
    }
    .readonly-field {
        background-color: #e9e9e9;
        cursor: not-allowed;
        color: #777;
        padding-right: 40px; /* Space for copy icon */
    }
    .copy-user-id {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #777;
    }
    .copy-user-id:hover {
        color: var(--primary-color);
    }
    .role-selection-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
        width: 100%;
    }
    .role-box {
        padding: 25px;
        border: 2px solid transparent;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        text-align: center;
        flex: 1;
        background-image: linear-gradient(45deg, #f9f9f9, #e0e0e0);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        opacity: 0;
        transform: translateY(20px);
        animation: roleBoxIn 0.5s forwards;
    }
    .role-box:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        border-color: var(--primary-color);
    }
    .role-box.selected {
        border-color: var(--primary-color);
        background-image: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        color: #fff;
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 15px 30px rgba(4, 59, 92, 0.25);
    }
    .role-box.selected i, .role-box.selected p {
        color: #fff;
    }
    .role-box i {
        font-size: 2.5em;
        margin-bottom: 15px;
        transition: color 0.3s ease;
        color: var(--primary-color);
    }
    .role-box p {
        margin: 0;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    #reselect-role {
        margin-top: 10px;
        background: none;
        border: none;
        color: var(--primary-color);
        cursor: pointer;
        text-decoration: underline;
        font-weight: 600;
    }
    #registration-fields.fade-in {
        animation: fadeIn 0.5s ease-in-out forwards;
    }
    .role-selection-wrapper.fade-out {
        animation: fadeOut 0.5s ease-in-out forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; }
    }
    @keyframes roleBoxIn {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .links-container {
        text-align: center;
        width: 100%;
        margin-top: 10px;
    }
    .links-container a {
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    .input-wrapper {
        position: relative;
        width: 100%;
    }
    .input-wrapper.valid input, .input-wrapper.valid select {
        border: 2px solid #28a745;
    }
    .valid-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #28a745;
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .input-wrapper.valid .valid-icon {
        opacity: 1;
    }
    .invalid-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #d9534f; /* Red color for error */
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }
    .input-wrapper.invalid .invalid-icon {
        opacity: 1;
    }
    .input-wrapper.invalid input, .input-wrapper.invalid select {
        border: 2px solid #d9534f; /* Red border for invalid fields */
    }
    .password-wrapper .toggle-password {
        right: 40px;
    }
    .form-container form .form-row .input-wrapper {
        margin: 0;
        flex: 1;
    }
    #password-requirements {
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        width: 100%;
        box-sizing: border-box;
    }
    #password-requirements .requirements-title {
        font-weight: 600;
        margin-bottom: 10px;
    }
    #password-requirements .requirements-list li {
        color: #d9534f; /* Red for invalid */
        margin-bottom: 5px;
        transition: color 0.3s ease;
    }
    #password-requirements .requirements-list li.valid {
        color: #28a745; /* Green for valid */
    }
    #password-requirements .requirements-list li i {
        margin-right: 8px;
    }
    #password-success-message {
        color: #28a745;
        font-weight: 600;
        display: none; /* Oculto por defecto */
    }
    #password-success-message i {
        margin-right: 8px;
    }
    .role-selection-wrapper {
        background-color: transparent;
        border: none;
        border-radius: 16px;
        padding: 20px 0;
        margin-bottom: 25px;
        width: 100%;
        box-sizing: border-box;
        animation: slideInUp 0.6s ease-in-out;
    }
    .role-description {
        text-align: center;
        color: #777;
        margin-bottom: 20px;
        font-size: 0.9em;
    }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="container fade-in-content <?php if (isset($_POST['register'])) echo 'right-panel-active'; ?>" id="container">
        <div class="form-container sign-up-container scrollable-form">
            <form action="auth.php" method="post" id="registerForm">
                <input type="hidden" name="register" value="1">
                <input type="hidden" name="user_role" id="userRoleInput" value="">
                <h1>Crear Cuenta</h1>
                <div id="role-selection-wrapper" class="role-selection-wrapper">
                    <h3 style="font-weight: 600; margin-bottom: 10px; text-align: center; font-size: 1.5em;">Únete a EduClubs</h3>
                    <p class="role-description">Para comenzar, dinos quién eres. Tu rol definirá tu experiencia en la plataforma.</p>
                    <div id="role-selection-container" class="role-selection-container" style="margin-bottom: 0;">
                        <div class="role-box" data-role="student">
                            <i class="fas fa-user-graduate"></i>
                            <p>Soy Alumno</p>
                        </div>
                        <div class="role-box" data-role="teacher">
                            <i class="fas fa-chalkboard-teacher"></i>
                            <p>Soy Maestro</p>
                        </div>
                    </div>
                </div>
                <div id="registration-fields" style="display: none; width: 100%;">
                    <button type="button" id="reselect-role" style="display: none;">Reelegir rol</button>
                    <div class="user-id-display" id="userIdDisplay" style="display: none; width: 100%;">
                        <label>Tu ID de usuario:</label>
                        <div class="user-id-field-wrapper">
                            <input type="text" id="userId" readonly class="readonly-field" />
                            <i class="fas fa-copy copy-user-id" title="Copiar ID"></i>
                        </div>
                        <input type="hidden" name="user_id" id="userIdHidden" value="" />
                    </div>
                    <div class="form-row">
                        <div class="input-wrapper">
                            <input type="text" name="nombres" placeholder="Nombre(s)" required value="<?= htmlspecialchars($_POST['nombres'] ?? '') ?>" />
                            <i class="fas fa-check-circle valid-icon"></i>
                        </div>
                        <div class="input-wrapper">
                            <input type="text" name="paterno" placeholder="Apellido Paterno" required value="<?= htmlspecialchars($_POST['paterno'] ?? '') ?>" />
                            <i class="fas fa-check-circle valid-icon"></i>
                        </div>
                    </div>
                    <div class="input-wrapper">
                        <input type="text" name="materno" placeholder="Apellido Materno" required value="<?= htmlspecialchars($_POST['materno'] ?? '') ?>" />
                        <i class="fas fa-check-circle valid-icon"></i>
                    </div>
                    <div class="input-wrapper">
                        <input type="tel" name="telefono" placeholder="Número de Teléfono (10 dígitos)" required value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        <i class="fas fa-check-circle valid-icon"></i>
                        <i class="fas fa-times-circle invalid-icon"></i>
                    </div>
                    <div class="input-wrapper">
                        <input type="email" name="email" placeholder="Correo (Gmail o .edu.mx)" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                        <i class="fas fa-check-circle valid-icon"></i>
                        <i class="fas fa-times-circle invalid-icon"></i>
                    </div>
                    <div id="student-fields" style="display: none;">
                        <div class="form-row">
                            <div class="input-wrapper">
                                <select name="semestre">
                                    <option value="">Semestre</option>
                                    <option value="1">1er Semestre</option>
                                    <option value="2">2do Semestre</option>
                                    <option value="3">3er Semestre</option>
                                    <option value="4">4to Semestre</option>
                                    <option value="5">5to Semestre</option>
                                    <option value="6">6to Semestre</option>
                                </select>
                            </div>
                            <div class="input-wrapper">
                                <select name="turno">
                                    <option value="">Turno</option>
                                    <option value="matutino">Matutino</option>
                                    <option value="vespertino">Vespertino</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="input-wrapper">
                                <select name="carrera" required>
                                    <option value="">Selecciona tu carrera</option>
                                    <option value="CONTABILIDAD">CONTABILIDAD</option>
                                    <option value="LOGISTICA">LOGÍSTICA</option>
                                    <option value="MECANICA INDUSTRIAL">MECÁNICA INDUSTRIAL</option>
                                    <option value="ALIMENTOS Y BEBIDAS">ALIMENTOS Y BEBIDAS</option>
                                    <option value="PROGRAMACION">PROGRAMACIÓN</option>
                                    <option value="HOSPEDAJE">HOSPEDAJE</option>
                                </select>
                            </div>
                            <div class="input-wrapper">
                                <select name="grupo" required>
                                    <option value="">Selecciona tu grupo</option>
                                    <option value="A" <?= (isset($_POST['grupo']) && $_POST['grupo'] == 'A') ? 'selected' : '' ?>>A</option>
                                    <option value="B" <?= (isset($_POST['grupo']) && $_POST['grupo'] == 'B') ? 'selected' : '' ?>>B</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="password-wrapper input-wrapper">
                        <input id="password" type="password" name="password" placeholder="Contraseña" required />
                        <i class="fas fa-eye-slash toggle-password"></i>
                        <i class="fas fa-check-circle valid-icon"></i>
                    </div>
                    <div id="password-requirements" style="text-align: left; font-size: 0.8em; margin-top: 10px; display: none;">
                        <p class="requirements-title">La contraseña debe contener:</p>
                        <ul class="requirements-list" style="list-style: none; padding-left: 0;">
                            <li id="length"><i class="fas fa-times-circle"></i> <strong>Longitud:</strong> 8+ caracteres</li>
                            <li id="uppercase"><i class="fas fa-times-circle"></i> <strong>Mayúscula:</strong> Al menos una (A-Z)</li>
                            <li id="number"><i class="fas fa-times-circle"></i> <strong>Número:</strong> Al menos uno (0-9)</li>
                        </ul>
                        <div id="password-success-message">
                            <i class="fas fa-check-circle"></i> Se cumplen todos los requerimentos correctamente
                        </div>
                    </div>
                    <button type="submit">Registrarse</button>
                </div>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="auth.php" method="post">
                <input type="hidden" name="login" value="1">
                <h1>Iniciar Sesión</h1>
                <input type="email" name="email" placeholder="Correo Electrónico" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Contraseña" required />
                    <i class="fas fa-eye-slash toggle-password"></i>
                </div>
                <div class="links-container">
                    <a href="password_reset_request.php">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit">Iniciar Sesión</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <div class="logo-container">
                        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
                        <span>EduClubs</span>
                    </div>
                    <h1>Hola, Futuro Miembro</h1>
                    <p>Regístrate con tus datos para unirte a nuestra comunidad de clubes y empezar una nueva aventura.</p>
                    <button class="ghost" id="signIn">Iniciar Sesi&oacute;n</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <div class="logo-container">
                        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
                        <span>EduClubs</span>
                    </div>
                    <h1>&iexcl;Bienvenido de Nuevo!</h1>
                    <p>Si ya tienes una cuenta, inicia sesión para acceder a tu panel de control y seguir explorando los clubes.</p>
                    <button class="ghost" id="signUp">Crear Cuenta</button>
                </div>
            </div>
        </div>
    </div>


    <a href="index.php" class="back-button">Atr&aacute;s</a>

    <script src="../js/auth-landing.js?v=<?php echo time(); ?>"></script>
    <script src="../js/notification.js?v=<?php echo time(); ?>"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.toggle-password').forEach(item => {
                item.addEventListener('click', function (e) {
                    const passwordInput = this.previousElementSibling;
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.classList.remove('fa-eye-slash');
                        this.classList.add('fa-eye');
                    } else {
                        passwordInput.type = 'password';
                        this.classList.remove('fa-eye');
                        this.classList.add('fa-eye-slash');
                    }
                });
            });

            const passwordInput = document.getElementById('password');
            const requirementsContainer = document.getElementById('password-requirements');
            const requirementsList = requirementsContainer.querySelector('.requirements-list');
            const successMessage = document.getElementById('password-success-message');
            const requirements = {
                length: document.getElementById('length'),
                uppercase: document.getElementById('uppercase'),
                number: document.getElementById('number')
            };
            const submitButton = document.querySelector('#registerForm button[type="submit"]');

            if (passwordInput) {
                passwordInput.addEventListener('focus', () => {
                    requirementsContainer.style.display = 'block';
                });

                function validatePassword() {
                    const value = passwordInput.value;
                    const validations = {
                        length: value.length >= 8,
                        uppercase: /[A-Z]/.test(value),
                        number: /[0-9]/.test(value)
                    };

                    let allValid = true;
                    for (const key in validations) {
                        const requirement = requirements[key];
                        const icon = requirement.querySelector('i');
                        if (validations[key]) {
                            requirement.classList.add('valid');
                            icon.classList.remove('fa-times-circle');
                            icon.classList.add('fa-check-circle');
                        } else {
                            requirement.classList.remove('valid');
                            icon.classList.remove('fa-check-circle');
                            icon.classList.add('fa-times-circle');
                            allValid = false;
                        }
                    }
                    
                    if (allValid) {
                        requirementsContainer.style.display = 'none';
                    } else {
                        requirementsContainer.style.display = 'block';
                        requirementsList.style.display = 'block';
                        successMessage.style.display = 'none';
                    }

                    passwordInput.parentElement.classList.toggle('valid', allValid);
                }

                passwordInput.addEventListener('input', validatePassword);
            }

            const registerForm = document.getElementById('registerForm');
            const roleSelectionWrapper = document.getElementById('role-selection-wrapper');
            const roleSelectionContainer = document.getElementById('role-selection-container');
            const roleBoxes = document.querySelectorAll('.role-box');
            const userRoleInput = document.getElementById('userRoleInput');
            const registrationFields = document.getElementById('registration-fields');
            const reselectRoleBtn = document.getElementById('reselect-role');
            const userIdDisplay = document.getElementById('userIdDisplay');
            const userIdInput = document.getElementById('userId');
            const userIdHidden = document.getElementById('userIdHidden');
            const studentFields = document.getElementById('student-fields');

            function showRoleSelection() {
                registrationFields.classList.remove('fade-in');
                roleSelectionWrapper.style.display = 'block';
                registrationFields.style.display = 'none';
                reselectRoleBtn.style.display = 'none';
                userRoleInput.value = '';
                // Clear form fields
                registerForm.reset();
                userIdInput.value = '';
                userIdHidden.value = '';
                userIdDisplay.style.display = 'none';
                roleBoxes.forEach(box => box.classList.remove('selected'));

                // Clear all validation states
                registerForm.querySelectorAll('.input-wrapper').forEach(wrapper => {
                    wrapper.classList.remove('valid', 'invalid');
                });
                const passwordWrapper = passwordInput ? passwordInput.parentElement : null;
                if (passwordWrapper) {
                    passwordWrapper.classList.remove('valid');
                }
            }

            function handleRoleSelection(selectedRole) {
                userRoleInput.value = selectedRole;
                roleSelectionWrapper.classList.add('fade-out');
                setTimeout(() => {
                    roleSelectionWrapper.style.display = 'none';
                    roleSelectionWrapper.classList.remove('fade-out'); // Reset for re-selection
                    registrationFields.style.display = 'flex';
                    registrationFields.classList.add('fade-in');
                }, 500);

                registrationFields.style.flexDirection = 'column';
                registrationFields.style.alignItems = 'center';
                reselectRoleBtn.style.display = 'block';

                const semestreSelect = studentFields.querySelector('select[name="semestre"]');
                const turnoSelect = studentFields.querySelector('select[name="turno"]');
                const carreraSelect = studentFields.querySelector('select[name="carrera"]');
                const grupoSelect = studentFields.querySelector('select[name="grupo"]');

                if (selectedRole === 'student') {
                    studentFields.style.display = 'block';
                    if (semestreSelect) semestreSelect.required = true;
                    if (turnoSelect) turnoSelect.required = true;
                    if (carreraSelect) carreraSelect.required = true;
                    if (grupoSelect) grupoSelect.required = true;
                } else {
                    studentFields.style.display = 'none';
                    if (semestreSelect) {
                        semestreSelect.required = false;
                        semestreSelect.value = '';
                    }
                    if (turnoSelect) {
                        turnoSelect.required = false;
                        turnoSelect.value = '';
                    }
                    if (carreraSelect) {
                        carreraSelect.required = false;
                        carreraSelect.value = '';
                    }
                    if (grupoSelect) {
                        grupoSelect.required = false;
                        grupoSelect.value = '';
                    }
                }

                const timestamp = Date.now();
                const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                let prefix = (selectedRole === 'student') ? '@al' : '@ma';
                
                const userId = `${prefix}${timestamp.toString().slice(-5)}${randomNum}`;
                userIdInput.value = userId;
                userIdHidden.value = userId;
                userIdDisplay.style.display = 'block';

                // Scroll to the bottom of the form to ensure the button is visible
                setTimeout(() => {
                    const submitButton = registerForm.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                }, 500); // Delay matches the fadeIn animation
            }

            roleBoxes.forEach((box, index) => {
                box.style.animationDelay = `${index * 0.15}s`;
                box.addEventListener('click', function() {
                    const selectedRole = this.dataset.role;
                    roleBoxes.forEach(b => b.classList.remove('selected'));
                    this.classList.add('selected');
                    setTimeout(() => handleRoleSelection(selectedRole), 300);
                });
            });

            reselectRoleBtn.addEventListener('click', showRoleSelection);

            // On page load, if a role is already set (e.g., form submission failed), show the form
            const initialRole = "<?= htmlspecialchars($_POST['user_role'] ?? '') ?>";
            if (initialRole) {
                const selectedBox = document.querySelector(`.role-box[data-role='${initialRole}']`);
                if (selectedBox) {
                    selectedBox.classList.add('selected');
                }
                handleRoleSelection(initialRole);
            }

            if (registerForm) {
                // --- Real-time DB validation for email and phone ---
                function debounce(func, delay) {
                    let timeout;
                    return function(...args) {
                        const context = this;
                        clearTimeout(timeout);
                        timeout = setTimeout(() => func.apply(context, args), delay);
                    };
                }

                function checkAvailability(inputElement) {
                    const field = inputElement.name;
                    const value = inputElement.value.trim();
                    const wrapper = inputElement.closest('.input-wrapper');

                    // Reset state
                    wrapper.classList.remove('valid', 'invalid');

                    const formatValidationRules = {
                        telefono: val => /^[0-9]{10}$/.test(val),
                        email: val => {
                            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) return false;
                            const host = val.substring(val.lastIndexOf('@') + 1).toLowerCase();
                            return host === 'gmail.com' || host.endsWith('edu.mx');
                        }
                    };

                    if (!formatValidationRules[field](value)) {
                        return; // Don't check DB if format is invalid
                    }

                    const formData = new FormData();
                    formData.append('field', field);
                    formData.append('value', value);

                    fetch('check_availability.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.available) {
                            wrapper.classList.add('valid');
                            wrapper.classList.remove('invalid');
                        } else {
                            wrapper.classList.add('invalid');
                            wrapper.classList.remove('valid');
                        }
                    })
                    .catch(error => {
                        console.error('Error during availability check:', error);
                        wrapper.classList.add('invalid');
                        wrapper.classList.remove('valid');
                    });
                }

                const debouncedCheck = debounce(checkAvailability, 500);

                const emailInput = registerForm.querySelector('input[name="email"]');
                const phoneInput = registerForm.querySelector('input[name="telefono"]');

                if (emailInput) {
                    emailInput.addEventListener('input', () => debouncedCheck(emailInput));
                }
                if (phoneInput) {
                    phoneInput.addEventListener('input', () => debouncedCheck(phoneInput));
                }

                // --- Standard validation for other fields ---
                const inputsToValidate = [
                    { selector: 'input[name="nombres"]', validation: value => /^[a-zA-Z\s]+$/.test(value) && value.length > 0 },
                    { selector: 'input[name="paterno"]', validation: value => /^[a-zA-Z\s]+$/.test(value) && value.length > 0 },
                    { selector: 'input[name="materno"]', validation: value => /^[a-zA-Z\s]+$/.test(value) && value.length > 0 }
                ];

                inputsToValidate.forEach(item => {
                    const input = registerForm.querySelector(item.selector);
                    if (input) {
                        const wrapper = input.closest('.input-wrapper');
                        if (wrapper) {
                            const eventType = input.tagName.toLowerCase() === 'select' ? 'change' : 'input';
                            input.addEventListener(eventType, function() {
                                const isValid = item.validation(this.value.trim());
                                wrapper.classList.toggle('valid', isValid);
                            });
                        }
                    }
                });

                // Remover clase de error cuando el usuario empiece a escribir
                const inputs = registerForm.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="tel"], select');
                inputs.forEach(input => {
                    input.addEventListener('input', function() {
                        this.classList.remove('error');
                    });
                    input.addEventListener('change', function() {
                        this.classList.remove('error');
                    });
                });

                registerForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    // Validar campos vacíos antes de enviar
                    const userRole = userRoleInput.value;
                    const userId = userIdHidden.value.trim();
                    const nombres = this.querySelector('input[name="nombres"]').value.trim();
                    const paterno = this.querySelector('input[name="paterno"]').value.trim();
                    const materno = this.querySelector('input[name="materno"]').value.trim();
                    const telefono = this.querySelector('input[name="telefono"]').value.trim();
                    const email = this.querySelector('input[name="email"]').value.trim();
                    const semestre = this.querySelector('select[name="semestre"]').value;
                    const turno = this.querySelector('select[name="turno"]').value;
                    const password = this.querySelector('input[name="password"]').value.trim();

                    let emptyFields = [];
                    if (!userRole) emptyFields.push('Tipo de usuario');
                    if (!userId) emptyFields.push('ID de Usuario');
                    if (!nombres) emptyFields.push('Nombre(s)');
                    if (!paterno) emptyFields.push('Apellido Paterno');
                    if (!materno) emptyFields.push('Apellido Materno');
                    if (!telefono) emptyFields.push('Teléfono');
                    if (!email) emptyFields.push('Correo Electrónico');
                    if (userRole === 'student') {
                        if (!semestre) emptyFields.push('Semestre');
                        if (!turno) emptyFields.push('Turno');
                    }
                    if (!password) emptyFields.push('Contraseña');

                    if (emptyFields.length > 0) {
                        const message = 'Por favor completa los siguientes campos: ' + emptyFields.join(', ');
                        showNotification(message, 'error');

                        // Resaltar campos vacíos con borde rojo
                        document.getElementById('userId').classList.toggle('error', !userId);
                        this.querySelector('input[name="nombres"]').classList.toggle('error', !nombres);
                        this.querySelector('input[name="paterno"]').classList.toggle('error', !paterno);
                        this.querySelector('input[name="materno"]').classList.toggle('error', !materno);
                        this.querySelector('input[name="telefono"]').classList.toggle('error', !telefono);
                        this.querySelector('input[name="email"]').classList.toggle('error', !email);
                        if (userRole === 'student') {
                            this.querySelector('select[name="semestre"]').classList.toggle('error', !semestre);
                            this.querySelector('select[name="turno"]').classList.toggle('error', !turno);
                        } else {
                            this.querySelector('select[name="semestre"]').classList.remove('error');
                            this.querySelector('select[name="turno"]').classList.remove('error');
                        }
                        this.querySelector('input[name="password"]').classList.toggle('error', !password);

                        return; // Detener el envío del formulario
                    }

                    const formData = new FormData(this);
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.textContent = 'Creando...';

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', 'auth.php', true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.onload = function() {
                        if (xhr.status >= 200 && xhr.status < 400) {
                            try {
                                const data = JSON.parse(xhr.responseText);
                                if (data.success && data.redirect) {
                                    showNotification('¡Registro exitoso! Redirigiendo...', 'success');
                                    setTimeout(() => {
                                        window.location.href = data.redirect;
                                    }, 1500);
                                } else {
                                    data.errors.forEach(error => {
                                        showNotification(error, 'error');
                                    });
                                }
                            } catch (e) {
                                showNotification('Error inesperado. Inténtalo de nuevo.', 'error');
                            }
                        } else {
                            showNotification('Error de conexión con el servidor.', 'error');
                        }
                        submitBtn.textContent = 'Registrarse';
                    };
                    xhr.onerror = function() {
                        showNotification('Error de conexión.', 'error');
                        submitBtn.textContent = 'Registrarse';
                    };
                    xhr.send(formData);
                });
            }

            <?php if (!empty($errors) && !$is_ajax): ?>
                <?php foreach ($errors as $error): ?>
                    showNotification('<?php echo addslashes($error); ?>', 'error');
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if ($success_message && !$is_ajax): ?>
                showNotification('<?php echo addslashes($success_message); ?>', 'success');
            <?php endif; ?>

            const copyButton = document.querySelector('.copy-user-id');
            if (copyButton) {
                copyButton.addEventListener('click', function() {
                    const userIdInput = document.getElementById('userId');
                    navigator.clipboard.writeText(userIdInput.value).then(() => {
                        showNotification('¡ID de usuario copiado!', 'success');
                    }).catch(err => {
                        showNotification('No se pudo copiar el ID.', 'error');
                    });
                });
            }
        });
    </script>
</body>
</html>
<?php ob_end_flush(); ?>
