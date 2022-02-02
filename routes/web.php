<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OutletController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServiceController;
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

    Route::get('/outlets/datatable', [OutletController::class, 'datatable'])->name('outlets.datatable');
    Route::apiResource('/outlets', OutletController::class);

    Route::get('/users/datatable', [UserController::class, 'datatable'])->name('users.datatable');
    Route::apiResource('/users', UserController::class);

    Route::get('/services/datatable', [ServiceController::class, 'datatable'])->name('services.datatable');
    Route::apiResource('/services', ServiceController::class);
});
