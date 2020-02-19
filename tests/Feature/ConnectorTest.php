<?php


namespace NetLinker\FairQueue\Tests\Feature;

use Illuminate\Queue\Connectors\RedisConnector;
use Illuminate\Queue\QueueManager;
use NetLinker\FairQueue\Connectors\FairQueueConnector;
use NetLinker\FairQueue\Drivers\FairQueueDriver;
use NetLinker\FairQueue\Tests\TestCase;


class ConnectorTest
{

    public function testConnection(): void
    {

        $this->app['config']->set('queue.connections.fair-queue', [
            'driver' => 'fair-queue',
            'connection' => 'fair-queue',
        ]);

        /** @var QueueManager $queue */
        $queue = $this->app['queue'];

        /** @var FairQueueConnector $connection */
        $connection = $queue->connection('fair-queue');

        $this->assertInstanceOf(FairQueueDriver::class, $connection);
    }
}