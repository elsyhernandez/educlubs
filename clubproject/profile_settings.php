<?php require_once 'includes/student_header.php'; ?>
<title>Configuración de Perfil</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/css/profile-settings.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/notification.css">

    <div class="settings-container">
        <h2>Configuración de Perfil</h2>
        
        <form id="profile-form" action="<?= BASE_URL ?>/actions/update_profile.php" method="POST" enctype="multipart/form-data">
            <div class="profile-picture-section">
                <div class="avatar-preview" id="avatar-preview">
                    <?php if (!empty($user['profile_picture'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Foto de perfil">
                    <?php else: ?>
                        <span><?= strtoupper(substr($user['nombres'], 0, 1)) ?></span>
                    <?php endif; ?>
                </div>
                <label for="profile_picture" class="upload-label">
                    <i class="fas fa-camera"></i> Cambiar foto
                </label>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="nombres">Nombres</label>
                    <input type="text" id="nombres" name="nombres" value="<?= htmlspecialchars($user['nombres'] ?? '') ?>" required>
                </div>
<div class="form-group">
                        <label for="semestre">Semestre</label>
                        <select id="semestre" name="semestre" required>
                            <option value="">Selecciona tu semestre</option>
                            <?php
                            $semestres = ["1er Semestre", "2do Semestre", "3er Semestre", "4to Semestre", "5to Semestre", "6to Semestre"];
                            foreach ($semestres as $semestre) {
                                // Asegurarse de que $user['semestre'] exista para evitar errores
                                $user_semestre = isset($user['semestre']) ? $user['semestre'] : '';
                                $selected = ($user_semestre == $semestre) ? 'selected' : '';
                                echo "<option value=\"$semestre\" $selected>" . htmlspecialchars($semestre) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="carrera">Carrera</label>
                        <select id="carrera" name="carrera" required>
                            <option value="">Selecciona tu carrera</option>
                            <?php
                            $carreras = ["Ing. en Sistemas Computacionales", "Ing. Industrial", "Ing. en Mecatrónica", "Ing. en Gestión Empresarial", "Lic. en Administración"];
                            foreach ($carreras as $carrera) {
                                $user_carrera = isset($user['carrera']) ? $user['carrera'] : '';
                                $selected = ($user_carrera == $carrera) ? 'selected' : '';
                                echo "<option value=\"$carrera\" $selected>" . htmlspecialchars($carrera) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="turno">Turno</label>
                        <select id="turno" name="turno" required>
                            <option value="">Selecciona tu turno</option>
                            <?php
                            $turnos = ["Matutino", "Vespertino"];
                            foreach ($turnos as $turno) {
                                $user_turno = isset($user['turno']) ? $user['turno'] : '';
                                $selected = ($user_turno == $turno) ? 'selected' : '';
                                echo "<option value=\"$turno\" $selected>" . htmlspecialchars($turno) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                <div class="form-group">
                    <label for="paterno">Apellido Paterno</label>
                    <input type="text" id="paterno" name="paterno" value="<?= htmlspecialchars($user['paterno'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="materno">Apellido Materno</label>
                    <input type="text" id="materno" name="materno" value="<?= htmlspecialchars($user['materno'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
                </div>
                <div class="form-group full-width">
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>

    <script src="<?= BASE_URL ?>/js/notification.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profile-form');
            const saveButtonContainer = form.querySelector('.btn-save').parentElement;
            const inputs = form.querySelectorAll('input, select');

            // Hide save button initially
            saveButtonContainer.style.display = 'none';

            // Show button on any input change
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    saveButtonContainer.style.display = 'block';
                });
            });

            document.getElementById('profile_picture').onchange = function (evt) {
                const [file] = this.files;
                if (file) {
                    const previewContainer = document.getElementById('avatar-preview');
                    previewContainer.innerHTML = ''; // Clear existing content
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    previewContainer.appendChild(img);
                    saveButtonContainer.style.display = 'block'; // Also show on picture change
                }
            };

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        saveButtonContainer.style.display = 'none'; // Hide button on successful save
                        if (data.profile_picture_url) {
                            const previewContainer = document.getElementById('avatar-preview');
                            const existingImg = previewContainer.querySelector('img');
                            if (existingImg) {
                                existingImg.src = data.profile_picture_url;
                            }
                        }
                    } else {
                        showNotification(data.message, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Ocurrió un error de red. Por favor, inténtalo de nuevo.', 'error');
                });
            });

        });
    </script>

</body>
</html>
