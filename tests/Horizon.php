<?php

namespace NetLinker\FairQueue\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use NetLinker\FairQueue\Tests\Stubs\User;


class Horizon extends TestCase
{


    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->withFactories(__DIR__ . '/database/factories');
        $this->loadLaravelMigrations();

        Artisan::call('cache:clear');
        Redis::command('flushdb');
    }

    /**
     * @test
     *
     * @throws \Throwable
     */
    public function horizon()
    {
        dump('install horizon');

        Artisan::call('horizon:install');

        dump('run horizon');
        Config::set('horizon.environments.local.supervisor-1.connection', 'fair-queue');
        Config::set('horizon.environments.production.supervisor-1.connection', 'fair-queue');
        Config::set('horizon.waits.fair-queue:default', 60);

        dump(base_path());
        Event::listen('*', function ($eventName, array $data) {

//            dump($data);
        });

        $config = config('horizon');
        Artisan::call('horizon');
    }

}
