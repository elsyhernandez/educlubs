document.addEventListener('DOMContentLoaded', () => {
    const modalOverlay = document.getElementById('unsubscribeModal');
    if (!modalOverlay) return;

    const modalContainer = modalOverlay.querySelector('.modal-container');
    const cancelModalButton = modalOverlay.querySelector('.btn-cancel');
    const confirmButton = modalOverlay.querySelector('#confirmUnsubscribe');
    const clubCards = document.querySelectorAll('.registered-club-card');

    let activeClub = null;

    // --- Modal Management ---
    const openModal = (clubCard) => {
        activeClub = clubCard;
        const clubName = clubCard.dataset.clubName;
        modalOverlay.querySelector('.modal-body strong').textContent = clubName;
        modalOverlay.classList.add('active');
    };

    const closeModal = () => {
        modalOverlay.classList.remove('active');
        activeClub = null;
    };

    // --- Event Listeners ---
    clubCards.forEach(card => {
        const unsubscribeButton = card.querySelector('.btn-unsubscribe');
        if (unsubscribeButton) {
            unsubscribeButton.addEventListener('click', () => openModal(card));
        }
    });

    if (cancelModalButton) {
        cancelModalButton.addEventListener('click', closeModal);
    }
    
    modalOverlay.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            closeModal();
        }
    });

    confirmButton.addEventListener('click', async () => {
        if (!activeClub) return;

        const clubName = activeClub.dataset.clubName;
        const clubType = activeClub.dataset.clubType;
        const formData = new FormData();
        formData.append('club_name', clubName);
        formData.append('club_type', clubType);

        try {
            const response = await fetch('actions/unsubscribe_club.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                // Add a class to trigger the animation
                activeClub.classList.add('removing');

                // Listen for the end of the transition to remove the element
                activeClub.addEventListener('transitionend', () => {
                    activeClub.remove();
                    
                    // Check if there are any cards left and show a message if needed
                    const remainingCards = document.querySelectorAll('.registered-club-card').length;
                    if (remainingCards === 0) {
                        const grid = document.querySelector('.club-grid');
                        const noClubsMessage = document.querySelector('.no-clubs-message');
                        
                        if (grid) {
                            if (noClubsMessage) {
                                noClubsMessage.style.display = 'block';
                            } else {
                                // If the message doesn't exist, create it
                                grid.innerHTML = `
                                    <div class="no-clubs-message" style="text-align: center; padding: 40px; border: 1px dashed #ccc; border-radius: 8px;">
                                        <i class="fas fa-info-circle" style="font-size: 2rem; margin-bottom: 15px; color: #3498db;"></i>
                                        <p>No estás inscrito en ningún club o asesoría.</p>
                                        <a href="student_dashboard.php" class="btn-explore" style="display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px;">Explorar Clubs</a>
                                    </div>
                                `;
                            }
                        }
                    }
                }, { once: true }); // Use 'once' to ensure the event fires only one time
            } else {
                // Error: Show an alert or a more sophisticated notification
                alert(result.message || 'No se pudo dar de baja del club.');
            }
        } catch (error) {
            alert('Error de conexión. Inténtalo de nuevo.');
        } finally {
            closeModal();
        }
    });
});
