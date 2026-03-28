<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $token = substr($authHeader, 7);
        $hashed = hash('sha256', $token);

        $user = User::where('api_token', $hashed)->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        auth()->setUser($user);

        return $next($request);
    }
}
