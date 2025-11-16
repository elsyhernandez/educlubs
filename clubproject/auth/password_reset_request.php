<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
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
        $info = "Si existe una cuenta con esos datos, se ha enviado un código de recuperación a tu correo.";
    } else {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d H:i:s', time() + 600); 

        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $code, $expires]);

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = SMTP_PORT;

            $mail->setFrom(SMTP_USERNAME, 'EduClubs');
            $mail->addAddress($email, $user['username']);

            $mail->isHTML(true);
            $mail->Subject = 'Tu codigo de recuperacion de contrasena';
            $mail->Body    = "Hola,<br><br>Tu código para restablecer la contraseña es: <b>$code</b><br><br>Este código expirará en 10 minutos.<br><br>Si no solicitaste esto, puedes ignorar este correo.";
            $mail->AltBody = "Tu código de recuperación es: $code";

            $mail->send();
            
            header("Location: verify_code.php?user_id=" . urlencode($user_id));
            exit();

        } catch (Exception $e) {
            $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - EduClubs</title>
    <link rel="stylesheet" href="../css/auth-modern.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-wrapper">
        <div class="auth-container">
            <div class="logo-container">
                <img src="https://imgs.search.brave.com/iH58Yz2SiQN00OY9h2I7Efo09BFFa5heeAaEj_uNTsM/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jYnRp/czI1OC5lZHUubXgv/d3AtY29udGVudC91/cGxvYWRzLzIwMjQv/MDgvY2J0aXMyNTgt/bG9nby5wbmc" alt="Logo CBTis 258">
                <span>EduClubs</span>
            </div>
            <h1>Recuperar Contraseña</h1>
            <p>Ingresa tus datos para enviarte un código de recuperación.</p>
            
            <?php if($info): ?>
                <div class="message success">
                    <?= htmlspecialchars($info) ?>
                </div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="message error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="input-wrapper">
                    <input id="user_id" name="user_id" placeholder="ID de Usuario" required value="<?=htmlspecialchars($_POST['user_id'] ?? '')?>">
                </div>
                <div class="input-wrapper">
                    <input id="email" name="email" type="email" placeholder="Correo Electrónico" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
                </div>
                <button type="submit">Solicitar recuperación</button>
            </form>
            <div class="auth-links">
                <a href="auth.php">Volver a iniciar sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
