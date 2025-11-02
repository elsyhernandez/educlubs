const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');
const container = document.querySelector('.container');

signUpButton.addEventListener('click', () => {
    container.classList.add("right-panel-active");
});

signInButton.addEventListener('click', () => {
    container.classList.remove("right-panel-active");
});

document.addEventListener('DOMContentLoaded', function() {
    // Modal para Olvidaste tu ID
    const modal = document.getElementById('forgotIdModal');
    const forgotIdLink = document.getElementById('forgotIdLink');
    const closeButton = document.querySelector('#forgotIdModal .close-button');
    const findIdForm = document.getElementById('findIdForm');
    const idResult = document.getElementById('id-result');
    const placeDataBtn = document.getElementById('place-data-btn');
    let foundUserId = '';
    let foundEmail = '';

    if (forgotIdLink) {
        forgotIdLink.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'block';
        });
    }

    if (closeButton) {
        closeButton.addEventListener('click', function() {
            modal.style.display = 'none';
            idResult.innerHTML = '';
            placeDataBtn.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            idResult.innerHTML = '';
            placeDataBtn.style.display = 'none';
        }
    });

    if (findIdForm) {
        findIdForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            idResult.textContent = 'Buscando...';

            fetch('auth.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    idResult.innerHTML = `Tu ID de usuario es: <strong>${data.user_id}</strong>`;
                    foundUserId = data.user_id;
                    foundEmail = data.email;
                    placeDataBtn.style.display = 'inline-block';
                } else {
                    idResult.textContent = data.error || 'Ocurrió un error.';
                    placeDataBtn.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                idResult.textContent = 'Error de conexión.';
                placeDataBtn.style.display = 'none';
            });
        });
    }

    if (placeDataBtn) {
        placeDataBtn.addEventListener('click', function() {
            document.querySelector('.sign-in-container input[name="user_id"]').value = foundUserId;
            document.querySelector('.sign-in-container input[name="email"]').value = foundEmail;
            modal.style.display = 'none';
            idResult.innerHTML = '';
            placeDataBtn.style.display = 'none';
        });
    }
});
