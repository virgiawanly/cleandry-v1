<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
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

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {
    Route::get('/', [PageController::class, 'dashboard'])->name('pages.dashboard');

    Route::get('/select-outlet', [OutletController::class, 'selectOutlet'])->name('outlets.select');
    Route::post('/select-outlet', [OutletController::class, 'setOutlet']);
    Route::get('/outlets/data', [OutletController::class, 'data'])->name('outlets.data');
    Route::get('/outlets/datatable', [OutletController::class, 'datatable'])->name('outlets.datatable');
    Route::apiResource('/outlets', OutletController::class);

    Route::get('/users/data', [UserController::class, 'data'])->name('users.data');
    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::apiResource('/users', UserController::class);

    Route::get('/members/datatable', [MemberController::class, 'datatable'])->name('members.datatable');
    Route::apiResource('/members', MemberController::class);

    Route::get('/transactions/new-transaction', [TransactionController::class, 'newTransaction']);
    Route::post('/transactions', [TransactionController::class, 'store']);
});

Route::middleware(['auth', 'outlet'])->prefix('/o/{outlet}')->group(function () {
    Route::get('/services/datatable', [ServiceController::class, 'datatable'])->name('services.datatable');
    Route::apiResource('/services', ServiceController::class);
});
