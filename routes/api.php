<?php

use App\Http\Controllers\auth\AuthController;
use App\Http\Controllers\TodoController;
use App\Http\Controllers\UserController;
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


Route::controller(TodoController::class)->prefix("todo")->group(function() {
    Route::post('list','list');
    Route::post('create','create');
    Route::put('update/{id}','update');
    Route::get('get/{id}','get');
    Route::delete('delete/{id}','destroy');
    Route::post('/file-upload','fileUpload');
    Route::post('/export-data','export');
});


/**
 * Auth
 */
Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::get('/account-verify/{token}', 'verifyAccount');
    Route::post('login', 'login');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
    Route::post("login-with-mobile",'sendOtp')->name('sendOtp');
    Route::post("verify-otp",'verifyOtp');
});

/**
 * User
 */
Route::middleware(['auth:sanctum','throttle:1|30'])->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('list', 'list');
        Route::put('update', 'update');
        Route::get('get', 'get');
        Route::delete('delete','destroy');
        Route::get('logout', 'logout');
        Route::post('change-password', 'changePassword');
    });
});
