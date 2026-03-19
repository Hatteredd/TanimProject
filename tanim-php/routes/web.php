<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderAdminController;
use App\Http\Controllers\Admin\ReviewAdminController;
use App\Http\Controllers\Admin\ProductAdminController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\DataController;

// ── Public ──────────────────────────────────────────────────────────
Route::get('/', [ProductController::class, 'home'])->name('home');

Route::get('/marketplace',        [ProductController::class, 'index'])->name('marketplace');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// ── Auth ─────────────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',   [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register',[AuthController::class, 'register']);
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Email Verification ───────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('success', 'Email verified! Welcome to Tanim.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// ── Authenticated (verified users) ───────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile',  [ProfileController::class, 'update'])->name('profile.update');

    // Cart
    Route::get('/cart',                     [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}',      [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}',[CartController::class, 'remove'])->name('cart.remove');

    // Orders
    Route::get('/checkout',         [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::post('/orders',          [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders',           [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',   [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'downloadReceipt'])->name('orders.receipt');

    // Reviews
    Route::post('/products/{product}/reviews',  [ReviewController::class, 'store'])->name('reviews.store');
    Route::patch('/reviews/{review}',           [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}',          [ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// ── Admin ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users
    Route::get('/users',                        [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create',                 [UserController::class, 'create'])->name('users.create');
    Route::post('/users',                       [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit',            [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}',                 [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',              [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/toggle-active',  [UserController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/role',           [UserController::class, 'updateRole'])->name('users.update-role');

    // Products (admin content management)
    Route::get('/products',                     [ProductAdminController::class, 'index'])->name('products.index');
    Route::get('/products/create',              [ProductAdminController::class, 'create'])->name('products.create');
    Route::post('/products',                    [ProductAdminController::class, 'store'])->name('products.store');
    Route::post('/products/import',             [ProductAdminController::class, 'import'])->name('products.import');
    Route::get('/products/{product}/edit',      [ProductAdminController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}',           [ProductAdminController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}',        [ProductAdminController::class, 'destroy'])->name('products.destroy');
    Route::delete('/products/{product}/photos/{photo}', [ProductAdminController::class, 'destroyPhoto'])->name('products.photos.destroy');
    Route::post('/products/{id}/restore',       [ProductAdminController::class, 'restore'])->name('products.restore');
    Route::delete('/products/{id}/force',       [ProductAdminController::class, 'forceDelete'])->name('products.force-delete');

    // Orders
    Route::get('/orders',                       [OrderAdminController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',               [OrderAdminController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/status',       [OrderAdminController::class, 'updateStatus'])->name('orders.update-status');

    // Reports & Analytics
    Route::get('/reports',                      [ReportController::class, 'index'])->name('reports');

    // Data Management
    Route::get('/data',                         [DataController::class, 'index'])->name('data.index');
    Route::get('/data/{table}',                 [DataController::class, 'viewTable'])->name('data.table');
    Route::get('/data/{table}/export',          [DataController::class, 'exportCsv'])->name('data.export');

    // Settings
    Route::get('/settings',                     [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings',                    [SettingsController::class, 'update'])->name('settings.update');

    // Activity Logs
    Route::get('/logs',                         [LogController::class, 'index'])->name('logs');
    Route::post('/logs/clear',                  [LogController::class, 'clear'])->name('logs.clear');

    // Charts
    Route::get('/charts',                       [OrderAdminController::class, 'charts'])->name('charts');
    Route::get('/charts/sales-range',           [OrderAdminController::class, 'salesByDateRange'])->name('charts.sales-range');

    // Reviews
    Route::get('/reviews',                      [ReviewAdminController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}',          [ReviewAdminController::class, 'destroy'])->name('reviews.destroy');

    // Expenses
    Route::get('/expenses',              [ExpenseController::class, 'index'])->name('expenses');
    Route::post('/expenses',             [ExpenseController::class, 'store'])->name('expenses.store');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    // Employees
    Route::get('/employees',               [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees',              [EmployeeController::class, 'store'])->name('employees.store');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});
