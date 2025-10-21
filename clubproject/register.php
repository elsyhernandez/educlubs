<?php
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'student';

    // decidir role por prefijo del user_id
    if (stripos($user_id, '@tea_2025') === 0) $role = 'teacher';
    elseif (stripos($user_id, '@alp_2025') === 0) $role = 'student';
    else $errors[] = "El ID debe comenzar con @alp_2025 (alumno) o @tea_2025 (maestro).";

    // validaciones email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";
    else {
        $host = substr(strrchr($email, "@"), 1);
        // permitir gmail y .edu.mx
        if (strtolower($host) !== 'gmail.com' && substr(strtolower($host), -6) !== 'edu.mx') {
            $errors[] = "Solo se permiten correos Gmail o institucionales .edu.mx.";
        }
    }

    if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    if (!$username) $errors[] = "Nombre de usuario es requerido.";

    // verificar si ya existe user_id o email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ? OR email = ?");
    $stmt->execute([$user_id, $email]);
    if ($stmt->fetchColumn() > 0) $errors[] = "El ID o el correo ya están registrados.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (user_id,email,username,password_hash,role) VALUES (?,?,?,?,?)");
        $stmt->execute([$user_id,$email,$username,$hash,$role]);
        $_SESSION['user'] = ['user_id'=>$user_id,'email'=>$email,'username'=>$username,'role'=>$role];
        // redirige al dashboard según role
        if ($role === 'teacher') redirect('teacher_dashboard.php');
        redirect('student_dashboard.php');
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
<title>Crear cuenta</title>
    <link rel="stylesheet" href="css/auth-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-user-plus"></i> Crear cuenta</h2>
    <?php if(!empty($errors)): ?>
        <div class="message error">
            <?php foreach($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?>
        </div>
    <?php endif; ?>
    <form method="post" id="registerForm">
        <div class="form-group">
            <label for="user_id">ID de usuario</label>
            <i class="fas fa-id-card icon"></i>
            <input id="user_id" name="user_id" required value="<?=htmlspecialchars($_POST['user_id'] ?? '')?>" placeholder="ej: @alp_2025_01">
        </div>
        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <i class="fas fa-envelope icon"></i>
            <input id="email" name="email" type="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>" placeholder="Solo Gmail o .edu.mx">
        </div>
        <div class="form-group">
            <label for="username">Nombre de usuario</label>
            <i class="fas fa-user icon"></i>
            <input id="username" name="username" required value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
        </div>
        <div class="form-group">
            <label for="password">Contraseña</label>
            <div class="password-wrapper">
                <i class="fas fa-lock icon"></i>
                <input id="password" name="password" type="password" required>
                <span class="toggle-password"><i class="fas fa-eye"></i></span>
            </div>
        </div>
        <p class="note" style="font-size: 0.8rem; color: #555; margin-top: 15px;">Si tu ID empieza con <code>@tea_2025</code>, tu cuenta será de maestro.</p>
        <button type="submit" class="auth-btn">Crear cuenta</button>
        <div class="auth-links">
            <a class="auth-link" href="login.php">¿Ya tienes cuenta? Inicia sesión</a>
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
