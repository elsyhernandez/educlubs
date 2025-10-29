function showNotification(message, type = 'success') {
    // Remove any existing notification
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;

    // Create icon element using Font Awesome
    const icon = document.createElement('i');
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-times-circle',
        info: 'fas fa-info-circle',
        warning: 'fas fa-exclamation-triangle'
    };
    icon.className = `icon ${icons[type] || icons.info}`;

    const messageSpan = document.createElement('span');
    messageSpan.className = 'message';
    messageSpan.innerHTML = message; // Use innerHTML to allow for simple HTML like lists

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

    // Automatically hide after 4.5 seconds to match CSS animation
    setTimeout(() => {
        if (document.body.contains(notification)) {
            closeBtn.onclick();
        }
    }, 4500);
}
