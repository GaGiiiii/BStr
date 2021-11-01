<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
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

  // Posts
  Route::post('/posts', [PostController::class, 'store']);

  // Likes
  Route::post('/likes', [LikeController::class, 'store']);
  Route::delete('/likes/{rating}', [LikeController::class, 'destroy']);

  // Comments
  Route::post('/comments', [CommentController::class, 'store']);
  Route::put('/comments/{comment}', [CommentController::class, 'update']);
  Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
// PROTECTED =======================================================================================
