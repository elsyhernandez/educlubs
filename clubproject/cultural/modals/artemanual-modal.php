<!-- Arte Manual -->
<div class="modal" id="modal-artemanual">
  <div class="modal-content danza">
    <div class="modal-header">
      <h2>Club de Arte Manual</h2>
      <span class="close-btn" onclick="cerrarModal('artemanual')">&times;</span>
    </div>
    <div class="modal-body">
      <div class="modal-left slide-in-left">
        <p><strong>Profesor responsable:</strong> Mtra. Patricia Torres</p>
        <p><strong>Descripción:</strong> Aprende técnicas de manualidades, decoración y arte funcional.</p>
        <a class="btn-inscribirme" href="cultural/artemanual.php">Ver más</a>
      </div>
      <div class="modal-right slide-in-right">
        <img src="assets/images/pin.jpg" alt="Arte Manual">
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
