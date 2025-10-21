<?php
require 'config.php';
$info = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND email = ?");
    $stmt->execute([$user_id,$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $info = "Si existe una cuenta con esos datos, se ha enviado un enlace de recuperación.";
    } else {
        // generar token
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id,token,expires_at) VALUES (?,?,?)");
        $stmt->execute([$user_id,$token,$expires]);

        // Simulación de envío de correo
        $reset_link = "http://localhost/proyecto/educlubs/clubproject/password_reset.php?token=$token";
        
        // En un entorno real, aquí se usaría una librería como PHPMailer para enviar el correo.
        // mail($email, "Recuperación de contraseña", "Para restablecer tu contraseña, haz clic en el siguiente enlace: $reset_link");

        // Para fines de prueba en local, mostramos el enlace directamente.
        $info = "<strong>Modo de prueba:</strong> En un entorno real, se enviaría un correo. Por favor, usa el siguiente enlace para restablecer tu contraseña:<br><a href='$reset_link'>Restablecer contraseña</a>";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Recuperar contraseña</title>
    <link rel="stylesheet" href="css/auth-modern.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-key"></i> Recuperar Contraseña</h2>
        <?php if($info): ?>
            <div class="message success" style="background-color: rgba(33, 147, 176, 0.1); color: #2193b0; border: 1px solid #2193b0; text-align: left; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                <?= $info ?>
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
            <button type="submit" class="auth-btn">Solicitar recuperación</button>
            <div class="auth-links">
                <a class="auth-link" href="login.php">Volver a iniciar sesión</a>
                <a class="auth-link" href="index.php">Volver al inicio</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
