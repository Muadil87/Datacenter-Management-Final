<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\InternalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\SocialController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Session;

// ==========================================
// PUBLIC ROUTES
// ==========================================

// Home & Resources
Route::get('/', [ResourceController::class, 'home'])->name('home');
Route::get('/resources', [ResourceController::class, 'index'])->name('resources.index');
Route::get('/resources/ajax-filter', [ResourceController::class, 'ajaxFilter'])->name('resources.filter');
Route::get('/rules', [GuestController::class, 'rules'])->name('rules');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::get('/auth/pending', function () {
    return view('auth.pending');
})->name('auth.pending');
Route::get('/auth/deactivated', function () {
    return view('auth.deactivated');
})->name('auth.deactivated');
Route::get('/auth/rejected', function () {
    return view('auth.rejected');
})->name('auth.rejected');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);


Route::get('/auth/{provider}', [SocialController::class, 'redirect'])
    ->where('provider', 'google|github');

Route::get('/auth/{provider}/callback', [SocialController::class, 'callback'])
    ->where('provider', 'google|github');

// Language Switch
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr'])) {
        Session::put('locale', $locale);
    }
    return back();
})->name('lang.switch');

// Social Login
Route::get('/auth/{provider}/redirect', [SocialController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialController::class, 'callback'])->name('social.callback');

// ==========================================
// AUTHENTICATED USERS ROUTES
// ==========================================

Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Incidents & Notifications
    Route::get('/incidents/create', [IncidentController::class, 'create'])->name('incidents.create');
    Route::post('/incidents/store', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.api');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    
    // Maintenance (Protected)
    Route::get('/maintenances', [MaintenanceController::class, 'index'])->name('maintenances.index');
    Route::get('/maintenances/filter', [MaintenanceController::class, 'ajaxFilter'])->name('maintenances.filter');
    Route::post('/maintenances/{id}/resolve', [MaintenanceController::class, 'resolve'])->name('maintenances.resolve');
    
    // Reservations (All authenticated users can make reservations)
    Route::get('/reserve/{resource_id}', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reserve/{resource_id}', [ReservationController::class, 'store'])->name('reservations.store');
    
    // API for resource availability
    Route::get('/api/resource/{resource_id}/reservations', [ReservationController::class, 'getReservations'])->name('api.resource.reservations');
});

// ==========================================
// ADMIN ROUTES
// ==========================================

Route::middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard & Logs
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    
    // User Management
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'usersManagement'])->name('admin.users');
    Route::post('/admin/users/{user}/approve', [AdminController::class, 'approveUser'])->name('admin.users.approve');
    Route::post('/admin/users/{user}/reject', [AdminController::class, 'rejectUser'])->name('admin.users.reject');
    Route::post('/admin/users/{user}/deactivate', [AdminController::class, 'deactivateUser'])->name('admin.users.deactivate');
    Route::patch('/admin/users/{user}/activate', [AdminController::class, 'activate'])->name('admin.users.activate');
    Route::patch('/admin/users/{user}/role', [AdminController::class, 'updateRole'])->name('admin.users.updateRole');
    Route::patch('/admin/users/{user}/approve-old', [AdminController::class, 'approveRegistration'])->name('admin.users.approve-old');
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    
    // Resources (Read-only)
    Route::get('/admin/resources', [ResourceController::class, 'adminIndex'])->name('admin.resources.index');
    Route::patch('/admin/resources/{resource}/status', [ResourceController::class, 'updateStatus'])->name('admin.resources.status');
    Route::patch('/admin/resources/{resource}/manager', [AdminController::class, 'assignManager'])->name('admin.resources.assignManager');
    
    // Reservations (View all)
    Route::get('/admin/reservations', [AdminController::class, 'viewReservations'])->name('admin.reservations');
    
    // Incidents Management
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::patch('/incidents/{incident}/status', [IncidentController::class, 'updateStatus'])->name('incidents.updateStatus');
    Route::post('/incidents/{id}/resolve', [IncidentController::class, 'resolve'])->name('incidents.resolve');
    
    // Maintenance Calendar & Scheduling
    Route::get('/admin/maintenance/calendar', [AdminController::class, 'maintenanceCalendar'])->name('admin.maintenance.calendar');
    Route::get('/admin/maintenance/create', [AdminController::class, 'maintenanceScheduleForm'])->name('admin.maintenance.create');
    Route::post('/admin/maintenance/schedule', [AdminController::class, 'scheduleMaintenance'])->name('admin.maintenance.schedule');
    Route::patch('/admin/maintenance/{maintenance}/status', [AdminController::class, 'updateMaintenanceStatus'])->name('admin.maintenance.status');
    
    // Incident History & Statistics
    Route::get('/admin/incidents/history', [AdminController::class, 'incidentHistory'])->name('admin.incidents.history');
});

// ==========================================
// ADMIN & MANAGER ROUTES
// ==========================================

Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    // Resource Management
    Route::post('/resources', [ResourceController::class, 'store'])->name('resources.store');
    Route::patch('/resources/{resource}', [ResourceController::class, 'update'])->name('resources.update');
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->name('resources.destroy');
});

// ==========================================
// MANAGER ROUTES
// ==========================================

Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/manager/dashboard', [ManagerController::class, 'index'])->name('manager.dashboard');
    Route::patch('/reservations/{id}/handle', [ReservationController::class, 'handleRequest'])->name('reservations.handle');
    
    // Incident Reporting for Managers
    Route::get('/manager/incidents/report', [ManagerController::class, 'reportIncidentForm'])->name('manager.incidents.report');
    Route::post('/manager/incidents/report', [ManagerController::class, 'reportIncident'])->name('manager.incidents.store');
    
    // Manager Incidents List - view internal user incidents
    Route::get('/manager/incidents', [ManagerController::class, 'listIncidents'])->name('manager.incidents.list');
    Route::get('/manager/incidents/{incident}', [ManagerController::class, 'showIncident'])->name('manager.incidents.show');
    Route::patch('/manager/incidents/{incident}/status', [ManagerController::class, 'updateIncidentStatus'])->name('manager.incidents.updateStatus');
    Route::delete('/manager/incidents/{incident}', [ManagerController::class, 'deleteIncident'])->name('manager.incidents.delete');
    
    // Manager Maintenance - plan and manage maintenance
    Route::get('/manager/maintenance', [ManagerController::class, 'listMaintenance'])->name('manager.maintenance.list');
    Route::post('/manager/maintenance', [ManagerController::class, 'createMaintenance'])->name('manager.maintenance.create');
});

// ==========================================
// INTERNAL USER ROUTES
// ==========================================

Route::middleware(['auth', 'role:internal'])->group(function () {
    Route::get('/internal/dashboard', [InternalController::class, 'index'])->name('internal.dashboard');
    Route::get('/internal/reservations', [InternalController::class, 'myReservations'])->name('internal.reservations');
    
    // Incident Reporting
    Route::get('/internal/incidents/report', [InternalController::class, 'reportIncidentForm'])->name('internal.incidents.report');
    Route::post('/internal/incidents/report', [InternalController::class, 'reportIncident'])->name('internal.incidents.store');
    Route::get('/internal/incidents/my-incidents', [InternalController::class, 'myIncidents'])->name('internal.incidents.my');
});