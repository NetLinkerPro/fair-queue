<?php

namespace NetLinker\FairQueue\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use NetLinker\FairQueue\HorizonManager;
use NetLinker\FairQueue\Queues\QueueConfiguration;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;
use NetLinker\FairQueue\Sections\Queues\Models\Queue;
use NetLinker\FairQueue\Sections\Supervisors\Models\Supervisor;
use NetLinker\FairQueue\Tests\Mocks\TestJob;
use NetLinker\FairQueue\Tests\Mocks\TestStatusJob;
use NetLinker\FairQueue\Tests\Stubs\Owner;
use NetLinker\FairQueue\Tests\Stubs\User;

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
            Artisan::call('view:clear',[]);
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

        $owner = factory(Owner::class)->create();
        factory(User::class)->create(['owner_uuid' => $owner->uuid,]);
        Auth::login(User::all()->first());

        $horizon = factory(Horizon::class)->create();
        $supervisor = factory(Supervisor::class)->create();

        $queue1 = factory(Queue::class)->create([
            'horizon_uuid' => $horizon->uuid,
            'supervisor_uuid' => $supervisor->uuid,
            'queue' => 'fair_queue_test_job_status',
        ]);

        $queue2 = factory(Queue::class)->create([
            'horizon_uuid' => $horizon->uuid,
            'supervisor_uuid' => $supervisor->uuid,
            'queue' => 'default',
        ]);

        QueueConfiguration::$queuesResolver = function () use (&$queue1, &$queue2) {
            return [
               'fair_queue_test_job_status' => $queue1,
                'default' => $queue2,
            ];
        };
        HorizonManager::$horizonResolver = function () use (&$horizon) {
            return $horizon;
        };

        TestStatusJob::dispatch()->onQueue('fair_queue_test_job_status');

        $this->browse(function (Browser $browser) {

            TestHelper::maximizeBrowserToScreen($browser);
            $browser->visit('fair-queue/accesses');
            TestHelper::browserWatch($browser, false, ['fair_queue_test_job_status']);

        });

        $this->assertTrue(true);
    }
}
