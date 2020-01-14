<?php

namespace Netlinker\FairQueue;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\ServiceProvider;
use Netlinker\FairQueue\Connectors\FairQueueConnector;
use Netlinker\FairQueue\Workers\FairQueueWorker;

class FairQueueServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'netlinker');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'netlinker');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        $this->registerFairQueueConnector();

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/fair-queue.php', 'fair-queue');

        // Register the service the package provides.
        $this->app->singleton('fair-queue', function ($app) {
            return new FairQueue;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['fair-queue'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {

        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/fair-queue.php' => config_path('fair-queue.php'),
        ], 'fair-queue.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/netlinker'),
        ], 'fair-queue.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/netlinker'),
        ], 'fair-queue.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/netlinker'),
        ], 'fair-queue.views');*/

        // Registering package commands.
        // $this->commands([]);
    }

    /**
     * Register in application fair queue connector
     */
    private function registerFairQueueConnector()
    {

        /** @var QueueManager $manager */
        $manager = $this->app['queue'];

        $manager->addConnector('fair-queue', function () {
            return new FairQueueConnector($this->app['redis']);
        });

    }
}
