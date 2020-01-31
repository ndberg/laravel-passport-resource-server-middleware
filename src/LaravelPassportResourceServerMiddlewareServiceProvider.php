<?php

namespace Ndberg\LaravelPassportResourceServerMiddleware;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Ndberg\LaravelPassportResourceServerMiddleware\Middleware\VerifyAccessToken;

class LaravelPassportResourceServerMiddlewareServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-passport-resource-server-middleware');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-passport-resource-server-middleware');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->aliasMiddleware(VerifyAccessToken::class, 'VerifyAccessToken');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-passport-resource-server-middleware.php'),
            ], 'config');

            $this->loadMigrationsFrom(__DIR__ . '/../resources/Migrations/');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-passport-resource-server-middleware'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-passport-resource-server-middleware'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-passport-resource-server-middleware'),
            ], 'lang');*/

            // Publishing Migrations
            // TODO -> does publish multiple times! WHY?
            if (! class_exists('CreateUsersTable')){
                $this->publishes([
                    __DIR__.'/../database/migrations/create_users_table.stub.php' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_users_table.php'),
                ], 'migrations');
            }

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-passport-resource-server-middleware');

        // Register the main class to use with the facade
        //$this->app->singleton('laravel-passport-resource-server-middleware', function () {
        //    return new LaravelPassportResourceServerMiddlewareServiceProvider();
        //});
    }

    /**
     * Register the Middleware to a middlewareGroup
     *
     * @param  string  $middleware
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function registerMiddleware($middleware)
    {
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('api', $middleware);
    }

    /**
     * Alias the Middleware that it can be added in the main project
     *
     * @param  string  $middleware
     * @param  string  $alias
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function aliasMiddleware(string $middleware, string $alias)
    {
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware($alias, $middleware);
    }
}
