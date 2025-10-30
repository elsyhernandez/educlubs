<?php
require '../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['reset_verified']) || $_SESSION['reset_verified'] !== true) {
    die("Acceso no autorizado. Por favor, verifica tu código primero.");
}

$user_id = $_SESSION['reset_user_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    if (!preg_match('/[A-Z]/', $password)) $errors[] = "La contraseña debe contener al menos una letra mayúscula.";
    if (!preg_match('/[0-9]/', $password)) $errors[] = "La contraseña debe contener al menos un número.";
    if ($password !== $password_confirm) $errors[] = "Las contraseñas no coinciden.";

    if (empty($errors)) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$new_hash, $user_id]);

        unset($_SESSION['reset_verified']);
        unset($_SESSION['reset_user_id']);

        $success = "¡Tu contraseña ha sido actualizada con éxito! Ahora puedes <a href='auth.php' class='auth-link'>iniciar sesión</a>.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - EduClubs</title>
    <link rel="stylesheet" href="../css/auth-modern.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/notification.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-sync-alt"></i> Restablecer Contraseña</h2>

        <?php if ($success): ?>
            <div class="message success" style="background-color: rgba(33, 147, 176, 0.1); color: #2193b0; border: 1px solid #2193b0; text-align: left; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                ¡Tu contraseña ha sido actualizada con éxito! Ahora puedes <a href='auth.php' class='auth-link'>iniciar sesión</a>.
                <br><br>
                <a href="auth.php" class="auth-btn" style="display: inline-block; text-decoration: none;">Volver</a>
            </div>
        <?php else: ?>
            <form method="post">
                <div class="form-group">
                    <div class="password-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="password" name="password" placeholder="Nueva Contraseña" required>
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="password-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="password_confirm" name="password_confirm" placeholder="Confirmar Contraseña" required>
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>
                
                <div id="password-requirements" style="text-align: left; font-size: 0.8em; margin-top: 10px;">
                    <p class="requirements-title">La contraseña debe contener:</p>
                    <ul class="requirements-list" style="list-style: none; padding-left: 0;">
                        <li id="length"><i class="fas fa-times-circle"></i> <strong>Longitud:</strong> 8+ caracteres</li>
                        <li id="uppercase"><i class="fas fa-times-circle"></i> <strong>Mayúscula:</strong> Al menos una (A-Z)</li>
                        <li id="number"><i class="fas fa-times-circle"></i> <strong>Número:</strong> Al menos uno (0-9)</li>
                    </ul>
                </div>

                <button type="submit" class="auth-btn">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-password').forEach(item => {
            item.addEventListener('click', function () {
                const passwordInput = this.parentElement.querySelector('input');
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        const passwordInput = document.getElementById('password');
        const requirementsContainer = document.getElementById('password-requirements');
        if (passwordInput && requirementsContainer) {
            const requirements = {
                length: document.getElementById('length'),
                uppercase: document.getElementById('uppercase'),
                number: document.getElementById('number')
            };

            function validatePassword() {
                const value = passwordInput.value;
                const validations = {
                    length: value.length >= 8,
                    uppercase: /[A-Z]/.test(value),
                    number: /[0-9]/.test(value)
                };

                for (const key in validations) {
                    const requirement = requirements[key];
                    if (validations[key]) {
                        requirement.classList.add('valid');
                    } else {
                        requirement.classList.remove('valid');
                    }
                }
            }
            passwordInput.addEventListener('input', validatePassword);
        }
    });
</script>

<script src="../js/notification.js?v=<?php echo time(); ?>"></script>
<?php if (!empty($errors)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const errorMessage = "<ul><?php foreach ($errors as $error) echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>'; ?></ul>";
        showNotification(errorMessage, 'error');
    });
</script>
<?php endif; ?>

<?php if ($success): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showNotification("¡Tu contraseña ha sido actualizada con éxito!", 'success');
    });
</script>
<?php endif; ?>
</body>
</html>
