<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Master Data
Route::get('materials/export', [MaterialController::class, 'export'])->name('materials.export');
Route::resource('materials', MaterialController::class);

Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
Route::resource('customers', CustomerController::class);

Route::get('inventory/movements/export', [StockMovementController::class, 'export'])->name('inventory.movements.export');
Route::get('inventory/movements', [StockMovementController::class, 'index'])->name('inventory.movements');

// Reports & Analytics
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
    Route::get('/finance', [ReportController::class, 'finance'])->name('finance');
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-payments', [ReportController::class, 'exportPayments'])->name('export.payments');
});

// Procurement
Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

// Orders & Lifecycle
Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
Route::resource('orders', OrderController::class)->except(['edit', 'update', 'destroy']);
Route::post('orders/{order}/start-production', [OrderController::class, 'startProduction'])->name('orders.start-production');
Route::post('orders/{order}/finish-production', [OrderController::class, 'finishProduction'])->name('orders.finish-production');
Route::post('orders/{order}/mark-delivered', [OrderController::class, 'markAsDelivered'])->name('orders.mark-delivered');
Route::get('orders/{order}/print', [OrderController::class, 'printInvoice'])->name('orders.print');
Route::post('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');

// Production Phase (Materials & Additional Costs)
Route::post('/orders/{order}/add-material', [OrderController::class, 'addMaterial'])->name('orders.add-material');
Route::delete('/order-materials/{orderMaterial}', [OrderController::class, 'removeMaterial'])->name('orders.remove-material');
Route::post('/orders/{order}/add-cost', [OrderController::class, 'addCost'])->name('orders.add-cost');
