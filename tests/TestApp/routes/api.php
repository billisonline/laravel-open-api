<?php

use Illuminate\Support\Facades\Route;
use TestApp\Http\Controllers\PostController;

Route::get('posts', [PostController::class, 'index']);