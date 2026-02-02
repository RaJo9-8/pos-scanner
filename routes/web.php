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
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
    Route::put('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/sales-data', [DashboardController::class, 'getSalesData'])->name('api.sales-data');
    Route::get('/api/profit-data', [DashboardController::class, 'getProfitData'])->name('api.profit-data');

    Route::get('/products/trashed', [ProductController::class, 'trashed'])->name('products.trashed')->middleware('level:1');

    // Pindahkan route users/trashed ke sini untuk testing
    Route::get('/users/trashed', [UserController::class, 'trashed'])->name('users.trashed')->middleware('level:1,2');

    Route::middleware('level:1')->group(function() {
        Route::get('/test-delete-user', function() {
            $user = \App\Models\User::find(4);
            if ($user) {
                $user->delete();
                return "User {$user->name} deleted! Trashed count: " . \App\Models\User::onlyTrashed()->count();
            }
            return "User not found";
        });

        Route::get('/test-restore-user', function() {
            $user = \App\Models\User::onlyTrashed()->first();
            if ($user) {
                $user->restore();
                return "User {$user->name} restored! Trashed count: " . \App\Models\User::onlyTrashed()->count();
            }
            return "No trashed users found";
        });
    });

    Route::middleware('level:1,2')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
        
        // Test route untuk debugging
        Route::get('/debug-users-trashed', function() {
            $authStatus = auth()->check() ? 'Logged in as: ' . auth()->user()->name : 'Not logged in';
            $userLevel = auth()->check() ? auth()->user()->level : 'N/A';
            $users = \App\Models\User::onlyTrashed()->get();
            $result = [
                'auth_status' => $authStatus,
                'user_level' => $userLevel,
                'trashed_count' => $users->count(),
                'users' => []
            ];
            foreach($users as $user) {
                $result['users'][] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'level' => $user->level,
                    'deleted_at' => $user->deleted_at
                ];
            }
            return response()->json($result);
        });
    });

    Route::middleware('level:1,2,3')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        
        Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');
        Route::get('/api/products/barcode/{barcode}', [ProductController::class, 'findByBarcode'])->name('api.products.barcode');
        
        // Stock Management Routes
        Route::resource('stock-in', StockInController::class);
        Route::resource('stock-out', StockOutController::class);
        Route::get('/stock-in/get-product/{id}', [StockInController::class, 'getProduct'])->name('stock-in.get-product');
        Route::get('/stock-out/get-product/{id}', [StockOutController::class, 'getProduct'])->name('stock-out.get-product');
        
        // Test routes untuk debugging
        Route::get('/test-stock-in', function() {
            return 'Stock in route working! Available routes: stock-in.index';
        });
        Route::get('/test-stock-out', function() {
            return 'Stock out route working! Available routes: stock-out.index';
        });
        Route::get('/test-create-stock-out', function() {
            $product = \App\Models\Product::first();
            if (!$product) {
                return "No products found";
            }
            
            \App\Models\StockOut::create([
                'code' => \App\Models\StockOut::generateCode(),
                'product_id' => $product->id,
                'user_id' => 1, // hardcoded user ID
                'quantity' => 1,
                'reason' => 'Test',
                'notes' => 'Test data',
                'date' => now(),
            ]);
            
            return "Stock out test data created!";
        });
        Route::get('/test-create-stock-in', function() {
            try {
                $product = \App\Models\Product::first();
                if (!$product) {
                    return "No products found";
                }
                
                $stockIn = \App\Models\StockIn::create([
                    'code' => \App\Models\StockIn::generateCode(),
                    'product_id' => $product->id,
                    'user_id' => 1, // hardcoded user ID
                    'quantity' => 1,
                    'purchase_price' => 10000,
                    'total_price' => 10000,
                    'supplier' => 'Test Supplier',
                    'notes' => 'Test data',
                    'date' => now(),
                ]);
                
                return "Stock in test data created! ID: " . $stockIn->id;
            } catch (\Exception $e) {
                return "Error: " . $e->getMessage();
            }
        });
        Route::get('/test-stock-in-data', function() {
            try {
                $stockIn = \App\Models\StockIn::with(['product', 'user'])->get();
                $data = [];
                foreach ($stockIn as $item) {
                    $data[] = [
                        'id' => $item->id,
                        'code' => $item->code,
                        'product_name' => $item->product ? $item->product->name : 'N/A',
                        'user_name' => $item->user ? $item->user->name : 'N/A',
                        'quantity' => $item->quantity,
                    ];
                }
                return response()->json($data);
            } catch (\Exception $e) {
                return "Error: " . $e->getMessage();
            }
        });
        
        // Test route untuk debugging
        Route::get('/test-products-create', function() {
            return 'Products create route working! User level: ' . (auth()->check() ? auth()->user()->level : 'not logged in');
        });
        
        // Test barcode API
        Route::get('/test-barcode-api/{barcode}', function($barcode) {
            $product = \App\Models\Product::where('barcode', $barcode)->first();
            if ($product) {
                return response()->json([
                    'success' => true,
                    'product' => $product
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
        });
    });

    Route::middleware('level:1,2,3,4,5')->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    });

    Route::middleware('level:1')->group(function () {
        Route::post('/products/{product}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::get('/test-trashed', function() {
            return 'Trashed route working!';
        });
    });

    Route::middleware('level:3,4')->group(function () {
        Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('/transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
        Route::get('/transactions/search-product', [TransactionController::class, 'searchProduct'])->name('transactions.search-product');
        Route::get('/transactions/get-product-barcode/{barcode}', [TransactionController::class, 'getProductByBarcode'])->name('transactions.get-product-barcode');
    });

    Route::middleware('level:1,2,3,4')->group(function () {
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // Test route untuk membuat transaksi dummy
        Route::get('/test-create-transaction', function() {
            $product = \App\Models\Product::first();
            if (!$product) {
                return "No products found in database";
            }
            
            $transaction = \App\Models\Transaction::create([
                'user_id' => auth()->id(),
                'total_amount' => $product->selling_price,
                'cash_amount' => $product->selling_price,
                'change_amount' => 0,
                'invoice_number' => 'INV-' . date('YmdHis') . '-TEST',
                'notes' => 'Test transaction',
                'status' => 'completed'
            ]);
            
            \App\Models\TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->selling_price,
                'subtotal' => $product->selling_price
            ]);
            
            return "Test transaction created! Total transactions: " . \App\Models\Transaction::count();
        });
    });

    // Test route di luar middleware untuk debugging
    Route::get('/test-returns-all', function() {
        $returns = \App\Models\ReturnTransaction::with(['transaction.user', 'user'])->get();
        return response()->json($returns);
    });
    
    Route::middleware('level:3')->group(function () {
        Route::resource('returns', ReturnController::class);
        Route::put('/returns/{returnTransaction}/approve', [ReturnController::class, 'approve'])->name('returns.approve');
        Route::put('/returns/{returnTransaction}/reject', [ReturnController::class, 'reject'])->name('returns.reject');
        Route::get('/returns/get-transaction-items/{transactionId}', [ReturnController::class, 'getTransactionItems'])->name('returns.get-transaction-items');
    });

    Route::middleware('level:1,2')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    Route::middleware('level:5')->group(function () {
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/financial', [ReportController::class, 'financial'])->name('reports.financial');
        Route::get('/reports/products', [ReportController::class, 'inventory'])->name('reports.products');
        Route::get('/reports/stock-in', [ReportController::class, 'stockIn'])->name('reports.stock-in');
        Route::get('/reports/stock-out', [ReportController::class, 'stockOut'])->name('reports.stock-out');
        Route::get('/reports/return', [ReportController::class, 'returnReport'])->name('reports.return');
    });

    Route::middleware('level:1')->group(function () {
        Route::get('/system-backup', [DashboardController::class, 'backup'])->name('system.backup');
        Route::post('/system-backup', [DashboardController::class, 'doBackup'])->name('system.backup.do');
    });
});
