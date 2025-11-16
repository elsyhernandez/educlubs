<!-- Contenedor del Nuevo Modal -->
<div id="customModal" class="modal-overlay">
    <div class="modal-container" id="modalContainer">
        <!-- El contenido se generará dinámicamente con JavaScript -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '<?php echo BASE_URL; ?>';
    const userCarrera = '<?php echo htmlspecialchars($_SESSION['user']['carrera'] ?? ''); ?>';
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
                        <h3 class="modal-title">Confirmar Registro</h3>
                        <span class="modal-close">&times;</span>
                    </div>
                    <form id="registrationForm">
                        <div class="modal-body">
                            <p>¿Estás seguro de que quieres unirte al club de <strong>${clubName}</strong>?</p>
                            <p class="modal-sub-message">Al confirmar, quedarás inscrito oficialmente.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel">Cancelar</button>
                            <button type="submit" class="btn-confirm">Confirmar</button>
                        </div>
                    </form>`;
                break;
            case 'asesoria_form': // Nuevo caso para el formulario de asesorías
                content = `
                    <div class="modal-header">
                        <h3 class="modal-title">Inscripción a Asesoría</h3>
                        <span class="modal-close">&times;</span>
                    </div>
                    <form id="registrationForm">
                        <div class="modal-body">
                            <p>Estás a punto de inscribirte a la asesoría de <strong>${clubName}</strong>.</p>
                            <div class="form-group">
                                <label for="carrera">Carrera:</label>
                                <input type="text" id="carrera" name="carrera" value="${userCarrera}" readonly required>
                            </div>
                            <div class="form-group">
                                <label for="maestro">Maestro que imparte:</label>
                                <input type="text" id="maestro" name="maestro" placeholder="Nombre del maestro" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel">Cancelar</button>
                            <button type="submit" class="btn-confirm">Confirmar</button>
                        </div>
                    </form>`;
                break;
            case 'success':
                content = `
                    <div class="modal-header">
                        <h3 class="modal-title">¡Éxito!</h3>
                        <span class="modal-close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="modal-icon icon-success"></div>
                        <p class="modal-message">${data.message}</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn-confirm" onclick="window.location.href='../club.php?type=' + clubType">Volver a Clubes</button>
                    </div>`;
                break;
            case 'error':
                const isProfileIncomplete = data.message.includes('Tu perfil está incompleto');
                const buttonHtml = isProfileIncomplete
                    ? `<button class="btn-confirm" onclick="window.location.href='../profile_settings.php'">Ir a mi Perfil</button>`
                    : `<button class="btn-cancel">Cerrar</button>`;

                content = `
                    <div class="modal-header">
                        <h3 class="modal-title">Error</h3>
                        <span class="modal-close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="modal-icon icon-error"></div>
                        <p class="modal-message">${data.message}</p>
                    </div>
                    <div class="modal-footer">
                        ${buttonHtml}
                    </div>`;
                break;
        }
        modalContainer.innerHTML = content;

        // Asignar eventos a los botones de cerrar
        const closeButton = modalContainer.querySelector('.modal-close');
        if (closeButton) {
            closeButton.addEventListener('click', closeModal);
        }
        const cancelButton = modalContainer.querySelector('.btn-cancel');
        if (cancelButton) {
            cancelButton.addEventListener('click', closeModal);
        }

        if (state === 'form' || state === 'asesoria_form') {
            document.getElementById('registrationForm').addEventListener('submit', handleRegistration);
        }
    }

    // Función para abrir el modal
    function openModal() {
        modal.style.display = 'flex';
        if (typeof clubType !== 'undefined' && clubType === 'asesoria') {
            renderModalContent('asesoria_form');
        } else {
            renderModalContent('form');
        }
    }

    // Función para cerrar el modal
    function closeModal() {
        modal.style.display = 'none';
    }

    // Manejar el proceso de registro
    async function handleRegistration(event) {
        event.preventDefault();

        const form = document.getElementById('registrationForm');
        const formData = new FormData(form);
        formData.append('action', 'register');
        formData.append('type', clubType);
        formData.append('club', clubName);

        // Si es una asesoría, añadir los campos extra
        if (clubType === 'asesoria') {
            const carrera = document.getElementById('carrera').value;
            const maestro = document.getElementById('maestro').value;
            if (!carrera || !maestro) {
                renderModalContent('error', { message: 'Por favor, completa todos los campos.' });
                return;
            }
            formData.append('carrera', carrera);
            formData.append('maestro', maestro);
        }

        renderModalContent('loading', { message: 'Procesando tu registro...' });

        try {
            const response = await fetch('../actions/register_club.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json(); // Parse the JSON response body

            if (response.ok && result.success) {
                renderModalContent('success', { message: result.message });
            } else {
                // Handle both network errors (response not ok) and application errors (result.success is false)
                renderModalContent('error', { message: result.message || 'Ocurrió un error desconocido.' });
            }
        } catch (error) {
            // This will now catch JSON parsing errors or total network failures
            renderModalContent('error', { message: 'No se pudo conectar con el servidor. Inténtalo de nuevo.' });
        }
    }

    // Listeners
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Asignar el evento al botón de registro principal de la página
    if (btnRegistrar) {
        btnRegistrar.addEventListener('click', openModal);
    }
});
</script>
