<?php

use App\Http\Controllers\TodoController;
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
});