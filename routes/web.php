<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/* ===========================
    CONTROLLERS
=========================== */
use App\Models\User;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\UserMangement;
use App\Http\Controllers\Products\DashboardController;
use App\Http\Controllers\Products\CategoryController;
use App\Http\Controllers\Products\SliderController;
use App\Http\Controllers\Products\ProductController;
use App\Http\Controllers\OAuth\GoogleController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Cart\WishlistController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\OrderManagementController;
use App\Http\Controllers\Order\ShippingMethodController;

use App\Http\Controllers\Payment\PaymentMethodController;

use App\Http\Controllers\Payment\PaymentController;
use App\Http\Controllers\Admin\SalesReportController;

/* ===========================
    MIDDLEWARE
=========================== */
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\StaffMiddleware;


/* ===========================
    DEBUG DB ROUTE
=========================== */
Route::get('/debug-db', function () {
    try {
        $user = User::first();
        return $user ? "DB OK: {$user->email}" : "DB OK: No users found";
    } catch (\Exception $e) {
        return "DB ERROR: " . $e->getMessage();
    }
});


/* ===========================
    PUBLIC (NO LOGIN REQUIRED)
=========================== */

/* Home + Products */
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/store', [ProductController::class, 'index'])->name('store.home');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/ajax-filter', [ProductController::class, 'ajaxFilter'])->name('products.ajaxFilter');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

/* Cart */
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{productId}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{itemId}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{itemId}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/cart/destroy', [CartController::class, 'destroy'])->name('cart.destroy');


/* Wishlist */
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add/{productId}', [WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/remove/{itemId}', [WishlistController::class, 'destroy'])->name('wishlist.remove');

/* Google OAuth */
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


/* ===========================
   AUTHENTICATED USERS ONLY
=========================== */
Route::middleware(['auth'])->group(function () {

    /* User Profile */
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.user_profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Orders & Checkout */
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    // Support both UUID and numeric ID for order detail
    Route::get('/orders/{order_id}', [OrderController::class, 'show'])->name('orders.order_detail');
    Route::get('/orders/id/{id}', [OrderController::class, 'showById'])->name('orders.order_detail_by_id');
        Route::get('/checkout', [\App\Http\Controllers\Order\CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('/checkout/form', [OrderController::class, 'checkoutPage'])->name('checkout.form');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
    Route::get('/checkout/success/{order_id}', [OrderController::class, 'paymentSuccess'])->name('checkout.success');

    // User: Mark order as delivered (received)
    Route::post('/orders/delivered/{order_id}', [OrderController::class, 'markDelivered'])->name('orders.markDelivered');

});


/* ===========================
   STAFF + ADMIN
=========================== */
Route::middleware(['auth', StaffMiddleware::class])
    ->prefix('admin/reports')
    ->group(function () {
        Route::get('/order-report', [\App\Http\Controllers\Admin\SalesReportController::class, 'orderReport']);
        Route::get('/order-chart', [\App\Http\Controllers\Admin\SalesReportController::class, 'orderChart']);
        Route::get('/order-top-products', [\App\Http\Controllers\Admin\SalesReportController::class, 'orderTopProducts']);
    });

Route::middleware(['auth', StaffMiddleware::class])
    ->prefix('dashboard')
    ->group(function () {
        /* Categories */
        Route::resource('categories', CategoryController::class)
            ->names('dashboard.categories');
        /* Shipping Methods */
        Route::resource('shipping_methods', ShippingMethodController::class)
            ->names([
                'index'   => 'dashboard.shipping.index',
                'create'  => 'dashboard.shipping.create',
                'store'   => 'dashboard.shipping.store',
                'edit'    => 'dashboard.shipping.edit',
                'update'  => 'dashboard.shipping.update',
                'destroy' => 'dashboard.shipping.destroy',
                'show'    => 'dashboard.shipping.show',
            ]);
        Route::resource('payment-method', PaymentMethodController::class)
            ->names([
                'index'   => 'dashboard.payment-method.index',
                'create'  => 'dashboard.payment-method.create',
                'store'   => 'dashboard.payment-method.store',
                'edit'    => 'dashboard.payment-method.edit',
                'update'  => 'dashboard.payment-method.update',
                'destroy' => 'dashboard.payment-method.destroy',
                'show'    => 'dashboard.payment-method.show',
            ]);
    });

// ===============================
// ✅ ADMIN SALES REPORT ROUTES
// ===============================
Route::prefix('admin/reports')
    ->middleware(['auth'])
    ->name('report.')
    ->group(function () {

        // ✅ Report Dashboard / List
        Route::get('/', [SalesReportController::class, 'index'])
            ->name('index');

        // ✅ Generate Report (GET = Form, POST = Generate)
        Route::match(['get', 'post'], '/generate', [SalesReportController::class, 'generate'])
            ->name('generate');

        // ✅ Chart Data API
        Route::get('/chart', [SalesReportController::class, 'chart'])
            ->name('chart');

        // ✅ Export to Excel
        Route::get('/export/excel/{id}', [SalesReportController::class, 'exportExcel'])
            ->name('export.excel');

        // ✅ Export to PDF
        Route::get('/export/pdf/{id}', [SalesReportController::class, 'exportPDF'])
            ->name('export.pdf');
    });


/* ===========================
   ADMIN + STAFF DASHBOARD
=========================== */
Route::middleware(['auth', StaffMiddleware::class])
    ->prefix('dashboard')
    ->group(function () {

    /* Dashboard */
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.home');

    /* Product Management */
    Route::resource('products', ProductController::class)
        ->except(['index', 'show'])
        ->names('dashboard.products');

    /* Sliders */
    Route::resource('sliders', SliderController::class)->names('dashboard.sliders');

    /* User Management */
    Route::get('/management', [UserMangement::class, 'index'])->name('user.user_management');
    Route::get('/management/edit/{id}', [UserMangement::class, 'edit'])->name('user.edit_user');
    Route::post('/management/update/{id}', [UserMangement::class, 'update'])->name('user.update_user');
    Route::get('/management/delete/{id}', [UserMangement::class, 'destroy'])->name('user.destroy');

        /* Order Management */
        Route::get('/order/management', [OrderManagementController::class, 'adminDashboard'])
            ->name('admin.manage.orders');


        // Admin order detail route
        Route::get('/order/detail/{order_id}', [OrderManagementController::class, 'showOrderDetail'])
            ->name('orders.admin_order_detail');

        // Admin: Mark order as delivered
        Route::post('/order/delivered/{order_id}', [OrderManagementController::class, 'markOrderDelivered'])
            ->name('orders.admin_mark_delivered');

        // Admin: Update order status
        Route::post('/order/status/{order_id}', [OrderManagementController::class, 'updateOrderStatus'])
            ->name('orders.admin_update_status');
});


/* ===========================
    PAYMENT ROUTES (SEPARATE FILE)
=========================== */
Route::match(['get', 'post'], '/pay', [PaymentController::class,'create'])->name('pay.create');
Route::get('/pay/{invoice}', [PaymentController::class, 'view'])->name('pay.view');
Route::get('/pay-check/{invoice}', [PaymentController::class, 'check'])->name('pay.check');
Route::post('/pay/{invoice}/simulate-pay', [PaymentController::class, 'simulate'])->name('pay.simulate');
Route::get('/pay/{invoice}/success', [PaymentController::class, 'success'])->name('pay.success');


require __DIR__.'/payment.php';

/* Laravel Auth Default */
require __DIR__.'/auth.php';

