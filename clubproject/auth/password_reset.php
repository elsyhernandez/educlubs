<?php
require '../includes/config.php';
$token = $_GET['token'] ?? ($_POST['token'] ?? null);
$errors = [];
$success = '';

if (!$token) {
    die("Token de restablecimiento no proporcionado.");
}

// Validar token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
$stmt->execute([$token]);
$reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset_request) {
    die("El token es inválido, ha expirado o ya fue utilizado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (strlen($password) < 6) {
        $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    }
    if ($password !== $password_confirm) {
        $errors[] = "Las contraseñas no coinciden.";
    }

    if (empty($errors)) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Actualizar contraseña del usuario
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$new_hash, $reset_request['user_id']]);

        // Marcar token como usado
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $stmt->execute([$reset_request['id']]);

        $success = "¡Tu contraseña ha sido actualizada con éxito! Ahora puedes <a href='index.php' class='auth-link'>iniciar sesión</a>.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="../css/auth-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-sync-alt"></i> Restablecer Contraseña</h2>

        <?php if (!empty($errors)): ?>
            <div class="message error">
                <?php foreach ($errors as $error): ?>
                    <div>- <?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="message success" style="background-color: rgba(33, 147, 176, 0.1); color: #2193b0; border: 1px solid #2193b0; text-align: left; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?= $success ?>
            </div>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <label for="password">Nueva Contraseña</label>
                    <div class="password-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="password" name="password" required>
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar Contraseña</label>
                    <div class="password-wrapper">
                        <i class="fas fa-lock icon"></i>
                        <input type="password" id="password_confirm" name="password_confirm" required>
                        <span class="toggle-password"><i class="fas fa-eye"></i></span>
                    </div>
                </div>

                <button type="submit" class="auth-btn">Actualizar Contraseña</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(item => {
        item.addEventListener('click', function () {
            const passwordInput = this.previousElementSibling;
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
</script>

</body>
</html>
