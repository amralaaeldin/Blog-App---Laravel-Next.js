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
Route::post('tags', [TagController::class, 'index']);

// comments routes
Route::middleware(['auth:sanctum'])
    ->post('posts/{postId}/comments', [CommentController::class, 'store']);
Route::middleware(['auth:sanctum', 'is_owner:comment'])
    ->patch('posts/{postId}/comments/{id}', [CommentController::class, 'update']);
Route::middleware(['is_owner_or_can:comment'])
    ->delete('posts/{postId}/comments/{id}', [CommentController::class, 'destroy']);

// posts routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::middleware(['is_accepted'])
        ->post('posts', [PostController::class, 'store']);
    Route::middleware(['is_owner:post'])
        ->patch('posts/{id}', [PostController::class, 'update']);
    Route::middleware(['is_owner_or_can:post'])
        ->delete('posts/{id}', [PostController::class, 'destroy']);
});

// users routes
Route::middleware(['auth:sanctum', 'is_yourself_or_can'])
    ->apiResource('users', UserController::class)
    ->only(['show', 'update', 'destroy']);

Route::group(['middleware' => ['auth:sanctum', 'can:accept-users']], function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/accept', [UserController::class, 'indexPending']);
    Route::patch('users/{id}/accept', [UserController::class, 'accept']);
});

require __DIR__ . '/auth-api.php';
