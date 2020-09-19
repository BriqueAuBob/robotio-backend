<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use DB;
use Illuminate\Http\Request;

class MemberHasReact
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::check() && Auth::user()->hasRole('user')) {
            return response()->json([
                'status' => 401,
                'message' => 'User should react to the welcome message before accessing this resource'
            ], 401);
        }
        return $next($request);
    }
}
