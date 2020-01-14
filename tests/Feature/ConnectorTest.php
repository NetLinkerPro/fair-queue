<?php


namespace Netlinker\FairQueue\Tests\Feature;

use Illuminate\Queue\Connectors\RedisConnector;
use Illuminate\Queue\QueueManager;
use Netlinker\FairQueue\Connectors\FairQueueConnector;
use Netlinker\FairQueue\Drivers\FairQueueDriver;
use Netlinker\FairQueue\Tests\TestCase;


class ConnectorTest extends TestCase
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