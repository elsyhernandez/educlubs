<?php
require_once 'includes/student_header.php';

// Fetch clubs the student is registered in
$stmt = $pdo->prepare("
    SELECT cr.club_name, cr.club_type, cr.created_at AS registration_date
    FROM club_registrations cr
    WHERE cr.user_id = ?
    UNION ALL
    SELECT tr.materia AS club_name, 'asesoria' AS club_type, tr.created_at AS registration_date
    FROM tutoring_registrations tr
    WHERE tr.user_id = ?
    ORDER BY registration_date DESC
");
$stmt->execute([$user['user_id'], $user['user_id']]);
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<title>Mis Clubs</title>
<link rel="stylesheet" href="css/main-modern.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="css/unsubscribe-styles.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<div class="main-container">
    <h2>Mis Clubs y Asesorías</h2>
    <p class="subtitle">Aquí puedes ver y administrar todos los clubs y asesorías en los que estás inscrito.</p>
    <?php if (empty($clubs)): ?>
        <div class="no-clubs-message">
            <i class="fas fa-info-circle"></i>
            <p>No estás inscrito en ningún club o asesoría.</p>
            <a href="student_dashboard.php" class="btn-explore">Explorar Clubs</a>
        </div>
    <?php else: ?>
        <div class="club-grid">
            <?php 
            $club_icons = [
                'cultural' => 'fas fa-paint-brush',
                'deportivo' => 'fas fa-futbol',
                'civil' => 'fas fa-flag',
                'asesoria' => 'fas fa-chalkboard-teacher'
            ];
            foreach ($clubs as $club): 
                $icon_class = $club_icons[$club['club_type']] ?? 'fas fa-star';
            ?>
                <div class="club-card registered-club-card" 
                     data-club-name="<?= htmlspecialchars($club['club_name']) ?>" 
                     data-club-type="<?= htmlspecialchars($club['club_type']) ?>">
                    <div class="card-icon"><i class="<?= $icon_class ?>"></i></div>
                    <div class="card-content">
                        <h3><?= htmlspecialchars($club['club_name']) ?></h3>
                        <p><strong>Tipo:</strong> <?= htmlspecialchars(ucfirst($club['club_type'])) ?></p>
                        <p><small>Inscrito el: <?= htmlspecialchars(date("d/m/Y", strtotime($club['registration_date']))) ?></small></p>
                    </div>
                    <button class="btn-unsubscribe" title="Darse de Baja">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Confirmación para Darse de Baja -->
<div id="unsubscribeModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Baja</h3>
        </div>
        <div class="modal-body">
            <p>¿Estás seguro de que quieres darte de baja del club de <strong></strong>?</p>
            <p class="modal-sub-message">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel">Cancelar</button>
            <button type="button" id="confirmUnsubscribe" class="btn-confirm-danger">Darse de Baja</button>
        </div>
    </div>
</div>

<script src="js/unsubscribe.js?v=<?php echo time(); ?>"></script>
</body>
</html>
