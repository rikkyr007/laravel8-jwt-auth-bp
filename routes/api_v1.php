<?php

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

Route::post('login', [App\Http\Controllers\Api\v1\Auth\AuthController::class, 'login'])->name('register');
Route::post('register', [App\Http\Controllers\Api\v1\Auth\AuthController::class, 'register'])->name('login');

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('logout', [App\Http\Controllers\Api\v1\Auth\AuthController::class, 'logout'])->name('logout');
    Route::post('get_user', [App\Http\Controllers\Api\v1\Auth\AuthController::class, 'getUser'])->name('getUser');
});
