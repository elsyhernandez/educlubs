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
        $info = "No encontramos una cuenta con esos datos.";
    } else {
        // generar token
        $token = bin2hex(random_bytes(16));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id,token,expires_at) VALUES (?,?,?)");
        $stmt->execute([$user_id,$token,$expires]);

        // En entorno real: enviar email con link tipo /password_reset.php?token=...
        // En local (XAMPP) mostramos link para probar:
        $info = "Se creó un token. Usa este enlace para cambiar la contraseña (validez 1 hora): ";
        $info .= "<br><a href='password_reset.php?token=$token'>Abrir link de recuperación</a>";
    }
}
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Recuperar contraseña</title></head><body>
  <h2>Recuperar contraseña</h2>
  <?php if($info) echo "<div>$info</div>"; ?>
  <form method="post">
    <label>ID de usuario</label><input name="user_id" required>
    <label>Correo (el registrado)</label><input name="email" required>
    <button type="submit">Solicitar recuperación</button>
  </form>
  <p><a href="login.php">Volver</a></p>
</body></html>
