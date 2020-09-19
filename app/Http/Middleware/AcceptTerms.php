<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AcceptTerms
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->accept_cgu) {
            return response()->json([
                'status' => 403,
                'message' => 'User must accept the terms of service to continue'
            ], 403);
        }

        return $next($request);
    }
}
