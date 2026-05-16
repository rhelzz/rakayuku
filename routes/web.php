<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\ClosingController;
use App\Http\Controllers\StockOpnameController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [FinanceController::class, 'index'])->name('index');
    Route::get('/cashflow', [FinanceController::class, 'cashflowDetail'])->name('cashflow');
    Route::post('/cashflow', [FinanceController::class, 'storeCashflow'])->name('cashflow.store');
    Route::get('/cashflow/export', [FinanceController::class, 'exportCashflow'])->name('cashflow.export');
    Route::get('/inventory', [FinanceController::class, 'inventoryDetail'])->name('inventory');
    Route::get('/receivables', [FinanceController::class, 'receivablesDetail'])->name('receivables');
    Route::get('/payables', [FinanceController::class, 'payablesDetail'])->name('payables');
    Route::get('/export-overall', [FinanceController::class, 'exportOverall'])->name('export.overall');
});

Route::prefix('closing')->name('closing.')->group(function () {
    Route::get('/', [ClosingController::class, 'index'])->name('index');
    Route::get('/{closing}', [ClosingController::class, 'show'])->name('show');
    Route::post('/close', [ClosingController::class, 'close'])->name('close');
    Route::post('/{closing}/reopen', [ClosingController::class, 'reopen'])->name('reopen');
    Route::get('/{closing}/export', [ClosingController::class, 'exportClosing'])->name('export');
});

Route::get('materials/export', [MaterialController::class, 'export'])->name('materials.export');
Route::resource('materials', MaterialController::class);

Route::get('customers/export', [CustomerController::class, 'export'])->name('customers.export');
Route::resource('customers', CustomerController::class);

Route::get('inventory/movements/export', [StockMovementController::class, 'export'])->name('inventory.movements.export');
Route::get('inventory/movements', [StockMovementController::class, 'index'])->name('inventory.movements');

Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
    Route::get('/', [StockOpnameController::class, 'index'])->name('index');
    Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
    Route::post('/', [StockOpnameController::class, 'store'])->name('store');
    Route::get('/{stockOpname}', [StockOpnameController::class, 'show'])->name('show');
    Route::post('/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('complete');
    Route::get('/{stockOpname}/export', [StockOpnameController::class, 'export'])->name('export');
});

Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/analytics', [ReportController::class, 'analytics'])->name('analytics');
    Route::get('/finance', [ReportController::class, 'finance'])->name('finance');
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export-payments', [ReportController::class, 'exportPayments'])->name('export.payments');
});

Route::get('purchases/export', [PurchaseController::class, 'export'])->name('purchases.export');
Route::post('purchases/{purchase}/pay', [PurchaseController::class, 'pay'])->name('purchases.pay');
Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');
Route::get('orders/export-receivables', [OrderController::class, 'exportReceivables'])->name('orders.export_receivables');
Route::get('orders/{order}/print', [OrderController::class, 'printInvoice'])->name('orders.print');
Route::resource('orders', OrderController::class)->except(['destroy']);
Route::post('orders/{order}/start-production', [OrderController::class, 'startProduction'])->name('orders.start-production');
Route::post('orders/{order}/finish-production', [OrderController::class, 'finishProduction'])->name('orders.finish-production');
Route::post('orders/{order}/mark-delivered', [OrderController::class, 'markAsDelivered'])->name('orders.mark-delivered');
Route::post('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::post('orders/{order}/pay', [OrderController::class, 'pay'])->name('orders.pay');

Route::post('/orders/{order}/add-material', [OrderController::class, 'addMaterial'])->name('orders.add-material');
Route::delete('/order-materials/{orderMaterial}', [OrderController::class, 'removeMaterial'])->name('orders.remove-material');
Route::post('/orders/{order}/add-cost', [OrderController::class, 'addCost'])->name('orders.add-cost');
Route::delete('/production-costs/{productionCost}', [OrderController::class, 'removeCost'])->name('orders.remove-cost');

Route::post('/orders/{order}/add-residue', [OrderController::class, 'addResidue'])->name('orders.add-residue');
Route::delete('/order-residues/{orderResidue}', [OrderController::class, 'removeResidue'])->name('orders.remove-residue');

