function showNotification(message, type = 'success') {
    // Remove any existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    const icon = document.createElement('span');
    icon.className = 'icon';
    icon.textContent = type === 'success' ? '✔️' : '⚠️';

    const messageSpan = document.createElement('span');
    messageSpan.className = 'message';
    messageSpan.textContent = message;

    const closeBtn = document.createElement('button');
    closeBtn.className = 'close-btn';
    closeBtn.innerHTML = '&times;';
    closeBtn.onclick = () => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    };

    notification.appendChild(icon);
    notification.appendChild(messageSpan);
    notification.appendChild(closeBtn);

    document.body.appendChild(notification);

    // Show the notification with a slide-in effect
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    // Automatically hide after 5 seconds
    setTimeout(() => {
        if (document.body.contains(notification)) {
            closeBtn.onclick();
        }
    }, 5000);
}
