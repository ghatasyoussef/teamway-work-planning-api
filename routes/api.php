<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserShiftController;
use Illuminate\Support\Facades\Route;

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

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
//
Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::group(['middleware' => ['adminauth']], function () {
        Route::post('users/make-admin', [UserController::class, 'makeAdmin']);
        Route::get('users/search', [UserController::class, 'search']);
        Route::get('shifts/search', [UserShiftController::class, 'search']);

        Route::apiResource('shifts', UserShiftController::class, ['except' => ['show', 'index']]);

    });

    Route::apiResource(
        'users', UserController::class,
    );

    Route::apiResource('shifts', UserShiftController::class, ['only' => ['show', 'index']]);

});
