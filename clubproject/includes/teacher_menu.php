<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;

$current_page = basename($_SERVER['REQUEST_URI']);
$dashboard_active = (strpos($current_page, 'dashboard.php') !== false) ? 'active' : '';
$clubs_active = (strpos($current_page, 'view_clubs.php') !== false) ? 'active' : '';
?>
<div class="menu-container">
    <button class="menu-toggle" id="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>
    <nav class="menu-nav" id="menu-nav">
        <ul>
            <li><a href="../teacher/dashboard.php" class="<?= $dashboard_active ?>"><i class="fas fa-tachometer-alt"></i>Inicio</a></li>
            <li><a href="../view_clubs.php" class="<?= $clubs_active ?>"><i class="fas fa-users-cog"></i>Clubes</a></li>
        </ul>
    </nav>
</div>

<?php if ($user): ?>
<div class="user-menu-container">
    <div class="user-profile-trigger">
        <div class="avatar"><?= strtoupper($user['username'][0] ?? 'U') ?></div>
        <span class="username"><?= htmlspecialchars($user['username'] ?? '') ?></span>
    </div>
    <div class="user-menu">
        <div class="user-info">
            <strong><?= htmlspecialchars($user['username'] ?? '') ?></strong>
            <br>
            <small>Maestro</small>
        </div>
        <a href="../auth/logout.php?redirect=index.php" class="logout">
            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
        </a>
    </div>
</div>
<?php endif; ?>

<style>
    .menu-nav a.active {
        pointer-events: none;
        color: #ccc;
    }
    .user-menu-container {
        position: relative;
        margin-left: auto;
    }
    .user-profile-trigger {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 5px;
        border-radius: 50px;
        transition: background-color 0.3s ease;
    }
    .user-profile-trigger:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    .avatar {
        width: 40px;
        height: 40px;
        min-width: 40px;
        min-height: 40px;
        border-radius: 50%;
        background: var(--accent-color, #952f57);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 18px;
        border: 2px solid #fff;
        box-sizing: border-box;
    }
    .username {
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        margin-right: 10px;
    }
    .user-menu {
      opacity: 0;
      visibility: hidden;
      position: absolute;
      top: calc(100% + 5px);
      right: 0;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.15);
      min-width: 220px;
      z-index: 110;
      overflow: hidden;
      transform: translateY(10px);
      transition: opacity 0.2s ease, transform 0.2s ease, visibility 0s 0.2s;
    }
    .user-menu-container:hover .user-menu {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
      transition: opacity 0.2s ease, transform 0.2s ease, visibility 0s 0s;
    }
    .user-menu .user-info {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        color: #333;
    }
    .user-menu .user-info strong {
        font-size: 16px;
    }
    .user-menu a {
        padding: 12px 15px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #333;
        transition: background-color 0.2s ease;
    }
    .user-menu a:hover {
        background: #f1f3f5;
    }
    .user-menu a.logout {
        color: #d93025;
        font-weight: 500;
    }
    .user-menu a .fas {
        width: 20px;
        text-align: center;
    }
</style>
