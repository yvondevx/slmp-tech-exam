<?php

use App\Http\Controllers\Api\DataController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json(['token' => $user->api_token]);
    });

    Route::middleware('api.token')->group(function () {
        Route::get('users', [DataController::class, 'users']);
        Route::get('posts', [DataController::class, 'posts']);
        Route::get('comments', [DataController::class, 'comments']);
        Route::get('albums', [DataController::class, 'albums']);
        Route::get('photos', [DataController::class, 'photos']);
        Route::get('todos', [DataController::class, 'todos']);
        Route::get('all', [DataController::class, 'all']);
    });
});
