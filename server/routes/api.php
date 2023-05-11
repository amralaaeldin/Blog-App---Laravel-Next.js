<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;

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
Route::get('tags', [TagController::class, 'index']);
Route::middleware(['auth:sanctum'])
    ->get('tags/{tag}', [TagController::class, 'getPostsOnTag']);

// comments routes
Route::middleware(['auth:sanctum', 'is_accepted'])
    ->post('posts/{postId}/comments', [CommentController::class, 'store']);
Route::middleware(['auth:sanctum', 'is_owner:comment'])
    ->patch('posts/{postId}/comments/{id}', [CommentController::class, 'update']);
Route::middleware(['auth:sanctum', 'is_owner_or_can:comment,admin,super-admin'])
    ->delete('posts/{postId}/comments/{id}', [CommentController::class, 'destroy']);

// posts routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::get('posts/{id}/comments', [PostController::class, 'show']);
    Route::middleware(['is_accepted'])
        ->post('posts', [PostController::class, 'store']);
    Route::middleware(['is_owner:post'])
        ->patch('posts/{id}', [PostController::class, 'update']);
    Route::middleware(['is_owner_or_can:post,admin,super-admin'])
        ->delete('posts/{id}', [PostController::class, 'destroy']);
});

// users routes
Route::group(['middleware' => ['auth:sanctum', 'can:accept-users']], function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/accept', [UserController::class, 'indexPending']);
    Route::patch('users/{id}/accept', [UserController::class, 'accept']);
});

Route::group(['middleware' => ['auth:sanctum', 'is_yourself_or_can:admin,super_admin', 'is_it_user']], function () {
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::patch('users/{id}', [UserController::class, 'update']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});

// super_admin routes 
Route::group(['middleware' => ['auth:sanctum', 'is_yourself_or_can:super_admin']], function () {
    Route::get('admins', [AdminController::class, 'index']);
    Route::get('admins/{id}', [AdminController::class, 'show']);
    Route::post('admins', [AdminController::class, 'create']);
    Route::patch('admins/{id}', [AdminController::class, 'update']);
    Route::delete('admins/{id}', [AdminController::class, 'destroy']);
});


require __DIR__ . '/auth-api.php';
