
(function() {
    'use strict';

    function initializeChart() {
        const chart = document.getElementById('stats-chart');
        if (!chart) return;

        const openValue = chart.dataset.ouvert;
        const resolvedValue = chart.dataset.resolu;

        setTimeout(() => {
            const barOpen = document.getElementById('bar-ouvert');
            const barResolved = document.getElementById('bar-resolu');

            if (barOpen) barOpen.style.height = openValue + "%";
            if (barResolved) barResolved.style.height = resolvedValue + "%";
        }, 300);
    }

    // Notification management
    window.markAsRead = function(id, token) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            }
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        }).catch(error => {
            console.error('Error marking notification as read:', error);
        });
    };

    // Sidebar toggle
    window.toggleSidebar = function() {
        const sidebar = document.querySelector('.sidebar');
        const toggleBtn = document.getElementById('sidebarToggle') || document.querySelector('.sidebar-toggle');

        if (!sidebar) return;

        sidebar.classList.toggle('active');

        if (sidebar.classList.contains('active')) {
            if (toggleBtn) toggleBtn.classList.add('button-hidden');
        } else {
            if (toggleBtn) toggleBtn.classList.remove('button-hidden');
        }
    };

    // Initialization
    document.addEventListener('DOMContentLoaded', function() {
        initializeChart();
    });

})();
