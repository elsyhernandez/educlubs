<!-- Teatro -->
<div class="modal" id="modal-teatro">
  <div class="modal-content danza">
    <div class="modal-header">
      <h2>Club de Teatro</h2>
      <span class="close-btn" onclick="cerrarModal('teatro')">&times;</span>
    </div>
    <div class="modal-body">
      <div class="modal-left slide-in-left">
        <p><strong>Profesor responsable:</strong> Mtro. Jorge Ramírez</p>
        <p><strong>Descripción:</strong> Participa en obras teatrales, mejora tu expresión corporal y trabajo en equipo.</p>
        <a class="btn-inscribirme" href="clubs/cultural/teatro.php">Ver más</a>
      </div>
      <div class="modal-right slide-in-right">
        <img src="clubs/assets/images/danza5.jpg" alt="Teatro">
      </div>
    </div>
  </div>
</div>
<style>
.modal {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.6);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(5px);
}

.modal-content.danza {
  background: white;
  border-radius: 12px;
  max-width: 800px;
  width: 90%;
  box-shadow: 0 8px 30px rgba(0,0,0,0.3);
  overflow: hidden;
  animation: fadeUp 0.5s ease;
}

.modal-header {
  background: #952f57;
  color: white;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h2 {
  margin: 0;
  font-size: 1.5rem;
}

.close-btn {
  font-size: 24px;
  cursor: pointer;
}

.modal-body {
  display: flex;
  flex-direction: row;
  padding: 20px;
  gap: 20px;
}

.modal-left, .modal-right {
  flex: 1;
}

.modal-left p {
  font-size: 1rem;
  margin-bottom: 10px;
  color: #333;
}

.modal-right img {
  width: 100%;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.btn-vermas, .btn-inscribirme {
  display: inline-block;
  margin-top: 15px;
  background: linear-gradient(90deg, #4D0011, #62152d);
  color: white;
  padding: 10px 20px;
  border-radius: 20px;
  text-decoration: none;
  font-weight: 600;
  transition: background 0.3s ease;
}

.btn-vermas:hover, .btn-inscribirme:hover {
  background: linear-gradient(90deg, #62152d, #4D0011);
}

@keyframes slideInLeft {
  from { transform: translateX(-50px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes slideInRight {
  from { transform: translateX(50px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

.slide-in-left {
  animation: slideInLeft 0.8s ease forwards;
}

.slide-in-right {
  animation: slideInRight 0.8s ease forwards;
}

@media (max-width: 768px) {
  .modal-body {
    flex-direction: column;
  }
}
@media (max-width: 480px) {
  .modal-content.danza {
    width: 95%;
    border-radius: 8px;
    padding: 10px;
  }

  .modal-header {
    flex-direction: column;
    align-items: flex-start;
    padding: 10px;
  }

  .modal-header h2 {
    font-size: 1.2rem;
    margin-bottom: 5px;
  }

  .close-btn {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
  }

  .modal-body {
    flex-direction: column;
    padding: 10px;
    gap: 10px;
  }

  .modal-left {
    align-items: flex-start;
    text-align: left;
  }

  .modal-left p {
    font-size: 0.95rem;
    margin-bottom: 8px;
  }

  .btn-inscribirme {
    font-size: 0.9rem;
    padding: 8px 16px;
    border-radius: 16px;
  }

  .modal-right img {
    border-radius: 8px;
    box-shadow: none;
  }
}

</style>
<script>
function abrirModal(id) {
  document.getElementById('modal-' + id).style.display = 'flex';
}

function cerrarModal(id) {
  document.getElementById('modal-' + id).style.display = 'none';
}
</script>
