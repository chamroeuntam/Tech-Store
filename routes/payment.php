
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payment\PaymentController;
use App\Http\Middleware\AdminMiddleware;

Route::post('/payment/calculate-order-totals', [PaymentController::class, 'calculateOrderTotals'])->name('payment.calculate_order_totals');

Route::middleware(['auth', AdminMiddleware::class])
    ->prefix('dashboard')
    ->group(function () {
        Route::resource('payments', PaymentController::class)->names([
            'index'   => 'dashboard.payment.index',
            'create'  => 'dashboard.payment.create',
            'store'   => 'dashboard.payment.store',
            'edit'    => 'dashboard.payment.edit',
            'update'  => 'dashboard.payment.update',
            'destroy' => 'dashboard.payment.destroy',
            'show'    => 'dashboard.payment.show',
        ]);
    });
