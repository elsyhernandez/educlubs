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
        // El código es válido, marcamos que se ha verificado y redirigimos
        $_SESSION['reset_verified'] = true;
        $_SESSION['reset_user_id'] = $user_id;
        
        // Marcar el token como usado para que no se pueda reutilizar para verificación
        $stmt = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
        $stmt->execute([$reset_request['id']]);

        header("Location: password_reset.php");
        exit();
    } else {
        $error = "El código es incorrecto, ha expirado o ya fue utilizado.";
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Verificar Código</title>
    <link rel="stylesheet" href="../css/auth-modern.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-shield-alt"></i> Verificar Código</h2>
        <p style="color: #666; margin-bottom: 20px;">Hemos enviado un código a tu correo. Por favor, ingrésalo a continuación.</p>
        
        <?php if($error): ?>
            <div class="message error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="code">Código de 6 dígitos</label>
                <i class="fas fa-hashtag icon"></i>
                <input id="code" name="code" required maxlength="6" pattern="\d{6}" title="El código debe ser de 6 dígitos numéricos.">
            </div>
            <button type="submit" class="auth-btn">Verificar y Continuar</button>
            <div class="auth-links">
                <a class="auth-link" href="password_reset_request.php">¿No recibiste el código? Reenviar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
