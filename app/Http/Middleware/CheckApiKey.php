<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = env('TODOLIST_API_KEY', 'rahasia');
        $apiKey = $request->header('X-API-Key');

        if (empty($apiKey) || ($expectedKey !== '' && $apiKey !== $expectedKey)) {
            return response()->json([
                'message' => 'Unauthorized: Invalid or missing X-API-Key header',
            ], 401);
        }

        return $next($request);
    }
}
