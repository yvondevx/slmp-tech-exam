<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $providedApiKey = $request->header('X-API-KEY');
        $expectedApiKey = config('app.api_key');

        if (!is_string($providedApiKey) || $providedApiKey === '' || !is_string($expectedApiKey) || $expectedApiKey === '') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if (!hash_equals($expectedApiKey, $providedApiKey)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
