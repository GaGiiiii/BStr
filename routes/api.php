<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\HelpController;
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

// Auth ==========================================================================================
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::get('/loggedIn', [UserController::class, 'loggedIn']);
// Auth ==========================================================================================


// Most Popular Products For Category ============================================================
Route::get('/categories/{category}/most-popular-posts', [HelpController::class, 'mostPopularPosts']);
// Most Popular Products For Category ============================================================


// User Points ===================================================================================
Route::get('/users/{user}/points', [HelpController::class, 'usersPoints']);
// User Points ===================================================================================

// Posts ==========================================================================================
Route::get('/posts', [PostController::class, 'index']);
// Posts ==========================================================================================

// comments ==========================================================================================
Route::get('/comments', [CommentController::class, 'index']);
// comments ==========================================================================================

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

  // Posts
  Route::post('/posts', [PostController::class, 'store']);
  Route::put('/posts/{post}', [PostController::class, 'update']);
  Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});
// PROTECTED =======================================================================================
