<?php
require 'config.php';
$token = $_GET['token'] ?? ($_POST['token'] ?? null);
$message = '';

if (!$token) { echo "Token faltante."; exit; }

// buscar token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW()");
$stmt->execute([$token]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { echo "Token inválido o expirado."; exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newpw = $_POST['password'] ?? '';
    if (strlen($newpw) < 6) $message = "La contraseña debe tener mínimo 6 caracteres.";
    else {
        $hash = password_hash($newpw, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?")->execute([$hash, $row['user_id']]);
        $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$row['id']]);
        $message = "Contraseña actualizada. <a href='login.php'>Iniciar sesión</a>";
    }
}
?>
<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Cambiar contraseña</title></head><body>
  <h2>Cambiar contraseña de <?=htmlspecialchars($row['user_id'])?></h2>
  <?php if($message) echo "<div>$message</div>"; ?>
  <form method="post">
    <input type="hidden" name="token" value="<?=htmlspecialchars($token)?>">
    <label>Nueva contraseña</label><input name="password" type="password" required>
    <button type="submit">Actualizar</button>
  </form>
</body></html>
