@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notifications</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                            <i class="bi bi-check-all me-1"></i>Mark All as Read
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="loadNotifications()">
                            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="notificationsContainer">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    padding: 16px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #f0f7ff;
    border-left: 3px solid var(--navy-600);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-content {
    display: flex;
    gap: 16px;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}

.notification-icon.success {
    background-color: #d4edda;
    color: #155724;
}

.notification-icon.warning {
    background-color: #fff3cd;
    color: #856404;
}

.notification-icon.danger {
    background-color: #f8d7da;
    color: #721c24;
}

.notification-icon.info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.notification-text {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 4px;
    color: var(--navy-900);
}

.notification-message {
    font-size: 13px;
    color: #666;
    margin-bottom: 6px;
}

.notification-meta {
    display: flex;
    gap: 16px;
    font-size: 12px;
    color: #999;
}

.notification-module {
    background-color: #f8f9fa;
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 500;
}

.notification-actions {
    display: flex;
    gap: 8px;
}

.delete-btn {
    color: #dc3545;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.delete-btn:hover {
    background-color: #f8d7da;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #999;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});

function loadNotifications() {
    const container = document.getElementById('notificationsContainer');
    
    fetch('{{ route("notifications.get") }}')
        .then(response => response.json())
        .then(data => {
            const notifications = data.notifications || [];
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-bell-slash"></i>
                        <h5>No Notifications</h5>
                        <p>You don't have any notifications at the moment.</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifications.forEach(notification => {
                const iconClass = getNotificationTypeClass(notification.type);
                const timeAgo = getTimeAgo(notification.created_at);
                html += `
                    <div class="notification-item ${!notification.is_read ? 'unread' : ''}" onclick="markAsRead(${notification.id})">
                        <div class="notification-content">
                            <div class="notification-icon ${iconClass}">
                                <i class="bi bi-${getNotificationIcon(notification.type)}"></i>
                            </div>
                            <div class="notification-text">
                                <div class="notification-title">${notification.title}</div>
                                <div class="notification-message">${notification.message}</div>
                                <div class="notification-meta">
                                    <span><i class="bi bi-clock me-1"></i>${timeAgo}</span>
                                    <span class="notification-module"><i class="bi bi-tag me-1"></i>${notification.module}</span>
                                    ${notification.sent_email ? '<span><i class="bi bi-envelope me-1"></i>Email sent</span>' : ''}
                                    ${notification.sent_whatsapp ? '<span><i class="bi bi-whatsapp me-1"></i>WhatsApp sent</span>' : ''}
                                </div>
                            </div>
                            <div class="notification-actions">
                                <button class="delete-btn" onclick="event.stopPropagation(); deleteNotification(${notification.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <i class="bi bi-exclamation-triangle"></i>
                    <h5>Error Loading Notifications</h5>
                    <p>There was an error loading your notifications. Please try again.</p>
                    <button class="btn btn-primary mt-3" onclick="loadNotifications()">Retry</button>
                </div>
            `;
        });
}

function getNotificationTypeClass(type) {
    const classes = {
        'save': 'success',
        'update': 'info',
        'delete': 'danger',
        'warning': 'warning',
        'info': 'info'
    };
    return classes[type] || 'info';
}

function getNotificationIcon(type) {
    const icons = {
        'save': 'check-circle',
        'update': 'pencil',
        'delete': 'trash',
        'warning': 'exclamation-triangle',
        'info': 'info-circle'
    };
    return icons[type] || 'bell';
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
  const seconds = Math.floor((now - date) / 1000);
    
    if (seconds < 60) return 'Just now';
    if (seconds < 3600) return Math.floor(seconds / 60) + ' minutes ago';
    if (seconds < 86400) return Math.floor(seconds / 3600) + ' hours ago';
    if (seconds < 604800) return Math.floor(seconds / 86400) + ' days ago';
    
    return date.toLocaleDateString();
}

function markAsRead(id) {
    fetch('{{ route("notifications.mark-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ notification_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

function markAllAsRead() {
    fetch('{{ route("notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        }
    })
    .catch(error => {
        console.error('Error marking all notifications as read:', error);
    });
}

function deleteNotification(id) {
    if (!confirm('Are you sure you want to delete this notification?')) {
        return;
    }
    
    fetch('{{ route("notifications.delete") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ notification_id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadNotifications();
        } else {
            alert('Error deleting notification');
        }
    })
    .catch(error => {
        console.error('Error deleting notification:', error);
        alert('Error deleting notification');
    });
}
</script>
@endsection