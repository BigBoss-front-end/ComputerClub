<?php

use App\Http\Controllers\API\BookingController;
use App\Http\Controllers\API\ClientController;
use App\Http\Controllers\API\ComputerController;
use App\Http\Controllers\API\StatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/computers/list', [ComputerController::class, 'list']);
Route::post('/computers/one', [ComputerController::class, 'one']);
Route::post('/computers/create', [ComputerController::class, 'create']);
Route::post('/computers/update', [ComputerController::class, 'update']);
Route::post('/computers/delete', [ComputerController::class, 'delete']);

Route::post('/bookings/list', [BookingController::class, 'list']);
Route::post('/bookings/one', [BookingController::class, 'one']);
Route::post('/bookings/create', [BookingController::class, 'create']);
Route::post('/bookings/update', [BookingController::class, 'update']);
Route::post('/bookings/delete', [BookingController::class, 'delete']);

Route::post('/clients/list', [ClientController::class, 'list']);
Route::post('/clients/one', [ClientController::class, 'one']);
Route::post('/clients/create', [ClientController::class, 'create']);
Route::post('/clients/update', [ClientController::class, 'update']);
Route::post('/clients/delete', [ClientController::class, 'delete']);

Route::post('/statuses/list', [StatusController::class, 'list']);
Route::post('/statuses/one', [StatusController::class, 'one']);