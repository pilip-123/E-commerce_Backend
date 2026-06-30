<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\AdminPageController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');

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

        Route::get('permissions', [AdminPageController::class, 'permissions'])->name('permissions')
            ->middleware(AdminMiddleware::class);
        Route::put('permissions', [AdminPageController::class, 'updatePermissions'])->name('permissions.update')
            ->middleware(AdminMiddleware::class);
    });

