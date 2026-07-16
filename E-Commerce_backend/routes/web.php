<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PromotionController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    /** @var \App\Models\User|null $user */
    $user = auth()->user();

    if ($user) {
        $route = $user->hasPermission('dashboard.view') ? 'admin.dashboard' : 'dashboard';
        return redirect()->route($route);
    }

    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'km'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('language.switch');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');

Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.forgot');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.forgot.submit');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.reset.submit');

// Social Auth (web / session-based)
Route::prefix('auth')->name('auth.social.')->group(function () {
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('redirect');
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])->name('callback');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'permission:dashboard.view'])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [AdminDashboardController::class, 'getData'])->name('dashboard.data');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::post('/notifications/announcement', [NotificationController::class, 'sendAnnouncement'])->name('notifications.announcement');

        Route::get('promotions/vip-codes', [AdminPageController::class, 'vipCodes'])->name('promotions.vip-codes');
        Route::post('promotions/vip-codes/generate', [AdminPageController::class, 'generateVipCode'])->name('promotions.vip-codes.generate');
        Route::delete('promotions/vip-codes/{id}', [AdminPageController::class, 'deleteVipCode'])->name('promotions.vip-codes.delete');
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('products', ProductController::class);
        Route::resource('promotions', PromotionController::class);
        Route::resource('reviews', ReviewController::class)->only(['index', 'destroy']);
        Route::resource('orders', OrderController::class)->except(['show', 'create', 'store']);
        Route::resource('users', UserController::class)->except(['create', 'store']);
        Route::get('customers', [AdminPageController::class, 'customers'])->name('customers');
        Route::get('profile', [AdminPageController::class, 'profile'])->name('profile');
        Route::put('profile', [AdminPageController::class, 'updateProfile'])->name('profile.update');
        Route::post('profile/notifications', [AdminPageController::class, 'updateNotificationPreferences'])->name('notifications.preferences');

        Route::prefix('export')->name('export.')->group(function () {
            Route::get('products', [ExportController::class, 'products'])->name('products');
            Route::get('orders', [ExportController::class, 'orders'])->name('orders');
            Route::get('categories', [ExportController::class, 'categories'])->name('categories');
            Route::get('users', [ExportController::class, 'users'])->name('users');
            Route::get('reviews', [ExportController::class, 'reviews'])->name('reviews');
            Route::get('promotions', [ExportController::class, 'promotions'])->name('promotions');
            Route::get('customers', [ExportController::class, 'customers'])->name('customers');
            Route::get('vip-codes', [ExportController::class, 'vipCodes'])->name('vip-codes');
            Route::get('inventory-history', [ExportController::class, 'inventoryHistory'])->name('inventory-history');
        });

        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])->name('index');
            Route::get('stock-in', [InventoryController::class, 'stockInForm'])->name('stock-in');
            Route::post('stock-in', [InventoryController::class, 'stockIn'])->name('stock-in.store');
            Route::get('stock-out', [InventoryController::class, 'stockOutForm'])->name('stock-out');
            Route::post('stock-out', [InventoryController::class, 'stockOut'])->name('stock-out.store');
            Route::get('transfer', [InventoryController::class, 'transferForm'])->name('transfer');
            Route::post('transfer', [InventoryController::class, 'transfer'])->name('transfer.store');
            Route::get('adjustment', [InventoryController::class, 'adjustmentForm'])->name('adjustment');
            Route::post('adjustment', [InventoryController::class, 'adjustment'])->name('adjustment.store');
            Route::get('stock-count', [InventoryController::class, 'stockCountForm'])->name('stock-count');
            Route::post('stock-count', [InventoryController::class, 'stockCount'])->name('stock-count.store');
            Route::get('history', [InventoryController::class, 'history'])->name('history');
            Route::delete('history/clear', [InventoryController::class, 'clearHistory'])->name('history.clear');
            Route::get('valuation', [InventoryController::class, 'valuation'])->name('valuation');
        });

        Route::get('permissions', [AdminPageController::class, 'permissions'])->name('permissions')
            ->middleware(AdminMiddleware::class);
        Route::put('permissions', [AdminPageController::class, 'updatePermissions'])->name('permissions.update')
            ->middleware(AdminMiddleware::class);
    });

