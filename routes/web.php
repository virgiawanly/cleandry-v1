<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SimulationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->group(function () {
    // Dashboard page
    Route::get('/', [PageController::class, 'dashboard'])->name('pages.dashboard');

    // Import templates
    Route::get('/download/template/services', [ServiceController::class, 'downloadTemplate'])->name('services.template.download');
    Route::get('/download/template/inventories', [InventoryController::class, 'downloadTemplate'])->name('inventories.template.download');
    Route::get('/download/template/members', [MemberController::class, 'downloadTemplate'])->name('members.template.download');

    // Logout
    Route::middleware('auth')->post('/logout', [AuthController::class, 'logout']);
});

// Admin only
Route::middleware('auth', 'role:admin')->group(function () {
    // Outlets management
    Route::get('/outlets/data', [OutletController::class, 'data'])->name('outlets.data');
    Route::get('/outlets/datatable', [OutletController::class, 'datatable'])->name('outlets.datatable');
    Route::apiResource('/outlets', OutletController::class);

    // Users management
    Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::apiResource('/users', UserController::class);

    // Select outlet for admin
    Route::get('/select-outlet', [OutletController::class, 'selectOutlet'])->name('outlets.select');
    Route::post('/select-outlet', [OutletController::class, 'setOutlet']);

    // Inventories management
    Route::get('/inventories/datatable', [InventoryController::class, 'datatable'])->name('inventories.datatable');
    Route::post('/inventories/import/excel', [InventoryController::class, 'importExcel'])->name('inventories.import.excel');
    Route::get('/inventories/export/excel', [InventoryController::class, 'exportExcel'])->name('inventories.export.excel');
    Route::get('/inventories/export/pdf', [InventoryController::class, 'exportPDF'])->name('inventories.export.pdf');
    Route::apiResource('/inventories', InventoryController::class);
});

Route::middleware('auth', 'outlet')->prefix('/o/{outlet}')->group(function () {
    Route::redirect('/', '/');
});

Route::middleware('auth', 'role:admin', 'outlet')->prefix('/o/{outlet}')->group(function () {
    // Services management
    Route::get('/services/datatable', [ServiceController::class, 'datatable'])->name('services.datatable');
    Route::post('/services/import/excel', [ServiceController::class, 'importExcel'])->name('services.import.excel');
    Route::get('/services/export/excel', [ServiceController::class, 'exportExcel'])->name('services.export.excel');
    Route::get('/services/export/pdf', [ServiceController::class, 'exportPDF'])->name('services.export.pdf');
    Route::apiResource('/services', ServiceController::class);
});

Route::middleware('auth', 'role:admin,cashier', 'outlet')->prefix('/o/{outlet}')->group(function () {
    // Members management
    Route::get('/members/datatable', [MemberController::class, 'datatable'])->name('members.datatable');
    Route::post('/members/import/excel', [MemberController::class, 'importExcel'])->name('members.import.excel');
    Route::get('/members/export/excel', [MemberController::class, 'exportExcel'])->name('members.export.excel');
    Route::get('/members/export/pdf', [MemberController::class, 'exportPDF'])->name('members.export.pdf');
    Route::apiResource('/members', MemberController::class);

    // New transaction page
    Route::get('/transactions/new-transaction', [TransactionController::class, 'newTransaction']);
    Route::get('/transactions/new-transaction/get-services', [ServiceController::class, 'datatable'])->name('newTransaction.services');
    Route::get('/transactions/new-transaction/get-members', [MemberController::class, 'datatable'])->name('newTransaction.members');

    // Transaction report
    Route::get('/transactions/report', [TransactionController::class, 'report'])->name('transactions.report');
    Route::get('/transactions/report/get-report', [TransactionController::class, 'getReport'])->name('transactions.getReport');

    // Transaction histories page
    Route::get('/transactions/datatable', [TransactionController::class, 'datatable'])->name('transactions.datatable');
    Route::put('/transactions/{transaction}/status', [TransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
    Route::put('/transactions/{transaction}/payment', [TransactionController::class, 'updatePayment'])->name('transactions.updatePayment');
    Route::get('/transactions/{transaction}/invoice', [TransactionController::class, 'invoice'])->name('transactions.invoice');
    Route::get('/transactions/{transaction}/invoice/pdf', [TransactionController::class, 'invoicePDF'])->name('transactions.invoicePDF');
    Route::get('/transactions/{transaction}/whatsapp', [TransactionController::class, 'sendWhatsapp'])->name('transactions.sendWhatsapp');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
});

Route::middleware('auth', 'role:admin')->group(function () {
    // Algorithm
    Route::get('/simulation/employee', [SimulationController::class, 'employee']);
    Route::get('/simulation/fee', [SimulationController::class, 'fee']);
    Route::get('/simulation/books', [SimulationController::class, 'books']);
});
