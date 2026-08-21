<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\GlobalSearchController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.get');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/notifications/settings', [NotificationController::class, 'settings'])->name('notifications.settings');
    Route::post('/notifications/settings', [NotificationController::class, 'updateSettings'])->name('notifications.update-settings');
    Route::delete('/notifications', [NotificationController::class, 'delete'])->name('notifications.delete');
});

require __DIR__.'/auth.php';

// Resource routes for main modules with role-based access
// Admin - Full access to users and settings
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UsersController::class);
    Route::resource('settings', SettingController::class);
    Route::post('users/{id}/permissions', [UsersController::class, 'assignPermissions'])->name('users.permissions');
    Route::get('users/{id}/permissions/data', [UsersController::class, 'permissions'])->name('users.permissions.data');
});

// Dispatcher & Manager - Operations access
Route::middleware(['auth', 'role:admin,dispatcher,manager'])->group(function () {
    Route::resource('customers', CustomerController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('jobs', JobController::class);
    Route::resource('trips', TripController::class);
    Route::resource('dispatch', DispatchController::class);
    Route::post('/dispatch/export', [DispatchController::class, 'export'])->name('dispatch.export');
    Route::resource('pod', PodController::class);
    Route::resource('tracking', TrackingController::class);
    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{id}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('maintenance', MaintenanceController::class);
    Route::post('/maintenance/export', [MaintenanceController::class, 'export'])->name('maintenance.export');
    Route::post('/import/process', [ImportController::class, 'process'])->name('import.process');
    Route::get('/import/export', [ImportController::class, 'export'])->name('import.export');
    Route::post('/pod/export', [PodController::class, 'export'])->name('pod.export');
    Route::post('/jobs/export', [JobController::class, 'export'])->name('jobs.export');
    Route::post('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::post('/vehicles/export', [VehicleController::class, 'export'])->name('vehicles.export');
    Route::post('/drivers/export', [DriverController::class, 'export'])->name('drivers.export');
});

// Accounts & Manager - Financial access
Route::middleware(['auth', 'role:admin,accounts,manager'])->group(function () {
    Route::resource('expenses', ExpenseController::class);
    Route::post('/expenses/export', [ExpenseController::class, 'export'])->name('expenses.export');
    Route::resource('invoices', InvoiceController::class);
    Route::post('/invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
    Route::get('/billing/{id}', [BillingController::class, 'show'])->name('billing.show');
    Route::put('/billing/{id}', [BillingController::class, 'update'])->name('billing.update');
    Route::delete('/billing/{id}', [BillingController::class, 'destroy'])->name('billing.destroy');
    Route::post('/billing/filter', [BillingController::class, 'filter'])->name('billing.filter');
    Route::get('/billing/export', [BillingController::class, 'export'])->name('billing.export');
    Route::get('/billing/monthly-summary', [BillingController::class, 'showMonthlySummary'])->name('billing.monthly-summary');
    Route::resource('reports', ReportsController::class);
    Route::post('/reports/filter', [ReportsController::class, 'filter'])->name('reports.filter');
    Route::post('/reports/export', [ReportsController::class, 'export'])->name('reports.export');
});

// Permission-based routes for fine-grained control (admin can assign individual permissions)
// These routes work alongside role-based access for additional security
Route::middleware(['auth'])->group(function () {
    // Individual permission checks can be added here for specific actions
    // Example: Route::post('users/{id}/permissions', [UsersController::class, 'assignPermissions'])
    //          ->middleware('permission:manage_permissions');
});

// API routes for AJAX requests with role-based access
Route::middleware(['auth', 'role:admin,dispatcher,manager'])->group(function () {
    Route::get('/api/jobs/{id}', [JobController::class, 'show']);
    Route::get('/api/customers/{id}', [CustomerController::class, 'show']);
    Route::get('/api/drivers/{id}', [DriverController::class, 'show']);
    Route::get('/api/vehicles/{id}', [VehicleController::class, 'show']);
    Route::get('/api/trips/{id}', [TripController::class, 'show']);
    Route::get('/api/dispatch/{id}', [DispatchController::class, 'show']);
    Route::get('/api/pod/{id}', [PodController::class, 'show']);
    Route::get('/api/maintenance/{id}', [MaintenanceController::class, 'show']);
    Route::get('/api/documents/{id}', [DocumentController::class, 'show']);
    Route::get('/api/tracking/search', [TrackingController::class, 'search']);
});

// Global search - available to all authenticated users
Route::middleware(['auth'])->group(function () {
    Route::get('/api/global-search', [GlobalSearchController::class, 'search']);
});

Route::middleware(['auth', 'role:admin,accounts,manager'])->group(function () {
    Route::get('/api/invoices/{id}', [InvoiceController::class, 'show']);
    Route::get('/api/expenses/{id}', [ExpenseController::class, 'show']);
    Route::get('/api/billing/{id}', [BillingController::class, 'show']);
    Route::get('/api/reports/{id}', [ReportsController::class, 'show']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/api/users/{id}', [UsersController::class, 'show']);
    Route::get('/api/settings/{id}', [SettingController::class, 'show']);
});

// Include generated CRUD resource routes (admin)
// require __DIR__ . '/crud_resources.php'; // Disabled to avoid conflicts with new controllers

// Generic page router fallback: any URL like /customers-page will try to load resources/views/pages/{page}.blade.php
Route::get('{page}', function ($page) {
    if (view()->exists('pages.' . $page)) {
        return view('pages.' . $page);
    }
    abort(404);
});
