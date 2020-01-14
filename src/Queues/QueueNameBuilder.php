<?php


namespace Netlinker\FairQueue\Queues;


use Netlinker\FairQueue\Exceptions\FairQueueException;
use Netlinker\FairQueue\Models\ModelKey;

class QueueNameBuilder
{

    /**
     * Build queue name
     *
     * @param $queue
     * @param $modelId
     * @param string $modelKey
     * @return string
     * @throws FairQueueException
     */
    public static function build($queue, $modelKey, $modelId)
    {
        return 'fair_queue:' . $queue . ':' . $modelKey . ':' . $modelId;
    }
}