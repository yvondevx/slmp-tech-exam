<?php

use App\Http\Controllers\Api\DataController;
use Illuminate\Support\Facades\Route;

Route::middleware('api.key')->group(function () {
    Route::get('users', [DataController::class, 'users']);
    Route::get('posts', [DataController::class, 'posts']);
    Route::get('comments', [DataController::class, 'comments']);
    Route::get('albums', [DataController::class, 'albums']);
    Route::get('photos', [DataController::class, 'photos']);
    Route::get('todos', [DataController::class, 'todos']);
    Route::get('all', [DataController::class, 'all']);
});
