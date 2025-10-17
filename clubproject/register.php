<?php
require 'config.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = trim($_POST['user_id'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = 'student';

    // decidir role por prefijo del user_id
    if (stripos($user_id, '@tea_2025') === 0) $role = 'teacher';
    elseif (stripos($user_id, '@alp_2025') === 0) $role = 'student';
    else $errors[] = "El ID debe comenzar con @alp_2025 (alumno) o @tea_2025 (maestro).";

    // validaciones email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";
    else {
        $host = substr(strrchr($email, "@"), 1);
        // permitir gmail y .edu.mx
        if (strtolower($host) !== 'gmail.com' && substr(strtolower($host), -6) !== 'edu.mx') {
            $errors[] = "Solo se permiten correos Gmail o institucionales .edu.mx.";
        }
    }

    if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    if (!$username) $errors[] = "Nombre de usuario es requerido.";

    // verificar si ya existe user_id o email
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ? OR email = ?");
    $stmt->execute([$user_id, $email]);
    if ($stmt->fetchColumn() > 0) $errors[] = "El ID o el correo ya están registrados.";

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (user_id,email,username,password_hash,role) VALUES (?,?,?,?,?)");
        $stmt->execute([$user_id,$email,$username,$hash,$role]);
        $_SESSION['user'] = ['user_id'=>$user_id,'email'=>$email,'username'=>$username,'role'=>$role];
        // redirige al dashboard según role
        if ($role === 'teacher') redirect('teacher_dashboard.php');
        redirect('student_dashboard.php');
    }
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Crear cuenta</title>
<style>
body{font-family:Arial;background:#f5f7fb;padding:20px}
.container{max-width:520px;margin:30px auto;background:#fff;padding:24px;border-radius:10px;box-shadow:0 6px 16px rgba(0,0,0,0.06)}
input,button{width:100%;padding:10px;margin:8px 0;border-radius:6px;border:1px solid #ddd}
.error{color:#b00020;background:#ffecec;padding:8px;border-radius:6px}
.note{font-size:13px;color:#444}
</style>
</head>
<body>
<div class="container">
  <h2>Crear cuenta</h2>

  <?php if(!empty($errors)): ?>
    <div class="error">
      <?php foreach($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?>
    </div>
  <?php endif; ?>

  <form method="post" id="registerForm">
    <label>ID de usuario (ej: @alp_2025_01 o @tea_2025_01)</label>
    <input name="user_id" required value="<?=htmlspecialchars($_POST['user_id'] ?? '')?>">
    <label>Correo (solo Gmail o .edu.mx)</label>
    <input name="email" required value="<?=htmlspecialchars($_POST['email'] ?? '')?>">
    <label>Nombre de usuario</label>
    <input name="username" required value="<?=htmlspecialchars($_POST['username'] ?? '')?>">
    <label>Contraseña</label>
    <input name="password" type="password" required>
    <p class="note">Si pones un ID que empieza con <code>@tea_2025</code> la cuenta quedará como maestro.</p>
    <button type="submit">Crear cuenta</button>
  </form>
  <p><a href="index.php">Volver</a></p>
</div>
</body>
</html>
