<?php

namespace App\Providers;

use Schema;
use App\Http\Kernel;
use App\Models\User;
use GuzzleHttp\Client;
use App\Helpers\UrlParser;
use App\Observers\UserObserver;
use Illuminate\Contracts\View\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Container\Container;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() : void
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        $this->app->bind(UrlParser::class, static function (Container $app) {
            return new UrlParser(new Client());
        });
        $this->app->alias(UrlParser::class, 'url-parser');
    }

    /**
     * Bootstrap any application services.
     *
     * @param Factory $view
     * @param Dispatcher $events
     * @param Repository $config
     * @param Kernel $kernel
     * @return void
     */
    public function boot(Factory $view, Dispatcher $events, Repository $config, Kernel $kernel): void
    {
        // Remove PHP version from header
        header_remove('X-Powered-By');

        // Setting up carbon local to handle date format
        \Carbon\Carbon::setUTF8(true);
        \Carbon\Carbon::setLocale(config('app.locale'));
        setlocale(LC_ALL, config('app.locale'));

        // Set Schema default string length to avoid migration issues
        Schema::defaultStringLength(191);

        // Register Model observers
        $this->registerObserver();
    }

    /**
     * Function used to register every model observer
     */
    private function registerObserver() : void
    {
        User::observe(UserObserver::class);
    }
}
