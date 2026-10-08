<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetDocumentController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\AssetTransferDocumentController;
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


/*
|--------------------------------------------------------------------------
| Assets
|--------------------------------------------------------------------------
*/

Route::resource('assets', AssetController::class);

Route::post(
    '/assets/{asset}/documents',
    [AssetDocumentController::class, 'store']
)->name('asset-documents.store');

Route::get(
    '/assets/{asset}/documents/{document}',
    [AssetDocumentController::class, 'show']
)->name('asset-documents.show');

Route::post(
    '/assets/{asset}/documents/image/replace',
    [AssetDocumentController::class, 'replaceImage']
)->name('asset-documents.image.replace');

Route::delete(
    '/assets/{asset}/documents/image',
    [AssetDocumentController::class, 'destroyImage']
)->name('asset-documents.image.destroy');

/*
|--------------------------------------------------------------------------
| Departments
|--------------------------------------------------------------------------
*/

Route::get(
    '/departments/{department}/assets',
    [DepartmentController::class, 'assets']
)->name('departments.assets');

Route::resource('departments', DepartmentController::class);


/*
|--------------------------------------------------------------------------
| Categories
|--------------------------------------------------------------------------
*/

Route::resource('categories', CategoryController::class);


/*
|--------------------------------------------------------------------------
| Employees
|--------------------------------------------------------------------------
*/

Route::resource('employees', EmployeeController::class);


/*
|--------------------------------------------------------------------------
| Asset Transfers
|--------------------------------------------------------------------------
|
| Transfer workflow:
|
| Create
|   ↓
| Pending
|   ↓
| Approve
|   ↓
| Approved
|   ↓
| Complete
|   ↓
| Asset department/occupant updated
|   ↓
| Asset history created
|
*/


// Transfer list
Route::get(
    '/transfers',
    [AssetTransferController::class, 'index']
)->name('transfers.index');

// Create transfer form
Route::get(
    '/transfers/create',
    [AssetTransferController::class, 'create']
)->name('transfers.create');

// Store transfer
Route::post(
    '/transfers',
    [AssetTransferController::class, 'store']
)->name('transfers.store');

// Transfer details
Route::get(
    '/transfers/{transfer}',
    [AssetTransferController::class, 'show']
)->name('transfers.show');

// Approve transfer
Route::post(
    '/transfers/{transfer}/approve',
    [AssetTransferController::class, 'approve']
)->name('transfers.approve');

// Complete transfer
Route::post(
    '/transfers/{transfer}/complete',
    [AssetTransferController::class, 'complete']
)->name('transfers.complete');

// Cancel transfer
Route::post(
    '/transfers/{transfer}/cancel',
    [AssetTransferController::class, 'cancel']
)->name('transfers.cancel');


Route::post(
    '/transfers/{transfer}/documents',
    [AssetTransferDocumentController::class, 'store']
)->name('transfer-documents.store');

Route::get(
    '/transfers/{transfer}/documents/{document}',
    [AssetTransferDocumentController::class, 'show']
)->name('transfer-documents.show');






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