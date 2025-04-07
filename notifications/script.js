// Function to load notifications via AJAX
function loadNotifications() {
    fetch('get_notifications.php')  // Path to your PHP script to fetch notifications
        .then(response => response.json())
        .then(data => {
            const notificationsContent = document.getElementById('notificationsContent');
            if (data.length === 0) {
                notificationsContent.innerHTML = "<p>Không có thông báo mới.</p>";
            } else {
                notificationsContent.innerHTML = data.map(notification => `
                    <div class="notification-item">
                        <div class="notification-text">${notification.message}</div>
                        <div class="notification-date">${notification.created_at}</div>
                    </div>
                `).join('');
            }
        })
        .catch(error => {
            console.error("Lỗi khi tải thông báo:", error);
        });
}

// Open the notifications modal and load notifications
document.getElementById('notificationsModal').addEventListener('show.bs.modal', function () {
    loadNotifications();
});
