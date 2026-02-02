<!-- Notification Dropdown Partial -->
<div id="notification-dropdown" class="notification-dropdown" style="display: none;">
    <div class="notification-dropdown-header">
        <h3>{{ __('Notifications') }}</h3>
        <button class="notification-close-btn" onclick="closeNotificationDropdown()">&times;</button>
    </div>

    <div class="notification-dropdown-list" id="notification-list">
        <div class="notification-loading" style="text-align: center; padding: 20px; color: #666;">
            {{ __('Loading...') }}
        </div>
    </div>

    <div class="notification-dropdown-footer">
        <a href="{{ route('notifications.index') }}" class="notification-see-all">
            <span>{{ __('See All Notifications') }}</span> →
        </a>
    </div>
</div>

<style>
    .notification-dropdown {
        position: fixed !important;
        top: 70px !important;
        right: 20px !important;
        width: 380px;
        background: rgba(20, 30, 50, 0.98);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(6, 182, 212, 0.3);
        border-radius: 12px;
        box-shadow: 0 15px 50px rgba(6, 182, 212, 0.4), 0 0 30px rgba(6, 182, 212, 0.2);
        z-index: 999999 !important;
        display: flex;
        flex-direction: column;
        max-height: 380px;
        height: auto;
    }

    .notification-dropdown-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(6, 182, 212, 0.15);
        background: rgba(6, 182, 212, 0.1);
        border-radius: 12px 12px 0 0;
        flex-shrink: 0;
    }

    .notification-dropdown-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
    }

    .notification-close-btn {
        background: none;
        border: none;
        font-size: 24px;
        color: #6b7a90;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .notification-close-btn:hover {
        color: #06b6d4;
    }

    .notification-dropdown-list {
        overflow-y: auto;
        overflow-x: hidden;
        flex: 1;
        min-height: auto;
        max-height: 240px;
    }

    .notification-dropdown-list::-webkit-scrollbar {
        width: 8px;
    }

    .notification-dropdown-list::-webkit-scrollbar-track {
        background: rgba(6, 182, 212, 0.05);
        border-radius: 4px;
    }

    .notification-dropdown-list::-webkit-scrollbar-thumb {
        background: rgba(6, 182, 212, 0.5);
        border-radius: 4px;
        cursor: pointer;
    }

    .notification-dropdown-list::-webkit-scrollbar-thumb:hover {
        background: rgba(6, 182, 212, 0.8);
        cursor: pointer;
    }

    .notification-item {
        padding: 10px 12px;
        border-bottom: 1px solid rgba(6, 182, 212, 0.08);
        cursor: pointer;
        transition: background-color 0.2s;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .notification-item:hover {
        background-color: rgba(6, 182, 212, 0.15);
    }

    .notification-item.unread {
        background-color: rgba(6, 182, 212, 0.2);
        border-left: 3px solid #06b6d4;
        padding-left: 9px;
    }

    .notification-icon {
        font-size: 18px;
        flex-shrink: 0;
        margin-top: 1px;
        color: #06b6d4;
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-title {
        font-weight: 600;
        color: #ffffff;
        font-size: 13px;
        margin: 0 0 2px 0;
    }

    .notification-message {
        color: #a0aec0;
        font-size: 12px;
        margin: 0 0 4px 0;
        word-wrap: break-word;
        line-height: 1.3;
    }

    .notification-time {
        font-size: 11px;
        color: #6b7a90;
        margin: 0;
    }

    .notification-empty {
        text-align: center;
        padding: 30px 20px;
        color: #6b7a90;
    }

    .notification-empty-icon {
        font-size: 32px;
        margin-bottom: 10px;
        opacity: 0.5;
    }

    .notification-dropdown-footer {
        padding: 12px 16px;
        background: rgba(6, 182, 212, 0.08);
        text-align: center;
        border-radius: 0 0 12px 12px;
        flex-shrink: 0;
    }

    .notification-see-all {
        color: #06b6d4;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: color 0.2s;
    }

    .notification-see-all:hover {
        color: #22d3ee;
    }

    .notification-bell-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        z-index: 1000;
    }

    .notification-bell-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 20px;
        transition: transform 0.2s;
        color: #fbc531;
    }

    .notification-bell-btn:hover {
        transform: scale(1.1);
        color: #f9ca24;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ef4444;
        color: white;
        border-radius: 50%;
        padding: 2px 5px;
        font-size: 10px;
        font-weight: bold;
    }
</style>

