document.addEventListener('DOMContentLoaded', function() {
    const notificationIcon = document.getElementById('notification-icon');
    const notificationPanel = document.getElementById('notification-panel');
    const notificationCount = document.getElementById('notification-count');
    const notificationBody = document.getElementById('notification-body');

    let isPanelOpen = false;

    function fetchNotifications() {
        fetch('../actions/fetch_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateNotificationUI(data.notifications);
                }
            })
            .catch(error => console.error('Error al cargar las notificaciones:', error));
    }

    function updateNotificationUI(notifications) {
        const unreadCount = notifications.length;
        
        if (unreadCount > 0) {
            notificationCount.textContent = unreadCount;
            notificationCount.style.display = 'block';
        } else {
            notificationCount.style.display = 'none';
        }

        notificationBody.innerHTML = ''; // Limpiar notificaciones anteriores
        if (notifications.length > 0) {
            notifications.forEach(notification => {
                const item = document.createElement('div');
                item.className = 'notification-item';
                item.innerHTML = `<p><strong>${notification.student_name}</strong> se ha inscrito en tu club <strong>${notification.club_name}</strong>.</p>`;
                notificationBody.appendChild(item);
            });
        } else {
            notificationBody.innerHTML = '<p>No hay notificaciones nuevas.</p>';
        }
    }

    function markNotificationsAsRead() {
        fetch('../actions/mark_notification_read.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                notificationCount.style.display = 'none';
                notificationCount.textContent = '0';
            }
        })
        .catch(error => console.error('Error al marcar las notificaciones como leídas:', error));
    }

    notificationIcon.addEventListener('click', () => {
        isPanelOpen = !isPanelOpen;
        if (isPanelOpen) {
            notificationPanel.style.display = 'block';
            fetchNotifications(); // Actualizar al abrir
            if (parseInt(notificationCount.textContent) > 0) {
                markNotificationsAsRead();
            }
        } else {
            notificationPanel.style.display = 'none';
        }
    });

    // Cerrar el panel si se hace clic fuera de él
    document.addEventListener('click', function(event) {
        if (!notificationIcon.contains(event.target) && !notificationPanel.contains(event.target)) {
            notificationPanel.style.display = 'none';
            isPanelOpen = false;
        }
    });

    // Cargar notificaciones al cargar la página y luego cada 30 segundos
    fetchNotifications();
    setInterval(fetchNotifications, 30000);
});
