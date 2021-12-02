<?php

namespace NetLinker\FairQueue\Connectors;

use Illuminate\Contracts\Redis\Factory as Redis;
use Illuminate\Queue\Connectors\RedisConnector as BaseConnector;
use Illuminate\Support\Arr;
use NetLinker\FairQueue\Drivers\FairQueueDriver;

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
            $this->redis,
            Arr::get($config, 'queue', config('fair-queue.default_queue')),
            Arr::get($config, 'connection', $this->connection),
            Arr::get($config, 'retry_after', config('fair-queue.retry_after')),
            Arr::get($config, 'block_for', config('fair-queue.block_for'))
        );
    }
}
