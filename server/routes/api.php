<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminController;


// tags routes
Route::get('tags', [TagController::class, 'index']);
Route::middleware(['auth'])
    ->get('tags/{tag}', [TagController::class, 'indexPostsOnTag']);

// comments routes
Route::middleware(['auth', 'is_accepted'])
    ->post('posts/{postId}/comments', [CommentController::class, 'store']);
Route::middleware(['auth', 'is_owner:comment'])
    ->patch('posts/{postId}/comments/{id}', [CommentController::class, 'update']);
Route::middleware(['auth', 'is_owner_or_can:comment,admin,super_admin'])
    ->delete('posts/{postId}/comments/{id}', [CommentController::class, 'destroy']);

// posts routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('posts', [PostController::class, 'index']);
    Route::get('posts/{id}', [PostController::class, 'show']);
    Route::get('posts/{id}/comments', [PostController::class, 'show']);
    Route::middleware(['is_accepted'])
        ->post('posts', [PostController::class, 'store']);
    Route::middleware(['is_owner:post'])
        ->patch('posts/{id}', [PostController::class, 'update']);
    Route::middleware(['is_owner_or_can:post,admin,super_admin'])
        ->delete('posts/{id}', [PostController::class, 'destroy']);
});

// users routes
Route::group(['middleware' => ['auth', 'can:accept-users']], function () {
    Route::get('users', [UserController::class, 'index']);
    Route::get('users/pending', [UserController::class, 'indexPending']);
    Route::get('users/{id}', [UserController::class, 'show']);
    Route::patch('users/{id}/accept', [UserController::class, 'accept']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);
});

Route::group(['middleware' => ['auth', 'role:user']], function () {
    Route::get('u/profile', [UserController::class, 'show']);
    Route::patch('u/profile', [UserController::class, 'update']);
});

// super_admin routes 
Route::group(['middleware' => ['auth', 'role:super_admin']], function () {
    Route::get('admins', [AdminController::class, 'index']);
    Route::get('admins/{id}', [AdminController::class, 'show']);
    Route::post('admins', [AdminController::class, 'store']);
    Route::patch('admins/{id}', [AdminController::class, 'update']);
    Route::delete('admins/{id}', [AdminController::class, 'destroy']);
});

require __DIR__ . '/auth.php';
