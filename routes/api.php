<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;

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

Route::prefix('link')->group(function(){

    Route::post('create', [LinkController::class, 'create']);
    Route::get('mostClickedLinks', [LinkController::class, 'mostClickedLinks']);
    Route::get('linksByUser/{userId}', [LinkController::class, 'linksByUser']);
    //Route::post('')
});


