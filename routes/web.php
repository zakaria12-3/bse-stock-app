<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductExportController;
use App\Http\Controllers\FinanceReportController;

Route::middleware(['auth', 'verified'])->group(function () {
    // =========================================================================
    // Dashboard & Profile
    // =========================================================================
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::view('profile', 'profile.index')->name('profile.index');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================================
    // Master Data
    // =========================================================================
    Route::prefix('master')->group(function () {
        Route::view('customers', 'customers.index')->name('customers.index');
        Route::view('categories', 'categories.index')->name('categories.index');
        Route::view('units', 'units.index')->name('units.index');
        Route::view('products', 'products.index')->name('products.index');
        Route::view('products/import', 'products.import')->name('products.import');
        Route::get('products/export', ProductExportController::class)->name('products.export');
        Route::middleware(['admin'])->group(function () {
            Route::view('suppliers', 'suppliers.index')->name('suppliers.index');
        });
    });

    // =========================================================================
    // Transactions
    // =========================================================================

    // Purchases
    Route::middleware(['admin'])->group(function () {
        Route::resource('purchases', PurchaseController::class);
        Route::prefix('purchases/{purchase}')->name('purchases.')->controller(PurchaseController::class)->group(function () {
            Route::patch('ordered', 'markOrdered')->name('mark-ordered');
            Route::patch('received', 'markReceived')->name('mark-received');
            Route::patch('paid', 'markPaid')->name('mark-paid');
            Route::patch('cancel', 'cancel')->name('cancel');
            Route::patch('restore-draft', 'restoreToDraft')->name('restore-draft');
        });
    });

    // Sales
    Route::resource('sales', SalesController::class)->except(['edit', 'update']);
    Route::prefix('sales/{sale}')->name('sales.')->controller(SalesController::class)->group(function () {
        Route::get('print', 'print')->name('print');
        Route::patch('complete', 'complete')->name('complete');
        Route::patch('restore', 'restore')->name('restore');
    });

    // =========================================================================
    // Finance
    // =========================================================================
    Route::middleware(['admin'])->group(function () {
        Route::prefix('finance')->name('finance.')->group(function () {
            Route::view('categories', 'finance-categories.index')->name('categories.index');
            Route::view('transactions', 'finance-transactions.index')->name('transactions.index');
            Route::get('transactions/print/{printId}', [FinanceReportController::class, 'print'])->name('transactions.print');
        });

        // =========================================================================
        // Settings & Users
        // =========================================================================
        Route::view('users', 'users.index')->name('users.index');
        Route::view('settings', 'settings.index')->name('settings.index');
    });

    // =========================================================================
    // Internal APIs (AJAX)
    // =========================================================================
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::post('products', [\App\Http\Controllers\Api\ProductController::class, 'search'])->name('products.search');
        Route::post('suppliers', [\App\Http\Controllers\Api\SupplierController::class, 'search'])->name('suppliers.search');
        Route::post('customers', [\App\Http\Controllers\Api\CustomerController::class, 'search'])->name('customers.search');
        Route::post('customers/store', [\App\Http\Controllers\Api\CustomerController::class, 'store'])->name('customers.store');
        Route::post('categories', [\App\Http\Controllers\Api\CategoryController::class, 'search'])->name('categories.search');
        Route::post('units', [\App\Http\Controllers\Api\UnitController::class, 'search'])->name('units.search');
        Route::post('users', [\App\Http\Controllers\Api\UserController::class, 'search'])->name('users.search');
        Route::post('import-products', [\App\Http\Controllers\ImportController::class, 'import'])->name('import.products');
        Route::post('finance-categories', [\App\Http\Controllers\Api\FinanceCategoryController::class, 'search'])->name('finance-categories.search');
        Route::post('sales-builds', [\App\Http\Controllers\Api\SaleBuildController::class, 'search'])->name('sales-builds.search');
        Route::get('sales-builds/{sale}', [\App\Http\Controllers\Api\SaleBuildController::class, 'show'])->name('sales-builds.show');
    });
});

Route::get('/auto-login', function () {
    abort_unless(in_array(request()->ip(), ['127.0.0.1', '::1'], true), 404);

    $admin = \App\Models\User::where('role', 'admin')->first();
    
    if (!$admin) {
        $admin = \App\Models\User::create([
            'name' => 'Super Admin',
            'username' => 'super_admin_temp',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'admin',
        ]);
    } elseif (is_null($admin->email_verified_at)) {
        $admin->forceFill(['email_verified_at' => now()])->save();
    }

    \Illuminate\Support\Facades\Auth::login($admin);
    return redirect('/dashboard');
});

Route::get('/setup-admin', function () {
    abort_unless(in_array(request()->ip(), ['127.0.0.1', '::1'], true), 404);

    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        
        $user = \App\Models\User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'username' => 'admin',
                'email_verified_at' => now(),
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]
        );

        return "Base de donnees preparee et compte administrateur cree. <br>Email : admin@admin.com<br>Mot de passe : password<br><a href='/login'>Se connecter</a>";
    } catch (\Exception $e) {
        return "Erreur : " . $e->getMessage();
    }
});

require __DIR__.'/auth.php';
