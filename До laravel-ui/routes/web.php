<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\RecoveryController;
use App\Http\Controllers\Web\RegistrationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use function Ramsey\Uuid\v4;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [LoginController::class, 'index'])->name('login');

// Route::get('/recovery', [RecoveryController::class, 'index'])->name('recovery');

// Route::get('/registration', [RegistrationController::class, 'index'])->name('registration');

Auth::routes(['verify' => true]);

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

    Route::get('/logout', [AuthController::class, 'logout']);
});
