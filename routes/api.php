<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

// Auth
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/loggedIn', [UserController::class, 'loggedIn']);


// PROTECTED ======================================================================================
Route::group(['middleware' => 'auth:sanctum'], function () {
  // Auth
  Route::post('/logout', [UserController::class, 'logout']);
});
// PROTECTED =======================================================================================
