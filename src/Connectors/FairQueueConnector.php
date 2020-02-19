<?php

namespace NetLinker\FairQueue\Connectors;

use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\Connectors\RedisConnector as BaseConnector;
use Illuminate\Queue\Worker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Request;
use Laravel\Horizon\Horizon;
use Laravel\Horizon\MasterSupervisor;
use Laravel\Horizon\Supervisor;
use NetLinker\FairQueue\Drivers\FairQueueDriver;
use NetLinker\FairQueue\Events\WorkerStarted;
use NetLinker\FairQueue\Events\WorkerStarting;
use NetLinker\FairQueue\FairQueue;

class FairQueueConnector extends BaseConnector
{

    /**
     * Create a new Redis queue connector instance.
     *
     * @param \Illuminate\Contracts\Redis\Factory $redis
     * @param string|null $connection
     * @return void
     * @throws \NetLinker\FairQueue\Exceptions\FairQueueException
     */
    public function __construct(Redis $redis, $connection = null)
    {
        event(new WorkerStarting());
        event(new WorkerStarted());
        parent::__construct($redis, $connection);
    }

    /**
     * Establish a queue connection.
     *
     * @param array $config
     * @return FairQueueDriver
     */
    public function connect(array $config)
    {
        return new FairQueueDriver(
            $this->redis, $config['queue'] ?? 'default',
            Arr::get($config, 'connection', $this->connection),
            Arr::get($config, 'retry_after', 60),
            Arr::get($config, 'block_for', null)
        );
    }
}
