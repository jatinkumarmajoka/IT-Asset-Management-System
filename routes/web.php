<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ImportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', fn () => redirect()->route('dashboard'));

// Dashboard
Route::get('/dashboard', DashboardController::class)
    ->name('dashboard');

// Assets
Route::resource('assets', AssetController::class);

// Departments
Route::get(
    '/departments/{department}/assets',
    [DepartmentController::class, 'assets']
)->name('departments.assets');

Route::resource('departments', DepartmentController::class);

// Categories
Route::resource('categories', CategoryController::class);

// Employees
Route::resource('employees', EmployeeController::class);

/*
|--------------------------------------------------------------------------
| Imports
|--------------------------------------------------------------------------
*/

// Import history / upload page
Route::get(
    '/imports',
    [ImportController::class, 'index']
)->name('imports.index');

// Upload import file
Route::post(
    '/imports',
    [ImportController::class, 'store']
)->name('imports.store');

// Process uploaded document
Route::post(
    '/imports/{import}/process',
    [ImportController::class, 'process']
)->name('imports.process');

// Validate / normalize extracted records
Route::post(
    '/imports/{import}/validate',
    [ImportController::class, 'validateImport']
)->name('imports.validate');

// Confirm and create actual assets
Route::post(
    '/imports/{import}/confirm',
    [ImportController::class, 'confirm']
)->name('imports.confirm');

// View import details
Route::get(
    '/imports/{import}',
    [ImportController::class, 'show']
)->name('imports.show');

// Delete import
Route::delete(
    '/imports/{import}',
    [ImportController::class, 'destroy']
)->name('imports.destroy');