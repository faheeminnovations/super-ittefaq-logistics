<?php

use Illuminate\Support\Facades\Route;

// Resource routes for each controller
Route::resource('customers', App\Http\Controllers\CustomersController::class);
Route::resource('dispatches', App\Http\Controllers\DispatchesController::class);
Route::resource('documents', App\Http\Controllers\DocumentsController::class);
Route::resource('drivers', App\Http\Controllers\DriversController::class);
Route::resource('expenses', App\Http\Controllers\ExpensesController::class);
Route::resource('invoices', App\Http\Controllers\InvoicesController::class);
Route::resource('transport_jobs', App\Http\Controllers\JobsController::class);
Route::resource('maintenances', App\Http\Controllers\MaintenancesController::class);
Route::resource('pods', App\Http\Controllers\PodsController::class);
Route::resource('reports', App\Http\Controllers\ReportsController::class);
Route::resource('settings', App\Http\Controllers\SettingsController::class);
Route::resource('trackings', App\Http\Controllers\TrackingsController::class);
Route::resource('trips', App\Http\Controllers\TripsController::class);
Route::resource('users', App\Http\Controllers\UsersController::class);
Route::resource('vehicles', App\Http\Controllers\VehiclesController::class);
