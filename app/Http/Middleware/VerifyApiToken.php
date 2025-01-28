<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiToken;

class VerifyApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization'); // assume the token is sent in the Authorization header
        
        // If token starts with "Bearer ", remove it
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        // Find the token in the database
        $apiToken = ApiToken::where('token', $token)->first();

        if (!$apiToken || ($apiToken->isExpired())) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Optionally, associate the request with the token (or user, if applicable)
        $request->attributes->set('apiToken', $apiToken);

        return $next($request);
    }
}
