<?php
require '../includes/config.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$user_id = $_GET['user_id'] ?? '';

if (!$user_id) {
    die("ID de usuario no proporcionado.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE user_id = ? AND token = ? AND used = 0 AND expires_at > NOW()");
    $stmt->execute([$user_id, $code]);
    $reset_request = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reset_request) {
        $_SESSION['reset_verified'] = true;
        $_SESSION['reset_user_id'] = $user_id;
        
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $stmt->execute([$reset_request['id']]);

        header("Location: password_reset.php");
        exit();
    } else {
        $error = "El código es incorrecto, ha expirado o ya fue utilizado.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código - EduClubs</title>
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
            <h2><i class="fas fa-shield-alt"></i> Verificar Código</h2>
            <p>Hemos enviado un código a tu correo. Por favor, ingrésalo a continuación.</p>
            
            <?php if($error): ?>
                <div class="message error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <div class="form-group">
                    <i class="fas fa-hashtag icon"></i>
                    <input id="code" name="code" placeholder="Código de 6 dígitos" required maxlength="6" pattern="\d{6}" title="El código debe ser de 6 dígitos numéricos.">
                </div>
                <button type="submit" class="auth-btn">Verificar y Continuar</button>
            </form>
            <div class="auth-links">
                <a href="password_reset_request.php">¿No recibiste el código? Reenviar</a>
                <a href="auth.php" style="display: block; margin-top: 10px;">Volver a iniciar sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
