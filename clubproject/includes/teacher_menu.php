<?php
$current_page = basename($_SERVER['REQUEST_URI']);
$dashboard_active = (strpos($current_page, 'dashboard.php') !== false) ? 'active' : '';
$clubs_active = (strpos($current_page, 'view_clubs.php') !== false) ? 'active' : '';
?>
<link rel="stylesheet" href="../css/notification.css">
<div class="header-container">
    <div class="menu-container">
        <button class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        <nav class="menu-nav" id="menu-nav">
            <ul>
                <li><a href="teacher/dashboard.php" class="<?= $dashboard_active ?>"><i class="fas fa-tachometer-alt"></i>Inicio</a></li>
                <li><a href="../view_clubs.php" class="<?= $clubs_active ?>"><i class="fas fa-users-cog"></i>Clubes</a></li>
            </ul>
        </nav>
    </div>

    <div class="notification-icon" id="notification-icon">
        <i class="fas fa-bell"></i>
        <span class="notification-count" id="notification-count">0</span>
    </div>
</div>

<div class="notification-panel" id="notification-panel">
    <div class="notification-header">
        <h4>Notificaciones</h4>
    </div>
    <div class="notification-body" id="notification-body">
        <!-- Las notificaciones se cargarán aquí -->
    </div>
</div>

<style>
    .menu-nav a.active {
        pointer-events: none;
        color: #ccc; /* O cualquier otro estilo para indicar que está deshabilitado */
    }
</style>
<script src="../js/notification.js"></script>
