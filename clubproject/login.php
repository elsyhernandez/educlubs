<?php
require 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) $errors[] = "Usuario no encontrado.";
    else {
        if ($user['email'] !== $email) $errors[] = "El correo debe ser el mismo usado al registrarte.";
        if (!password_verify($password, $user['password_hash'])) $errors[] = "Contraseña incorrecta.";
    }

    if (empty($errors)) {
        $_SESSION['user'] = ['user_id'=>$user['user_id'],'email'=>$user['email'],'username'=>$user['username'],'role'=>$user['role']];
        if ($user['role'] === 'teacher') redirect('teacher_dashboard.php');
        redirect('student_dashboard.php');
    }
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Iniciar sesión</title>
<style>
body{font-family:Arial;background:#f5f7fb;padding:20px}
.container{max-width:480px;margin:30px auto;background:#fff;padding:24px;border-radius:10px;box-shadow:0 6px 16px rgba(0,0,0,0.06)}
input,button{width:100%;padding:10px;margin:8px 0;border-radius:6px;border:1px solid #ddd}
.error{color:#b00020;background:#ffecec;padding:8px;border-radius:6px}
a.link{display:inline-block;margin-top:8px;color:#2b6ef6;text-decoration:none}
</style>
</head>
<body>
<div class="container">
  <h2>Iniciar sesión</h2>
  <?php if($errors): ?><div class="error"><?php foreach($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?></div><?php endif; ?>
  <form method="post">
    <label>ID de usuario</label>
    <input name="user_id" required value="<?=htmlspecialchars($_POST['user_id'] ?? '')?>">
    <label>Correo (el mismo con el que te registraste)</label>
    <input name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
    <label>Contraseña</label>
    <input name="password" type="password" required>
    <a class="link" href="password_reset_request.php">Olvidé mi contraseña</a>
    <button type="submit">Entrar</button>
  </form>
  <p><a href="index.php">Volver</a></p>
</div>
</body>
</html>
