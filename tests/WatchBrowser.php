<?php

namespace NetLinker\FairQueue\Tests;

use Illuminate\Queue\QueueManager;
use Illuminate\Queue\Worker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use Laravel\Horizon\MasterSupervisor;
use Laravel\Horizon\Supervisor;
use NetLinker\FairQueue\Tests\Mocks\TestStatusJob;
use NetLinker\FairQueue\Tests\Stubs\Owner;
use NetLinker\FairQueue\Tests\Stubs\User;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\WorkerOptions;

class WatchBrowser extends BrowserTestCase
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
        $this->withFactories(__DIR__ . '/../database/factories');
        $this->loadLaravelMigrations();

        Artisan::call('cache:clear');
        Redis::command('flushdb');
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        parent::refreshApplication();

        if (Schema::hasTable('users_test')) {
            Auth::login(User::all()->first());
        }

    }


    /**
     * @test
     *
     * @throws \Throwable
     */
    public function watch()
    {

        $horizon = factory(Horizon::class)->create();
        Config::set('fair-queue.horizon_uuid', $horizon->uuid);

        $owner = factory(Owner::class)->create();
        factory(User::class)->create(['owner_uuid' => $owner->uuid,]);
        Auth::login(User::all()->first());

        foreach (range(1, 2) as $number){
            TestStatusJob::dispatch([]);
        }
        foreach (range(1, 2) as $number){
            TestStatusJob::dispatch([])->onQueue('second');
        }

        $m = app()->make(MasterSupervisor::class);



        Artisan::call('queue:work', ['--queue' => 'default,second']);

        $this->browse(function (Browser $browser) {

            TestHelper::maximizeBrowserToScreen($browser);
            $browser->visit('fair-queue/accesses');
            TestHelper::browserWatch($browser, false, ['default', 'second']);

        });

        $this->assertTrue(true);
    }
}
