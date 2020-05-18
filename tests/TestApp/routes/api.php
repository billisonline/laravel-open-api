<?php

use BYanelli\OpenApiLaravel\Tests\TestApp\app\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use TestApp\Http\Controllers\PostController;

Route::get('posts', [PostController::class, 'index']);
Route::post('posts', [PostController::class, 'store']);
Route::get('posts/{post}', [PostController::class, 'show']);
Route::put('posts/{post}', [PostController::class, 'update']);
Route::delete('posts/{post}', [PostController::class, 'destroy']);
Route::post('users/authenticate', [UserController::class, 'authenticate']);