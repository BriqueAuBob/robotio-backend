<?php

namespace App\Http\Middleware;

use Auth;
use Cache;
use Carbon\Carbon;
use Clockwork;
use Closure;
use Illuminate\Http\Request;
use jeremykenedy\LaravelLogger\App\Models\Activity;

class LastActivity
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check() && $user = Auth::user()) {
            $user->last_activity = Carbon::now();
            $user->save();
            Cache::put('user-is-online-' . Auth::user()->getKey(), true, Carbon::now()->addMinutes(5));
        }
        return $next($request);
    }
}
