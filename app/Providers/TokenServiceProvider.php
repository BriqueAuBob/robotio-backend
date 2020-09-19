<?php

namespace App\Providers;

use App\Guards\TokenGuard;
use App\Models\User;
use Auth;
use Illuminate\Support\ServiceProvider;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * @return void
     */
    public function register(): void
    {
        $this->registerGuard();
    }

    /**
     * Register the token guard.
     * @return void
     */
    private function registerGuard(): void
    {
        Auth::extend('gca-token', function ($app, $name, array $config) {
            return tap($this->makeGuard($config), function ($guard) use ($app) {
                $this->app->refresh('request', $guard, 'setRequest');
            });
        });
    }

    /**
     * Make an instance of the token guard.
     * @param array $config
     * @return TokenGuard
     */
    protected function makeGuard(array $config): TokenGuard
    {
        return new TokenGuard(
            Auth::createUserProvider($config['provider']),
            $this->app['request']
        );
    }
}
