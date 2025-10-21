<?php
require 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) $errors[] = "Usuario no encontrado.";
    else {
        if ($user['email'] !== $email) $errors[] = "El correo debe ser el mismo usado al registrarte.";
        if (!password_verify($password, $user['password_hash'])) $errors[] = "Contraseña incorrecta.";
    }

    if (empty($errors)) {
        $_SESSION['user'] = ['user_id'=>$user['user_id'],'email'=>$user['email'],'username'=>$user['username'],'role'=>$user['role']];
        if ($user['role'] === 'teacher') redirect('teacher_dashboard.php');
        redirect('student_dashboard.php');
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/auth-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-sign-in-alt"></i> Iniciar sesión</h2>
    <?php if($errors): ?>
        <div class="message error">
            <?php foreach($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?>
        </div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="user_id">ID de usuario</label>
            <i class="fas fa-user icon"></i>
            <input id="user_id" name="user_id" required value="<?=htmlspecialchars($_POST['user_id'] ?? '')?>">
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <i class="fas fa-envelope icon"></i>
            <input id="email" name="email" type="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-wrapper">
                <i class="fas fa-lock icon"></i>
                <input id="password" name="password" type="password" required>
                <span class="toggle-password"><i class="fas fa-eye"></i></span>
            </div>
        </div>
        <button type="submit" class="auth-btn">Entrar</button>
        <div class="auth-links">
            <a class="auth-link" href="password_reset_request.php">¿Olvidaste tu contraseña?</a>
            <a class="auth-link" href="register.php">Crear una cuenta</a>
            <a class="auth-link" href="index.php">Volver al inicio</a>
        </div>
    </form>
</div>
</div>
<script>
    document.querySelector('.toggle-password').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
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
</script>
</body>
</html>
