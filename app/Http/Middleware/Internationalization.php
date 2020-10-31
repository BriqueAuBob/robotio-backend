<?php

namespace App\Http\Middleware;
use Illuminate\Foundation\Application;

use Closure;

class Internationalization
{
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $request->header("Content-Language");
        if(!$locale){
            $locale = $this->app->config->get("app.locale");
        }
        $this->app->setLocale($locale);

        $response = $next($request);
        $response->headers->set("Content-Language", $locale);

        return $response;
    }
}
