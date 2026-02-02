@extends('layout')

@section('content')
    <style>
        .notifications-page {
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .notifications-header {
            margin-bottom: 40px;
        }

        .notifications-header h1 {
            font-size: 36px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #06b6d4 0%, #22d3ee 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .notifications-header p {
            color: #a0aec0;
            font-size: 16px;
        }

        .notifications-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .notification-card {
            background: rgba(20, 30, 50, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.15);
            padding: 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }

        .notification-card:hover {
            background: rgba(20, 30, 50, 0.9);
            border-color: rgba(6, 182, 212, 0.4);
            box-shadow: 0 8px 30px rgba(6, 182, 212, 0.15);
        }

        .notification-card.unread {
            background: rgba(6, 182, 212, 0.1);
            border-left: 4px solid #06b6d4;
            border-color: #06b6d4;
            padding-left: 16px;
        }

        .notification-icon {
            font-size: 32px;
            flex-shrink: 0;
        }

        .notification-content {
            flex: 1;
            min-width: 0;
        }

        .notification-title {
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 6px;
        }

        .notification-message {
            font-size: 14px;
            color: #a0aec0;
            margin-bottom: 10px;
            line-height: 1.5;
            word-wrap: break-word;
        }

        .notification-time {
            font-size: 12px;
            color: #6b7a90;
        }

        .notification-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-mark-read {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-mark-read:hover {
            box-shadow: 0 4px 15px rgba(6, 182, 212, 0.4);
            transform: translateY(-2px);
        }

        .btn-mark-read:disabled {
            background: rgba(107, 122, 144, 0.3);
            cursor: not-allowed;
            transform: none;
        }

        .notifications-empty {
            text-align: center;
            padding: 80px 20px;
            background: rgba(20, 30, 50, 0.5);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(6, 182, 212, 0.15);
            border-radius: 12px;
        }

        .notifications-empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.6;
        }

        .notifications-empty-text {
            color: #a0aec0;
            font-size: 16px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #06b6d4;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .back-link:hover {
            color: #22d3ee;
            gap: 12px;
        }
    </style>

    <div class="notifications-page">
        <a href="{{ route('home') }}" class="back-link">← {{ __('Back to Home') }}</a>

        <div class="notifications-header">
            <h1>{{ __('My Notifications') }}</h1>
            <p>{{ __('Stay updated with your latest messages') }}</p>
        </div>

        <div class="notifications-container" id="notifications-container">
            <div style="text-align: center; padding: 60px 20px;">
                <div style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;">⏳</div>
                <p style="color: #7f8c8d; font-size: 16px;">{{ __('Loading notifications...') }}</p>
            </div>
        </div>
    </div>

    <script>
        // Format time relative to now
        function formatTime(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMins / 60);
            const diffDays = Math.floor(diffHours / 24);

            if (diffMins < 1) return '{{ __("now") }}';
            if (diffMins < 60) return diffMins + ' {{ __("min") }}' + (diffMins > 1 ? 's' : '');
            if (diffHours < 24) return diffHours + 'h';
            if (diffDays < 7) return diffDays + 'd';

            return date.toLocaleDateString();
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

        // Get icon based on notification type
        function getNotificationIcon(type) {
            const icons = {
                'incident_reported': '<i class="fas fa-circle" style="color: #ef4444;"></i>',
                'incident_in_progress': '<i class="fas fa-wrench"></i>',
                'incident_resolved': '<i class="fas fa-check-circle"></i>',
                'reservation_created': '<i class="fas fa-file-alt"></i>',
                'reservation_pending': '⏳',
                'reservation_approved': '<i class="fas fa-check-circle"></i>',
                'reservation_refused': '<i class="fas fa-times-circle"></i>',
                'account_activated': '<i class="fas fa-star"></i>',
                'general': '<i class="fas fa-bell"></i>'
            };
            return icons[type] || icons['general'];
        }

        // Load all notifications for this page
        function loadAllNotifications() {
            const container = document.getElementById('notifications-container');

            fetch('{{ route("notifications.api") }}?limit=100')
                .then(response => response.json())
                .then(notifications => {
                    if (notifications.length === 0) {
                        container.innerHTML = `
                            <div class="notifications-empty">
                                <div class="notifications-empty-icon"><i class="fas fa-bell"></i></div>
                                <p class="notifications-empty-text">{{ __('No notifications yet') }}</p>
                            </div>
                        `;
                        return;
                    }

                    container.innerHTML = notifications.map(notif => {
                        const date = new Date(notif.created_at);
                        const timeText = formatTime(date);
                        const unreadClass = notif.is_read ? '' : 'unread';
                        const icon = getNotificationIcon(notif.type);

                        return `
                            <div class="notification-card ${unreadClass}">
                                <div class="notification-icon">${icon}</div>
                                <div class="notification-content">
                                    <div class="notification-title">${escapeHtml(notif.title)}</div>
                                    <div class="notification-message">${escapeHtml(notif.message)}</div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div class="notification-time">${timeText}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');

                    // Mark all notifications as read
                    markAllNotificationsAsRead();
                })
                .catch(error => {
                    container.innerHTML = '<div class="notifications-empty"><div class="notifications-empty-icon"><i class="fas fa-exclamation-triangle"></i></div><p class="notifications-empty-text">Error loading notifications</p></div>';
                    console.error('Error:', error);
                });
        }

        // Mark all notifications as read
        function markAllNotificationsAsRead() {
            fetch('{{ route("notifications.api") }}?limit=100')
                .then(response => response.json())
                .then(notifications => {
                    // Mark each unread notification as read
                    notifications.forEach(notif => {
                        if (!notif.is_read) {
                            fetch(`/notifications/${notif.id}/read`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                    'Content-Type': 'application/json'
                                }
                            }).catch(error => console.error('Error marking notification as read:', error));
                        }
                    });
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }

        // Load on page load
        document.addEventListener('DOMContentLoaded', loadAllNotifications);
    </script>
@endsection