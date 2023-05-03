<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;

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

Route::middleware(['auth:sanctum', 'role:admin'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('comments', CommentController::class);
Route::apiResource('posts', PostController::class);
Route::apiResource('tags', TagController::class);
Route::apiResource('users', UserController::class);

require __DIR__ . '/auth-api.php';