<script>
    // Global function to open notification dropdown
    function openNotificationDropdown() {
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';

        if (dropdown.style.display === 'block') {
            loadNotifications();
        }
    }

    // Global function to close notification dropdown
    function closeNotificationDropdown() {
        document.getElementById('notification-dropdown').style.display = 'none';
    }

    // Load notifications via AJAX
    function loadNotifications() {
        const listContainer = document.getElementById('notification-list');

        fetch('{{ route("notifications.api") }}?limit=10')
            .then(response => response.json())
            .then(notifications => {
                if (notifications.length === 0) {
                    listContainer.innerHTML = `
                        <div class="notification-empty">
                            <div class="notification-empty-icon">●</div>
                            <p>{{ __('No notifications yet') }}</p>
                        </div>
                    `;
                    return;
                }

                listContainer.innerHTML = notifications.map(notif => {
                    const date = new Date(notif.created_at);
                    const timeText = formatTime(date);
                    const unreadClass = notif.is_read ? '' : 'unread';
                    const icon = getNotificationIcon(notif.type);
                    const href = getNotificationLink(notif.type, notif.related_id);
                    const cursorStyle = href ? 'cursor: pointer;' : 'cursor: default;';

                    return `
                        <div class="notification-item ${unreadClass}" data-notif-id="${notif.id}" data-notif-href="${href || ''}" style="${cursorStyle}">
                            <div class="notification-icon">${icon}</div>
                            <div class="notification-content">
                                <p class="notification-title">${escapeHtml(notif.title)}</p>
                                <p class="notification-message">${escapeHtml(notif.message)}</p>
                                <p class="notification-time">${timeText}</p>
                            </div>
                        </div>
                    `;
                }).join('');

                // Add click handlers after rendering
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.addEventListener('click', function () {
                        const notifId = this.getAttribute('data-notif-id');
                        const href = this.getAttribute('data-notif-href');

                        // Only mark as read and navigate if there's a valid href
                        if (href) {
                            markAsReadAndNavigate(notifId, href);
                        } else {
                            // For informational notifications, just mark as read
                            markAsReadAndClose(notifId);
                        }
                    });
                });
            })
            .catch(error => {
                listContainer.innerHTML = '<div class="notification-empty"><p>Error loading notifications</p></div>';
                console.error('Error:', error);
            });
    }

    // Format time relative to now
    function formatTime(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffMins < 1) return '{{ __("now") }}';
        if (diffMins < 60) return diffMins + ' min' + (diffMins > 1 ? 's' : '');
        if (diffHours < 24) return diffHours + 'h';
        if (diffDays < 7) return diffDays + 'd';

        return date.toLocaleDateString();
    }

    // Get notification icon based on type
    function getNotificationIcon(type) {
        const icons = {
            'incident_reported': '●',
            'incident_in_progress': '◐',
            'incident_resolved': '✓',
            'reservation_created': '✎',
            'reservation_pending': '⧗',
            'reservation_approved': '✓',
            'reservation_refused': '✕',
            'account_activated': '✓',
            'user_registration': '👤',
            'general': '●'
        };
        return icons[type] || icons['general'];
    }

    // Get the link to navigate to based on notification type
    function getNotificationLink(type, relatedId) {
        const baseUrl = '{{ url("/") }}';
        const userRole = '{{ auth()->user()->role ?? "" }}';

        // Determine navigation based on notification type
        if (type === 'user_registration') {
            // Admin notifications about new user registrations - go to admin users page
            return `${baseUrl}/admin/users`;
        } else if (type === 'incident_reported') {
            // Incident reported
            if (userRole === 'manager') {
                return `${baseUrl}/manager/incidents`;
            }
            return `${baseUrl}/logs`;
        } else if (type === 'reservation_pending' || type === 'reservation_approved' || type === 'reservation_refused') {
            // Manager notifications about reservations - go to manager reservations or dashboard
            if (userRole === 'manager') {
                return `${baseUrl}/manager/dashboard`;
            }
        } else if (type === 'reservation_created') {
            // User notifications about their own reservations
            if (userRole === 'internal') {
                return `${baseUrl}/internal/reservations`;
            }
        } else if (type === 'incident_in_progress' || type === 'incident_resolved') {
            // Other incident notifications - go to incidents list
            if (userRole === 'admin') {
                return `${baseUrl}/admin/incidents`;
            } else if (userRole === 'manager') {
                return `${baseUrl}/manager/incidents`;
            }
        }

        // For informational notifications (no specific page), return null
        // This will prevent navigation
        return null;
    }

    // Mark notification as read and navigate
    function markAsReadAndNavigate(notifId, href) {
        fetch(`/notifications/${notifId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Failed to mark as read');
                return response.json();
            })
            .then(data => {
                // Reload notifications to update UI
                loadNotifications();
                // Navigate after marking as read
                setTimeout(() => {
                    window.location.href = href;
                }, 100);
            })
            .catch(error => {
                console.error('Error marking as read:', error);
                // Still navigate even if marking as read fails
                window.location.href = href;
            });
    }

    // Mark notification as read and close dropdown
    function markAsReadAndClose(notifId) {
        fetch(`/notifications/${notifId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Failed to mark as read');
                return response.json();
            })
            .then(data => {
                // Reload notifications to update UI
                loadNotifications();
            })
            .catch(error => console.error('Error marking as read:', error));
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }

    // Click-away listener
    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('notification-dropdown');
        const bellBtn = document.querySelector('.notification-bell-btn');
        const bellWrapper = document.querySelector('.notification-bell-wrapper');

        if (dropdown && bellWrapper && !bellWrapper.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>