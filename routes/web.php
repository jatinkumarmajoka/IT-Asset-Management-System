
<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\OperationalRequestReviewController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetDocumentController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\AssetTransferDocumentController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\OperationalRequestController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest / Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('/reset-password/{token}', [
        NewPasswordController::class,
        'create',
    ])->name('password.reset');

    Route::post('/reset-password', [
        NewPasswordController::class,
        'store',
    ])
        ->middleware('throttle:5,1')
        ->name('password.store');
});

/*
|--------------------------------------------------------------------------
| Authenticated Application Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'approved.session'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Operational Requests
    |--------------------------------------------------------------------------
    */

    Route::get('/operational-requests', [
        OperationalRequestController::class,
        'index',
    ])->name('operational-requests.index');

    Route::get('/operational-requests/create', [
        OperationalRequestController::class,
        'create',
    ])->name('operational-requests.create');

    Route::post('/operational-requests', [
        OperationalRequestController::class,
        'store',
    ])->name('operational-requests.store');

    /*
    |--------------------------------------------------------------------------
    | Super Admin Only
    |--------------------------------------------------------------------------
    */

    Route::middleware('super.admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/', [AdminController::class, 'index'])
                ->name('index');

            Route::post('/accounts/generate', [
                AdminController::class,
                'generateAccount',
            ])->name('accounts.generate');

            Route::post('/accounts/{user}/approve', [
                AdminController::class,
                'approveAccount',
            ])->name('accounts.approve');

            Route::post('/accounts/{user}/reject', [
                AdminController::class,
                'rejectAccount',
            ])->name('accounts.reject');

            Route::get('/staff/{user}/edit', [
                AdminController::class,
                'editStaff',
            ])->name('staff.edit');

            Route::put('/staff/{user}', [
                AdminController::class,
                'updateStaff',
            ])->name('staff.update');

            Route::put('/staff/{user}/permissions', [
                AdminController::class,
                'updatePermissions',
            ])->name('staff.permissions');

            Route::post('/staff/{user}/password-reset-link', [
                AdminController::class,
                'sendPasswordResetLink',
            ])->name('staff.password-reset-link');

            Route::post('/transfers/{transfer}/approve', [
                AssetTransferController::class,
                'approve',
            ])->name('transfers.approve');

            Route::post('/transfers/{transfer}/complete', [
                AssetTransferController::class,
                'complete',
            ])->name('transfers.complete');

            Route::post('/operational-requests/{operationalRequest}/approve', [
                OperationalRequestReviewController::class,
                'approve',
            ])->name('operational-requests.approve');

            Route::post('/operational-requests/{operationalRequest}/reject', [
                OperationalRequestReviewController::class,
                'reject',
            ])->name('operational-requests.reject');
        });

    /*
    |--------------------------------------------------------------------------
    | Assets
    |--------------------------------------------------------------------------
    */

    Route::middleware('module.access:assets')->group(function () {
        Route::resource('assets', AssetController::class);

        Route::post('/assets/bulk-action', [AssetController::class, 'bulkAction'])
            ->name('assets.bulk-action');

        Route::post('/filter-presets', [AssetController::class, 'storeFilterPreset'])
            ->name('filter-presets.store');

        Route::get('/assets/export', [AssetController::class, 'export'])
            ->name('assets.export');

        Route::get('/assets/template', [AssetController::class, 'downloadTemplate'])
            ->name('assets.template');

        Route::post('/assets/import', [AssetController::class, 'import'])
            ->name('assets.import');

        Route::post('/assets/{asset}/documents/image/replace', [
            AssetDocumentController::class,
            'replaceImage',
        ])->name('asset-documents.image.replace');

        Route::delete('/assets/{asset}/documents/image', [
            AssetDocumentController::class,
            'destroyImage',
        ])->name('asset-documents.image.destroy');

        Route::post('/assets/{asset}/documents', [
            AssetDocumentController::class,
            'store',
        ])->name('asset-documents.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Asset Document Viewing
    |--------------------------------------------------------------------------
    */

    Route::get('/assets/{asset}/documents/{document}', [
        AssetDocumentController::class,
        'show',
    ])
        ->middleware('module.access:assets,bills')
        ->name('asset-documents.show');

    /*
    |--------------------------------------------------------------------------
    | Bill / Invoice Replacement and Deletion
    |--------------------------------------------------------------------------
    */

    Route::post('/assets/{asset}/documents/bill/replace', [
        AssetDocumentController::class,
        'replaceBill',
    ])
        ->middleware('module.access:bills')
        ->name('asset-documents.bill.replace');

    Route::delete('/assets/{asset}/documents/bill', [
        AssetDocumentController::class,
        'destroyBill',
    ])
        ->middleware('module.access:bills')
        ->name('asset-documents.bill.destroy');

    /*
    |--------------------------------------------------------------------------
    | Departments
    |--------------------------------------------------------------------------
    */

    Route::middleware('module.access:departments')->group(function () {
        Route::get('/departments/{department}/assets', [
            DepartmentController::class,
            'assets',
        ])->name('departments.assets');

        Route::resource('departments', DepartmentController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Categories
    |--------------------------------------------------------------------------
    */

    Route::middleware('module.access:categories')->group(function () {
        Route::resource('categories', CategoryController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Employees
    |--------------------------------------------------------------------------
    */

    Route::middleware('module.access:employees')->group(function () {
        Route::resource('employees', EmployeeController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | Asset Transfers
    |--------------------------------------------------------------------------
    */

    Route::middleware('module.access:transfers')->group(function () {
        Route::get('/transfers', [
            AssetTransferController::class,
            'index',
        ])->name('transfers.index');

        Route::get('/transfers/create', [
            AssetTransferController::class,
            'create',
        ])->name('transfers.create');

        Route::post('/transfers', [
            AssetTransferController::class,
            'store',
        ])->name('transfers.store');

        Route::get('/transfers/{transfer}', [
            AssetTransferController::class,
            'show',
        ])->name('transfers.show');

        Route::post('/transfers/{transfer}/cancel', [
            AssetTransferController::class,
            'cancel',
        ])->name('transfers.cancel');

        Route::post('/transfers/{transfer}/documents', [
            AssetTransferDocumentController::class,
            'store',
        ])->name('transfer-documents.store');

        Route::get('/transfers/{transfer}/documents/{document}', [
            AssetTransferDocumentController::class,
            'show',
        ])->name('transfer-documents.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Imports
    |--------------------------------------------------------------------------
    */

    Route::middleware('module.access:imports')->group(function () {
        Route::get('/imports', [
            ImportController::class,
            'index',
        ])->name('imports.index');

        Route::post('/imports', [
            ImportController::class,
            'store',
        ])->name('imports.store');

        Route::post('/imports/{import}/process', [
            ImportController::class,
            'process',
        ])->name('imports.process');

        Route::post('/imports/{import}/validate', [
            ImportController::class,
            'validateImport',
        ])->name('imports.validate');

        Route::post('/imports/{import}/confirm', [
            ImportController::class,
            'confirm',
        ])->name('imports.confirm');

        Route::get('/imports/{import}', [
            ImportController::class,
            'show',
        ])->name('imports.show');

        Route::delete('/imports/{import}', [
            ImportController::class,
            'destroy',
        ])->name('imports.destroy');
    });
});
