<!-- Topbar extracted from original HTML -->
<div id="topbar">
  <button class="btn btn-sm d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
    <i class="bi bi-list fs-4"></i>
  </button>
  <div class="search">
    <i class="bi bi-search"></i>
    <input type="text" id="globalSearch" placeholder="Search job #, vehicle, driver, customer...">
    <div id="searchResults" class="search-results"></div>
  </div>
  <div class="ms-auto d-flex align-items-center gap-2">
    <!-- Notifications -->
    <div class="dropdown">
      <button class="topbar-icon-btn position-relative" data-bs-toggle="dropdown" id="notificationDropdown">
        <i class="bi bi-bell"></i>
        <span class="dot" id="notificationDot"></span>
      </button>
      <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
        <div class="dropdown-header d-flex justify-content-between align-items-center">
          <span>Notifications</span>
          <button class="btn btn-sm btn-link text-decoration-none" onclick="markAllAsRead()">Mark all as read</button>
        </div>
        <div class="notification-list" id="notificationList">
          <div class="text-center py-3 text-muted">
            <i class="bi bi-bell-slash fs-4"></i>
            <p class="mb-0 mt-2">No new notifications</p>
          </div>
        </div>
        <div class="dropdown-footer">
          <a href="{{ route('notifications.index') }}" class="text-decoration-none">View all notifications</a>
        </div>
      </div>
    </div>
    
    <!-- Messages -->
    <button class="topbar-icon-btn position-relative">
      <i class="bi bi-envelope"></i>
      <span class="dot"></span>
    </button>
    
    <!-- User Menu -->
    <div class="dropdown">
      <div class="user-chip" data-bs-toggle="dropdown" style="cursor: pointer;">
        <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AU', 0, 2)) }}</div>
        <div>
          <div style="font-size:12.5px;font-weight:700;color:var(--navy-900);">{{ auth()->user()->name ?? 'Admin User' }}</div>
          <div style="font-size:10.5px;color:var(--muted);">{{ ucfirst(auth()->user()->role ?? 'Administrator') }}</div>
        </div>
        <i class="bi bi-chevron-down" style="font-size:11px;color:var(--muted);"></i>
      </div>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ url('/profile') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
        @if(auth()->user()->isAdmin())
        <li><a class="dropdown-item" href="{{ url('/settings') }}"><i class="bi bi-gear me-2"></i>Settings</a></li>
        @endif
        <li><hr class="dropdown-divider"></li>
        <li>
          <form method="POST" action="{{ route('logout') }}" id="logoutForm">
            @csrf
          </form>
          <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</div>

<style>
.notification-dropdown {
  width: 320px;
  max-height: 400px;
  overflow-y: auto;
}

.notification-list {
  max-height: 300px;
  overflow-y: auto;
}

.notification-item {
  padding: 12px 16px;
  border-bottom: 1px solid #eee;
  cursor: pointer;
  transition: background-color 0.2s;
}

.notification-item:hover {
  background-color: #f8f9fa;
}

.notification-item.unread {
  background-color: #f0f7ff;
}

.notification-item:last-child {
  border-bottom: none;
}

.notification-content {
  display: flex;
  gap: 12px;
}

.notification-icon {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
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
  font-size: 13px;
  margin-bottom: 2px;
}

.notification-message {
  font-size: 12px;
  color: #666;
  margin-bottom: 4px;
}

.notification-time {
  font-size: 11px;
  color: #999;
}

.dropdown-footer {
  padding: 10px 16px;
  border-top: 1px solid #eee;
  text-align: center;
}

.dropdown-footer a {
  font-size: 12px;
  color: var(--navy-800);
}
</style>

<script>
// Load notifications on page load
document.addEventListener('DOMContentLoaded', function() {
  loadNotifications();
  
  // Refresh notifications every 30 seconds
  setInterval(loadNotifications, 30000);
});

function loadNotifications() {
  const notificationList = document.getElementById('notificationList');
  const notificationDot = document.getElementById('notificationDot');
  
  if (!notificationList) return;
  
  fetch('{{ route("notifications.get") }}')
    .then(response => response.json())
    .then(data => {
      const notifications = data.notifications || [];
      const unreadCount = data.unread_count || 0;
      
      if (unreadCount > 0) {
        notificationDot.style.display = 'block';
        notificationDot.textContent = unreadCount > 9 ? '9+' : unreadCount;
      } else {
        notificationDot.style.display = 'none';
      }
      
      if (notifications.length === 0) {
        notificationList.innerHTML = `
          <div class="text-center py-3 text-muted">
            <i class="bi bi-bell-slash fs-4"></i>
            <p class="mb-0 mt-2">No new notifications</p>
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
                <div class="notification-time">${timeAgo}</div>
              </div>
            </div>
          </div>
        `;
      });
      
      notificationList.innerHTML = html;
    })
    .catch(error => {
      console.error('Error loading notifications:', error);
      notificationList.innerHTML = `
        <div class="text-center py-3 text-muted">
          <i class="bi bi-exclamation-triangle fs-4"></i>
          <p class="mb-0 mt-2">Error loading notifications</p>
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
</script>