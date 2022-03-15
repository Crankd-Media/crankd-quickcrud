<?php

namespace Crankd\Quickcrud;

use Crankd\Quickcrud\Commands\ModuleCommand;
use Illuminate\Support\ServiceProvider;

class QuickcrudServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Crankd\Quickcrud\QuickcrudController');
        $this->loadViewsFrom(__DIR__ . '/views', 'quickcrud');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/routes.php';

        $this->app->bind('make:module', function ($app) {
            return new ModuleCommand();
        });
        $this->commands([
            'make:module',
        ]);

    }
}