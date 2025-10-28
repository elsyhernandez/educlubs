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
            // In a real application, you would email this to the user.
            // For this demo, we'll display it directly.
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
    <link rel="stylesheet" href="../css/auth-landing.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../css/notification.css?v=<?php echo time(); ?>">
    <style>
        .recovery-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .recovery-container h1 {
            margin-bottom: 20px;
        }
        .recovery-container p {
            margin-bottom: 20px;
            color: #666;
        }
        .recovery-container input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .recovery-container button {
            width: 100%;
        }
        .message, .error {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }
        .message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: text-decoration 0.3s ease;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="recovery-container">
        <h1>Recuperar ID de Usuario</h1>
        <p>Introduce tu correo electrónico para encontrar tu ID de usuario.</p>
        
        <?php if ($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form action="forgot_id.php" method="post">
            <input type="email" name="email" placeholder="Tu correo electrónico" required>
            <button type="submit">Buscar mi ID</button>
        </form>
        <a href="auth.php" class="back-link">Volver a Iniciar Sesión</a>
    </div>
</body>
</html>
