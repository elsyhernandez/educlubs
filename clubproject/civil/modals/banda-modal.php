<!-- Banda de Guerra -->
<div class="modal" id="modal-banda">
  <div class="modal-content danza">
    <div class="modal-header">
      <h2>Club de Banda de Guerra</h2>
      <span class="close-btn" onclick="cerrarModal('banda')">&times;</span>
    </div>
    <div class="modal-body">
      <div class="modal-left slide-in-left">
        <p><strong>Profesor responsable:</strong> Profr. Rafael Ortega</p>
        <p><strong>Descripción:</strong> Aprende ejecución de instrumentos marciales, disciplina y participación en actos cívicos.</p>
        <a class="btn-inscribirme" href="civil/banda.php">Ver más</a>
      </div>
      <div class="modal-right slide-in-right">
        <img src="assets/images/banda.jpg" alt="Banda de Guerra">
      </div>
    </div>
  </div>
</div>
<script>
function abrirModal(id) {
  document.getElementById('modal-' + id).style.display = 'flex';
}

function cerrarModal(id) {
  document.getElementById('modal-' + id).style.display = 'none';
}
</script>
