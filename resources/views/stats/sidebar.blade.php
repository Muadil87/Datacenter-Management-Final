<div class="sidebar">
    <button class="close-sidebar-btn" onclick="toggleSidebar()">&times;</button>
    <h2>Admin Panel</h2>
    <a href="{{ route('admin.dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.users') }}" class="{{ Request::is('admin/users*') ? 'active' : '' }}">User Management</a>
    <a href="{{ route('logs.index') }}" class="{{ Request::is('logs*') ? 'active' : '' }}">Audit Trail</a>
    <a href="{{ route('resources.index') }}" class="{{ Request::is('resources*') ? 'active' : '' }}">Inventory</a>
    <a href="{{ route('maintenances.index') }}"
        class="{{ Request::is('maintenances*') ? 'active' : '' }}">Maintenance</a>
    <hr style="margin: 20px 0; border: none; border-top: 1px solid #444;">
    <a href="{{ route('home') }}" class="back-to-home-btn">← Back to Home</a>
</div>