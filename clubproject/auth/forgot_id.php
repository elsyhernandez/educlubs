<?php
require '../includes/config.php';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, introduce un correo electrónico válido.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $message = "Tu ID de Usuario es: <strong>" . htmlspecialchars($user['user_id']) . "</strong>";
        } else {
            $error = "No se encontró ninguna cuenta con ese correo electrónico.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar ID de Usuario - EduClubs</title>
    <link rel="stylesheet" href="../css/auth-modern.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="auth-container">
        <h2><i class="fas fa-id-card"></i> Recuperar ID</h2>
        <p style="color: #666; margin-bottom: 20px;">Introduce tu correo para encontrar tu ID de usuario.</p>
        
        <?php if ($message): ?>
            <div class="message success" style="background-color: rgba(33, 147, 176, 0.1); color: #2193b0; border: 1px solid #2193b0; text-align: left; padding: 12px; border-radius: 8px; margin-bottom: 20px;"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="forgot_id.php" method="post">
            <div class="form-group">
                <i class="fas fa-envelope icon"></i>
                <input type="email" name="email" placeholder="Tu correo electrónico" required>
            </div>
            <button type="submit" class="auth-btn">Buscar mi ID</button>
        </form>
        <div class="auth-links">
            <a href="auth.php" class="auth-link">Volver a Iniciar Sesión</a>
        </div>
    </div>
</div>
</body>
</html>
