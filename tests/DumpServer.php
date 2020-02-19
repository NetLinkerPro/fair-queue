<?php

namespace NetLinker\FairQueue\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;


class DumpServer extends TestCase
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
    public function dumpServer()
    {
        Artisan::call('dump-server');
    }

}
