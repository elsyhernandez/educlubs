<?php
require_once 'config.php';

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['user_id']) || $_SESSION['user']['role'] !== 'student') {
    redirect(BASE_URL . '/auth/index.php');
}

// Fetch the latest user data to ensure the profile picture and other info are up-to-date
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user']['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    redirect(BASE_URL . '/auth/index.php');
}
$_SESSION['user'] = $user; // Refresh the session data
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/student-header.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/menu.css?v=<?php echo time(); ?>">
  <?php if (basename($_SERVER['PHP_SELF']) == 'student_dashboard.php'): ?>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/student-dashboard.css?v=<?php echo time(); ?>">
  <?php endif; ?>
</head>
<body>
  <header class="main-header">
    <div class="logo">
        <img src="<?= BASE_URL ?>/admin/assets/img/logo.png" alt="Logo EduClubs" style="height: 70px; margin-right: 15px;">
        <img src="https://cbtis258.edu.mx/wp-content/uploads/2024/08/cbtis258-logo.png" alt="Logo CBTis 258" style="height: 70px; margin-right: 15px;">
        <span>EduClubs - Panel de Alumno</span>
    </div>
    <div class="header-actions">
        <?php include 'menu.php'; ?>
        <?php
        $currentPage = basename($_SERVER['PHP_SELF']);
        if ($currentPage === 'student_clubs.php') : ?>
            <a href="<?= BASE_URL ?>/student_dashboard.php" class="btn transparent">Volver</a>
        <?php elseif ($currentPage === 'profile_settings.php' || $currentPage === 'club.php') : ?>
            <a href="<?= BASE_URL ?>/student_dashboard.php" class="btn transparent">Volver</a>
        <?php else : ?>
            <a href="<?= BASE_URL ?>/student_clubs.php" class="btn transparent">Mis Clubs</a>
        <?php endif; ?>
        <?php if ($currentPage !== 'profile_settings.php') : ?>
        <div class="usericon">
            <div class="avatar">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Foto de perfil">
                <?php else: ?>
                    <?= strtoupper(substr($user['nombres'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
            <div class="user-menu">
                <div class="user-info">
                    <div class="profile-picture-container">
                        <?php if (!empty($user['profile_picture'])): ?>
                            <img src="<?= BASE_URL ?>/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Foto de perfil" class="profile-picture">
                        <?php else: ?>
                            <div class="no-profile-picture">
                                <span>No tiene foto</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="user-details">
                        <div class="username"><?= strtoupper(htmlspecialchars($user['nombres'] . ' ' . $user['paterno'])) ?></div>
                        <div class="user-id"><?= strtoupper(htmlspecialchars($user['user_id'] ?? '')) ?></div>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/profile_settings.php" class="settings"><i class="fas fa-cog"></i>Configuración</a>
                <a href="<?= BASE_URL ?>/auth/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i>Cerrar sesión</a>
            </div>
        </div>
        <?php endif; ?>
    </div>
  </header>
  <script src="<?= BASE_URL ?>/js/menu.js?v=<?php echo time(); ?>"></script>
