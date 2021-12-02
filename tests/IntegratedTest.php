<?php

namespace NetLinker\FairQueue\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use NetLinker\FairQueue\Sections\Horizons\Models\Horizon;
use NetLinker\FairQueue\Tests\Mocks\TestJob;
use NetLinker\FairQueue\Tests\Stubs\Company;
use NetLinker\FairQueue\Tests\Stubs\Owner;
use NetLinker\FairQueue\Tests\Stubs\User;

class IntegratedTest
{

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('cache:clear');
        Redis::command('flushdb');

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->withFactories(__DIR__ . '/database/factories');
    }

    public function test_fair_identifier_without_user()
    {
        $job = new TestJob();
        $job->modelId = 0;
        dispatch(($job)->onQueue('prestashop_update'));
        dispatch(($job)->onQueue('prestashop_update'));

        // Execute job with variable $modelId = 0;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        // Execute job with variable $modelId = 0;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        // Execute job with variable $modelId = 0;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        // Execute job with variable $modelId = 0;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);

        $this->assertEquals(2, Cache::get('test_job'));
    }

    public function test_fair_identifier_with_user()
    {
        $owner =  factory(Owner::class)->create();
        factory(User::class)->create(['owner_uuid'=> $owner->uuid]);

        $job = new TestJob();
        $job->modelId = 1;
        dispatch(($job)->onQueue('prestashop_update'));
        dispatch(($job)->onQueue('prestashop_update'));
        $job = new TestJob();
        $job->modelId = 0;
        dispatch(($job)->onQueue('prestashop_update'));

        // Execute job with variable $modelId = 1;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        // Execute job with variable $modelId = 1;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        // Execute job with variable $modelId = 1;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        // Execute job with variable $modelId = 1;
        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);

        $this->assertEquals(3, Cache::get('test_job'));
    }

    public function test_many_users()
    {
        $owner =  factory(Owner::class)->create();
        factory(User::class)->create(['owner_uuid'=> $owner->uuid]);

        $user1 = factory(User::class)->create(['owner_uuid'=> $owner->uuid]);
        $user2 =  factory(User::class)->create(['owner_uuid'=> $owner->uuid]);
        $user3 =  factory(User::class)->create(['owner_uuid'=> $owner->uuid]);

        foreach ([$user1, $user2, $user3] as $user) {

            foreach (range(1, 20) as $number) {

                $job = new TestJob();
                $job->modelId = $user->id;
                $job->handleSleep = 100;
                dispatch(($job)->onQueue('prestashop_update'));

            }

        }

        foreach (range(1, 20) as $number) {
            Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
        }

        $user4 = factory(User::class)->create(['owner_uuid'=> $owner->uuid]);

        foreach (range(1, 20) as $number) {

            $job = new TestJob();
            $job->modelId = $user4->id;
            $job->handleSleep = 100;
            dispatch(($job)->onQueue('prestashop_update'));

        }

       sleep(2); // for update max ID in cache (property `refresh_max_id` in config`)

        foreach (range(1, 60) as $number) {
            Artisan::call('queue:work', ['--once' => 'true', '--queue' => 'prestashop_update']);
        }

        $this->assertEquals(80, Cache::get('test_job'));
    }

}
