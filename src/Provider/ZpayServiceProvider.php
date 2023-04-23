<?php

namespace Saidtech\Zpay\Provider;

use Saidtech\Zpay\Zpay;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Saidtech\Zpay\Console\Commands\InstallCommand;
use Saidtech\Zpay\Http\Controllers\ZpayController;

class ZpayServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        // $this->publishes([
        //     __DIR__.'/../config/courier.php' => config_path('courier.php'),
        // ]);
        // $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        // $this->loadTranslationsFrom(__DIR__.'/../../lang', 'zpay');
        $this->loadViewsFrom(__DIR__.'/../views', 'zpay');

        
        $this->registerCommands();
        $this->defineRoutes();
        
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // $this->mergeConfigFrom(
        //     // __DIR__.'/../../config/zpay.php', 'zpay'
        // );
        

        if (! app()->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../../config/zpay.php', 'zpay');
        }

        $this->app->bind('zpay', function($app) {
            return new Zpay();
        });
    }

    /**
     * Define the zpay routes.
     *
     * @return void
     */
    protected function defineRoutes()
    {
        if (app()->routesAreCached() || config('zpay.routes') === false) {
            return;
        }

        Route::group($this->routeConfiguration(), function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        });
    }

    protected function routeConfiguration()
    {
        return [
            'prefix' => config('zpay.prefix'),
            'middleware' => config('zpay.guard'),
        ];
    }

     /**
     * Register Sanctum's migration files.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if (Zpay::shouldRunMigrations()) {
            return $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        }
    }

    /**
     * Register the console commands for the package.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->registerMigrations();

            $this->commands([
                // InstallCommand::class,
            ]);
        }
    }
    
}