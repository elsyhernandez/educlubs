<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
require '../includes/config.php';
require '../includes/mail_config.php';

$info = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ? AND email = ?");
    $stmt->execute([$user_id, $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Mensaje genérico para no revelar si un usuario existe o no
        $info = "Si existe una cuenta con esos datos, se ha enviado un código de recuperación a tu correo.";
    } else {
        // Generar un código de 6 dígitos
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 600); // 10 minutos de validez

        // Guardar el código en la base de datos
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $code, $expires]);

        // Enviar el correo con PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            // Destinatarios
            $mail->setFrom(SMTP_USERNAME, 'EduClubs');
            $mail->addAddress($email, $user['username']);

            // Contenido
            $mail->isHTML(true);
            $mail->Subject = 'Tu codigo de recuperacion de contrasena';
            $mail->Body    = "Hola,<br><br>Tu código para restablecer la contraseña es: <b>$code</b><br><br>Este código expirará en 10 minutos.<br><br>Si no solicitaste esto, puedes ignorar este correo.";
            $mail->AltBody = "Tu código de recuperación es: $code";

            $mail->send();
            
            // Redirigir a la página de verificación
            header("Location: verify_code.php?user_id=" . urlencode($user_id));
            exit();

        } catch (Exception $e) {
            $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Recuperar contraseña</title>
<link rel="stylesheet" href="../css/auth-modern.css?v=<?php echo time(); ?>">
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
                <?= htmlspecialchars($info) ?>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
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
                <a class="auth-link" href="auth.php">Volver a iniciar sesión</a>
                <a class="auth-link" href="index.php">Volver al inicio</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
