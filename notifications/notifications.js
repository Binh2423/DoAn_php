
function fetchNotifications() {
    fetch('get_notifications.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const notifications = data.notifications;
                const notificationContainer = document.getElementById('notification-list');
                
                notifications.forEach(notification => {
                    const notificationItem = document.createElement('div');
                    notificationItem.classList.add('notification-item');
                    notificationItem.innerHTML = `
                        <p>${notification.type}: ${notification.reference_id}</p>
                        <button onclick="markAsRead(${notification.id})">Mark as Read</button>
                    `;
                    notificationContainer.appendChild(notificationItem);
                });
            }
        })
        .catch(error => console.log('Error fetching notifications:', error));
}

function markAsRead(notificationId) {
    fetch('mark_as_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `notification_id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);  // Optionally, show a success message
            fetchNotifications();  // Refresh notifications
        }
    })
    .catch(error => console.log('Error marking notification as read:', error));
}

// Call fetchNotifications to get the notifications on page load
window.onload = fetchNotifications;
