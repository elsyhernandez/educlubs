<?php
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
            <li><a href="teacher/dashboard.php" class="<?= $dashboard_active ?>"><i class="fas fa-tachometer-alt"></i>Inicio</a></li>
            <li><a href="../view_clubs.php" class="<?= $clubs_active ?>"><i class="fas fa-users-cog"></i>Clubes</a></li>
        </ul>
    </nav>
</div>
<style>
    .menu-nav a.active {
        pointer-events: none;
        color: #ccc; /* Or any other style to indicate it's disabled */
    }
</style>
