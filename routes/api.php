<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MessageController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Custom API routes that work with web authentication
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/users/search', [UserController::class, 'search']);
    Route::post('/messages/bulk', [MessageController::class, 'sendBulk']);
}); 