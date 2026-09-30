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
// Custom route for viewing department assets in a popup
Route::get(
    '/departments/{department}/assets',
    [DepartmentController::class, 'assets']
)->name('departments.assets');

Route::resource('departments', DepartmentController::class);

// Categories
Route::resource('categories', CategoryController::class);

// Employees
Route::resource('employees', EmployeeController::class);

// Imports
Route::get(
    '/imports',
    [ImportController::class, 'index']
)->name('imports.index');

Route::post(
    '/imports',
    [ImportController::class, 'store']
)->name('imports.store');

Route::get(
    '/imports/{import}',
    [ImportController::class, 'show']
)->name('imports.show');

Route::delete(
    '/imports/{import}',
    [ImportController::class, 'destroy']
)->name('imports.destroy');