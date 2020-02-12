<?php

namespace NetLinker\FairQueue\Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Laravel\Dusk\Browser;
use NetLinker\FairQueue\Sections\Accounts\Models\Account;
use NetLinker\FairQueue\Sections\Applications\Models\Application;
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
            Auth::login(User::all()->last());
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
        Auth::login(User::all()->last());

        foreach (range(1, 10) as $number){
            dump('added ' . $number);
            TestStatusJob::dispatch([])->onQueue('test_status:owner');
        }



        $this->browse(function (Browser $browser) {

            TestHelper::maximizeBrowserToScreen($browser);
            $browser->visit('fair-queue/job-statuses');
            TestHelper::browserWatch($browser, false, ['test_status:owner']);
        });

        $this->assertTrue(true);
    }
}
