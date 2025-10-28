<?php
require 'config.php';
$errors = [];
$success_message = '';

// Check if the request is AJAX
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lógica de LOGIN
    if (isset($_POST['login'])) {
        $user_id = trim($_POST['user_id'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $errors[] = "Usuario no encontrado.";
        } else {
            if ($user['email'] !== $email) {
                $errors[] = "El correo debe ser el mismo usado al registrarte.";
            }
            if (!password_verify($password, $user['password_hash'])) {
                $errors[] = "Contraseña incorrecta.";
            }
        }

        if (empty($errors)) {
            $_SESSION['user'] = ['user_id' => $user['user_id'], 'email' => $user['email'], 'username' => $user['username'], 'role' => $user['role']];
            $redirect_url = ($user['role'] === 'teacher') ? 'teacher_dashboard.php' : 'student_dashboard.php';
            if ($is_ajax) {
                echo json_encode(['success' => true, 'redirect' => $redirect_url]);
                exit();
            }
            redirect($redirect_url);
        }
    }
    // Lógica de REGISTER
    elseif (isset($_POST['register'])) {
        $user_id = trim($_POST['user_id'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = isset($_POST['user_role']) ? $_POST['user_role'] : 'student';

        // Validar que el rol sea válido
        if (!in_array($role, ['student', 'teacher'])) {
            $errors[] = "Tipo de usuario inválido.";
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

        if (!$username) $errors[] = "Nombre de usuario es requerido.";
        elseif (!preg_match('/^[a-zA-Z\s]+$/', $username)) $errors[] = "El nombre de usuario solo debe contener letras.";

        if (empty($errors)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ? OR email = ?");
            $stmt->execute([$user_id, $email]);
            if ($stmt->fetchColumn() > 0) $errors[] = "El ID o el correo ya están registrados.";
        }

        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (user_id, email, username, password_hash, role) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $email, $username, $hash, $role])) {
                $roleText = $role === 'teacher' ? 'maestro' : 'alumno';
                $success_message = "¡Registro exitoso! Tu ID de usuario es: <strong>$user_id</strong> (como $roleText). Ahora puedes iniciar sesión.";

                if ($is_ajax) {
                    echo json_encode([
                        'success' => true,
                        'message' => "¡Registro exitoso! Tu ID de usuario es: <strong>$user_id</strong> (como $roleText). Ahora puedes iniciar sesión.",
                        'user_id' => $user_id,
                        'role' => $role
                    ]);
                    exit();
                }
            } else {
                $errors[] = "Error en el registro. Por favor, inténtalo de nuevo.";
            }
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
    <link rel="stylesheet" href="css/auth-landing.css">
    <link rel="stylesheet" href="css/notification.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    .overlay-panel .logo-container {
        position: static;
        transform: none;
        margin-bottom: 15px;
    }
    .overlay-panel .logo-container span {
        color: white;
    }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="container <?php if (isset($_POST['register'])) echo 'right-panel-active'; ?>" id="container">
        <div class="form-container sign-up-container">
            <form action="auth.php" method="post" id="registerForm">
                <input type="hidden" name="register" value="1">
                <h1>Crear Cuenta</h1>
                <select name="user_role" id="userRole" required>
                    <option value="">Selecciona tu tipo</option>
                    <option value="student" <?= (isset($_POST['user_role']) && $_POST['user_role'] === 'student') ? 'selected' : '' ?>>Alumno</option>
                    <option value="teacher" <?= (isset($_POST['user_role']) && $_POST['user_role'] === 'teacher') ? 'selected' : '' ?>>Maestro</option>
                </select>
                <div class="user-id-display" id="userIdDisplay" style="display: none;">
                    <label>Tu ID de usuario:</label>
                    <input type="text" id="userId" readonly class="readonly-field" />
                    <input type="hidden" name="user_id" id="userIdHidden" value="" />
                </div>
                <input type="text" name="username" placeholder="Nombre de Usuario" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
                <input type="email" name="email" placeholder="Correo (Gmail o .edu.mx)" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                <div class="password-wrapper">
                    <input id="password" type="password" name="password" placeholder="Contraseña" required />
                    <i class="fas fa-eye-slash toggle-password"></i>
                </div>
                <div id="password-requirements" style="text-align: left; font-size: 0.8em; margin-top: 10px; display: none;">
                    <p class="requirements-title">La contraseña debe contener:</p>
                    <ul class="requirements-list" style="list-style: none; padding-left: 0;">
                        <li id="length"><i class="fas fa-times-circle"></i> <strong>Longitud:</strong> 8+ caracteres</li>
                        <li id="uppercase"><i class="fas fa-times-circle"></i> <strong>Mayúscula:</strong> Al menos una (A-Z)</li>
                        <li id="number"><i class="fas fa-times-circle"></i> <strong>Número:</strong> Al menos uno (0-9)</li>
                    </ul>
                </div>
                <button type="submit">Registrarse</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="auth.php" method="post">
                <input type="hidden" name="login" value="1">
                <h1>Iniciar Sesión</h1>
                <input type="text" name="user_id" placeholder="ID de Usuario" required value="<?= htmlspecialchars($_POST['user_id'] ?? '') ?>" />
                <input type="email" name="email" placeholder="Correo Electrónico" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                <div class="password-wrapper">
                    <input type="password" name="password" placeholder="Contraseña" required />
                    <i class="fas fa-eye-slash toggle-password"></i>
                </div>
                <a href="password_reset_request.php">¿Olvidaste tu contraseña?</a>
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
                    <h1>&iexcl;Bienvenido de Nuevo!</h1>
                    <p>Para mantenerte conectado con nosotros, por favor inicia sesi&oacute;n con tu informaci&oacute;n personal.</p>
                    <p class="note" style="font-size: 0.8em; margin-top: 20px;">El ID de usuario determina si eres alumno o maestro.</p>
                    <button class="ghost" id="signIn">Iniciar Sesi&oacute;n</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <div class="logo-container">
                        <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
                        <span>EduClubs</span>
                    </div>
                    <h1>&iexcl;Hola, Amigo!</h1>
                    <p>Selecciona si eres alumno o maestro y obtén tu ID automáticamente</p>
                    <button class="ghost" id="signUp">Crear Cuenta</button>
                </div>
            </div>
        </div>
    </div>
    <a href="index0.php" class="back-button">Atr&aacute;s</a>

    <script src="js/auth-landing.js"></script>
    <script src="js/notification.js?v=<?php echo time(); ?>"></script>
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
                if (submitButton) {
                    submitButton.disabled = !allValid;
                }
            }

            passwordInput.addEventListener('input', validatePassword);
            validatePassword();
        }

        const registerForm = document.getElementById('registerForm');
        const userRoleSelect = document.getElementById('userRole');
        const userIdDisplay = document.getElementById('userIdDisplay');
        const userIdInput = document.getElementById('userId');
        const userIdHidden = document.getElementById('userIdHidden');

        if (registerForm) {
            // Generar ID automáticamente cuando se selecciona el rol
            if (userRoleSelect) {
                userRoleSelect.addEventListener('change', function() {
                    const selectedRole = this.value;
                    if (selectedRole) {
                        // Generar ID único basado en el rol y timestamp
                        const timestamp = Date.now();
                        const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                        let userId = '';

                        if (selectedRole === 'student') {
                            userId = `@alp_2025_${timestamp.toString().slice(-6)}${randomNum}`;
                        } else if (selectedRole === 'teacher') {
                            userId = `@tea_2025_${timestamp.toString().slice(-6)}${randomNum}`;
                        }

                        userIdInput.value = userId;
                        userIdHidden.value = userId;
                        userIdDisplay.style.display = 'block';
                        this.classList.remove('error');
                    } else {
                        userIdDisplay.style.display = 'none';
                        userIdInput.value = '';
                        userIdHidden.value = '';
                    }
                });
            }

            // Remover clase de error cuando el usuario empiece a escribir
            const inputs = registerForm.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], select');
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
                const userRole = userRoleSelect.value;
                const userId = userIdHidden.value.trim();
                const username = this.querySelector('input[name="username"]').value.trim();
                const email = this.querySelector('input[name="email"]').value.trim();
                const password = this.querySelector('input[name="password"]').value.trim();

                // Verificar si algún campo está vacío
                if (!userRole || !userId || !username || !email || !password) {
                    let emptyFields = [];
                    if (!userRole) emptyFields.push('Tipo de usuario');
                    if (!userId) emptyFields.push('ID de Usuario');
                    if (!username) emptyFields.push('Nombre de Usuario');
                    if (!email) emptyFields.push('Correo Electrónico');
                    if (!password) emptyFields.push('Contraseña');

                    const message = 'Por favor completa los siguientes campos: ' + emptyFields.join(', ');
                    showNotification(message, 'error');

                    // Resaltar campos vacíos con borde rojo
                    userRoleSelect.classList.toggle('error', !userRole);
                    userIdInput.classList.toggle('error', !userId);
                    this.querySelector('input[name="username"]').classList.toggle('error', !username);
                    this.querySelector('input[name="email"]').classList.toggle('error', !email);
                    this.querySelector('input[name="password"]').classList.toggle('error', !password);

                    return; // Detener el envío del formulario
                }

                const formData = new FormData(this);
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Creando...';

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'auth.php', true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 400) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            if (data.success) {
                                showNotification(data.message, 'success');
                                setTimeout(() => {
                                    document.getElementById('signIn').click();
                                    registerForm.reset();
                                }, 2000);
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
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Registrarse';
                };
                xhr.onerror = function() {
                    showNotification('Error de conexión.', 'error');
                    submitBtn.disabled = false;
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
    });
    </script>
</body>
</html>
