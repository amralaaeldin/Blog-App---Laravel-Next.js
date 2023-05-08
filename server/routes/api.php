<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;

use Illuminate\Support\Facades\Auth;


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

// tags routes
Route::post('tags', [TagController::class, 'store']);

Route::middleware(['auth:sanctum', 'role:admin'])->get('/user', function (Request $request) {
    return $request->user();
});

// comments routes
Route::apiResource('comments', CommentController::class)->only(['destroy', 'update']);
Route::get('posts/{postId}/comments', [CommentController::class, 'index']);
Route::post('posts/{postId}/comments', [CommentController::class, 'store']);

// posts routes
Route::apiResource('posts', PostController::class);

// users routes
Route::apiResource('users', UserController::class);

require __DIR__ . '/auth-api.php';
