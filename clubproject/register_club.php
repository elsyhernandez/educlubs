<?php
require 'config.php';
if (!isset($_SESSION['user'])) redirect('login.php');

$type = $_GET['type'] ?? '';
$club = $_GET['club'] ?? '';
$user = $_SESSION['user'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $club_name = $_POST['club'] ?? '';
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM clubs WHERE club_name = ?");
    $stmt->execute([$club_name]);
    if ($stmt->fetchColumn() == 0) {
        $errors[] = "El club no está registrado y no se puede procesar la inscripción.";
    }

    if (empty($errors)) {
        // si es asesoría
        if ($_POST['type'] === 'asesoria') {
            $paterno = trim($_POST['paterno'] ?? '');
            $materno = trim($_POST['materno'] ?? '');
            $nombres = trim($_POST['nombres'] ?? '');
            $carrera = trim($_POST['carrera'] ?? '');
            $turno = trim($_POST['turno'] ?? '');
            $maestro = trim($_POST['maestro'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');

            if (!preg_match('/^\d{10}$/', $telefono)) $errors[] = "El teléfono debe tener exactamente 10 dígitos.";
            if (!$paterno || !$materno || !$nombres) $errors[] = "Nombre completo es requerido.";

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO tutoring_registrations (materia,paterno,materno,nombres,carrera,turno,maestro,telefono,user_id) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$_POST['club'],$paterno,$materno,$nombres,$carrera,$turno,$maestro,$telefono,$user['user_id']]);
                $success = true;
            }
        } else {
            // cultural/deportivo/civil
            $paterno = trim($_POST['paterno'] ?? '');
            $materno = trim($_POST['materno'] ?? '');
            $nombres = trim($_POST['nombres'] ?? '');
            $semestre = trim($_POST['semestre'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $turno = trim($_POST['turno'] ?? '');

            // validar correo (permitir gmail y edu.mx y tener @)
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo inválido.";
            else {
                $host = substr(strrchr($correo, "@"), 1);
                if (strtolower($host) !== 'gmail.com' && substr(strtolower($host), -6) !== 'edu.mx') {
                    $errors[] = "Solo se permiten correos Gmail o institucionales .edu.mx.";
                }
            }
            if (!$paterno || !$materno || !$nombres) $errors[] = "Nombre completo es requerido.";

            if (empty($errors)) {
                $stmt = $pdo->prepare("INSERT INTO club_registrations (club_type,club_name,paterno,materno,nombres,semestre,correo,turno,user_id) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([$_POST['type'],$_POST['club'],$paterno,$materno,$nombres,$semestre,$correo,$turno,$user['user_id']]);
                $success = true;
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Registro club</title>
<link rel="stylesheet" href="css/main-modern.css">
</head>
<body>
<div class="main-container">
  <h2>Registro - <?=htmlspecialchars($club)?> (<?=htmlspecialchars($type)?>)</h2>

  <?php if($success): ?>
    <div class="message success">Tu registro ha sido exitoso. <a href="student_dashboard.php">Volver a inicio</a></div>
  <?php else: ?>
    <?php if($errors): ?><div class="message error"><?php foreach($errors as $e) echo "<div>- ".htmlspecialchars($e)."</div>"; ?></div><?php endif; ?>

    <?php if($type === 'asesoria'): ?>
      <form method="post" class="form-grid">
        <input type="hidden" name="type" value="asesoria">
        <input type="hidden" name="club" value="<?=htmlspecialchars($club)?>">
        <div class="form-group"><label>Apellido paterno</label><input name="paterno" required value="<?=htmlspecialchars($_POST['paterno'] ?? '')?>"></div>
        <div class="form-group"><label>Apellido materno</label><input name="materno" required value="<?=htmlspecialchars($_POST['materno'] ?? '')?>"></div>
        <div class="form-group"><label>Nombres</label><input name="nombres" required value="<?=htmlspecialchars($_POST['nombres'] ?? '')?>"></div>
        <div class="form-group"><label>Carrera</label><input name="carrera" value="<?=htmlspecialchars($_POST['carrera'] ?? '')?>"></div>
        <div class="form-group"><label>Turno</label><input name="turno" value="<?=htmlspecialchars($_POST['turno'] ?? '')?>"></div>
        <div class="form-group"><label>Maestro</label><input name="maestro" value="<?=htmlspecialchars($_POST['maestro'] ?? '')?>"></div>
        <div class="form-group"><label>Teléfono (10 dígitos)</label><input name="telefono" value="<?=htmlspecialchars($_POST['telefono'] ?? '')?>"></div>
        <button type="submit" class="btn">Enviar registro</button>
      </form>
    <?php else: ?>
      <form method="post" class="form-grid">
        <input type="hidden" name="type" value="<?=htmlspecialchars($type)?>">
        <input type="hidden" name="club" value="<?=htmlspecialchars($club)?>">
        <div class="form-group"><label>Apellido paterno</label><input name="paterno" required value="<?=htmlspecialchars($_POST['paterno'] ?? '')?>"></div>
        <div class="form-group"><label>Apellido materno</label><input name="materno" required value="<?=htmlspecialchars($_POST['materno'] ?? '')?>"></div>
        <div class="form-group"><label>Nombres</label><input name="nombres" required value="<?=htmlspecialchars($_POST['nombres'] ?? '')?>"></div>
        <div class="form-group"><label>Semestre</label><input name="semestre" value="<?=htmlspecialchars($_POST['semestre'] ?? '')?>"></div>
        <div class="form-group"><label>Correo (Gmail o .edu.mx)</label><input name="correo" required value="<?=htmlspecialchars($_POST['correo'] ?? '')?>"></div>
        <div class="form-group"><label>Turno</label><input name="turno" value="<?=htmlspecialchars($_POST['turno'] ?? '')?>"></div>
        <button type="submit" class="btn">Enviar registro</button>
      </form>
    <?php endif; ?>
  <?php endif; ?>
  <p><a href="club.php?type=<?=urlencode($type)?>">Volver</a></p>
</div>
</body>
>>>>>>> Stashed changes
</html>
