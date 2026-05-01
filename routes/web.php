<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockMovementController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Master Data
Route::resource('materials', MaterialController::class);
Route::get('inventory/movements', [StockMovementController::class, 'index'])->name('inventory.movements');
Route::resource('customers', CustomerController::class);

// Procurement
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

// Orders & Lifecycle
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
