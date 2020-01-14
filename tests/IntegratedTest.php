<?php

namespace Netlinker\FairQueue\Tests;

use Illuminate\Cache\RedisStore;
use Illuminate\Queue\Console\WorkCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Netlinker\FairQueue\Tests\Mocks\TestJob;
use Netlinker\FairQueue\Tests\Stubs\User;
use Symfony\Component\Process\Process;

class IntegratedTest extends TestCase
{

    /**
     * Setup the test environment.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->withFactories(__DIR__ . '/database/factories');

    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('queue.default', 'fair-queue');
        $app['config']->set('fair-queue.models.user', 'Netlinker\FairQueue\Tests\Stubs\User');
        $app['config']->set('fair-queue.instance_config.queues.prestashop_update', [
            'user' => [
                'active' => true,
                'allow_ids' => [],
                'exclude_ids' => [],
            ]
        ]);

        $app['config']->set('queue.connections.fair-queue', [
            'driver' => 'fair-queue',
        ]);

    }

    public function test_fair_identifier_without_user()
    {
        Redis::command('flushdb');

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
        Redis::command('flushdb');

        factory(User::class)->create();

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
        Redis::command('flushdb');

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();

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

        $user4 = factory(User::class)->create();

        foreach (range(1, 20) as $number) {

            $job = new TestJob();
            $job->modelId = $user4->id;
            $job->handleSleep = 100;
            dispatch(($job)->onQueue('prestashop_update'));

        }

        foreach (range(1, 60) as $number) {
            Artisan::call('queue:work', ['--once' => 'true', '--queue' => 'prestashop_update']);
        }

//        $job = new TestJob();
//        $job->modelId = 1;
//        dispatch(($job)->onQueue('prestashop_update'));
//        dispatch(($job)->onQueue('prestashop_update'));
//        $job = new TestJob();
//        $job->modelId = 0;
//        dispatch(($job)->onQueue('prestashop_update'));
//
//        // Execute job with variable $modelId = 1;
//        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
//        // Execute job with variable $modelId = 1;
//        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
//        // Execute job with variable $modelId = 1;
//        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);
//        // Execute job with variable $modelId = 1;
//        Artisan::call('queue:work', ['--once' => true, '--queue' => 'prestashop_update']);

        $this->assertEquals(80, Cache::get('test_job'));
    }
}
