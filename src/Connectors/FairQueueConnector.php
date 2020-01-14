<?php

namespace Netlinker\FairQueue\Connectors;

use Illuminate\Queue\Connectors\ConnectorInterface;
use Illuminate\Contracts\Redis\Factory as Redis;
use Netlinker\FairQueue\Drivers\FairQueueDriver;

class FairQueueConnector implements ConnectorInterface
{

    /** @var Redis $redis */
    protected $redis;

    /**
     * Constructor
     *
     * @param Redis $redis
     */
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }


    /**
     * Establish a queue connection.
     *
     * @param array $config
     * @return \Illuminate\Contracts\Queue\Queue
     */
    public function connect(array $config)
    {
        return new FairQueueDriver($this->redis);
    }
}