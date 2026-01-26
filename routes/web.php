<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/sales-data', [DashboardController::class, 'getSalesData'])->name('api.sales-data');
    Route::get('/api/profit-data', [DashboardController::class, 'getProfitData'])->name('api.profit-data');

    Route::middleware('level:1,2')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    });

    Route::middleware('level:1,2,3')->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::get('/products/trashed', [ProductController::class, 'trashed'])->name('products.trashed');
        Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');
        Route::get('/api/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('api.products.barcode');
    });

    Route::middleware('level:3,4')->group(function () {
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
    });

    Route::middleware('level:1,2,3,4')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    });

    Route::middleware('level:3')->group(function () {
        Route::resource('returns', ReturnController::class);
        Route::put('/returns/{returnTransaction}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
        Route::put('/returns/{returnTransaction}/reject', [ReturnController::class, 'reject'])->name('returns.reject');
    });

    Route::middleware('level:1,2,5')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::middleware('level:5')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/reports/products', [ReportController::class, 'products'])->name('reports.products');
        Route::get('/api/reports/sales-data', [ReportController::class, 'salesData'])->name('api.reports.sales-data');
        Route::get('/api/reports/financial-data', [ReportController::class, 'financialData'])->name('api.reports.financial-data');
    });

    Route::middleware('level:1')->group(function () {
        Route::get('/system-backup', [DashboardController::class, 'backup'])->name('system.backup');
        Route::post('/system-backup', [DashboardController::class, 'doBackup'])->name('system.backup.do');
    });
});
