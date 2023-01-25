<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(
    [
        'middleware' => ['api'],
        'prefix' => 'auth',
    ],
    function () {
        Route::get('/logout', [UserController::class, 'logout']);
        Route::post('/registration', [UserController::class, 'registration']);
        Route::post('/login', [UserController::class, 'login']);
        Route::put('/change-password', [UserController::class, 'changePassword']);
    }
);

Route::group(
    [
        'middleware' => ['api'],
        'prefix' => 'user',
    ],
    function () {
        Route::get('/get-self-user', [UserController::class, 'getSelfUser']);
        Route::post('/create-transaction', [TransactionController::class, 'createTransaction']);
        Route::put('/update-self-user', [UserController::class, 'updateSelfUser']);
    }
);

Route::group(
    [
        'middleware' => ['api'],
        'prefix' => 'admin',
    ],
    function () {
        Route::get('/get-user-by-id/{id}', [UserController::class, 'getUserById']);
        Route::get('/get-all-users', [UserController::class, 'getAllUsers']);
        Route::get('/get-all-transactions', [TransactionController::class, 'getAllTransactions']);
        Route::get('/get-transaction-by-id/{id}', [TransactionController::class, 'getTransactionById']);
        Route::get('/export-transactions', [TransactionController::class, 'exportTransactions']);
    }
);

Route::group(
    [
        'middleware' => ['api'],
        'prefix' => 'car',
    ],
    function () {
        Route::get('/get-car-by-id/{id}', [CarController::class, 'getCarById']);
        Route::get('/get-all-cars', [CarController::class, 'getAllCars']);
        Route::get('/get-all-brands', [CarController::class, 'getAllBrands']);
        Route::get('/get-all-types', [CarController::class, 'getAllTypes']);
        Route::post('/create-car', [CarController::class, 'createCar']);
        Route::put('/update-car/{id}', [CarController::class, 'updateCar']);
        Route::delete('/delete-car/{id}', [CarController::class, 'deleteCar']);
    }
);



