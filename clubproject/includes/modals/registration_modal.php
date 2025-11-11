<!-- Contenedor del Nuevo Modal -->
<div id="customModal" class="modal-overlay">
    <div class="modal-container" id="modalContainer">
        <!-- El contenido se generará dinámicamente con JavaScript -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '<?php echo BASE_URL; ?>';
    // ==========================================================
    // --- Lógica del Nuevo Modal de Registro ---
    // ==========================================================
    const modal = document.getElementById('customModal');
    const modalContainer = document.getElementById('modalContainer');
    const btnRegistrar = document.getElementById('btn-registrar');

    // Función para renderizar el contenido del modal
    function renderModalContent(state, data = {}) {
        let content = '';
        switch (state) {
            case 'loading':
                content = `
                    <div class="modal-body">
                        <div class="spinner"></div>
                        <p class="modal-message">${data.message || 'Cargando...'}</p>
                    </div>`;
                break;
            case 'form':
                content = `
                    <div class="modal-header">
                        <h3 class="modal-title">Completar Registro</h3>
                        <span class="modal-close" onclick="closeModal()">&times;</span>
                    </div>
                    <form id="registrationForm">
                        <div class="modal-body">
                            <p>Confirma y completa tus datos para unirte al club de <strong>${clubName}</strong>.</p>
                            <div class="form-group">
                                <label for="modal_phone">Teléfono (10 dígitos)</label>
                                <input type="tel" id="modal_phone" name="phone" pattern="\\d{10}" required value="${data.phone || ''}">
                            </div>
                            <div class="form-group">
                                <label for="modal_semester">Semestre</label>
                                <select id="modal_semester" name="semester" required>
                                    <option value="">Selecciona tu semestre</option>
                                    ${["1er Semestre", "2do Semestre", "3er Semestre", "4to Semestre", "5to Semestre", "6to Semestre"].map(s => `<option value="${s}" ${data.semester === s ? 'selected' : ''}>${s}</option>`).join('')}
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="modal_blood_type">Tipo de Sangre</label>
                                <input type="text" id="modal_blood_type" name="blood_type" required value="${data.blood_type || ''}">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" onclick="closeModal()">Cancelar</button>
                            <button type="submit" class="btn-confirm">Confirmar Registro</button>
                        </div>
                    </form>`;
                break;
            case 'success':
                content = `
                    <div class="modal-header">
                        <h3 class="modal-title">¡Éxito!</h3>
                        <span class="modal-close" onclick="closeModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="modal-icon icon-success"></div>
                        <p class="modal-message">${data.message}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-confirm" onclick="window.top.location.href=BASE_URL + '/student_dashboard.php'">Ir a mi Dashboard</button>
                    </div>`;
                break;
            case 'error':
                content = `
                    <div class="modal-header">
                        <h3 class="modal-title">Error</h3>
                        <span class="modal-close" onclick="closeModal()">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="modal-icon icon-error"></div>
                        <p class="modal-message">${data.message}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-cancel" onclick="closeModal()">Cerrar</button>
                    </div>`;
                break;
        }
        modalContainer.innerHTML = content;
        if (state === 'form') {
            document.getElementById('registrationForm').addEventListener('submit', handleRegistration);
        }
    }

    // Función para abrir el modal y cargar datos del usuario
    async function openModal() {
        modal.style.display = 'flex';
        renderModalContent('loading', { message: 'Cargando tus datos...' });

        try {
            const response = await fetch(`${BASE_URL}/actions/register_club.php?action=get_user_data`);
            if (!response.ok) throw new Error('Error de red.');
            
            const result = await response.json();
            if (result.success) {
                renderModalContent('form', result.data);
            } else {
                renderModalContent('error', { message: result.message || 'No se pudieron cargar tus datos.' });
            }
        } catch (error) {
            renderModalContent('error', { message: 'No se pudo conectar con el servidor.' });
        }
    }

    // Función para cerrar el modal
    function closeModal() {
        modal.style.display = 'none';
    }

    // Manejar el proceso de registro
    async function handleRegistration(event) {
        event.preventDefault();
        renderModalContent('loading', { message: 'Procesando tu registro...' });

        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        formData.append('action', 'register');
        formData.append('type', clubType);
        formData.append('club', clubName);

        try {
            const response = await fetch(`${BASE_URL}/actions/register_club.php`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) throw new Error('Error en la respuesta del servidor.');

            const result = await response.json();
            if (result.success) {
                renderModalContent('success', { message: result.message });
            } else {
                renderModalContent('error', { message: result.message || 'Ocurrió un error desconocido.' });
            }
        } catch (error) {
            renderModalContent('error', { message: 'No se pudo conectar con el servidor. Inténtalo de nuevo.' });
        }
    }

    // Listeners
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>
