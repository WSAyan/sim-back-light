<?php

namespace App\Http\Middleware;

use App\Utils\ResponseFormatter;
use Closure;
use JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return ResponseFormatter::errorResponse(ERROR_TYPE_UNAUTHORIZED, 'Unauthorized', ['Token is Invalid']);
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return ResponseFormatter::errorResponse(ERROR_TYPE_UNAUTHORIZED, 'Unauthorized', ['Token is Expired']);
            } else {
                return ResponseFormatter::errorResponse(ERROR_TYPE_UNAUTHORIZED, 'Unauthorized', ['Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
