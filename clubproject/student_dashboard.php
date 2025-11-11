<?php require_once 'includes/student_header.php'; ?>
<title>Panel - Alumno</title>

  <div class="main-container">
    <h2>Selecciona un tipo de club</h2>
    <div class="club-grid">
      <a href="club.php?type=cultural" class="club-card">
        <div class="icon-wrapper">
          <i class="fas fa-palette"></i>
        </div>
        <h3>Cultural</h3>
        <p>Explora tu creatividad y talento.</p>
      </a>
      <a href="club.php?type=deportivo" class="club-card">
        <div class="icon-wrapper">
          <i class="fas fa-futbol"></i>
        </div>
        <h3>Deportivo</h3>
        <p>Participa en deportes y mantente activo.</p>
      </a>
      <a href="club.php?type=civil" class="club-card">
        <div class="icon-wrapper">
          <i class="fas fa-flag"></i>
        </div>
        <h3>Civil</h3>
        <p>Fomenta valores cívicos y patriotismo.</p>
      </a>
      <a href="club.php?type=asesoria" class="club-card">
        <div class="icon-wrapper">
          <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <h3>Asesorías</h3>
        <p>Refuerza tus conocimientos académicos.</p>
      </a>
    </div>
  </div>
</body>
</html>
