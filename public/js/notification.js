function formatTimeAgo(timestamp) {
    const currentTime = Math.floor(Date.now() / 1000);
    const timeDifference = currentTime - timestamp;

    if (timeDifference < 60) {
        return timeDifference + ' giây trước';
    } else if (timeDifference < 3600) {
        const minutes = Math.floor(timeDifference / 60);
        return minutes + ' phút trước';
    } else if (timeDifference < 86400) {
        const hours = Math.floor(timeDifference / 3600);
        return hours + ' giờ trước';
    } else {
        const days = Math.floor(timeDifference / 86400);
        return days + ' ngày trước';
    }
}

function deleteNotification(notificationId) {
    const notificationElement = document.getElementById(notificationId);
    if (notificationElement) {
        notificationElement.remove();
    }
}