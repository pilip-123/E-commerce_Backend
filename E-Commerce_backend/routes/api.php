<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SocialAuthController as ApiSocialAuthController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PromotionController as ApiPromotionController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\NotificationController as ApiNotificationController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Middleware\ApiTokenMiddleware;

/*
|--------------------------------------------------------------------------
| Public Routes (no authentication required)
|--------------------------------------------------------------------------
*/

// ─── Auth ───────────────────────────────────────────────────────────────
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login',    [AuthController::class, 'login']);

// Social Auth (API / stateless)
Route::prefix('auth')->group(function () {
    Route::get('/{provider}/redirect', [ApiSocialAuthController::class, 'redirect']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback']);
});

// ─── Catalog (browsable without login) ──────────────────────────────────
Route::get('/categories',            [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);
Route::get('/products',               [ProductController::class, 'index']);
Route::get('/products/{product}',     [ProductController::class, 'show']);
Route::get('/promotions/active',         [ApiPromotionController::class, 'active']);

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Bearer token required)
|--------------------------------------------------------------------------
*/

Route::middleware(ApiTokenMiddleware::class)->group(function () {

    // ─── Auth ───────────────────────────────────────────────────────────
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // ─── Profile ────────────────────────────────────────────────────────
    Route::get('/profile',            [ProfileController::class, 'show']);
    Route::patch('/profile',          [ProfileController::class, 'update']);
    
    // Use POST with _method=PATCH for image uploads via FormData:
    // POST /api/profile _method=PATCH + image file

    // ─── Wishlist ───────────────────────────────────────────────────────
    Route::get('/wishlist',             [WishlistController::class, 'index']);
    Route::post('/wishlist',            [WishlistController::class, 'store']);
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy']);

    // ─── Cart ───────────────────────────────────────────────────────────
    Route::get('/cart',          [CartController::class, 'index']);
    Route::post('/cart',         [CartController::class, 'store']);
    Route::patch('/cart/{cart}', [CartController::class, 'update']);
    Route::delete('/cart/{cart}', [CartController::class, 'destroy']);
    Route::delete('/cart',       [CartController::class, 'clear']);

    // ─── Discount ───────────────────────────────────────────────────────
    Route::post('/check-discount', function (\Illuminate\Http\Request $request) {
        $request->validate(['code' => 'required|string']);

        $code = strtoupper(trim($request->code));

        $discount = \App\Models\DiscountCode::where('code', $code)->first();

        if (!$discount) {
            // Check if user has a notification with this code (legacy)
            $hasNotification = $request->user()->notifications()
                ->where('data', 'like', '%' . $code . '%')
                ->exists();

            if (!$hasNotification) {
                return response()->json(['message' => 'Invalid or expired code.'], 422);
            }

            // Fallback legacy: 10% off
            return response()->json([
                'type' => 'percentage',
                'value' => 10,
                'code' => $code,
            ]);
        }

        if (!$discount->isValid()) {
            return response()->json(['message' => 'This discount code has already been used.'], 422);
        }

        return response()->json([
            'type' => $discount->discount_type,
            'value' => (float) $discount->discount_value,
            'code' => $discount->code,
        ]);
    });

    // ─── Checkout ───────────────────────────────────────────────────────
    Route::get('/checkout',     [CheckoutController::class, 'checkout']);
    Route::post('/checkout',    [CheckoutController::class, 'store']);

    // ─── Orders ─────────────────────────────────────────────────────────
    Route::get('/orders',         [OrderController::class, 'index']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);

    // ─── Reviews ────────────────────────────────────────────────────────
    Route::get('/reviews',     [ReviewController::class, 'index']);
    Route::post('/reviews',    [ReviewController::class, 'store']);

    // ─── Notifications ───────────────────────────────────────────────────
    Route::get('/notifications',                [ApiNotificationController::class, 'index']);
    Route::post('/notifications/{id}/read',     [ApiNotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all',      [ApiNotificationController::class, 'markAllAsRead']);
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Bearer token + admin role required)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->middleware([ApiTokenMiddleware::class, 'permission:dashboard.view'])
    ->group(function () {

        // ─── Dashboard ──────────────────────────────────────────────────
        Route::get('/dashboard', function () {
            return response()->json([
                'stats' => [
                    'orders'        => 0,
                    'revenue'       => 0,
                    'products'      => 0,
                    'customers'     => 0,
                    'pendingOrders' => 0,
                ],
                'recentOrders'  => [],
                'recentProducts' => [],
            ]);
        });

        // ─── Categories CRUD ────────────────────────────────────────────
        Route::post('/categories',                     [CategoryController::class, 'store']);
        Route::patch('/categories/{category}',    [CategoryController::class, 'update']);
        Route::delete('/categories/{category}',   [CategoryController::class, 'destroy']);

        // ─── Products CRUD ──────────────────────────────────────────────
        Route::post('/products',                       [ProductController::class, 'store']);
        Route::put('/products/{product}',         [ProductController::class, 'update']);
        Route::delete('/products/{product}',      [ProductController::class, 'destroy']);

        // ─── Promotions CRUD ────────────────────────────────────────────
        Route::get('/promotions',                    [ApiPromotionController::class, 'index']);
        Route::post('/promotions',                   [ApiPromotionController::class, 'store']);
        Route::get('/promotions/{promotion}',        [ApiPromotionController::class, 'show']);
        Route::put('/promotions/{promotion}',        [ApiPromotionController::class, 'update']);
        Route::delete('/promotions/{promotion}',     [ApiPromotionController::class, 'destroy']);

        // ─── Notifications ─────────────────────────────────────────────
        Route::post('/notifications/announcement',   [AdminNotificationController::class, 'sendAnnouncement']);
    });
